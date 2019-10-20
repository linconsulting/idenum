<?php

namespace Trial\Db\Tables;


/**
 * Description of Clienti
 *
 * @author giacomo.solazzi
 */
class Clienti extends AbstractDbTables {
    
    const DB_TABLE = 'gns_clienti_a';
    const DB_TABLE_ALIAS = 'gns_cl';
    
    const FK_PAESE = 'paesecl';
    const FK_INDIRIZZO = 'codindcl';
    
    const FLD_DESC_CLIENTE = 'desccl';
    const FLD_BP = 'nomecl';
    const FLD_ATTIVO = 'attivo';
    
    
    public static $tableFields = [
        self::PK => 'id',        
        self::FLD_BP => 'bp',
        self::FLD_DESC_CLIENTE => 'cliente',
        self::FK_PAESE => 'paese',
        self::FLD_ATTIVO => 'attivo',
        self::FK_INDIRIZZO => 'id indirizzo'        
    ];


    public function __construct($cond = NULL) {
        
        parent::__construct(self::DB_TABLE, $cond);
        
    }


    
    

}
