<?php
namespace Fillup\ZfAuthSaml\Provider\Identity;

use BjyAuthorize\Exception\InvalidRoleException;
use BjyAuthorize\Provider\Identity\ProviderInterface as IdentityProviderInterface;
use Zend\Permissions\Acl\Role\RoleInterface;
use Zend\Authentication\AuthenticationService;

class SamlIdentityProvider implements IdentityProviderInterface
{
        /**
     * @var AuthenticationService
     */
    protected $authService;

    /**
     * @var string|\Zend\Permissions\Acl\Role\RoleInterface
     */
    protected $defaultRole = 'guest';

    /**
     * @param AuthenticationService $authService
     */
    public function __construct()
    {
        $this->authService = new AuthenticationService();
    }
    
    /**
     * {@inheritDoc}
     */
    public function getIdentityRoles()
    {
        $identity = $this->authService->getIdentity();
        if($identity && is_array($identity['groups'])){
            return $identity['groups'];
        } else {
            return array($this->defaultRole);
        }
    }
    
    /**
     * Get the rule that's used if you're not authenticated
     *
     * @return string|\Zend\Permissions\Acl\Role\RoleInterface
     */
    public function getDefaultRole()
    {
        return $this->defaultRole;
    }
    
    /**
     * Set the rule that's used if you're not authenticated
     *
     * @param $defaultRole
     *
     * @throws \BjyAuthorize\Exception\InvalidRoleException
     */
    public function setDefaultRole($defaultRole)
    {
        if ( ! ($defaultRole instanceof RoleInterface || is_string($defaultRole))) {
            throw InvalidRoleException::invalidRoleInstance($defaultRole);
        }

        $this->defaultRole = $defaultRole;
    }
    
}
