<?php
namespace Fillup\ZfAuthSaml;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Adapter\Exception\InvalidArgumentException;
use Zend\Authentication\Result;

class Adapter implements AdapterInterface
{
    protected $auth;
    
    public function __construct() 
    {
        //try {
            $this->auth = new \SimpleSAML_Auth_Simple('default-sp');
        //} catch (\Excaption $e){
            
        //}
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
            return new Result(Result::SUCCESS,$this->auth->getAttributes());
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
    
}