<?php

namespace Trial\Resources\Pages\Helpers;

/**
 * 
 * Classe che gestisce le funzionalità comuni
 * usate per la visualizzazione e la gestione dei record del db
 * lato server e client 
 * 
 */
class ColumnHelper {

    public $fields = array();
    public $visibleFields = array();
    public $indexEditables = array();
    public $indexToExcl = array();
    public $indexToReplace = array();
    public $indexNotVisible = array();
    public $indexReferences = array();
    public $indexTranslatables = [];
    public $fkParentIndex = array();
    public $fkParentValue = NULL;

    /**
     * 
     * @param array $fields -- esempio ['id_rendic' => 'rendicontazione']
     */
    public function __construct($fields = []) {

        if (count($fields) > 0) {

            $this->fields = $fields;
            $this->setVisibleFields();
        }
    }

    /**
     * 
     * @return int
     */
    public function countFields() {

        return count($this->fields);
    }

    /**
     * inizializza i campi visibili
     * escludendo quelli definiti dalla proprietà $indexToExcl
     */
    public function setVisibleFields() {

        $fieldsExcluded = $this->getFieldByIndex($this->indexToExcl);
        $this->visibleFields = array_diff_key($this->fields, $fieldsExcluded);
    }

    /**
     * ritorna la lista di colonne 
     * escludendo quelle definite dalla proprietà $indexToExcl
     * 
     * @param bool $withReplace - se TRUE sostituisce attraverso gli indici indexToReplace
     *
     * @return array
     */
    public function getList($withReplace = FALSE) {

        $this->setVisibleFields();

        $aRet = $this->visibleFields;

        if ($withReplace) {
            $aRet = $this->replaceColumns();
        }


        return $aRet;
    }

    /**
     * effettua una sostituzione dei campi visibili
     * usato quando ci sono i campi referenziati
     * dove si vuole visualizzare ad esempio la descrizione di un id
     * 
     */
    private function replaceColumns() {

        $aRet = $this->visibleFields;

        if (count($this->indexToReplace) > 0) {
            
            while (current($this->indexToReplace)){
                                                
                if(isset($aRet[key($this->indexToReplace)])){                                        
                                        
                    foreach (array_flip(array_keys($aRet)) as $key => $index) {
                        
                        if($key == key($this->indexToReplace) && $index == 0){                            
                            
                            $aRet = array_merge(current($this->indexToReplace), is_array(array_slice($aRet, $index+1, count($aRet)-$index+1, TRUE)) ? array_slice($aRet, $index+1, count($aRet)-$index-1, TRUE) : [] );
                        }                        
                        if($key == key($this->indexToReplace) && $index > 0){
                            
                            $aRet = array_merge(array_slice($aRet, 0, $index, TRUE), current($this->indexToReplace), is_array(array_slice($aRet, $index+1, count($aRet)-$index-1, TRUE)) ? array_slice($aRet, $index+1, count($aRet)-$index-1, TRUE) : [] );
                            
                        }                        
                    }                    
                    
                }
                
                next($this->indexToReplace);                
            }            
            
        }

        return $aRet;
    }

    /**
     * ritorna la lista di colonne
     * in formato JSON con l'attributo
     * VISIBLE true o false 
     * da usare nella proprietà columns
     * del plugin DATATABLES
     * 
     * @param bool $withReplace - se TRUE sostituisce attraverso gli indici indexToReplace
     * 
     * @return string
     */
    public function getListJson($withReplace = FALSE) {

        $this->setVisibleFields();

        if ($withReplace) {
            $fields = $this->replaceColumns();
        } else {
            $fields = $this->visibleFields;
        }

        $aRet = array();

        foreach (array_flip(array_keys($fields)) as $index => $field) {

            $visible = TRUE;

            if (in_array($field, $this->indexNotVisible)) {
                $visible = FALSE;
            }

            $aRet[] = array('title' => $fields[$index], 'visible' => $visible);
        }

        return json_encode($aRet);
    }

    /**
     * ritorna la lista di colonne editabili
     * in base agli indici definiti dalla proprietà $indexEditables
     * 
     * Gli indici sono quelli visibili
     * 
     * @param bool $withReferences  se TRUE aggiunge anche i campi descrittivi referenziati (es descrizione valuta)
     * 
     * @return array
     */
    public function getEditables($withReferences = FALSE) {

        $fields = array();

        if ($withReferences) {

            $editables = array_merge(array_keys($this->getEditables()), array_keys($this->getReferenceables()));

            $editableVisible = array_intersect_key($editables, array_keys($this->visibleFields));

            $fields = $this->getFieldIndex($editableVisible);
        } else {

            $fields = $this->getFieldByIndex($this->indexEditables);
        }


        return $fields;
    }

    /**
     * Per sapere qual'è l'indice di una colonna editabile
     * conoscendo il nome del campo
     * 
     * @param string $key   è il nome del campo da ricercare
     * @return mixed        int per l'indice oppure FALSE se inesistente
     */
    public function getEditableIndex($key, $indexVisible = TRUE) {

        if ($indexVisible) {

            $baseArray = $this->visibleFields;
        } else {

            $baseArray = $this->fields;
        }

        $indexArray = array_flip(array_keys($baseArray));

        return in_array($key, array_keys($baseArray)) ? $indexArray[$key] : FALSE;
    }

    /**
     * 
     * restituisce l'indice del campo dal nome
     * 
     * @param string $key  "nome del campo" o array di nomi
     * @param boolean $visibleIndex     se TRUE la numerazione si basa sull'indici visibili
     * @return mixed    int o array di indici, FALSE se inesistente
     */
    public function getFieldIndex($key, $visibleIndex = FALSE) {

        $baseArray = $visibleIndex ? $this->visibleFields : $this->fields;

        if (is_array($key)) {

            $keysFields = array_flip(array_keys($baseArray));

            $keysToSearch = array_flip($key);

            $keyIndexes = array_intersect_key($keysFields, $keysToSearch);

            return array_values($keyIndexes);
        } else {

            $keysFields = array_keys($baseArray);

            $keysToIndexes = array_flip($keysFields);

            return in_array($key, $keysFields) ? $keysToIndexes[$key] : FALSE;
        }
    }

    /**
     * Per sapere qual'è il campo
     * conoscendo l'indice della colonna
     * 
     * @param int $key  indice o array di indici
     * @return mixed    array con l'elemento all'indice specificato, FALSE se non trovato
     */
    public function getFieldByIndex($key) {



        $keysFields = array_flip(array_keys($this->fields));

        if (is_array($key)) {

            $fields = array_intersect($keysFields, $key);

            return $fields;
        } else {

            $invFields = array_flip($keysFields);

            return in_array($key, $keysFields) ? array($invFields[$key] => $this->fields[$invFields[$key]]) : FALSE;
        }
    }

    /**
     * 
     * Per sapere se l'indice della colonna è un campo referenziato.
     * Se lo è ritorna l'indice della colonna a cui è referenziato
     * 
     * @param int $index    indice della colonna visibile
     * @return mixed        int per l'indice oppure FALSE se inesistente
     */
    public function indexIsReferred($index) {


        $keysVisibles = array_keys($this->visibleFields);

        if (isset($keysVisibles[$index])) {

            $fldName = $keysVisibles[$index];

            $fields = array_flip(array_keys($this->fields));

            if (in_array($fields[$fldName], array_keys($this->indexReferences))) {

                return $this->indexReferences[$fields[$fldName]];
            }
        }


        return FALSE;
    }

    /**
     * ritorna la lista dei campi referenziabili
     * 
     * associandogli l'indice della colonna visibile
     * 
     * @return array        | array("indice colonna visibile" => array("indice campo " => "indice a cui fa riferimento")
     */
    public function getReferenceables() {

        $keyFields = array_keys($this->fields);

        $referenciables = array_intersect_key($keyFields, $this->indexReferences);

        $fields = array_flip($referenciables);


        return $fields;
    }

}
