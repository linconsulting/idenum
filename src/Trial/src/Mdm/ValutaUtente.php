<?php


namespace Trial\Mdm;
use Trial\Db\Entity\Eav;

/**
 * Description of ValutaUtente
 *
 * @author giacomo.solazzi
 */
class ValutaUtente extends Eav {
    
    private static $ref = 'valuta locale';
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
