<?php

namespace Trial\Action;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;
//use Zend\Diactoros\Response\RedirectResponse;
use Zend\Authentication\AuthenticationService;
use Trial\Resources\Pages\PageDataLoader;
use Trial\Resources\Pages\Helpers\SspDatatableZend;
use Trial\Db\Tables\ProfiliUtente;
use Trial\Mdm\LocaleUtente;


/**
 * Description of RenderDatatable
 *
 * @author giacomo.solazzi
 */
class RenderDatatable implements ServerMiddlewareInterface {

    private $auth;
    private $translatorInfo;

    /**
     * 
     * @param AuthenticationService $auth
     * @param array $translatorInfo
     */
    public function __construct(AuthenticationService $auth, $translatorInfo) {

        $this->auth = $auth;
        $this->translatorInfo = $translatorInfo;
    }

    /**
     * 
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     * @return HtmlResponse
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate) {

        if ($this->auth->hasIdentity()) {

            $table = $request->getAttribute('tbl');
            $params = $request->getParsedBody();

            //nome della classe dalla tabella
            $resource = PageDataLoader::getClass($table);

            if (!is_null($resource)) {

                $objResource = new $resource();                                
                $objResource->setRequestData($params);
                
                if (ProfiliUtente::getUserLocale($this->auth->getIdentity()) != LocaleUtente::DEFAULT_LOCALE) {
                    
                    $objResource->translatorConfig = $this->translatorInfo;                    
                }
                
                
                return new JsonResponse(SspDatatableZend::generateData($objResource));
                
                
            } else {
                return new JsonResponse('KO');
            }
        } else {
            return new JsonResponse('KO');
        }
    }

}
