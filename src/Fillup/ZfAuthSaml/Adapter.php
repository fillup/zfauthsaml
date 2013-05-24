<?php
namespace Fillup\ZfAuthSaml;

use Zend\Authentication\Adapter\AdapterInterface;

class Adapter implements AdapterInterface
{
    protected $auth;
    
    public function __construct() 
    {
        try {
            $this->auth = new \SimpleSAML_Auth_Simple('default-sp');
        } catch (\Excaption $e){
            
        }
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
        
    }
    
    /*
     * Get login url
     * 
     * @return String
     */
    public function getLoginUrl()
    {
        return $this->auth->getLoginURL();
    }
    
}