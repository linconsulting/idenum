<?php


namespace Trial\Mdm;
use Trial\Db\Entity\Eav;

/**
 * Description of PackageUtente
 *
 * @author giacomo.solazzi
 */
class PackageUtente extends Eav {
    
    private static $ref = 'package utente';
    private static $refId = NULL;

    
    public function __construct() {
        
        if(is_null(self::$refId)){            
            parent::__construct(['oggetto' => self::$ref]);
            self::$refId = $this->rowset->current()[parent::PK];
        }
        
        
    }

    
    public function __toString() {
        
        return (string)self::$refId;
    }
    
    
    
}
