<?php

namespace Trial\Action;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Template;
use Zend\Authentication\AuthenticationService;



/**
 * Description of LoginAction
 *
 * @author giacomo.solazzi
 */
class LoginAction implements ServerMiddlewareInterface {

    
    private $template;
    private $authService;
    

    /**
     *      
     * @param \Zend\Expressive\Template\TemplateRendererInterface $template
     */
    public function __construct(Template\TemplateRendererInterface $template = null, AuthenticationService $auth = null) {
       
        $this->template = $template;
        $this->authService = $auth;        
        
        
    }

    /**
     * 
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     * @return HtmlResponse
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate) {


        if ($request->getMethod() === 'POST') {

            //dati ricevuti dal form di login
            return $this->authenticate($request);
            
        } elseif ($request->getUri()->getPath() == '/logout') {

            $this->authService->clearIdentity();
            return new RedirectResponse('/');
            
        } if ($this->authService->hasIdentity()) {
            
            //pagina di benvenuto o destinazione richiesta
            return new RedirectResponse('/welcome');
            
        } else {
            //pagina che genera la form della login            
            return new HtmlResponse($this->template->render('trial::login'));
        }
        
        
        
    }

    /**
     * 
     * @param ServerRequestInterface $request
     * @return RedirectResponse|HtmlResponse
     */
    public function authenticate(ServerRequestInterface $request) {


        if (!$this->authService->hasIdentity()) {

            $params = $request->getParsedBody();

            $adapter = $this->authService->getAdapter();
            $adapter->setUsername($params['usr']);
            $adapter->setPassword($params['psw']);

            $result = $this->authService->authenticate();                        

            if (!$result->isValid()) {
                return new HtmlResponse('KO');
            }
            
        }


        return new RedirectResponse('/welcome');
    }

}
