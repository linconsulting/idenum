<?php

namespace Trial\Db\Tables;

use Zend\Db\RowGateway\AbstractRowGateway;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Where;

/**
 * Description of TipiCosti
 *
 * @author giacomo.solazzi
 */
class TipiCosti extends AbstractDbTables {
    
    
    const FLD_DETTAGLIO_RICHIESTO = 'spec_required';
    const FLD_NOME_COSTO = 'nomec';
    
    const COSTO_RIPARTIBILE = '1';   
    const DETTAGLIO_RICHIESTO = '1';
    const RIMBORSO_KM = '5';
    
    
    const SUBL_MULTI = 4;      
    const VITTO = 1;
    const ALLOGGIO = 3;
    
    const SP_NO_DOC = 4;
    const SP_RIMB_KM = 5;
    const SP_CONTRATTUALE = 6;
    
    
    public static $translatableFlds = array(self::FLD_NOME_COSTO);
    public $dettagliSecondariRowset = NULL;
    public $rifSpecifiche = NULL;
    


    public function __construct($cond = NULL) {
        
        parent::__construct('gns_tipol_costo_a', $cond);
        
    }
    
    /**
     * 
     * @param int $idSpecificheTipoCosto
     * @param bool $onlyRowset
     * @return mixed \DbTables\SpecificheTipoCosto se $onlyRowset=FALSE o \DbTables\TipiCosti
     */
    
    
    public function dettagliSecondari($idSpecificheTipoCosto = NULL, $onlyRowset = TRUE){
        
        if(!is_null($idSpecificheTipoCosto)){
            $this->rifSpecifiche = $idSpecificheTipoCosto;
        }
        
        if (!is_null($this->rowset) && !is_null($this->rifSpecifiche)) {

            $ot = new SpecificheTipoCosto($this->rifSpecifiche);
            
            if(!$onlyRowset){
                return $ot;
            }
            
            $this->dettagliSecondariRowset = $ot->rowset;
            
            unset($ot);
        }

        return $this;
        
    }
    
    
    /**
     * 
     * ritorna la lista degli
     * id dei costi che hanno figli
     * 
     * @return array
     */
    
    
    public function listParent(){
        
        
        $aRet = array();
        
        if($this->rowset->count() == 0 && $this->rowset instanceof AbstractRowGateway){
            
            $this->rowset = $this->select(new Where([new Operator(self::PK, Operator::OPERATOR_GREATER_THAN_OR_EQUAL_TO, 0)]));
            
        }
        
        foreach ($this->rowset as $record){            
            
            if($this->multiLivello($record[self::PK])){
                
                $aRet[] = $record[self::PK];
                
            }
            
        }
                
        return $aRet;
        
    }
    
    /**
     * 
     * @return boolean
     */
    
    public function multiLivello($id){
        
        if(!is_null($this->rowset) && $this->rowset->count() > 0){
                        
            $w = (new Where([new Operator(SpecificheTipoCosto::FK_TIPI_COSTI, Operator::OPERATOR_EQUAL_TO, $id)]))->greaterThanOrEqualTo(SpecificheTipoCosto::FLD_LIVELLO, SpecificheTipoCosto::LIVELLO_SINGOLO);                        
            
            $cs = new SpecificheTipoCosto($w);
            
            $ret = $cs->rowset->count() > 0 ? TRUE : FALSE;
            
            unset($w);                        
            unset($cs);
            
            return $ret;
            
        }else{
            return FALSE;
        }
        
        
    }
    
    
    /**
     * 
     * ritorna la lista degli
     * id dei costi che sono multi costo
     * 
     * @return array
     */
    
    public function listMultiCost(){
        
        return array(self::SUBL_MULTI);
        
    }
    
    /**
     * 
     * @param array $ripartizione - [idVoceCosto => importo]
     * @param int $idVsp - voce di spesa
     * @return boolean
     */
    
    public function inserisciRipartizione($ripartizione, $idVsp){
        
        foreach ($ripartizione as $voceCosto => $importo) {
            
            $iad = new CostiRipartiti([CostiRipartiti::FK_VOCI_SPESE => $idVsp, CostiRipartiti::FK_COSTI_RIPARTIBILI => $voceCosto]);            
            $result = $iad->rowset->current();            
            
            $result->{$iad::FK_VOCI_SPESE} = $idVsp;
            $result->{$iad::FK_COSTI_RIPARTIBILI} = $voceCosto;
            $result->{$iad::FLD_IMPORTO} = $importo;            
            
            unset($iad);
            
            $ret = $result->save();
            
            unset($result);
            
            return $ret;
            
        }
        
        return TRUE;
        
        
    }
    


}
