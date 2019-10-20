<?php

namespace Trial\Action;

use Interop\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;
use Zend\Db\Adapter\Adapter;
use Trial\Resources\Template\PlatesTranslator;


class WelcomeFactory {

    public function __invoke(ContainerInterface $container) {

        $template = $container->get(TemplateRendererInterface::class);

        GlobalAdapterFeature::setStaticAdapter($container->get(Adapter::class));

        $container->get(PlatesTranslator::class);

        return new WelcomeAction($template, $container->get(AuthenticationService::class));
    }

}
