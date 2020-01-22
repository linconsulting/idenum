<?php


namespace Api\ApiContainer;

use Zend\Diactoros\Response\TextResponse;
use Zend\Db\Adapter\Adapter;



/**
 * Description of Ifxcal
 *
 * @author giacomo.solazzi
 */


class Ifxcal {
    
    private $dbParams;


    public function __construct($dbParams) {
        
        $this->dbParams = $dbParams;
        
    }

    

    public function process(){
        
        $dbAdapter = new Adapter($this->dbParams);
        
        $resulSet = $dbAdapter->query('SELECT * FROM `t1` WHERE `c1` = ?', [1]);
        
        return new TextResponse("Risposta OK - ".print_r($resulSet,TRUE));
        
    }
    
}
