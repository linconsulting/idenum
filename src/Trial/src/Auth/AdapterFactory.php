<?php

namespace Trial\Auth;

use Interop\Container\ContainerInterface;


/**
 * Description of AdapterFactory
 *
 * @author giacomo.solazzi
 */
class AdapterFactory {

    public function __invoke(ContainerInterface $container) {
        
        return new Adapter($container->get(\Zend\Db\Adapter\Adapter::class));
        
    }

}
