<?php

namespace Trial\Db\Tables;


/**
 * Description of CambiValute
 *
 * @author giacomo.solazzi
 */
class CambiValute extends AbstractDbTables {
    
    
    const FK_ANTICIPI = 'id_ant';
    const FK_VALUTE = 'id_valuta';
    
    const FLD_DATA = 'data_camb';
    const FLD_IMPORTO = 'importoc';
    const FLD_TASSO = 'tassoc';


    public function __construct($cond = NULL) {
        
        parent::__construct('gns_cambi_val', $cond);
        
    }


    
    

}
