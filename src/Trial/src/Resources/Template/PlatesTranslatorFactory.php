<?php

namespace Trial\Resources\Template;

use Interop\Container\ContainerInterface;
use Zend\I18n\Translator\Translator;
use Zend\Authentication\AuthenticationService;
use League\Plates\Engine as PlatesEngine;
use Trial\Db\Tables\ProfiliUtente;
use Trial\Mdm\LocaleUtente;

/**
 * Description of PlatesTranslatorFactory
 *
 * @author giacomo.solazzi
 */
class PlatesTranslatorFactory {

    public function __invoke(ContainerInterface $container) {

        $pTransl = new PlatesTranslator($container->get(Translator::class));

        $pTransl->register($container->get(PlatesEngine::class));

        $authService = $container->get(AuthenticationService::class);

        if (ProfiliUtente::getUserLocale($authService->getIdentity()) != LocaleUtente::DEFAULT_LOCALE) {

            $pTransl->setRequiredTranslation(TRUE);
        }

        return $pTransl;
    }

}
