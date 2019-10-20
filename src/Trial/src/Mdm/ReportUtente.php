<?php


namespace Trial\Mdm;
use Trial\Db\Entity\Eav;

/**
 * Description of ReportUtente
 *
 * @author giacomo.solazzi
 */
class ReportUtente extends Eav {
    
    private static $ref = 'report utente';
    private static $refId = NULL;
    
    const IMG_HEADER_SCH_DIP = 'img_header_sch_dip';
    const IMG_HEADER_SCH_EST = 'img_header_sch_est';
    const IMG_HEADER_SHO_DIP = 'img_header_sho_dip';
    
    const DEFAULT_IMG_HEADER_SCH_DIP = 'default_img_header_sch_dip';
    const DEFAULT_IMG_HEADER_SCH_EST = 'default_img_header_sch_est';
    const DEFAULT_IMG_HEADER_SHO_DIP = 'default_img_header_sho_dip';

    
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
