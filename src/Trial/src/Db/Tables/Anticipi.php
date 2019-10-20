<?php

namespace Trial\Db\Tables;


/**
 * Description of Anticipi
 *
 * @author giacomo.solazzi
 */
class Anticipi extends AbstractDbTables {
    
    
    
    // Foreign Keys
    const FK_RENDICONTAZIONI = 'id_rendic';    
    const FK_VALUTE = 'id_valuta';    
    
    const FLD_ORIGINE = 'person';
    const FLD_TASSO = 'tasso';
    const FLD_DATA_PRELIEVO = 'data_prel';
    const FLD_IMPORTO = 'importo';
    
    const PERSONALE = '1';
    const AZIENDALE = '0';
    
    public $valutaRowset = NULL;
    public $cambiRowset = NULL;
    public $rendicontazioneRowset = NULL;



    public function __construct($cond = NULL) {
        
        parent::__construct('gns_anticipi', $cond);
        
    }

    
    
    /**
     * 
     * @return $this - \DbTables\Anticipi
     */
    public function Valuta() {        
        

        if (!is_null($this->rowset)) {
            
            $ot = new Valute($this->rowset->current()[self::FK_VALUTE]);            
            $this->valutaRowset = $ot->rowset;
            unset($ot);
        }

        return $this;
    }
    
    
    /**
     * 
     * @param bool $obj - se si vuole l'oggetto istanziato
     * 
     * @return \DbTables\Anticipi | \DbTables\CambiValute
     * 
     */
    public function Cambi($obj = FALSE) {        
        

        if (!is_null($this->rowset)) {
            
            $ot = new CambiValute([CambiValute::FK_ANTICIPI => $this->rowset->current()[self::PK]]);            
            if($obj){
                return $ot;
            }
            
            $this->cambiRowset = $ot->rowset;
            unset($ot);
        }

        return $this;
    }






    /**
     * 
     * @param bool $obj - se si vuole l'oggetto istanziato
     * 
     * @return \DbTables\Anticipi | \DbTables\Rendicontazioni
     */
    public function Rendicontazione($obj = FALSE) {        
        

        if (!is_null($this->rowset)) {
            
            $ot = new Rendicontazioni($this->rowset->current()[self::FK_RENDICONTAZIONI]);            
            
            if($obj){
                return $ot;
            }
            
            $this->rendicontazioneRowset = $ot->rowset;
            unset($ot);
        }

        return $this;
    }
    
    
    
    

}
