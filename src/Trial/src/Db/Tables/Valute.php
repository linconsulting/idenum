<?php

namespace Trial\Db\Tables;



/**
 * Description of Valute
 *
 * @author giacomo.solazzi
 */
class Valute extends AbstractDbTables {
        
    const FLD_CODICE = 'codv';
    
    const HC = 1; // home currency
    
    public function __construct($cond = NULL) {
        
        parent::__construct('gns_valute_a', $cond);
        
    }    
    
    

}
