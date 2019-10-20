<?php

namespace Trial\Db\Tables;



/**
 * Description of TassiEffettivi
 *
 * @author giacomo.solazzi
 */
class TassiEffettivi extends AbstractDbTables {
        
    const FK_RENDICONTAZIONI = 'id_rendic';
    const FK_VALUTE = 'id_valuta';

    
    const FLD_TIPO_TASSO = 'tipot';
    const FLD_TASSO = 'tassoe';
    
    
    
    
    public function __construct($cond = NULL) {
        
        parent::__construct('gns_tassi_eff', $cond);
        
    }    
    
    

}
