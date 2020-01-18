<?php


namespace Api\ApiContainer;

use Zend\Diactoros\Response\TextResponse;



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
        
        return new TextResponse("Risposta OK");
        
    }
    
}
