<?php


namespace Trial\Db\Tables;


/**
 * Description of Utenti
 *
 * @author giacomo.solazzi
 */
class Utenti extends AbstractDbTables {
    
    
    const TIPO_INTERNO = 0;
    const TIPO_ESTERNO = 1;    
    const USR_IS_TECH = 'isTech';
    const USR_IS_COLLAB = 'isCollaborator';
    
    const FLD_IDENTITY = 'nome';

    
    public $areaRowset = NULL;
    public $profilo = NULL;
    public $ruoloRowset = NULL;
    
    protected static $identityId = NULL;


    public function __construct($cond = NULL) {
        
        parent::__construct('gns_utenti_a', $cond);
        
    }  


    /**
     * 
     * @param string $identity
     * @return int
     */
    
    public static function getIdFromIdentity($identity){
        
        if(!empty(static::$identityId)){
            return static::$identityId;
        }
        
        $class = new static([Utenti::FLD_IDENTITY => $identity]);
        $class::$identityId = $class->rowset->current()[Utenti::PK];
        unset($class);
        
        return static::$identityId;
        
    }
       
    
    /**
     * 
     * @return \Trial\Db\Tables\Utenti
     */
    
    public function ruolo(){        
        
        if (!is_null($this->rowset)) {
            
            $ot = new RuoliUtente([RuoliUtente::FK_UTENTI => $this->rowset->current()[self::PK]]);
            $this->ruoloRowset = $ot->rowset;
            unset($ot);
            
        }
        
        return $this;        
        
    }
    
    
    
    /**
     * 
     * @return \Trial\Db\Tables\Utenti
     */
    
    public function area($obj = FALSE, $onlyId = FALSE){        
        
        if (!is_null($this->rowset)) {
            
            $ot = new AreaUtente([AreaUtente::FK_UTENTI => $this->rowset->current()[self::PK]]);                                    
            
            if($obj){
                return $ot;
            }
            
            $this->areaRowset = $ot->rowset;
            unset($ot);
            
            if($onlyId){
                return $this->areaRowset->current()[AreaUtente::PK];
            }
            
        }
        
        
        return $this;        
        
    }
    
    
    
    /**
     * 
     * @param bool $obj
     * @param bool $attivo
     * @return \Trial\Db\Tables\Utenti | \Trial\Db\Tables\ProfiliUtente
     */
    
    public function profili($obj = FALSE, $attivo = TRUE){
        
        if (!is_null($this->rowset)) {
            
            $ot = new ProfiliUtente($attivo ? array(ProfiliUtente::FK_UTENTI => $this->rowset->current()[self::PK], ProfiliUtente::FLD_ATTIVO => 1) : $this->rowset->current()[self::PK]);            
            if($obj){
                return $ot;
            }
            $this->profilo = $ot->rowset;
            unset($ot);
            
        }
        
        return $this;    
        
        
        
    }
    
    
}
