<?php

namespace Trial\Db\Tables;



/**
 * Description of Paesi
 *
 * @author giacomo.solazzi
 */
class Paesi extends AbstractDbTables {

    const FLD_NOME = 'nomep';


    
    public function __construct($cond = NULL) {
        
        parent::__construct('gns_paesi_a', $cond);
        
    }    
    

      
    
    
    

}
