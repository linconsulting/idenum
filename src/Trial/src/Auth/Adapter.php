<?php

namespace Trial\Auth;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Adapter\DbTable\CallbackCheckAdapter;

/**
 * Description of Adapter
 *
 * @author giacomo.solazzi
 */
class Adapter implements AdapterInterface {

    private $username;
    private $password;    
    private $dbAdapter;
    
    /**
     * 
     * @param \Zend\Db\Adapter\Adapter $dbAdapter
     */
    
    public function __construct(\Zend\Db\Adapter\Adapter $dbAdapter) {
        
        $this->dbAdapter = $dbAdapter;
        
    }
    
    /**
     * 
     * @param string $password
     */

    public function setPassword($password) {
        $this->password = $password;
    }
    
    /**
     * 
     * @param string $username
     */

    public function setUsername($username) {
        $this->username = $username;
    }

    /**
     * Performs an authentication attempt
     *
     * @return \Zend\Authentication\Result
     * @throws \Zend\Authentication\Adapter\Exception\ExceptionInterface
     *     If authentication cannot be performed
     */
    public function authenticate() {


        $authAdapter = new CallbackCheckAdapter($this->dbAdapter, 'gns_utenti_a', 'nome', 'psw', function ($hash, $password) {

            $psw_cr = crypt($password, $hash);

            if (strpos($psw_cr, $hash) !== FALSE) {
                return TRUE;
            } else {
                return FALSE;
            }
        }
        );


        $authAdapter->setIdentity($this->username)->setCredential($this->password);

        return $authAdapter->authenticate();
    }

}
