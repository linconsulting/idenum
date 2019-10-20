<?php

namespace Trial\Auth;

use Interop\Container\ContainerInterface;
use Zend\Authentication\AuthenticationService;

/**
 * Description of AuthenticationServiceFactory
 *
 * @author giacomo.solazzi
 */
class AuthenticationServiceFactory {

    public function __invoke(ContainerInterface $container) {
        return new AuthenticationService(
                null, $container->get(Adapter::class)
        );
    }

}
