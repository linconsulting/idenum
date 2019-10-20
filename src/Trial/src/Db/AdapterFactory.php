<?php

namespace Trial\Db;

use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\Adapter;

/**
 * Description of AdapterFactory
 *
 * @author giacomo.solazzi
 */
class AdapterFactory {

    public function __invoke(ContainerInterface $container) {

        return new Adapter(DriverParam::$data);
        
    }

}
