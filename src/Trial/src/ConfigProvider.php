<?php

namespace Trial;

/**
 * The configuration provider for the Trial module
 *
 * @see https://docs.zendframework.com/zend-component-installer/
 */
use Zend\Authentication\AuthenticationService;
use Zend\I18n\Translator\Translator;
use Zend\I18n\Translator\TranslatorServiceFactory;
use Zend\Expressive\Plates\PlatesEngineFactory;
use League\Plates\Engine as PlatesEngine;
use Trial\Resources\Template\PlatesTranslator;
use Trial\Resources\Template\PlatesTranslatorFactory;


class ConfigProvider {

    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     *
     * @return array
     */
    public function __invoke() {
        return [
            'dependencies' => $this->getDependencies(),
            'templates' => $this->getTemplates(),
            'translator' => [
                'locale' => 'en',
                'translation_file_patterns' => [
                    [
                        'type' => 'phpArray',
                        'base_dir' => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Mdm' . DIRECTORY_SEPARATOR . 'Translation',
                        'pattern' => 'lang_%s.php'
                    ]
                ]
            ],
        ];
    }

    /**
     * Returns the container dependencies
     *
     * @return array
     */
    public function getDependencies() {
        return [
            'factories' => [
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
                PlatesTranslator::class => PlatesTranslatorFactory::class,                
                
            ],
        ];
    }

    /**
     * Returns the templates configuration
     *
     * @return array
     */
    public function getTemplates() {
        return [
            'paths' => [
                'trial' => [__DIR__ . '/templates/trial'],
                'trial-js' => [__DIR__ . '/templates/trial/js'],
                'trial-js-funct' => [__DIR__ . '/templates/trial/js/functions'],
                'trial-layout' => [__DIR__ . '/templates/layout'],
                'trial-error' => [__DIR__ . '/templates/error'],
            ],
        ];
    }

}
