<?php

namespace Trial\Db\Tables;



/**
 * Description of SettingsProfili
 *
 * @author giacomo.solazzi
 */
class SettingsProfili extends AbstractDbTables {

    
    // Foreign Keys
    const FK_PROFILIUTENTE = 'idprof';
    const FK_OGGETTI = 'idsobj';
    
    const FLD_VALORE = 'vobj';
    

    public function __construct($cond = NULL) {
        
        parent::__construct('gns_settings_profiles', $cond);
        
    }    
    
    

}
