<?php

namespace Trial\Db\Tables;

use Zend\Db\Sql\Predicate\Operator;

/**
 * Description of VociSpese
 *
 * @author giacomo.solazzi
 */
class VociSpese extends AbstractDbTables {

    // Foreign Keys
    const FK_SPESE = 'id_int';
    const FK_PAESI = 'id_paese';
    const FK_VALUTE = 'id_valuta';
    const FK_MOD_PAGAM = 'mod_pag';
    const FK_MOTIVI_INTERVENTO = 'motivo_int';
    const FK_MATRICOLE = 'id_matricola';
    const FK_TIPI_COSTI = 'id_tipo_costo';
    const FK_SPECIFICHE_TIPO_COSTO = 'id_subl_costo';
    // Fields
    const FLD_IMPORTO = 'importo';
    const FLD_CONTROVALORE_HC = 'importo_hc';
    const FLD_DATA_COSTO = 'data_costo';
    const FLD_MOTIVO_SPEC = 'motivo_spec';
    const FLD_APPROVAZIONE = 'id_approv';
    const FLD_COSTI_MULTIPLI = 'multi_costo';
    const FLD_DESCRIZ_SPESA = 'descrsp';
    const FLD_DESCRIZ_DOC = 'descrdoc';
    const FLD_MARCA_AUTO = 'marca_auto';
    const FLD_MODELLO_AUTO = 'mod_auto';
    const FLD_TARGA_AUTO = 'targa_auto';
    const FLD_KM_PERCORSI = 'km_percorsi';
    
    // Defaults fields value
    const DEF_DATA_COSTO = '0000-00-00';

    // Rowsets
    public $raccoglitoreRowset = NULL;
    public $paeseRowset = NULL;
    public $valutaRowset = NULL;
    public $modPagamRowset = NULL;
    public $motivoInterventoRowset = NULL;
    public $matricolaRowset = NULL;
    public $tipoCostoRowset = NULL;

    /**
     * 
     * @param mixed $cond
     */
    public function __construct($cond = NULL, $order = NULL) {

        parent::__construct('gns_spese_r', $cond, $order);
    }

    /**
     * @param bool $obj True se si vuole ottere una istanza dell'oggetto spese
     * 
     * @return mixed - \DbTables\VociSpese o \DbTables\Spese
     */
    public function raccoglitore($obj = FALSE) {


        if (!is_null($this->rowset)) {

            $ot = new Spese($this->rowset->current()[self::FK_SPESE]);
            
            if($obj){
                return $ot;
            }
            
            $this->raccoglitoreRowset = $ot->rowset;
            unset($ot);
        }

        return $this;
    }

    /**
     * 
     * @param bool $fullObj
     * @return mixed - \DbTables\TipiCosti se $fullObj = TRUE o \DbTables\VociSpese
     */
    public function tipoCosto($fullObj = FALSE) {

        if (!is_null($this->rowset)) {

            $ot = new TipiCosti($this->rowset->current()[self::FK_TIPI_COSTI]);
            $ot->rifSpecifiche = $this->rowset->current()[self::FK_SPECIFICHE_TIPO_COSTO];

            if ($fullObj) {
                return $ot;
            }

            $this->tipoCostoRowset = $ot->rowset;

            unset($ot);
        }

        return $this;
    }

    /**
     * 
     * @return $this - \DbTables\VociSpese
     */
    public function paese() {


        if (!is_null($this->rowset)) {

            $ot = new Paesi($this->rowset->current()[self::FK_PAESI]);
            $this->paeseRowset = $ot->rowset;
            unset($ot);
        }

        return $this;
    }

    /**
     * 
     * @return $this - \DbTables\VociSpese
     */
    public function valuta() {


        if (!is_null($this->rowset)) {

            $ot = new Valute($this->rowset->current()[self::FK_VALUTE]);
            $this->valutaRowset = $ot->rowset;
            unset($ot);
        }

        return $this;
    }

    /**
     * 
     * @return $this - \DbTables\VociSpese
     */
    public function modPagamento() {


        if (!is_null($this->rowset)) {

            $ot = new ModPagam($this->rowset->current()[self::FK_MOD_PAGAM]);
            $this->modPagamRowset = $ot->rowset;
            unset($ot);
        }

        return $this;
    }

    /**
     * 
     * @return $this - \DbTables\VociSpese
     */
    public function motivoIntervento() {


        if (!is_null($this->rowset)) {

            $ot = new MotiviIntervento($this->rowset->current()[self::FK_MOTIVI_INTERVENTO]);
            $this->motivoInterventoRowset = $ot->rowset;
            unset($ot);
        }

        return $this;
    }

    /**
     * 
     * @return $this - \DbTables\VociSpese
     */
    public function matricola() {


        if (!is_null($this->rowset)) {

            $ot = new Matricole($this->rowset->current()[self::FK_MATRICOLE]);
            $this->matricolaRowset = $ot->rowset;
            unset($ot);
        }

        return $this;
    }

    /**
     * 
     * @param string $pk La chiave primaria del record
     * @param bool $ckApprov
     * @param bool $onlyApprov 
     * @return  \DataValidation\FieldsValidatedCollection
     * 
     */
    public function ckFields($pk, $ckApprov = FALSE, $onlyApprov = FALSE) {

        $tfVal = new TableFieldsValidation\VociSpese($this->getTable(), $pk);
        $vRet = $tfVal->checkRecord($ckApprov, $onlyApprov);
        unset($tfVal);

        return $vRet;
    }

    /**
     * 
     * return mixed int o false
     */
    public function aggiungi() {

        $lastRowset = $this->datiVocePrecedente();
        $newVsp = new VociSpese();

        if (!is_null($lastRowset) && $lastRowset->count() > 0) {

            $preData = $lastRowset->toArray()[0];

            $data = array(
                self::FLD_DATA_COSTO => $preData[self::FLD_DATA_COSTO],
                self::FK_TIPI_COSTI => 0,
                self::FK_PAESI => $preData[self::FK_PAESI],
                self::FK_VALUTE => $preData[self::FK_VALUTE],
                self::FK_MOD_PAGAM => $preData[self::FK_MOD_PAGAM] != ModPagam::CONTANTE ? 0 : $preData[self::FK_MOD_PAGAM],
                self::FK_MOTIVI_INTERVENTO => $preData[self::FK_MOTIVI_INTERVENTO],
                self::FLD_MOTIVO_SPEC => $preData[self::FLD_MOTIVO_SPEC],
                self::FK_MATRICOLE => $preData[self::FK_MATRICOLE]
            );
        } else {

            $data = array(
                self::FLD_DATA_COSTO => '0000-00-00',
                self::FK_TIPI_COSTI => 0,
                self::FK_PAESI => 0,
                self::FK_VALUTE => 1,
                self::FK_MOD_PAGAM => 0,
                self::FK_MOTIVI_INTERVENTO => 0,
                self::FLD_MOTIVO_SPEC => '',
                self::FK_MATRICOLE => 0
            );
        }


        $newVsp->rowset->populate($data);
        $ret = $newVsp->rowset->save();

        unset($newVsp);
        unset($lastRowset);

        return $ret;
    }

    /**
     * 
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function datiVocePrecedente() {

        $aw = $this->getWhereClause();

        if (count($aw) > 0 && $aw[0]['left'] == self::FK_SPESE && $aw[0]['operator'] == Operator::OPERATOR_EQUAL_TO) {

            return $this->selectWith($this->getSql()->select()->where([VociSpese::FK_SPESE => $aw[0]['right']])->order(self::PK . ' desc')->limit(1));
        } else {
            return NULL;
        }
    }
    
    
    /**
     * 
     * @return boolean
     */

    public function voceSpesaApprovata() {

        if (!is_null($this->rowset) && $this->rowset->count() > 0) {

            $v = new TableFieldsValidation\VociSpese($this->getTable(), $this->rowset->current()[self::PK]);

            foreach ($v->checkRecord(TRUE, TRUE)->getFields() as $field) {

                if (!$field->responseIsOk()) {

                    return FALSE;
                }
            }

            return TRUE;
        }
        
        return FALSE;
    }
    
    
    /**
     * 
     * @param int $idVsp
     * @return boolean
     */
    
    public static function haControvaloreHC($idVsp) {

        $hcVsp = new static($idVsp);        
        
        $ret = $hcVsp->rowset->current()[self::FLD_CONTROVALORE_HC];
        
        unset($hcVsp);
        
        if ($ret > 0) {            
            
            return TRUE;
            
        }else{
            return FALSE;
        }
    }

}
