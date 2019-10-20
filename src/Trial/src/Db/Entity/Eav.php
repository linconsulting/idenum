<?php

namespace Trial\Db\Entity;

use Trial\Db\Tables\AbstractDbTables;

/**
 * Description of Eav
 *
 * @author giacomo.solazzi
 */
class Eav extends AbstractDbTables {

    
    public function __construct($cond = NULL) {
        
        parent::__construct('gns_oggetti_a', $cond);
        
    }
    

}
