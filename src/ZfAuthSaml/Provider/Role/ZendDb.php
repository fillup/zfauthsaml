<?php
namespace ZfAuthSaml\Provider\Role;

use Zend\Db\Sql\Sql;
use ZfAuthSaml\Provider\Role\DefaultRoleProviderInterface;

class ZendDb implements DefaultRoleProviderInterface
{
    public function addRole($serviceManager, $userRoleTable, $userIdField, $roleIdField, $userId, $roleId)
    {
        $adapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
        
        $sql = new Sql($adapter);
        $insert = $sql->insert()
                      ->into($userRoleTable)
                      ->columns(array($userIdField,$roleIdField))
                      ->values(array($userId,$roleId));
        $sqlString = $sql->getSqlStringForSqlObject($insert);
        $execute = $adapter->query($sqlString,$adapter::QUERY_MODE_EXECUTE);
        return $execute;
    }
}
