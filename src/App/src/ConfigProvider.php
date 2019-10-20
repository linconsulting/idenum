<?php

namespace App;

/**
 * The configuration provider for the App module
 *
 * @see https://docs.zendframework.com/zend-component-installer/
 */

use Zend\Authentication\AuthenticationService;
use Zend\I18n\Translator\Translator;
use Zend\I18n\Translator\TranslatorServiceFactory;
use Zend\Expressive\Plates\PlatesEngineFactory;
use League\Plates\Engine as PlatesEngine;



class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     *
     * @return array
     */
    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates'    => $this->getTemplates(),
        ];
    }

    /**
     * Returns the container dependencies
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            
            'factories'  => [
                Action\HomePageAction::class => Action\HomePageFactory::class,
                Auth\Adapter::class => Auth\AdapterFactory::class,
                AuthenticationService::class => Auth\AuthenticationServiceFactory::class,
                Action\LoginAction::class => Action\LoginFactory::class,
                Action\WelcomeAction::class => Action\WelcomeFactory::class,
                Action\DispatchAction::class => Action\DispatchFactory::class,
                Action\RenderDatatable::class => Action\RenderDatatableFactory::class,
                Action\AjaxProvider::class => Action\AjaxProviderFactory::class,
                Action\TestAction::class => Action\TestFactory::class,
                Translator::class => TranslatorServiceFactory::class,
                PlatesEngine::class => PlatesEngineFactory::class,
                PlatesTranslator::class => PlatesTranslatorFactory::class                
            ],
        ];
    }

    /**
     * Returns the templates configuration
     *
     * @return array
     */
    public function getTemplates()
    {
        return [
            'paths' => [
                'app'    => [__DIR__ . '/../templates/app'],
                'error'  => [__DIR__ . '/../templates/error'],
                'layout' => [__DIR__ . '/../templates/layout'],
            ],
        ];
    }
}
