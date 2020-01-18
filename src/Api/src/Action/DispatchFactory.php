<?php

namespace Api\Action;

use Interop\Container\ContainerInterface;
//use Zend\Expressive\Router\RouterInterface;


class DispatchFactory {
    
    
    public function __invoke(ContainerInterface $container) {

        //$router = $container->get(RouterInterface::class);        
        //$conf = $container->get('config');        
        
        //se voglio la configurazione del db
        // $conf['db']
        //preso dal file config\autoload\database.global.php
                
        return new DispatchAction($container->get('config'));
    }

}
