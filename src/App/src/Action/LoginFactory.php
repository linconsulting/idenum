<?php

namespace App\Action;

use Interop\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;
use Zend\Db\Adapter\Adapter;

class LoginFactory {

    public function __invoke(ContainerInterface $container) {

        $template = $container->get(TemplateRendererInterface::class);

        GlobalAdapterFeature::setStaticAdapter($container->get(Adapter::class));        

        return new LoginAction($template, $container->get(AuthenticationService::class));
    }

}
