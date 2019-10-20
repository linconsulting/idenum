<?php


namespace Trial\Resources\Pages;

use Zend\I18n\Translator\Translator;
use Trial\Resources\Pages\PageDataLoader;
use Trial\Db\Tables\ProfiliUtente;
use Trial\Mdm\LocaleUtente;





/**
 * Description of AbstractDataLoader
 *
 * @author giacomo.solazzi
 */
class AbstractDataLoader {
    
    
    protected $columnsHelper;
    protected $join = [];
    protected $translatableIndexFields = [];
    protected $where = [];


    public $requestData = NULL;
    public $translatorConfig = NULL;
    public $userIdentity = NULL;
    public $infoArea = [];
    
    
    
    /**
     * 
     * @param string $name - il nome della risorsa da istanziare
     * @return resource
     */
    
    public function getOtherResources($name){
        
        $resource = PageDataLoader::getClass($name);        
        $objResource = new $resource();        
        $objResource->userIdentity = $this->userIdentity;
        $objResource->translatorConfig = $this->translatorConfig;
        $objResource->infoArea = $this->infoArea;
        return $objResource;
    }
    
    
    /**
     * 
     * @return \Trial\Resources\Pages\Helpers\ColumnHelper
     */
    public function getColumnsHelper() {
        return $this->columnsHelper;
    }
    
    /**
     * 
     * @return array
     */
    public function dataJoinTables() {
        return $this->join;
    }
    
    
    /**
     * 
     * @return array|NULL
     */
    
    public function getWhere(){
        
        if(empty($this->where)){
            return NULL;
        }
        return $this->where;
               
    }
    
    /**
     * 
     * @param mixed $param
     */
    public function setRequestData($param = NULL) {

        $this->requestData = $param;
    }
    

    
    /**
     * 
     * @return array
     */
    public function setDatatableFields($withAlias = FALSE) {

        $columns = array();
        $i = -1;

        foreach (array_keys($this->columnsHelper->getList()) as $field) {

            if (isset($this->join[$field]) && isset($this->join[$field]['returnColumn'])) {

                if ($withAlias) {
                    $class = $this->join[$field]['class'];                                        
                    $field = $class::DB_TABLE_ALIAS.'.'.array_keys($this->join[$field]['returnColumn'])[0];
                } else {                    
                    $field = array_keys($this->join[$field]['returnColumn'])[0];
                }
                $columns[] = array('db' => $field, 'dt' => ++$i);
                continue;
            }
            
            if($withAlias){
                $field = static::DB_TABLE_ALIAS.'.'.$field;
            }

            $columns[] = array('db' => $field, 'dt' => ++$i);
            
        }

        return $columns;
    }
    
    
    
    /**
     * 
     * @param bool $withReplace - sostituisce il campo referenziato
     * @return string
     */
    
    public function translateDatatableHeaders($withReplace = FALSE){
        
        $fields = $this->getColumnsHelper()->getList($withReplace);        
        
        $aRet = array();
        
        $translReq = FALSE;
        
        if(ProfiliUtente::getUserLocale($this->userIdentity) != LocaleUtente::DEFAULT_LOCALE) {
            
            $translReq = TRUE;
            $translator = Translator::factory($this->translatorConfig);
            
        }        

        foreach (array_flip(array_keys($fields)) as $index => $field) {

            $visible = TRUE;

            if (in_array($field, $this->getColumnsHelper()->indexNotVisible)) {
                $visible = FALSE;
            }

            $aRet[] = array('title' => $translReq ? $translator->translate($fields[$index]) : $fields[$index], 'visible' => $visible);
        }
        
        if($translReq){
            unset($translator);
        }

        return json_encode($aRet);
        
    }
    
    
    
    
    
}
