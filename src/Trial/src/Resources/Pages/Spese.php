<?php

namespace Trial\Resources\Pages;

use Zend\Session\Container;
use Trial\Db\Tables\Utenti;
use Trial\Db\Tables\AreaUtente;
use Trial\Db\Tables\Clienti;

use Trial\Db\Tables\Spese as Sp;

use Trial\Resources\Pages\Helpers\ColumnHelper;
use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;


/**
 * Description of Spese
 *
 * @author giacomo.solazzi
 */
class Spese extends AbstractDataLoader {

    const PAGE_NAME = 'spese';
    const TEMPLATE_NAME = 'trial';
    const DB_TABLE_NAME = Sp::DB_TABLE;
    const DB_TABLE_ALIAS = Sp::DB_TABLE_ALIAS;
    const DB_TABLE_PK = Sp::PK;
        
    
    /**
     * 
     */
    public function __construct() {
        
        $this->columnsHelper = new ColumnHelper([
            Sp::PK => Sp::getFieldsLabel(Sp::PK),
            Sp::FK_RENDICONTAZIONI => Sp::getFieldsLabel(Sp::FK_RENDICONTAZIONI),
            Sp::FK_CLIENTI => Sp::getFieldsLabel(Sp::FK_CLIENTI),
            Sp::FLD_CAUSALE => Sp::getFieldsLabel(Sp::FLD_CAUSALE)]
        );

        $this->join = [
            Sp::FK_CLIENTI => array_merge(Sp::getRelationship(Sp::FK_CLIENTI), ['returnColumn' => [Clienti::FLD_DESC_CLIENTE => Clienti::getFieldsLabel(Clienti::FLD_DESC_CLIENTE)]])
        ];
        
        
        //lista indici di colonna che l'utente può modificare
        $this->columnsHelper->indexEditables = array(2, 3);

        //Lista colonne da sostituire        
        $this->columnsHelper->indexToReplace = [Sp::FK_CLIENTI => $this->join[Sp::FK_CLIENTI]['returnColumn']];
        
        //Lista indici di colonna che richiedono la traduzione
        $this->columnsHelper->indexTranslatables = array(2);
        
        $this->infoArea = AreaUtente::infoArea(new Utenti([Utenti::FLD_IDENTITY => (new Container('Zend_Auth'))->storage]));
        
        
    }
    
    

    /**
     * 
     * pid è il numero di rendicontazione
     * 
     * @return array
     */
    public function getWhere() {

        if (!isset($this->requestData['pid']) || is_null($this->requestData['pid']) || $this->requestData['pid'] == 0) {

            $user = new Utenti([Utenti::FLD_IDENTITY => (new Container('Zend_Auth'))->storage]);
            $strUsr = GlobalAdapterFeature::getStaticAdapter()->platform->quoteIdentifierChain([self::DB_TABLE_ALIAS, Sp::FK_UTENTI]);
            $where = array($strUsr . ' = ' . $user->rowset->current()[Utenti::PK]);
            unset($user);
        } else {

            $strRend = GlobalAdapterFeature::getStaticAdapter()->platform->quoteIdentifierChain([self::DB_TABLE_ALIAS, Sp::FK_RENDICONTAZIONI]);
            $where = array($strRend . ' = ' . $this->requestData['pid']);
        }

        return $where;
    }

    

}
