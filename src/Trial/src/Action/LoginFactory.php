<?php

namespace Trial\Action;

use Interop\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;
use Trial\Resources\Template\PlatesTranslator;

class LoginFactory {

    public function __invoke(ContainerInterface $container) {

        $template = $container->get(TemplateRendererInterface::class);

        GlobalAdapterFeature::setStaticAdapter($container->get(\Zend\Db\Adapter\Adapter::class));

        $container->get(PlatesTranslator::class);

        return new LoginAction($template, $container->get(AuthenticationService::class));
    }

}
