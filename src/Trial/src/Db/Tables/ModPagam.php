<?php

namespace Trial\Db\Tables;



/**
 * Description of ModPagam
 *
 * @author giacomo.solazzi
 */
class ModPagam extends AbstractDbTables {
    
    const CONTANTE = '3';
    const FLD_DESCRIZIONE = 'descrmpag';
    
    public function __construct($cond = NULL) {
        
        parent::__construct('gns_mod_pag_a', $cond);
        
    }    
    
    

}
