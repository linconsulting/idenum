<?php

namespace Api\Action;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\TextResponse;
use Zend\Expressive\Router\RouteResult;

use Api\ApiContainer\Ifxcal;


class DispatchAction implements ServerMiddlewareInterface {
    
    private $config;


    public function __construct($config = null) {
        
        $this->config = $config;
        
    }

    

    /**
     * 
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     * @return HtmlResponse
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate) {

        
        
        $routeResult = $request->getAttribute(RouteResult::class, false);
        $matchedParams =  $routeResult->getMatchedParams();
        
        
        switch ($matchedParams['name']) {
            
            case 'ifxcal':
                
                $api = new Ifxcal($this->config['db']);
                break;

            default:
                return new TextResponse("Risposta mancante");
                
        }
        
        return $api->process();
        
        
        
        
        /**
         Array
(
    [dependencies] => Array
        (
            [aliases] => Array
                (
                    [ValidatorManager] => Zend\Validator\ValidatorPluginManager
                    [Zend\Session\SessionManager] => Zend\Session\ManagerInterface
                    [Zend\Db\Adapter\Adapter] => Zend\Db\Adapter\AdapterInterface
                    [Zend\Expressive\Delegate\DefaultDelegate] => Zend\Expressive\Delegate\NotFoundDelegate
                )

            [factories] => Array
                (
                    [Zend\Validator\ValidatorPluginManager] => Zend\Validator\ValidatorPluginManagerFactory
                    [Zend\Session\Config\ConfigInterface] => Zend\Session\Service\SessionConfigFactory
                    [Zend\Session\ManagerInterface] => Zend\Session\Service\SessionManagerFactory
                    [Zend\Session\Storage\StorageInterface] => Zend\Session\Service\StorageFactory
                    [Zend\Db\Adapter\AdapterInterface] => Zend\Db\Adapter\AdapterServiceFactory
                    [App\Action\HomePageAction] => App\Action\HomePageFactory
                    [App\Auth\Adapter] => App\Auth\AdapterFactory
                    [Zend\Authentication\AuthenticationService] => Trial\Auth\AuthenticationServiceFactory
                    [App\Action\LoginAction] => App\Action\LoginFactory
                    [App\Action\WelcomeAction] => App\Action\WelcomeFactory
                    [App\Action\DispatchAction] => App\Action\DispatchFactory
                    [App\Action\RenderDatatable] => App\Action\RenderDatatableFactory
                    [App\Action\AjaxProvider] => App\Action\AjaxProviderFactory
                    [App\Action\TestAction] => Ap.....
         * 
         * 
         * 
         
                )

        )

    [db] => Array
        (
            [driver] => Mysqli
            [hostname] => localhost
            [database] => XXX
            [username] => XX
            [password] => XXX
            [options] => Array
                (
                    [buffer_results] => 1
                )

        )

    [config_cache_enabled] => 
    [debug] => 1
    [zend-expressive] => Array 
         * 
         * 
         * 
         * 
         * 
         
         
         */
        
        
        
    }

}
