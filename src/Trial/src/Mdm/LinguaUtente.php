<?php


namespace Trial\Mdm;
use Trial\Db\Entity\Eav;

/**
 * Description of LinguaUtente
 *
 * @author giacomo.solazzi
 */
class LinguaUtente extends Eav {
    
    private static $ref = 'lingua utente';
    private static $refId = NULL;
    
    const DEFAULT_ID_LANG = 1;
    const DEFAULT_LANG = 'italiano';


    
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
