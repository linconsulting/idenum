<?php

namespace Trial\Db\Tables;



/**
 * Description of CostiRipartiti
 *
 * @author giacomo.solazzi
 */
class CostiRipartiti extends AbstractDbTables {

    // Foreign Keys
    const FK_VOCI_SPESE = 'idvsp';    
    const FK_COSTI_RIPARTIBILI = 'idtca';
    
    const FLD_IMPORTO = 'importo';
    
    
    public function __construct($cond = NULL) {
        
        parent::__construct('gns_tipol_costo_iad', $cond);
        
    }    
    
    
    /**
     * 
     * @param float $importoDoc - Importo del documento voce di spesa
     * @return boolean
     */
    
    
    public function importiCoerenti($importoDoc){
        
        if(!is_null($this->rowset)){
            
            $impAss = 0;
            
            foreach ($this->rowset as $row) {                
                $impAss += $row[self::FLD_IMPORTO];                
            }
            
            if (($impAss - floatval($importoDoc)) < parent::DOUBLE_COMPARE_PRECISION) {
                return TRUE;
            } else {
                return FALSE;
            }
            
        }else{
            return FALSE;
        }
        
    }
    
    
    


}
