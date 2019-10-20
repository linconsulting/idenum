<?php

namespace Trial\Db\Tables;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\Feature\RowGatewayFeature;
use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Predicate\Operator;
use Zend\I18n\Translator\Translator;

/**
 * Description of AbstractDbTables
 *
 * @author giacomo.solazzi
 */
class AbstractDbTables extends TableGateway {

    const PK = 'id';
    const DOUBLE_COMPARE_PRECISION = 0.00001;

    protected static $translator = NULL;
    protected $whereClause = NULL;
    public $rowset = NULL;

    /**
     * 
     * @param mixed $cond se array esempio ['nomecampo' => valore] - se int è il valore della PK
     * 
     */
    public function __construct($table, $cond = NULL, $order = NULL, $features = NULL) {

        $this->table = $table;

        if (!is_null($features)) {
            $tableFeatures = $features;
        } else {
            $tableFeatures = array(new RowGatewayFeature(self::PK));
        }

        parent::__construct($table, GlobalAdapterFeature::getStaticAdapter(), $tableFeatures);


        if ($cond instanceof Where) {

            $this->whereClause = $cond;
        } else {

            $this->whereClause = new Where();
        }


        if (!is_null($cond) && !is_array($cond) && !($cond instanceof Where)) {

            $this->whereClause->addPredicates([self::PK => $cond]);
        }

        if (is_array($cond)) {

            $this->whereClause->addPredicates($cond);
        }


        // quando la cond è nulla istanzio la proprietà rowset con la classe rowgateway vuota

        if (is_null($cond)) {

            $this->rowset = $this->select($cond)->current()->populate([]);
        } else {

            $this->rowset = is_null($order) ? $this->select($this->whereClause) : $this->selectWith($this->getSql()->select()->where($this->whereClause)->order($order));
        }
    }

    /**
     * Verifica se esiste almeno un record
     * 
     * @return boolean
     */
    public function recordExists() {

        if (!is_null($this->rowset) && $this->rowset->count() > 0) {

            return TRUE;
        } else {

            return FALSE;
        }
    }

    /**
     * allinea il puntatore del resultSet
     * al record desiderato
     * 
     * @param string $pk - chiave primaria del record
     */
    protected function syncResultPointer($pk) {

        foreach ($this->rowset as $row) {
            if ($row[self::PK] == $pk) {
                break;
            }
        }
    }

    /**
     * @param bool $obj - True se si vuole l'istanza dell'oggetto \Zend\Db\Sql\Where
     * 
     * @return mixed array - ritorna il predicato where utilizzato | \Zend\Db\Sql\Where
     */
    public function getWhereClause($obj = FALSE) {

        if ($obj) {
            return $this->whereClause;
        }

        $aRet = array();

        foreach ($this->whereClause->getPredicates() as $predicate) {

            foreach ($predicate as $item) {

                if ($item instanceof Operator) {

                    $aRet[] = array('left' => $item->getLeft(), 'operator' => $item->getOperator(), 'right' => $item->getRight());
                }
            }
        }

        return $aRet;
    }

    

    /**
     * 
     * @param string $field
     * @return array
     */
    public function toSingleArray($field) {

        if (!is_null($this->rowset)) {

            foreach ($this->rowset as $row) {

                $ret[$row[self::PK]] = $row[$field];
            }

            return $ret;
        } else {
            return array();
        }
    }

    /**
     * 
     * Lista campi con rispettiva label
     * 
     * @return array|string
     */
    public static function getFieldsLabel($key = NULL) {
        
        if(is_null($key)){
            return static::$tableFields;
        }else if (isset(static::$tableFields[$key])) {
            return static::$tableFields[$key];
            
        } else {
            return [];
        }

        
    }

}
