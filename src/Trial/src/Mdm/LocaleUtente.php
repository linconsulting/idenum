<?php


namespace Trial\Mdm;


/**
 * Description of LocaleUtente
 *
 * @author giacomo.solazzi
 */
class LocaleUtente {
    
    const DEFAULT_LOCALE = 'it';
    const DEFAULT_LOCALE_TRANSL = 'en';


    
    public function __toString() {
        
        return (string)self::DEFAULT_LOCALE;
    }
    
}
