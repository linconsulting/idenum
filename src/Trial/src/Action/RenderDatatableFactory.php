<?php

namespace Trial\Action;

use Interop\Container\ContainerInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;



class RenderDatatableFactory {

    public function __invoke(ContainerInterface $container) {
        
        GlobalAdapterFeature::setStaticAdapter($container->get(Adapter::class));                               
        
                
        return new RenderDatatable($container->get(AuthenticationService::class), $container->get('config')['translator']);
        
        
    }

}
