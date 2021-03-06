<?php
namespace ZfAuthSaml\Authentication;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Adapter\Exception\InvalidArgumentException;
use Zend\Authentication\Result;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use ZfcUser\Mapper\UserInterface as UserMapperInterface;
use ZfcUser\Options\UserServiceOptionsInterface;
use ZfAuthSaml\Entity\User;

class Adapter implements AdapterInterface, ServiceManagerAwareInterface
{
    protected $auth;
    protected $zfcUserMapper;
    protected $zfcUserOptions;
    protected $serviceManager;
    protected $config;

    public function __construct(ServiceManager $sm = null) 
    {
        if(!is_null($sm)){
            $this->setServiceManager($sm);
        }
        $this->auth = new \SimpleSAML_Auth_Simple('default-sp');
    }
    
    /**
     * Checks if user is already authenticated
     *
     * @return \Zend\Authentication\Result
     * @throws \Zend\Authentication\Adapter\Exception\ExceptionInterface
     *               If authentication cannot be performed
     */
    public function authenticate()
    {
        if(!$this->auth->isAuthenticated()){
            throw new InvalidArgumentException('User is not authenticated',Result::FAILURE);
        } else {
            $attrs = $this->auth->getAttributes();
            
            $zfcUserOptions = $this->getZfcUserOptions();
            $config = $this->getConfig();
            // Check if local user already exists
            $userMapper = $this->getZfcUserMapper();
            $user = $userMapper->findByEmail($attrs[$config['emailField']][$config['emailFieldElement']]);
            if(!$user){
                $user = $this->instantiateLocalUser();
                $user->setDisplayName($attrs[$config['displayNameField']][$config['displayNameFieldElement']]);
                $user->setPassword('not actually stored');
                $user->setEmail($attrs[$config['emailField']][$config['emailFieldElement']]);
                $user->setUsername($attrs[$config['usernameField']][$config['usernameFieldElement']]);
                $user->setFirstName($attrs[$config['firstNameField']][$config['firstNameFieldElement']]);
                $user->setLastName($attrs[$config['lastNameField']][$config['lastNameFieldElement']]);
                $user->setGroups($attrs[$config['groupsField']]);
                $user->setRawIdentity($attrs);
                
                // If user state is enabled, set the default state value
                if ($zfcUserOptions->getEnableUserState()) {
                    if ($zfcUserOptions->getDefaultUserState()) {
                        $user->setState($zfcUserOptions->getDefaultUserState());
                    }
                }
                
                $insert = $userMapper->insert($user);
                
                if($insert){
                    $userId = $user->getId();
                    if($userId){
                        if($config['defaultRoleProvider']){
                            $defaultRoleProvider = new $config['defaultRoleProvider'];
                            
                            $addRole = $defaultRoleProvider->addRole(
                                    $this->getServiceManager(), 
                                    $config['userRoleTable'], 
                                    $config['userIdField'], 
                                    $config['roleIdField'], 
                                    $userId, 
                                    $config['defaultRoleId']
                            );
                        }
                    }
                } else {
                    return new Result(Result::FAILURE_IDENTITY_NOT_FOUND,$user,array('Unable to create local user'));
                }
            } else {
                if ($zfcUserOptions->getEnableUserState()) {
                    // Don't allow user to login if state is not in allowed list
                    if (!in_array($user->getState(), $zfcUserOptions->getAllowedLoginStates())) {
                        return new Result(Result::FAILURE_UNCATEGORIZED,$user,array('This existing user is not active.'));
                    }
                }
            }
            
            $roleProvider = $this->getServiceManager()->get('BjyAuthorize\RoleProviders');
            $validRoles = array();
            foreach($roleProvider as $provider){
                $providerRoles = $provider->getRoles();
                foreach($providerRoles as $role){
                    $validRoles[] = $role->getRoleId();
                }
            }
            $userValidRoles = array();
            foreach($attrs['groups'] as $group){
                if(in_array($group,$validRoles)){
                    $userValidRoles[] = $group;
                }
            }
            
            $user->setGroups($userValidRoles);
            
            return new Result(Result::SUCCESS,$user);
            
        }
            
    }
    
    /*
     * Get login url
     * 
     * @return String
     */
    public function getLoginUrl($returnUrl=null)
    {
        return $this->auth->getLoginURL($returnUrl);
    }
    
    /*
     * Get logout url
     * 
     * @return String
     */
    public function getLogoutUrl($returnUrl=null)
    {
        return $this->auth->getLogoutURL($returnUrl);
    }
    
    /**
     * @param  array $config
     * @return ZfAuthSaml\Authentication\Adapter
     */
    public function setConfig($config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        if (!is_array($this->config)) {
            $config = $this->getServiceManager()->get('Config');
            $this->setConfig($config['zfauthsaml']);
        }

        return $this->config;
    }
    
    
    /**
     * @param  UserServiceOptionsInterface $options
     * @return HybridAuth
     */
    public function setZfcUserOptions(UserServiceOptionsInterface $options)
    {
        $this->zfcUserOptions = $options;

        return $this;
    }

    /**
     * @return UserServiceOptionsInterface
     */
    public function getZfcUserOptions()
    {
        if (!$this->zfcUserOptions instanceof UserServiceOptionsInterface) {
            $this->setZfcUserOptions($this->getServiceManager()->get('zfcuser_module_options'));
        }

        return $this->zfcUserOptions;
    }
    
    /**
     * set zfcUserMapper
     *
     * @param  UserMapperInterface $zfcUserMapper
     * @return HybridAuth
     */
    public function setZfcUserMapper(UserMapperInterface $zfcUserMapper)
    {
        $this->zfcUserMapper = $zfcUserMapper;

        return $this;
    }

    /**
     * get zfcUserMapper
     *
     * @return UserMapperInterface
     */
    public function getZfcUserMapper()
    {
        if (!$this->zfcUserMapper instanceof UserMapperInterface) {
            $this->setZfcUserMapper($this->getServiceManager()->get('zfcuser_user_mapper'));
        }

        return $this->zfcUserMapper;
    }

    /**
     * Utility function to instantiate a fresh local user object
     *
     * @return mixed
     */
    protected function instantiateLocalUser()
    {
        $userModelClass = $this->getZfcUserOptions()->getUserEntityClass();

        return new $userModelClass;
    }
    
    /**
     * Retrieve service manager instance
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Set service manager instance
     *
     * @param  ServiceManager $serviceManager
     * @return void
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }
    
}