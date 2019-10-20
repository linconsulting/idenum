<?php

namespace Trial\Action;

use Interop\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Authentication\AuthenticationService;

use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;
use Trial\Resources\Template\PlatesTranslator;

use Zend\Db\Adapter\Adapter;


class DispatchFactory {

    public function __invoke(ContainerInterface $container) {
        
        $template = $container->get(TemplateRendererInterface::class);
        
        GlobalAdapterFeature::setStaticAdapter($container->get(Adapter::class));
        
        $container->get(PlatesTranslator::class);
        
        return new DispatchAction($template, $container->get(AuthenticationService::class), $container->get('config')['translator']);
        
        
    }

}
