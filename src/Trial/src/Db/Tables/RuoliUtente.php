<?php

namespace Trial\Db\Tables;


/**
 * Description of RuoliUtente
 *
 * @author giacomo.solazzi
 */
class RuoliUtente extends AbstractDbTables {

    
    // Foreign Keys
    const FK_RUOLI = 'ruolo_id';    
    const FK_UTENTI = 'user_id';
        

    public function __construct($cond = NULL) {
        
        parent::__construct('gns_acl_ruoli_utenti', $cond);
        
    }    

    
    

}
