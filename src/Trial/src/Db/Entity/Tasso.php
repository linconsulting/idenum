<?php

namespace Trial\Db\Entity;

/**
 * Description of Tasso
 *
 * @author giacomo.solazzi
 */
class Tasso {
    
    const IMPORTO = 'importo';
    const TASSO = 'tasso';
    
    /**
     * 
     * Calcola il tasso di cambio fra una valuta base ed una secondaria
     * 
     * @param double $impValVenduta
     * @param double $impValAcquistata
     * @return double
     */
    function calcolaTasso($impValVenduta, $impValAcquistata) {


        return $impValAcquistata / $impValVenduta;
    }
    
    
    /**
     * 
     * @param array-double $arrImporti
     * @param array-double $arrTassi
     * @return double tasso medio
     */
    function calcolaTassoMedio($arrImporti, $arrTassi) {

        $sumCtv = 0;

        foreach ($arrImporti as $indice => $importo) {

            $tasso = $arrTassi[$indice] > 0 ? $arrTassi[$indice] : 1;

            $sumCtv += $importo / $tasso;
        }

        $ctv = $sumCtv > 0 ? $sumCtv : 1;


        return array_sum($arrImporti) / $ctv;
    }
    
    /**
     * 
     * Calcola il tasso di cambio fra una valuta base ed una secondaria
     * 
     * esempio: eur/usd = 1.3221 poi usd/lkr = 157.4290 
     * il risultato è 1.3221 * 157.4290 = 208.1369 = eur/lkr
     * 
     * @param double $pRate il tasso di cambio che ha come base la valuta principale e come seconddaria la prima valuta estera
     * @param double $sRate il tasso di cambio che ha come base la prima valuta estera e come seconddaria la seconda valuta estera
     * @return double
     */
    function calcolaTassoCross($pRate, $sRate) {


        return $pRate * $sRate;
    }

}
