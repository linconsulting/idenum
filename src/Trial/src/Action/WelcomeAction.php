<?php

namespace Trial\Action;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Authentication\AuthenticationService;
use Zend\Expressive\Template;


/**
 * Description of WelcomeAction
 *
 * @author giacomo.solazzi
 */
class WelcomeAction implements ServerMiddlewareInterface {
    
    
    private $template;
    private $auth;
    
    
    /**
     *      
     * @param \Zend\Expressive\Template\TemplateRendererInterface $template
     */
    public function __construct(Template\TemplateRendererInterface $template = null, AuthenticationService $auth = null) {
       
        $this->template = $template;        
        $this->auth = $auth;
        
    }
    
    
    
    /**
     * 
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     * @return HtmlResponse
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate) {
        
        if($this->auth->hasIdentity()){            
            
            return new HtmlResponse($this->template->render('trial::welcome',['identity' => $this->auth->getIdentity()]));
            
        }else{
            return new RedirectResponse('/');
        }

        
        
    }

}
