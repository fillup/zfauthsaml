<?php
namespace ZfAuthSaml\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Authentication\AuthenticationService;
use ZfAuthSaml\Adapter as AuthAdapter;

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
            $authAdapter = new AuthAdapter($this->getServiceLocator());
            $loginUrl = $authAdapter->getLoginUrl('/return');
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
            //$auth->clearIdentity(); // Causes bug with sharing session with simpleSAMLphp
            unset($_SESSION['Zend_Auth']);
            $authAdapter = new AuthAdapter($this->getServiceLocator());
            $logoutUrl = $authAdapter->getLogoutUrl($config['zfauthsaml']['redirectAfterLogout']);
            $this->redirect()->toUrl($logoutUrl);
        } else {
            $this->redirect()->toUrl('/');
        }
    }
    
    public function returnAction()
    {
        $config = $this->getServiceLocator()->get('Config');
        $auth = new AuthenticationService();
        $authAdapter = new AuthAdapter($this->getServiceLocator());
        try {
            $result = $auth->authenticate($authAdapter);
            if(!$result->isValid()){
                $this->redirect()->toUrl($config['zfauthsaml']['redirectAfterLogout']);
            } else {
                $this->redirect()->toUrl($this->getLoginRedirectUrl());
            }
        } catch (\Exception $e) {
            $this->redirect()->toUrl('/');
        }
    }
    
    protected function getLoginRedirectUrl()
    {
        $config = $this->getServiceLocator()->get('Config');
        if($config['zfauthsaml']['dynamicLoginRedirect']){
            $redirect = $this->getRequest()->getQuery('redirect',false);
            if($redirect){
                return $redirect;
            } else {
                return isset($config['zfauthsaml']['redirectAfterLogin']) 
                    ? $config['zfauthsaml']['redirectAfterLogin']
                    : '/';
            }
        } else {
            return isset($config['zfauthsaml']['redirectAfterLogin']) 
                    ? $config['zfauthsaml']['redirectAfterLogin']
                    : '/';
        }
    }
    
}