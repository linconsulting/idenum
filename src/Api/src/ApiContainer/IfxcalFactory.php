<?php

namespace Api\ApiContainer;

use Interop\Container\ContainerInterface;



class IfxcalFactory {
    
    
    public function __invoke(ContainerInterface $container) {


        $conf = $container->get('config');        
        
        //se voglio la configurazione del db
        // $conf['db']
        //preso dal file config\autoload\database.global.php
                
        return new Ifxcal();
    }

}
