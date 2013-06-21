<?php
namespace ZfAuthSaml\Provider\Role;

interface DefaultRoleProviderInterface
{
    public function addRole($serviceManager, $userRoleTable, $userIdField, $roleIdField, $userId, $roleId);
}
