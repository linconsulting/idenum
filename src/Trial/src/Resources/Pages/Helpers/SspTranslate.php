<?php

namespace Trial\Resources\Pages\Helpers;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Join;
use Zend\Db\Sql\Sql;

/**
 * Description of SspTranslate:
 * 
 * estensione della funzionalità complex per
 * 
 * l'aggiunta delle traduzioni
 *
 * @author giacomo.solazzi
 */
class SspTranslate extends SspJoin {
    

    static function genData($request, $conn, $resource){
        
        $sql = new Sql($conn);        
        $select = $sql->select();
        $select->from([$resource::DB_TABLE_ALIAS => $resource::DB_TABLE_NAME]);
        
        
    }


        /**
     * Estende la funzionalità del metodo complex
     * aggiungendo le traduzioni e riportando il risultato in JSON
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array|\Zend\Db\Adapter\Adapter|PDO $conn PDO connection resource or connection parameters array
     *  @param  string $table SQL table to query
     *  @param  string $primaryKey Primary key of the table
     *  @param  array $columns Column information array
     *  @param  array $infoJoin example: array('type' => '', 'table' => $rend->table, 'alias' => 'rend', 'condition' => 'sp.id_rendic=rend.id'),
     *  @param  string $whereResult WHERE condition to apply to the result set
     *  @param  string $whereAll WHERE condition to apply to all queries
     *  @param  array $infotransl example: array('lang' => 'english', 'class' => array('NomeClasse1','NomeClasse2','NomeClasseN')),
     *  @return string          Server-side processing response json array
     */
    static function generateData($request, $conn, $table, $aliasTable, $primaryKey, $columns, $infoJoin = array(), $whereResult = null, $whereAll = null, $infoTransl = array()) {

        $bindings = array();

        if ($conn instanceof Adapter) {

            $connParam = $conn->getDriver()->getConnection()->getConnectionParameters();

            if ($connParam['driver'] != 'Pdo') {
                $db = (new Adapter(array_merge($connParam, ['driver' => 'pdo_mysql'])))->getDriver()->getConnection()->connect()->getResource();
            } else {
                $db = $conn->getDriver()->getConnection()->connect()->getResource();
            }
        } else {
            $db = self::db($conn);
        }
        
        
        $localWhereResult = array();
        $localWhereAll = array();
        $whereAllSql = '';
        // Build the SQL query string from the request
        $limit = self::limit($request, $columns);
        $order = self::order($request, $columns);
        $where = self::filter($request, $columns, $bindings);
        $whereResult = self::_flatten($whereResult);
        $whereAll = self::_flatten($whereAll);
        if ($whereResult) {
            $where = $where ?
                    $where . ' AND ' . $whereResult :
                    'WHERE ' . $whereResult;
        }
        if ($whereAll) {
            $where = $where ?
                    $where . ' AND ' . $whereAll :
                    'WHERE ' . $whereAll;
            $whereAllSql = 'WHERE ' . $whereAll;
        }


        $strJoin = '';

//        foreach ($infoJoin as $data) {
//
//            foreach ($data['join']->getJoins() as $j) {
//
//                $strJoin .= $j['type'] . ' JOIN ' . $j['name'][key($j['name'])] . ' ' . key($j['name']) . ' ON ' . $j['on'] . ' ';
//            }
//        }


        // Main query to actually get the data
        $data = self::sql_exec($db, $bindings, "SELECT " . implode(", ", self::pluck($columns, 'db')) . "
			 FROM $table $aliasTable
                         $strJoin
			 $where
			 $order
			 $limit"
        );


        // Traduzione righe db
        if (count($infoTransl) > 0 && count($data) > 0) {
            self::translate($infoTransl, $data, $columns);
        }



        // Data set length after filtering

        $fldCount = "$aliasTable.$primaryKey";

        $resFilterLength = self::sql_exec($db, $bindings, "SELECT COUNT({$fldCount})
			 FROM $table $aliasTable
                         $strJoin                             
			 $where"
        );
        $recordsFiltered = $resFilterLength[0][0];
        // Total data set length
        $resTotalLength = self::sql_exec($db, $bindings, "SELECT COUNT({$fldCount})
			 FROM   $table $aliasTable $strJoin " .
                        $whereAllSql
        );
        $recordsTotal = $resTotalLength[0][0];
        /*
         * Output
         */


        return array(
            "draw" => isset($request['draw']) ?
            intval($request['draw']) :
            0,
            "recordsTotal" => intval($recordsTotal),
            "recordsFiltered" => intval($recordsFiltered),
            "data" => self::data_output($columns, $data)
        );
    }

    /**
     * Esegue la traduzione dei campi traducibili
     * e sostituisce la stringa nell'array da passare
     * via json al datatable plugin nel client
     * 
     * @param array $info
     * @param array $data
     * @param array $columns 
     */
    private static function translate($info, &$data, $columns) {


        // iterazioni sulle classi per lingua
        foreach ($info['class'] as $cln) {

            // iterazione per riga dei risultati
            foreach ($data as $row => $cols) {

                $infoColumns = array_keys($cols);

                // iterazione per campo traducibile della classe
                foreach ($cln::$translatableFlds as $fld) {

                    if (in_array($fld, $infoColumns)) {

                        // iterazione e match su ogni colonna della riga
                        foreach ($columns as $iCols) {

                            if ($iCols['db'] == $fld) {
                                $transl = $cln::translate($info['lang'], $data[$row][$fld]);
                                $data[$row][$fld] = $transl;
                                $data[$row][$iCols['dt']] = $transl;
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * 
     * @param Join $join
     * @return string
     */
    private function joinToString(Join $join) {

        $strJoin = '';

        foreach ($join->getJoins() as $j) {

            $strJoin .= $j['type'] . ' JOIN ' . $j['name'] . ' ON ' . $j['on'] . ' ';
        }

        return $strJoin;
    }
    
    
    
    /**
     * Paging
     *
     * Construct the LIMIT clause for server-side processing SQL query
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array $columns Column information array
     *  @return string SQL limit clause
     */
    static function limit($request, $columns) {
        $limit = '';
        if (isset($request['start']) && $request['length'] != -1) {
            $limit = "LIMIT " . intval($request['start']) . ", " . intval($request['length']);
        }
        return $limit;
    }
    

}
