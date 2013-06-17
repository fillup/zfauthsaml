<?php
namespace Fillup\ZfAuthSaml\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Authentication\AuthenticationService;
use Fillup\ZfAuthSaml\Adapter as AuthAdapter;
use ZfcUser\Mapper\User as UserMapper;
use ZfcUser\Entity\User as ZfcUser;

class ZfAuthSamlController extends AbstractActionController
{
    public function loginAction()
    {
        $config = $this->getServiceLocator()->get('Config');
        $auth = new AuthenticationService();
        try {
            $hasIdent = $auth->hasIdentity();
        } catch(\Exception $e) {
            $hasIdent = false;
        }
        if(!$hasIdent){
            $authAdapter = new AuthAdapter();
            $authAdapter->setServiceManager($this->getServiceLocator());
            $loginUrl = $authAdapter->getLoginUrl($config['zfauthsaml']['loginReturn']);
            $this->redirect()->toUrl($loginUrl);
        } else {
            $this->redirect()->toUrl($this->getLoginRedirectUrl());
        }
        
    }
    
    public function identityAction()
    {
        $auth = new AuthenticationService();
        if(!$auth->hasIdentity()){
            $this->redirect()->toUrl('/login');
        } else {
            echo "<pre>".print_r($auth->getIdentity(),true)."</pre>";
        }
    }
    
    public function logoutAction()
    {
        $config = $this->getServiceLocator()->get('Config');
        $auth = new AuthenticationService();
        if($auth->hasIdentity()){
            //$auth->clearIdentity();
            unset($_SESSION['Zend_Auth']);
            $authAdapter = new AuthAdapter();
            $logoutUrl = $authAdapter->getLogoutUrl($config['zfauthsaml']['logoutReturn']);
            $this->redirect()->toUrl($logoutUrl);
        } else {
            $this->redirect()->toUrl('/');
        }
    }
    
    public function returnAction()
    {
        $config = $this->getServiceLocator()->get('Config');
        $auth = new AuthenticationService();
        $authAdapter = new AuthAdapter();
        $authAdapter->setServiceManager($this->getServiceLocator());
        try {
            $result = $auth->authenticate($authAdapter);
            if(!$result->isValid()){
                $this->redirect()->toUrl($config['zfauthsaml']['logoutReturn']);
            } else {
                $identity = $auth->getIdentity();
                $userMapper = $this->getServiceLocator()->get('zfcuser_user_mapper');
                $user = $userMapper->findByEmail($identity->getEmail());
                if($user){
                    if($config['zfauthsaml']['autoProvisionRoles']){
                        $this->provisionMissingRoles($identity->getGroups());
                    }
                    $this->redirect()->toUrl($this->getLoginRedirectUrl());
                } else {
                    $identity = $auth->getIdentity();
                    $newUser = new ZfcUser();
                    $newUser->setEmail($identity->getEmail());
                    $newUser->setUsername($identity->getEmail());
                    $newUser->setDisplayName($identity->getDisplayName());
                    $newUser->setPassword('password not actually stored');
                    $newUser->setState('1');
                    $userMapper->insert($newUser);
                }
            }
        } catch (\Exception $e) {
            $this->redirect()->toUrl('/');
        }
    }
    
    protected function getLoginRedirectUrl()
    {
        $confg = $this->getServiceLocator()->get('Config');
        if($config['zfauthsaml']['dynamicLoginRedirect']){
            return $this->getRequest()->getQuery('redirect',false);
        } else {
            return isset($config['zfauthsaml']['staticLoginRedirect']) 
                    ? $config['zfauthsaml']['staticLoginRedirect']
                    : '';
        }
    }
    
    public function provisionMissingRoles($roles)
    {
        if(is_array($roles)){
            
        }
    }
}