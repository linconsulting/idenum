<?php

namespace Trial\Mdm\DataValidation\Helpers;

use Zend\Diactoros\Response\JsonResponse;

/**
 * Description of JsonResp:
 * 
 * Risposte strutturate
 * 
 * a seguito di un controllo dati
 *
 * @author giacomo.solazzi
 */
class JsonResp {

    protected $response;

    /**
     * 
     * @param boolean $okResp   -- bool x indicare se esito è positivo o negativo
     * @param type $objResp     -- mixed che rappresenta l'informazione da trasferire (es: stringa per messaggio o array associativo per valori multipli)
     */
    public function __construct($okResp = TRUE, $objResp = NULL) {

        $this->response = array(
            'resp_ok' => $okResp,
            'resp_obj' => $objResp
        );
    }

    /**
     * 
     * @return string
     */
    public function __toString() {
        return new JsonResponse($this->response);
    }

}
