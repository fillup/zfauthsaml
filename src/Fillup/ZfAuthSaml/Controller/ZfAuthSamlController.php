<?php
namespace Fillup\ZfAuthSaml\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Authentication\AuthenticationService;
use Fillup\ZfAuthSaml\Entity\User;

class ZfAuthSamlController extends AbstractActionController
{
    public function loginAction()
    {
        $auth = new AuthenticationService();
        try {
            $hasIdent = $auth->hasIdentity();
        } catch(\Exception $e) {
            $hasIdent = false;
        }
        if(!$hasIdent){
            $authAdapter = new \Fillup\ZfAuthSaml\Adapter();
            $loginUrl = $authAdapter->getLoginUrl('http://restapi.local/return');
            $this->redirect()->toUrl($loginUrl);
        } else {
            $this->redirect()->toUrl('/identity');
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
        $auth = new AuthenticationService();
        if($auth->hasIdentity()){
            //$auth->clearIdentity();
            unset($_SESSION['Zend_Auth']);
            $authAdapter = new \Fillup\ZfAuthSaml\Adapter();
            $logoutUrl = $authAdapter->getLogoutUrl('http://restapi.local/');
            $this->redirect()->toUrl($logoutUrl);
        } else {
            $this->redirect()->toUrl('/');
        }
    }
    
    public function returnAction()
    {
        $auth = new AuthenticationService();
        $authAdapter = new \Fillup\ZfAuthSaml\Adapter();
        try {
            $result = $auth->authenticate($authAdapter);
            if(!$result->isValid()){
                $this->redirect()->toUrl('/');
            } else {
                $this->redirect()->toUrl('/identity');
            }
        } catch (\Exception $e) {
            $this->redirect()->toUrl('/');
        }
    }
}