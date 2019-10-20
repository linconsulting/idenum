<?php

namespace Trial\Action;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\TextResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Authentication\AuthenticationService;
use Zend\Expressive\Template;
use Trial\Reports\Lib\Fpdf181\Src\BasePdf as Pdf;

/**
 * Description of WelcomeAction
 *
 * @author giacomo.solazzi
 */
class TestAction implements ServerMiddlewareInterface {

    private $template;
    private $auth;
    private $pdf;

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

        if ($this->auth->hasIdentity()) {

            //return new HtmlResponse($this->template->render('trial::welcome',['identity' => $this->auth->getIdentity()]));

            $this->pdf = new Pdf();
            $this->pdf->SetAutoPageBreak(true, $this->pdf->getTopMargin());
            $this->pdf->AddPage();
            $this->pdf->SetFontBody();
            $txtFld = 'OK';
            $this->pdf->insertCarriageReturn(2);
            $this->pdf->printTextBox($txtFld, $this->pdf->GetStringWidth($txtFld), $this->pdf->hMed, $this->pdf->hMed, 'L', '');
            $this->pdf->insertCarriageReturn(2);

            
            return new TextResponse($this->pdf->Output('S'), 200, $this->pdf->getHeaders('rrd.pdf'));

            
        } else {
            return new RedirectResponse('/');
        }
    }

}
