<?php

namespace Trial\Mdm\DataValidation;


/**
 * Description of FieldValidated
 * 
 * Classe usata per le informazioni 
 * in risposta al controllo di validità dei campi
 * nel db
 * 
 * @author giacomo.solazzi
 */

class FieldValidated extends Helpers\JsonResp {
    
    
    public $name;
    public $class;
    public $recordID;


    public function __construct($name = '', $resultStatus = TRUE, $resultResponse = '', $class = '', $recordID = 0) {
        
        $this->name = $name;
        $this->class = $class;
        $this->recordID = $recordID;
        parent::__construct($resultStatus, $resultResponse);
        
    }
    
    
    
    /**
     * esito della convalida
     * 
     * @return boolean
     */
    
    public function responseIsOk(){
        
        if(is_array($this->response) && $this->response['resp_ok']){
            return TRUE;
        }else{
            return FALSE;
        }
        
    }
    
    
    /**
     * contenuto della risposta alla convalida
     * 
     * @return mixed - object or NULL
     */
    
    public function getResponseContent(){
        
        if(is_array($this->response) && $this->response['resp_obj']){
            return $this->response['resp_obj'];
        }else{
            return NULL;
        }
        
    }
    
    
    /**
     * array con esito + contenuto della risposta alla convalida
     * 
     * @return array - array('resp_ok' => BOOL, 'resp_obj' => OBJECT)
     */
    
    public function getFullResponse(){
        
        return $this->response;
        
    }
    
    
    
}
