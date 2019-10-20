<?php

namespace Trial\Resources\Pages\Helpers;

use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\I18n\Translator\Translator;

/**
 * Description of SspDatatableZend
 *
 * @author giacomo.solazzi
 */
class SspDatatableZend {

    private $sql;
    private $adapter;
    private $selectBase;
    private $selectCustom;
    private $resource;    

    /**
     * 
     * @param array $request
     * @param array $translationParams
     * @param object $resource
     */
    static function generateData($resource) {

        $class = new static();

        $class->resource = $resource;        
        $class->adapter = GlobalAdapterFeature::getStaticAdapter();
        $class->sql = new Sql(GlobalAdapterFeature::getStaticAdapter());
        $class->selectBase = $class->sql->select();

        $class->selectBase->columns(array_keys($resource->getColumnsHelper()->getList()));
        $class->selectBase->from([$resource::DB_TABLE_ALIAS => $resource::DB_TABLE_NAME]);
        $class->joinsFromResource();
        
        $class->selectCustom = clone $class->selectBase;

        $class->selectCustom->order($class->getOrderString());
        $class->limit();

        $where = $resource->getWhere();
        $customManualFilter = $class->filter();        
        
        if(!is_null($where) && !empty($customManualFilter)){
            
            $class->selectBase->where($where);
            $class->selectCustom->where(implode(' ', $where).' AND '.$customManualFilter);
        }
        
        if(!is_null($where) && empty($customManualFilter)){
            $class->selectBase->where($where);
            $class->selectCustom->where($where);
        }
        
        
        if(is_null($where) && !empty($customManualFilter)){
            $class->selectCustom->where($customManualFilter);
        }
               
        
        //$s = $class->selectCustom->getSqlString($class->adapter->getPlatform());        

        $statementCustom = $class->sql->prepareStatementForSqlObject($class->selectCustom);        
        $resultCustom = $statementCustom->execute();

        if ($resultCustom instanceof ResultInterface && $resultCustom->isQueryResult()) {
            $resultSet = new ResultSet;
            $resultSet->initialize($resultCustom);
            $dataCustom = $resultSet->toArray();
        } else {
            $dataCustom = [];
        }
        
        
        $statementBase = $class->sql->prepareStatementForSqlObject($class->selectBase);
        $resultBase = $statementBase->execute();

        $recordsTotal = $resultBase->count();
        $recordsFiltered = $resultCustom->count();        

        //* Output */

        $ret = array(
            "draw" => isset($class->resource->requestData['draw']) ? intval($class->resource->requestData['draw']) : 0,
            "recordsTotal" => intval($recordsTotal),
            "recordsFiltered" => intval($recordsFiltered),
            "data" => $class->dataOutput($dataCustom)
        );

        unset($class);
        
        return $ret;
    }

    /**
     * Ordering
     *
     * Construct the ORDER BY clause for server-side processing SQL query
     *
     *
     *  @return string SQL order by clause
     */
    private function getOrderString() {

        $columns = $this->resource->setDatatableFields(TRUE);

        $order = '';
        if (isset($this->resource->requestData['order']) && count($this->resource->requestData['order'])) {
            $orderBy = array();
            $dtColumns = $this->pluck($columns, 'dt');
            for ($i = 0, $ien = count($this->resource->requestData['order']); $i < $ien; $i++) {
                // Convert the column index into the column data property
                $columnIdx = intval($this->resource->requestData['order'][$i]['column']);
                $requestColumn = $this->resource->requestData['columns'][$columnIdx];
                $columnIdx = array_search($requestColumn['data'], $dtColumns);
                $column = $columns[$columnIdx];
                if ($requestColumn['orderable'] == 'true') {
                    $dir = $this->resource->requestData['order'][$i]['dir'] === 'asc' ? 'ASC' : 'DESC';

                    if (strpos($column['db'], ".") !== FALSE) {

                        $fld = explode('.', $column['db']);
                        $orderBy[] = $fld[0] . '.' . $fld[1] . " " . $dir;
                    } else {

                        $orderBy[] = $column['db'] . " " . $dir;
                    }
                }
            }
            $order = implode(', ', $orderBy);
        }
        return $order;
    }

    /**
     * 
     */
    private function joinsFromResource() {

        foreach ($this->resource->dataJoinTables() as $data) {

            foreach ($data['join']->getJoins() as $j) {

                $this->selectBase->join($j['name'], $j['on'], $j['columns'], $j['type']);
            }
        }
    }

    /**
     * 
     */
    private function limit() {

        if (isset($this->resource->requestData['start']) && $this->resource->requestData['length'] != -1) {
            $this->selectCustom->limit($this->resource->requestData['length']);
            $this->selectCustom->offset($this->resource->requestData['start']);
        }
    }

    /**
     * Adatta l'array del resultSet per il client
     * 
     * @param array $data
     * @return array
     */
    private function dataOutput($data) {
        
        $out = array();

        $columns = $this->resource->setDatatableFields();
        $indexToTranlate = $this->resource->getColumnsHelper()->indexTranslatables;
        $translRequired = count($indexToTranlate) > 0 ? TRUE : FALSE;        
        
        if($translRequired && empty($this->resource->translatorConfig)){
            $translRequired = FALSE;
        }        
        
        if($translRequired){
            $translator = Translator::factory($this->resource->translatorConfig);
        }
        

        for ($i = 0, $ien = count($data); $i < $ien; $i++) {

            $row = array();

            for ($j = 0, $jen = count($columns); $j < $jen; $j++) {

                $column = $columns[$j];
                
                $dataToStore = isset($data[$i][$column['db']]) ? $data[$i][$column['db']] : NULL;
                
                if($translRequired && !is_null($dataToStore) && in_array($column['dt'], $indexToTranlate)){
                    
                    $dataToStore = $translator->translate($dataToStore);
                    
                }

                $row[$column['dt']] = $dataToStore;
            }
            $out[] = $row;
        }

        return $out;
    }

    /**
     * Searching / Filtering
     *
     * Construct the WHERE clause for server-side processing SQL query.
     *
     * NOTE this does not match the built-in DataTables filtering which does it
     * word by word on any field. It's possible to do here performance on large
     * databases would be very poor
     *
     *
     *  @return string SQL where clause
     */
    private function filter() {
        $globalSearch = array();
        $columnSearch = array();

        $columns = $this->resource->setDatatableFields(TRUE);

        $dtColumns = $this->pluck($columns, 'dt');

        if (isset($this->resource->requestData['search']) && $this->resource->requestData['search']['value'] != '') {

            $str = $this->resource->requestData['search']['value'];

            for ($i = 0, $ien = count($this->resource->requestData['columns']); $i < $ien; $i++) {
                $requestColumn = $this->resource->requestData['columns'][$i];
                $columnIdx = array_search($requestColumn['data'], $dtColumns);
                $column = $columns[$columnIdx];
                if ($requestColumn['searchable'] == 'true') {

                    $binding = '%' . $str . '%';

                    if (strpos($column['db'], ".") !== FALSE) {

                        $fld = explode('.', $column['db']);
                        $globalSearch[] = $this->adapter->platform->quoteIdentifierChain([$fld[0], $fld[1]]) . " LIKE " . $this->adapter->platform->quoteValue($binding);
                    } else {
                        $globalSearch[] = $this->adapter->platform->quoteIdentifier($column['db']) . " LIKE " . $this->adapter->platform->quoteValue($binding);
                    }
                }
            }
        }
        // Individual column filtering
        if (isset($this->resource->requestData['columns'])) {
            for ($i = 0, $ien = count($this->resource->requestData['columns']); $i < $ien; $i++) {
                $requestColumn = $this->resource->requestData['columns'][$i];
                $columnIdx = array_search($requestColumn['data'], $dtColumns);
                $column = $columns[$columnIdx];
                $str = $requestColumn['search']['value'];
                if ($requestColumn['searchable'] == 'true' &&
                        $str != '') {

                    $binding = '%' . $str . '%';

                    if (strpos($column['db'], ".") !== FALSE) {

                        $fld = explode('.', $column['db']);
                        $columnSearch[] = $this->adapter->platform->quoteIdentifierChain([$fld[0], $fld[1]]) . " LIKE " . $this->adapter->platform->quoteValue($binding);
                    } else {
                        $columnSearch[] = $this->adapter->platform->quoteIdentifier($column['db']) . " LIKE " . $this->adapter->platform->quoteValue($binding);
                    }
                }
            }
        }
        // Combine the filters into a single string
        $where = '';
        if (count($globalSearch)) {
            $where = '(' . implode(' OR ', $globalSearch) . ')';
        }
        if (count($columnSearch)) {
            $where = $where === '' ?
                    implode(' AND ', $columnSearch) :
                    $where . ' AND ' . implode(' AND ', $columnSearch);
        }

        return $where;
    }

    /**
     * Pull a particular property from each assoc. array in a numeric array, 
     * returning and array of the property values from each item.
     *
     *  @param  array  $a    Array to get data from
     *  @param  string $prop Property to read
     *  @return array        Array of property values
     */
    private function pluck($a, $prop) {
        $out = array();
        for ($i = 0, $len = count($a); $i < $len; $i++) {
            $out[] = $a[$i][$prop];
        }
        return $out;
    }

}
