<?php

namespace Trial\Db\Tables;



/**
 * Description of Matricole
 *
 * @author giacomo.solazzi
 */
class Matricole extends AbstractDbTables {
    
    
    const FK_CLIENTI = 'clientematr';
    
    const FLD_MATRICOLA = 'codmatr';
    const FLD_DESCRIZIONE = 'descrmatr';
    
    public $clienteRowset = NULL;
        
    
    public function __construct($cond = NULL) {
        
        parent::__construct('gns_matricole_a', $cond);
        
    }    
    
    
    /**
     * 
     * @return $this - \DbTables\Matricole
     */
    public function Cliente() {        
        

        if (!is_null($this->rowset)) {
            
            $ot = new Clienti($this->rowset->current()[self::FK_CLIENTI]);            
            $this->clienteRowset = $ot->rowset;
            unset($ot);
        }

        return $this;
    }
    
    

}
