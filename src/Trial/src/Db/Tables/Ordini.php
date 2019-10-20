<?php

namespace Trial\Db\Tables;

/**
 * Description of Ordini
 *
 * @author giacomo.solazzi
 */
class Ordini extends AbstractDbTables {

    const FK_CLIENTI = 'rif_bp';
    const FK_MATRICOLE = 'rif_matr';
    const FLD_TIPO_ORD = 'tipo_ord';
    const FLD_ORDINE = 'ordine';

    public $matricolaRowset = NULL;

    /**
     * 
     * @param array $cond
     * @param object $features
     */
    
    public function __construct($cond = NULL, $features = NULL) {

        parent::__construct('gns_rif_ord_a', $cond, $features);
    }

    /**
     * 
     * @param bool $obj TRUE se si vuole ottenere l'oggetto \DbTables\Matricole
     * 
     * @return mixed - \DbTables\Ordini | \DbTables\Matricole
     */
    public function Matricola($obj = FALSE) {


        if (!is_null($this->rowset)) {

            $ot = new Matricole([Matricole::FLD_MATRICOLA => $this->rowset->current()[self::FK_MATRICOLE]]);

            if ($obj) {
                return $ot;
            }

            $this->matricolaRowset = $ot->rowset;
            unset($ot);
        }

        return $this;
    }

}
