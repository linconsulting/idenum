<?php

namespace Trial\Db\Tables\TableFieldsValidation;

use Trial\Db\Tables\AbstractDbTables;
use Trial\Db\Tables\VociSpese as Vsp;

use Trial\Mdm\DataValidation\FieldsValidatedCollection;
use Trial\Mdm\DataValidation\FieldValidated;

use Zend\Db\TableGateway\Feature\MetadataFeature;

/**
 * Description of VociSpese
 *
 * @author giacomo.solazzi
 */
class VociSpese extends AbstractDbTables {

    const FLD_MULTI_COSTO = 'multi_costo';
    const FLD_APPROVAZIONE = 'id_approv';
    const FLD_ID_NOTA = 'id_nota';
    const FLD_TIPO_COSTO = 'id_tipo_costo';

    public $fields;
    public $fkFieldsLabels = array();
    private $collectionFieldsValidated;
    private $ci;
    private $pk;

    public function __construct($table, $pk) {

        $this->pk = $pk;
        parent::__construct($table, $this->pk, new MetadataFeature());
        $this->fields = $this->getColumns();

        if (!isset($this->ci)) {
            $this->ci = & get_instance();
        }
    }

    /**
     * 
     * @param bool $ckApprov
     * @param bool $onlyApprov
     * @return  \DataValidation\FieldsValidatedCollection
     */
    public function checkRecord($ckApprov = FALSE, $onlyApprov = FALSE) {

        $this->collectionFieldsValidated = new FieldsValidatedCollection();

        $this->translateFkFieldLabels();

        if ($ckApprov) {
            $this->isApproved();
        }

        if ($ckApprov && $onlyApprov) {
            return $this->collectionFieldsValidated;
        }

        $this->isValidDate($this->fields[3]);

        $this->isValidFK(array(
            $this->fields[4],
            $this->fields[8],
            $this->fields[10],
            $this->fields[11],
            $this->fields[15],
            $this->fields[17]));

        $this->isValidSubCost();
        $this->isValidMultiCost();

        $this->isValidRimbKmMarca();
        $this->isValidRimbKmModel();
        $this->isValidRimbKmTarga();
        $this->isValidRimbKmDistanza();

        $this->isValidDescCC();
        $this->isValidImpRimb();

        return $this->collectionFieldsValidated;
    }

    /**
     * 
     */
    private function translateFkFieldLabels() {


        $this->fkFieldsLabels = array(
            'id_nota' => 'id spesa',
            'data_costo' => 'data documento',
            'id_tipo_costo' => 'tipo spesa',
            'id_subl_costo' => 'tipo spesa',
            'multi_costo' => 'tipo spesa',
            'id_paese' => 'paese',
            'id_valuta' => 'valuta',
            'importo' => 'importo',            
            'mod_pag' => 'mod. pagamento',
            'descrsp' => 'descrizione spesa carta di credito',
            'motivo_int' => 'motivo intervento'
        );
    }

    /**
     * 
     * verifica se la riga è stata APPROVATA
     * 
     *
     */
    private function isApproved() {


        $resultResponse = NULL;
        $resultStatus = FALSE;


        if ($this->rowset->current()[self::FLD_APPROVAZIONE] == '0') {
            $resultStatus = TRUE;
        }

        if (!$resultStatus) {
            $resultResponse = $this->txtMsg('Riga approvata, modifica non consentita');
        }

        $this->collectionFieldsValidated->addField(new FieldValidated($this->rowset->current()[self::FLD_APPROVAZIONE], $resultStatus, $resultResponse, __CLASS__, $this->rowset->current()[self::FLD_ID_NOTA]));
    }

    /**
     * 
     * verifica se la DATA è valida
     * 
     * @param string $field
     */
    private function isValidDate($field) {


        $resultResponse = NULL;
        $resultStatus = FALSE;


        if ($this->rowset->current()[$field] != '0000-00-00') {

            $resultStatus = TRUE;
        } else {

            $resultResponse = $this->txtMsg('Valore non selezionato', $field);
        }

        $this->collectionFieldsValidated->addField(new FieldValidated($field, $resultStatus, $resultResponse, __CLASS__, $this->rowset->current()[self::FLD_ID_NOTA]));
    }

    /**
     * 
     * verifica se la FK è valida
     * 
     * @param string $fields
     */
    private function isValidFK($fields) {


        if (is_array($fields)) {

            foreach ($fields as $field) {

                $resultResponse = NULL;
                $resultStatus = FALSE;


                if ($this->rowset->current()[$field] != '0') {

                    $resultStatus = TRUE;
                } else {

                    $resultResponse = $this->txtMsg('Valore non selezionato', $field);
                }

                $this->collectionFieldsValidated->addField(new FieldValidated($field, $resultStatus, $resultResponse, __CLASS__, $this->rowset->current()[self::FLD_ID_NOTA]));
            }
        }
    }

    /**
     * se è stato scelto un costo che richiede
     * la specifica di una ulteriore voce
     * il campo sublevel deve essere compilato
     * 
     */
    private function isValidSubCost() {

        $resultResponse = NULL;
        $resultStatus = FALSE;

        $hasChild = FALSE;

        $tc = new TipiCosti([Vsp::FK_TIPI_COSTI => $this->rowset->current()[self::FLD_TIPO_COSTO]]);

        if ($tc->rowset->current()[TipiCosti::FLD_DETTAGLIO_RICHIESTO] == TipiCosti::DETTAGLIO_RICHIESTO) {
            $hasChild = TRUE;
        }

        unset($tc);

        if (!$hasChild || ($hasChild && $this->rowset->current()[$this->fields[5]] != '0')) {

            $resultStatus = TRUE;
        } else {

            $resultResponse = $this->txtMsg('Dettagli tipo spesa incompleti');
        }

        $this->collectionFieldsValidated->addField(new FieldValidated($this->fields[5], $resultStatus, $resultResponse, __CLASS__, $this->rowset->current()[self::FLD_ID_NOTA]));
    }

    /**
     * se è stato scelto un costo MULTIPLO
     * gli importi associati devono coincidere
     *
     * 
     */
    private function isValidMultiCost() {

        $resultResponse = NULL;
        $resultStatus = FALSE;

        $ia = new CostiRipartiti([CostiRipartiti::FK_VOCI_SPESE => $this->rowset->current()[self::PK]]);

        if ($this->rowset->current()[$this->fields[6]] == '0' || ($this->rowset->current()[$this->fields[6]] != '0' && $ia->importiCoerenti($this->rowset->current()[$this->fields[11]]))) {

            $resultStatus = TRUE;
        } else {

            $resultResponse = $this->txtMsg('Totale ripartito diverso dal totale documento, correggere gli importi');
        }

        unset($ia);

        $this->collectionFieldsValidated->addField(new FieldValidated($this->fields[6], $resultStatus, $resultResponse, __CLASS__, $this->rowset->current()[self::FLD_ID_NOTA]));
    }

    /**
     *      
     * @param string $txt
     * @param string $field
     * @return string
     */
    private function txtMsg($txt, $field = NULL) {

        if (is_null($field)) {

            return sprintf('%s %s : %s', 'id spesa', $this->rowset->current()[self::FLD_ID_NOTA], $txt);
        } else {
            return sprintf('%s %s, %s : %s', 'id spesa', $this->rowset->current()[self::FLD_ID_NOTA], $this->fkFieldsLabels[$field], $txt);
        }
    }

    /**
     * se è stato scelto il rimborso chilometrico
     * la marca dell'auto deve essere compilata
     * 
     */
    private function isValidRimbKmMarca() {

        $resultResponse = NULL;
        $resultStatus = FALSE;        

        if ($this->rowset->current()[$this->fields[4]] != TipiCosti::RIMBORSO_KM || ($this->rowset->current()[$this->fields[4]] == TipiCosti::RIMBORSO_KM && $this->rowset->current()[$this->fields[19]] != '')) {

            $resultStatus = TRUE;
        } else {

            $resultResponse = $this->txtMsg('Marca auto non inserita');
        }

        $this->collectionFieldsValidated->addField(new FieldValidated($this->fields[19], $resultStatus, $resultResponse, __CLASS__, $this->rowset->current()[self::FLD_ID_NOTA]));
    }

    /**
     * se è stato scelto il rimborso chilometrico
     * il modello dell'auto deve essere compilato
     * 
     */
    private function isValidRimbKmModel() {

        $resultResponse = NULL;
        $resultStatus = FALSE;

        if ($this->rowset->current()[$this->fields[4]] != TipiCosti::RIMBORSO_KM || ($this->rowset->current()[$this->fields[4]] == TipiCosti::RIMBORSO_KM && $this->rowset->current()[$this->fields[20]] != '')) {

            $resultStatus = TRUE;
        } else {

            $resultResponse = $this->txtMsg('Modello auto non inserito');
        }

        $this->collectionFieldsValidated->addField(new FieldValidated($this->fields[20], $resultStatus, $resultResponse, __CLASS__, $this->rowset->current()[self::FLD_ID_NOTA]));
    }

    /**
     * se è stato scelto il rimborso chilometrico
     * la targa dell'auto deve essere compilata
     * 
     */
    private function isValidRimbKmTarga() {

        $resultResponse = NULL;
        $resultStatus = FALSE;

        if ($this->rowset->current()[$this->fields[4]] != TipiCosti::RIMBORSO_KM || ($this->rowset->current()[$this->fields[4]] == TipiCosti::RIMBORSO_KM && $this->rowset->current()[$this->fields[21]] != '')) {

            $resultStatus = TRUE;
        } else {

            $resultResponse = $this->txtMsg('Targa auto non inserita');
        }

        $this->collectionFieldsValidated->addField(new FieldValidated($this->fields[21], $resultStatus, $resultResponse, __CLASS__, $this->rowset->current()[self::FLD_ID_NOTA]));
    }

    /**
     * se è stato scelto il rimborso chilometrico
     * la distanza percorsa deve essere compilata
     * 
     */
    private function isValidRimbKmDistanza() {

        $resultResponse = NULL;
        $resultStatus = FALSE;

        if ($this->rowset->current()[$this->fields[4]] != TipiCosti::RIMBORSO_KM || ($this->rowset->current()[$this->fields[4]] == TipiCosti::RIMBORSO_KM && $this->rowset->current()[$this->fields[22]] != '')) {

            $resultStatus = TRUE;
        } else {

            $resultResponse = $this->txtMsg('Chilometri percorsi non inseriti');
        }

        $this->collectionFieldsValidated->addField(new FieldValidated($this->fields[22], $resultStatus, $resultResponse, __CLASS__, $this->rowset->current()[self::FLD_ID_NOTA]));
    }
    
    
    
    /**
     * 
     * verifica se il campo descrittivo
     * 
     * della spesa con CARTA DI CREDITO
     * 
     * è stato compilato
     *      
     */
    private function isValidDescCC() {

        $resultResponse = NULL;
        $resultStatus = FALSE;

        if ($this->rowset->current()[$this->fields[15]] == '3' || ($this->rowset->current()[$this->fields[15]] != '3' && $this->rowset->current()[$this->fields[24]] != '')) {

            $resultStatus = TRUE;
            
        } else {

            $resultResponse = $this->txtMsg('Campo obbligatorio', $this->fields[24]);
        }

        $this->collectionFieldsValidated->addField(new FieldValidated($this->fields[24], $resultStatus, $resultResponse, __CLASS__, $this->rowset->current()[self::FLD_ID_NOTA]));
    }
    
    
    /**
     * controllo IMPORTO RIMBORSABILE
     */
    
    private function isValidImpRimb(){
        
        $resultResponse = NULL;
        $resultStatus = FALSE;


        if ($this->rowset->current()[$this->fields[12]] == NULL || $this->rowset->current()[$this->fields[12]] <= $this->rowset->current()[$this->fields[11]]) {

            $resultStatus = TRUE;
            
        } else {

            $resultResponse = $this->txtMsg('Importo rimborsabile eccessivo, correggere il valore inserito');
        }

        $this->collectionFieldsValidated->addField(new FieldValidated($this->fields[12], $resultStatus, $resultResponse, __CLASS__, $this->rowset->current()[self::FLD_ID_NOTA]));
        
        
    }
    
    

}
