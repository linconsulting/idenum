<?php

namespace Trial\Db\Tables;

use Trial\Mdm\LinguaUtente;
use Trial\Mdm\PackageUtente;
use Trial\Mdm\TemplateUtente;
use Trial\Mdm\ValutaUtente;
use Trial\Mdm\ReportUtente;
use Trial\Mdm\LocaleUtente;
use Trial\Db\Tables\Utenti;




/**
 * Description of ProfiloUtente
 *
 * @author giacomo.solazzi
 */
class ProfiliUtente extends AbstractDbTables {

    // Foreign Keys
    const FK_UTENTI = 'idusr';
    const FLD_ATTIVO = 'attivo';
    
    public $settings = NULL;
    
    protected static $profiloUtente = NULL;


    public function __construct($cond = NULL) {

        parent::__construct('gns_utenti_profili', $cond);
    }

    /**
     * 
     * @return $this - \Trial\Db\Tables\ProfiliUtente
     */
    public function settings($obj = FALSE, $cond = NULL) {

        if (!is_null($this->rowset)) {

            $wCond = empty($cond) ? array_merge($cond, [SettingsProfili::FK_PROFILIUTENTE => $this->rowset->current()[self::PK]]) : array(SettingsProfili::FK_PROFILIUTENTE => $this->rowset->current()[self::PK]);

            $ot = new SettingsProfili($wCond);

            if ($obj) {
                return $ot;
            }

            $this->settings = $ot->rowset;
            unset($ot);
        }

        return $this;
    }

    /**
     *      
     * @param string $refId - id del riferimento associato al setting da recuperare
     * @param bool $obj True se si vuole ottenere il record dell'oggetto SettingsProfili
     * 
     * @return mixed \Trial\Db\Tables\ProfiliUtente | \Trial\Db\Tables\SettingsProfili
     */
    private function getSetting($refId, $obj = FALSE) {

        $ot = new SettingsProfili([SettingsProfili::FK_PROFILIUTENTE => $this->rowset->current()[self::PK], SettingsProfili::FK_OGGETTI => $refId]);

        if ($obj) {
            return $ot;
        }

        $vRet = $ot->rowset->count() > 0 ? $ot->rowset->current()->vobj : NULL;
        unset($ot);

        return $vRet;
    }

    /**
     * 
     * @param string $identity
     * @return string 
     */
    public static function getUserLocale($identity) {
        
        
        if(is_null(static::$profiloUtente)){
            static::$profiloUtente = (new Utenti([Utenti::FLD_IDENTITY => $identity]))->profili(TRUE);                
        }
        
        $locale = LocaleUtente::DEFAULT_LOCALE;
        
        if (static::$profiloUtente->getUserLanguage() != LinguaUtente::DEFAULT_LANG) {

            $locale = LocaleUtente::DEFAULT_LOCALE_TRANSL;
            
        }        
                
        return $locale;
    }

    /**
     * 
     * @param bool $obj True se si vuole ottenere il record dell'oggetto SettingsProfili
     * @return mixed string | \Trial\Db\Tables\SettingsProfili
     */
    public function getUserLanguage($obj = FALSE) {


        $idLang = $this->getSetting(sprintf('%s', new LinguaUtente()), $obj);

        if ($obj) {
            return $idLang;
        }


        if (!is_null($idLang)) {
            $ol = new Lingue($idLang);
            $lingua = $ol->rowset->current()->lingua;
            unset($ol);
        }

        return is_null($idLang) ? NULL : $lingua;
    }

    /**
     * 
     * @param bool $obj True se si vuole ottenere il record dell'oggetto SettingsProfili
     * @return mixed string | \Trial\Db\Tables\SettingsProfili
     */
    public function getUserPackage($obj = FALSE) {

        return $this->getSetting(sprintf('%s', new PackageUtente()), $obj);
    }

    /**
     * 
     * @param bool $obj True se si vuole ottenere il record dell'oggetto SettingsProfili
     * @return mixed string | \Trial\Db\Tables\SettingsProfili
     */
    public function getUserTemplate($obj = FALSE) {

        return $this->getSetting(sprintf('%s', new TemplateUtente()), $obj);
    }

    /**
     * 
     * @param bool $obj True se si vuole ottenere il record dell'oggetto SettingsProfili
     * @return mixed string | \Trial\Db\Tables\SettingsProfili
     */
    public function getUserCurrency($obj = FALSE) {

        return $this->getSetting(sprintf('%s', new ValutaUtente()), $obj);
    }

    /**
     * 
     * @param bool $obj True se si vuole ottenere il record dell'oggetto SettingsProfili
     * @return mixed string | \Trial\Db\Tables\SettingsProfili
     */
    public function getUserReport($obj = FALSE) {

        return $this->getSetting(sprintf('%s', new ReportUtente()), $obj);
    }

}
