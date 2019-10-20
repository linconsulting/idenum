<?php

namespace Trial\Db\Tables;

/**
 * Description of Rendicontazioni
 *
 * @author giacomo.solazzi
 */
class Rendicontazioni extends AbstractDbTables {

    // Foreign Keys
    const FK_UTENTI = 'id_utente';
    const FLD_DATA_CREAZIONE = 'data_creaz';

    public $utenteRowset = NULL;
    public $vociSpeseRowset = NULL;

    public function __construct($cond = NULL) {

        parent::__construct('gns_rendiconti', $cond);
    }

    /**
     * 
     * @param bool $obj TRUE se si vuole ottenere l'oggetto \DbTables\Utenti
     * @return mixed - \DbTables\Rendicontazioni | \DbTables\Utenti
     */
    public function Utente($obj = FALSE) {


        if (!is_null($this->rowset)) {

            $ot = new Utenti($this->rowset->current()[self::FK_UTENTI]);
            
            if($obj){
                return $ot;
            }
            
            $this->utenteRowset = $ot->rowset;
            unset($ot);
        }

        return $this;
    }

    /**
     * 
     * @param bool $obj TRUE se si vuole ottenere l'oggetto \DbTables\VociDiSpesa
     * @param array $cond   le condizioni del where aggiuntive
     * @param array $order es: [id => 'desc']
     * 
     * @return mixed - \DbTables\VociDiSpesa | \DbTables\Rendicontazioni
     */
    public function VociSpese($obj = FALSE, $cond = NULL, $order = NULL){


        if (!is_null($this->rowset)) {
            
            $ot = (new Spese([Spese::FK_RENDICONTAZIONI => $this->rowset->current()[self::PK]]))->VociDiSpesa(TRUE, $cond, $order);
            
            if($obj){
                return $ot;
            }
            
            $this->vociSpeseRowset = $ot->rowset;
            unset($ot);
        }

        return $this;
    }
    
    
    /**
     * Ritorna gli id delle valute utilizzate
     * 
     * @param bool $inclDescriz - se si vuole anche la descrizione della valuta
     * @return array
     */

    public function valuteUtilizzate($inclDescriz = FALSE) {
        
        $v = array();

        if (!is_null($this->vociSpeseRowset)) {

            foreach ($this->vociSpeseRowset as $row) {                
                $v[] = $row[VociSpese::FK_VALUTE];                
            }
        }
        
        $ret = array_unique($v);
        
        if($inclDescriz && !empty($ret)){
            
            $valute = new Valute([Valute::PK => $ret]);            
            unset($ret);
            
            foreach ($valute->rowset as $valuta) {                
                $ret[$valuta[$valute::PK]] = $valuta[$valute::FLD_CODICE];                
            }
            
            unset($valute);
        }

        return $ret;
    }
    
    
    
    

}
