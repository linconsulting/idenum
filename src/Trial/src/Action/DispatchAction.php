<?php

namespace Trial\Action;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Template;
use Zend\Authentication\AuthenticationService;
use Trial\Resources\Pages\PageDataLoader;

/**
 * Description of DispatchAction
 *
 * @author giacomo.solazzi
 */
class DispatchAction implements ServerMiddlewareInterface {

    private $template;
    private $authService;
    private $translatorConfig;

    /**
     * 
     * @param \Zend\Expressive\Template\TemplateRendererInterface $template
     */
    public function __construct(Template\TemplateRendererInterface $template = null, AuthenticationService $auth = null, $translatorParams = NULL) {

        
        $this->template = $template;
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

            //qui il loader che dipende dalla pagina richiesta
            $loaderName = $request->getAttribute('Zend\Expressive\Router\RouteResult')->getMatchedRouteName(); //nome del router
            $resource = PageDataLoader::getClass($loaderName);
            
            if(!is_null($resource)){
                
                $data = new $resource();
                $data->userIdentity = $this->authService->getIdentity();
                $data->translatorConfig = $this->translatorConfig;
                
                return new HtmlResponse($this->template->render($resource::TEMPLATE_NAME.'::'.$resource::PAGE_NAME, ['dataResource' => $data]));
                
            }else{
                return new RedirectResponse('/logout');
            }
            
            
        } else {
            return new RedirectResponse('/');
        }
    }

}
