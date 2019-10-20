<?php

namespace Trial\Db\Tables;



/**
 * Description of CostiRipartibili
 *
 * @author giacomo.solazzi
 */
class CostiRipartibili extends AbstractDbTables {

    // Foreign Keys
    const FK_SPECIFICHE_TIPO_COSTO = 'idsubcosto';    
    const FK_SPECIFICHE_TIPO_COSTO_ALIAS_ASSOC = 'idsublcostoassoc';
    
    
    public function __construct($cond = NULL) {
        
        parent::__construct('gns_tipol_costo_assoc', $cond);
        
    }    
    


}
