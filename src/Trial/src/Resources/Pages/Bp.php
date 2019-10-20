<?php

namespace Trial\Resources\Pages;

use Zend\Session\Container;
use Trial\Db\Tables\Utenti;
use Trial\Db\Tables\AreaUtente;
use Trial\Db\Tables\Clienti;
use Trial\Resources\Pages\Helpers\ColumnHelper;



/**
 * Description of Bp
 *
 * @author giacomo.solazzi
 */
class Bp extends AbstractDataLoader {
    
    const PAGE_NAME = 'bp';
    const TEMPLATE_NAME = 'trial';
    const DB_TABLE_NAME = Clienti::DB_TABLE;
    const DB_TABLE_ALIAS = Clienti::DB_TABLE_ALIAS;
    const DB_TABLE_PK = Clienti::PK;

    
    
    public function __construct() {                
                
        $this->columnsHelper = new ColumnHelper([
            Clienti::PK => Clienti::getFieldsLabel(Clienti::PK),
            Clienti::FLD_BP => Clienti::getFieldsLabel(Clienti::FLD_BP),
            Clienti::FLD_DESC_CLIENTE => Clienti::getFieldsLabel(Clienti::FLD_DESC_CLIENTE),
            Clienti::FK_PAESE => Clienti::getFieldsLabel(Clienti::FK_PAESE)]);
        
        //Lista indici di colonna che richiedono la traduzione
        $this->columnsHelper->indexTranslatables = array(2,3);
        
        //Lista indici di colonna non visibili all'utente
        $this->columnsHelper->indexNotVisible = array(0);
        
        $this->infoArea = AreaUtente::infoArea(new Utenti([Utenti::FLD_IDENTITY => (new Container('Zend_Auth'))->storage]));
        
    }

    

}
