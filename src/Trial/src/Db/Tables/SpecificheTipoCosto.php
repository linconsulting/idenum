<?php

namespace Trial\Db\Tables;



/**
 * Description of SpecificheTipoCosto
 *
 * @author giacomo.solazzi
 */
class SpecificheTipoCosto extends AbstractDbTables {

    
    const FK_TIPI_COSTI = 'idtipocosto';
    
    const FLD_NOME_COSTO_AGGIUNTIVO = 'nomesc';
    const FLD_LIVELLO = 'liv';
    
    const LIVELLO_SINGOLO = '1';


    public static $translatableFlds = array(self::FLD_NOME_COSTO_AGGIUNTIVO);
    
    public $costiRipartibiliRowset = NULL;
        
    
        
    public function __construct($cond = NULL) {
        
        parent::__construct('gns_tipol_costo_sl_a', $cond);
        
    }    
    
    
    /**
     * 
     * @return mixed \DbTables\SpecificheTipoCosto | BOOL
     */
    
    public function ripartibile(){
        
        if (!is_null($this->rowset)) {

            $ot = new CostiRipartibili([CostiRipartibili::FK_SPECIFICHE_TIPO_COSTO_ALIAS_ASSOC => $this->rowset->current()[parent::PK]]);                        
            
            $this->costiRipartibiliRowset = $ot->rowset;
            
            unset($ot);
                        
            if($ot->costiRipartibiliRowset->count() > 0){
                
                return TRUE;
                
            }else{
                return FALSE;
            }
            
        }
        
        return $this;
        
        
    }
    
    /**
     * 
     * @return mixed \DbTables\CostiRipartibili | NULL
     */
    
    public function costiRipartibiliAssociati(){
        
        if(!is_null($this->rowset)){
            
            return new CostiRipartibili([CostiRipartibili::FK_SPECIFICHE_TIPO_COSTO_ALIAS_ASSOC => $this->rowset->current()[parent::PK]]);                        
            
        }else{
            return NULL;
        }
        
    }


}
