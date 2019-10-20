<?php

namespace Trial\Db\Tables;



/**
 * Description of MotiviIntervento
 *
 * @author giacomo.solazzi
 */
class MotiviIntervento extends AbstractDbTables {
        
    const DA_SPECIFICARE = 9;
    const FLD_DESCRIZIONE = 'descrmot';
    
    public static $translatableFlds = array( self::FLD_DESCRIZIONE );



    public function __construct($cond = NULL) {
        
        parent::__construct('gns_motivi_a', $cond);        
        
        
    }    
    
    
    
    
    

}
