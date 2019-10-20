<?php

namespace Trial\Db\Entity;



/**
 * Description of DBEntityInterface
 *
 * @author giacomo.solazzi
 */
interface DbEntityInterface
{
    
    /**
     * 
     * Get and return the
     * Zend\Db\ResultSet\ResultSet
     */
    
    public function get();
    
}
