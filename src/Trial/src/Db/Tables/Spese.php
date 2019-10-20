<?php

namespace Trial\Db\Tables;

use Trial\Mdm\RiferimentiAssociati;
use Zend\Db\Sql\Predicate\In;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Join;


/**
 * Description of Spese
 *
 * @author giacomo.solazzi
 */
class Spese extends AbstractDbTables {

    const DB_TABLE = 'gns_spese_i';
    const DB_TABLE_ALIAS = 'gns_sp_i';
    
    // Foreign Keys
    const FK_UTENTI = 'id_usr';
    const FK_RENDICONTAZIONI = 'id_rendic';
    const FK_CLIENTI = 'id_cliente';
    const FLD_CAUSALE = 'causale';
    const FLD_DATA_CREAZIONE = 'data_creaz';
    const FK_TIPO_RIF_SPESA_ASSOCIATO = 'id_rif_ass';
    const FLD_TIPO_RIF_SPESA_ASS = 'tipo_rif_ass';
    const FLD_STATO_NOTA = 'id_stato_nota';
    const FLD_DATA_APPROVAZ = 'data_app';
    const FLD_DATA_DAL = 'data_dal';
    const FLD_DATA_AL = 'data_al';

    public static $tableFields = [
        self::PK => 'id',
        self::FK_UTENTI => 'id utente',
        self::FK_RENDICONTAZIONI => 'numero rendicontazione',
        self::FK_CLIENTI => 'id cliente',
        self::FLD_CAUSALE => 'causale',
        self::FLD_STATO_NOTA => 'stato spesa',
        self::FLD_DATA_CREAZIONE => 'data creazione',
        self::FLD_TIPO_RIF_SPESA_ASS => 'tipo associato',
        self::FK_TIPO_RIF_SPESA_ASSOCIATO => 'id tipo associato',
        self::FLD_DATA_DAL => 'data dal',
        self::FLD_DATA_AL => 'data al',
    ];
    
    public $rendicontazioneRowset = NULL;
    public $clienteRowset = NULL;
    public $ordineRowset = NULL;
    public $utenteRowset = NULL;
    public $vociSpese = NULL;

    /**
     * 
     * @param string|array $cond
     */
    public function __construct($cond = NULL) {

        parent::__construct(self::DB_TABLE, $cond);
    }

    

    /**
     * 
     * @return array - ['FK' => ['class' => classe della tabella join, 'join' => Zend\Db\Sql\Join]]
     */
    public static function getRelationship($key = NULL) {

        $relationship = [
            self::FK_CLIENTI => ['class' => Clienti::class, 'join' => (new Join())->join([Clienti::DB_TABLE_ALIAS => Clienti::DB_TABLE], Clienti::DB_TABLE_ALIAS.'.'.Clienti::PK.'='.self::DB_TABLE_ALIAS.'.'.self::FK_CLIENTI, [Clienti::FLD_DESC_CLIENTE], Join::JOIN_INNER)],
        ];

        if (is_null($key)) {

            return $relationship;
        } else if (isset($relationship[$key])) {

            return $relationship[$key];
        } else {
            return [];
        }
    }

    /**
     * @param bool $obj - True se si vuole ottenere l'istanza della rendicontazione
     * @return mixed - \DbTables\Spese | \DbTables\Rendicontazioni
     */
    public function rendicontazione($obj = FALSE) {

        if (!is_null($this->rowset)) {

            $ot = new Rendicontazioni($this->rowset->current()[self::FK_RENDICONTAZIONI]);

            if ($obj) {
                return $ot;
            }

            $this->rendicontazioneRowset = $ot->rowset;
            unset($ot);
        }

        return $this;
    }

    /**
     * 
     * @return boolean
     */
    public function haRendicontazione() {

        if (!is_null($this->rowset)) {

            $ot = new Rendicontazioni($this->rowset->current()[self::FK_RENDICONTAZIONI]);
            $ret = $ot->rowset->count() > 0 ? TRUE : FALSE;
            unset($ot);

            return $ret;
        } else {

            return FALSE;
        }
    }

    /**
     * 
     * @param bool $obj - True se si vuole ottenere l'istanza Ordini
     * @return mixed - \DbTables\Spese | \DbTables\Ordini
     * 
     */
    public function Ordine($obj = FALSE) {


        if (!is_null($this->rowset) && $this->rowset->current()[self::FLD_TIPO_RIF_SPESA_ASS] == RiferimentiAssociati::ORDINE) {

            $ot = new Ordini($this->rowset->current()[self::FK_TIPO_RIF_SPESA_ASSOCIATO]);
            if ($obj) {
                return $ot;
            }
            $this->ordineRowset = $ot->rowset;
            unset($ot);
        }

        return $this;
    }

    /**
     * 
     * @return $this - \DbTables\Spese
     */
    public function Cliente() {


        if (!is_null($this->rowset)) {

            $ot = new Clienti($this->rowset->current()[self::FK_CLIENTI]);
            $this->clienteRowset = $ot->rowset;
            unset($ot);
        }

        return $this;
    }

    /**
     * 
     * @return $this - \DbTables\Spese
     */
    public function Utente() {


        if (!is_null($this->rowset)) {

            $ot = new Utenti($this->rowset->current()[self::FK_UTENTI]);
            $this->utenteRowset = $ot->rowset;
            unset($ot);
        }

        return $this;
    }

    /**
     *
     * @param bool  $obj True se si vuole istanziare e ritornare l'oggetto
     * @param array $cond   le condizioni del where aggiuntive
     * @param array $order es: [id => 'desc']
     * 
     * @return mixed \DbTables\Spese | \DbTables\VociDiSpesa
     */
    public function VociDiSpesa($obj = FALSE, $cond = NULL, $order = NULL) {

        $wCond = [VociSpese::FK_SPESE => array_values($this->toSingleArray(parent::PK))];

        if (is_array($cond)) {

            $wCond = array_merge($wCond, $cond);
        }

        if ($cond instanceof Where) {

            $cond->addPredicate(new In(VociSpese::FK_SPESE, array_values($this->toSingleArray(parent::PK))));
            $wCond = $cond;
        }

        $ot = new VociSpese($wCond, $order);

        if ($obj) {
            return $ot;
        }

        $this->vociSpese = $ot->rowset;
        unset($ot);

        return $this;
    }

    /**
     * 
     * @param int $nr - numero di rendicontazione
     * @return boolean
     */
    public function associaRendicontazione($nr) {

        if (!is_null($this->rowset)) {

            $r = $this->rowset->current();
            $r->{self::FK_RENDICONTAZIONI} = $nr;

            $ret = $r->save() > 0 ? TRUE : FALSE;
            unset($r);

            return $ret;
        } else {
            return FALSE;
        }
    }

}
