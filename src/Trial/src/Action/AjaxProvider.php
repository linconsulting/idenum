<?php

namespace Trial\Action;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Authentication\AuthenticationService;
use Trial\Resources\Pages\Ajax\AjaxClassLoader;
use Trial\Resources\Pages\Helpers\JsonResp;



/**
 * Description of AjaxProvider
 *
 * @author giacomo.solazzi
 */
class AjaxProvider implements ServerMiddlewareInterface {

    private $authService;
    private $translatorConfig;

    /**
     * 
     * @param \Zend\Expressive\Template\TemplateRendererInterface $template
     */
    public function __construct(AuthenticationService $auth = null, $translatorParams = NULL) {

        $this->authService = $auth;
        $this->translatorConfig = $translatorParams;
    }

    /**
     * 
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     * @return HtmlResponse
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate) {

        if ($this->authService->hasIdentity()) {

            $params = $request->getParsedBody();
            
            //il loader che dipende dalla pagina richiesta            
            //intercetto per classe e metodo            
            if (!isset($params['cName'])) {
                return new JsonResponse('error');
            }

            $resource = AjaxClassLoader::getClass($params['cName']);

            if (!is_null($resource)) {

                $ajaxClass = new $resource();
                $ajaxClass->userIdentity = $this->authService->getIdentity();
                $ajaxClass->translatorConfig = $this->translatorConfig;

                if (isset($params['mArgs']) && !empty($params['mArgs'])) {
                    $ajaxClass->args = $params['mArgs'];
                }
                
                try {
                    
                    return (new JsonResp(TRUE, $ajaxClass->{$params['mName']}()))->__toString();
                    
                } catch (\Exception $exc) {                                        
                    
                    return (new JsonResponse(''))->withStatus(500, $exc->getMessage());
                    
                }                
                
            } else {
                
                return new JsonResp(TRUE, 'error');
            }
            
        } else {
            return (new JsonResponse(''))->withStatus(401, 'Login required');
        }
    }

}
