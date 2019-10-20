<?php

namespace Trial\Resources\Pages\Ajax;

use Trial\Db\Tables\Spese as Sp;
use Trial\Db\Tables\Utenti;
use Trial\Resources\Pages\Spese as SpRes;

/**
 * Description of Spese
 *
 * @author giacomo.solazzi
 */
class Spese {

    private $resource = NULL;
    private $args = [];
    private $userIdentity = NULL;
    private $translatorConfig = [];
    

    public function __construct() {

        $this->resource = new SpRes();
    }

    /**
     * 
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value) {
        $this->{$name} = $value;
    }

    /**
     * 
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        return $this->{$name};
    }
    
    /**
     * 
     * @return int - affected rows
     */
    public function delete() {

        $sp = new Sp();        
        $ar = $sp->delete([Sp::PK => $this->args['ref']]);
        unset($sp);
        return $ar;
    }

    /**
     * 
     * @return int - affected rows
     */
    public function updateCausale() {

        $sp = new Sp();        
        $ar = $sp->update([key($this->resource->getColumnsHelper()->getFieldByIndex($this->args['index'])) => $this->args['value']], [Sp::PK => $this->args['ref']]);
        unset($sp);

        return $ar;
    }

    /**
     * 
     * @return int - affected rows
     */
    public function insertCausale() {

        $sp = new Sp();
        $ar = $sp->insert([Sp::FK_UTENTI => Utenti::getIdFromIdentity($this->userIdentity), Sp::FLD_CAUSALE => $this->args['value']]);
        unset($sp);

        return $ar;
    }
    
    /**
     * 
     * @return int - affected rows
     */
    public function insertBp() {

        $sp = new Sp();
        $ar = $sp->insert([Sp::FK_UTENTI => Utenti::getIdFromIdentity($this->userIdentity), Sp::FK_CLIENTI => $this->args['value']]);
        unset($sp);

        return $ar;
    }

}
