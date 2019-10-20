<?php

namespace Trial\Db\Entity;

/**
 * Description of TassiRendicontazione
 *
 * @author giacomo.solazzi
 */
class TassiRendicontazione extends Tasso {

    protected $idRendicontazione = NULL;
    protected $valutaBase = NULL;
    protected $valuta = NULL;
    protected $includiPersonali = TRUE;
    protected $anticipi = NULL;
    protected $tassi = NULL;

    /**
     * 
     * @param int $idRendic
     */
    public function __construct($idRendic) {

        $this->idRendicontazione = $idRendic;
    }

    /**
     * 
     * @param int $idRendic
     */
    public function impostaRendicontazione($idRendic) {

        $this->idRendicontazione = $idRendic;
    }

    /**
     * 
     * In base alla rendicontazione ricava le
     * informazioni dell'utente associato
     * e ritorna l'id della valuta impostata
     * nel profilo utente
     * 
     * @param int $idRend
     * @return int [id della valuta]
     * 
     */
    public function valutaBase() {

        $rend = new \Trial\Db\Tables\Rendicontazioni($this->idRendicontazione);
        $ut = $rend->Utente(TRUE);
        unset($rend);
        $prof = $ut->profili(TRUE);
        unset($ut);


        if ($prof->recordExists()) {

            $ret = $prof->getUserCurrency();
            unset($prof);
            $this->valutaBase = $ret;
            return $ret;
        } else {
            unset($prof);
            return \Trial\Db\Tables\Valute::HC;
        }
    }

    /**
     * Ritorna gli id delle valute utilizzate
     * 
     * @param bool $conDescrizione - se si vuole anche la descrizione della valuta
     * @return array
     */
    public function valuteUsate($conDescrizione = FALSE) {

        $rend = new \Trial\Db\Tables\Rendicontazioni($this->idRendicontazione);
        $valute = $rend->VociSpese()->valuteUtilizzate($conDescrizione);
        unset($rend);
        return $valute;
    }

    /**
     *      
     * @param int $idCurr
     * @param bool $obj TRUE se si vuole ottenere l'oggetto \Trial\Db\Tables\TassiEffettivi
     * 
     * @return mixed \Trial\Db\Tables\TassiEffettivi | bool
     */
    public function tassoEsistente($idCurr = 0, $obj = FALSE) {

        $this->valuta = $idCurr;

        $wh = array(\Trial\Db\Tables\TassiEffettivi::FK_RENDICONTAZIONI => $this->idRendicontazione, \Trial\Db\Tables\TassiEffettivi::FLD_TIPO_TASSO => \Trial\Mdm\TipiTassi::GLOBALE);

        if ($idCurr) {
            $wh[\Trial\Db\Tables\TassiEffettivi::FK_VALUTE] = $this->valuta;
        }

        $tasso = new \Trial\Db\Tables\TassiEffettivi($wh);
        $ret = $tasso->recordExists();

        if ($obj) {
            return $tasso;
        }

        unset($tasso);

        return $ret;
    }

    /**
     *      
     * @param int $idCurr id della valuta     
     * @param bool $persIncl true se si devono includere nel calcolo anche i contanti dei dipendenti     
     * 
     * @return bool 
     */
    public function calcolaTassi($idCurr = 0, $persIncl = TRUE) {

        $this->valuta = $idCurr;
        $this->includiPersonali = $persIncl;

        if (!$idCurr) {

            foreach ($this->valuteUsate() as $valuta) {

                $this->valuta = $valuta;
                
                if (!$this->calcolaRegistra()) {
                    return FALSE;
                }
            }
        } else {

            return $this->calcolaRegistra();
        }

        return TRUE;
    }

    /**
     * 
     * Calcola il tasso medio degli anticipi
     * 
     * @param int $idRend
     * @param int $idCurr
     * @param bool $persIncl
     * @return array con importo e tasso medio degli anticipi
     */
    private function tassiAnticipi() {

        $wh = array(\Trial\Db\Tables\Anticipi::FK_RENDICONTAZIONI => $this->idRendicontazione, \Trial\Db\Tables\Anticipi::FK_VALUTE => $this->valuta);

        if (!$this->includiPersonali) {
            $wh[\Trial\Db\Tables\Anticipi::FLD_ORIGINE] = \Trial\Db\Tables\Anticipi::AZIENDALE;
        }

        $this->anticipi = new \Trial\Db\Tables\Anticipi($wh);

        if ($this->anticipi->recordExists()) {

            $importo = 0;

            foreach ($this->anticipi->rowset as $anticipo) {

                $importi[] = $anticipo[\Trial\Db\Tables\Anticipi::FLD_IMPORTO];
                $importo += $anticipo[\Trial\Db\Tables\Anticipi::FLD_IMPORTO];
                $tassi[] = $anticipo[\Trial\Db\Tables\Anticipi::FLD_TASSO];
            }

            $tasso = new \Trial\DbEntity\Tassi(\Trial\Mdm\TipiTassi::ANTICIPO, \Trial\DbEntity\Tasso::IMPORTO, $importo);
            $tasso->setTasso(\Trial\Mdm\TipiTassi::ANTICIPO, $this->calcolaTassoMedio($importi, $tassi));

            return $tasso;
        } else {

            return new \Trial\DbEntity\Tassi();
        }
    }

    /**
     * 
     * per calcolare il tasso medio di un anticipo basta leggere tutti i record della tabella anticipi e 
     * fare la media. 
     * Quando si calcola il tasso medio per gli acquisti bisogna leggere la tabella dei cambi 
     * entrando in chiave con l’id della valuta che usiamo come base e con gli id degli anticipi della rendicontazione. 
     * Dobbiamo poi verificare ogni volta se la valuta che stiamo trattando è locale o estera perché 
     * alla fine dobbiamo sempre riportare i tassi in valuta locale.
     * 
     * 
     * @param int $idRend
     * @param int $idCurr
     * @param bool $persIncl
     * @return array con importo e tasso medio dei cambi
     */
    private function tassiAcquisti() {

        // la somma degli importi cambiati deve essere suddivisa poi per valuta dell'anticipo collegato
        // per determinare i vari cambi e fare gli eventuali cross
        // infatti la valuta AUD la posso aver acquistata da un anticipo in EUR e da un anticipo in USD
        // bisogna suddividere gli importi cambiati per valuta di anticipo


        $tassiAcq = array();
        $cumulativoImpCambi = array();

        foreach ($this->anticipi->rowset as $anticipo) {


            $cambi = new \Trial\Db\Tables\CambiValute([\Trial\Db\Tables\CambiValute::FK_ANTICIPI => $anticipo[\Trial\Db\Tables\Anticipi::PK], \Trial\Db\Tables\CambiValute::FK_VALUTE => $this->valuta]);

            $importiCambiati = $cambi->toSingleArray($cambi::FLD_IMPORTO);

            $tassoc = $this->calcolaTassoMedio($importiCambiati, $cambi->toSingleArray($cambi::FLD_TASSO));

            if ($anticipo[\Trial\Db\Tables\Anticipi::FK_VALUTE] != $this->valutaBase()) {

                $valuta = $this->valuta;
                $tassiValuta = $this->calcolaTassi($anticipo[\Trial\Db\Tables\Anticipi::FK_VALUTE]);
                $this->valuta = $valuta;

                $tassoc = $this->calcolaTassoCross($tassiValuta[\Trial\Mdm\TipiTassi::GLOBALE][parent::TASSO], $tassoc);
            }

            $tassiAcq[] = $tassoc;
            $cumulativoImpCambi[] = array_sum($importiCambiati);
        }

        $tasso = new \Trial\DbEntity\Tassi(\Trial\Mdm\TipiTassi::ACQUISTO, \Trial\DbEntity\Tasso::IMPORTO, array_sum($cumulativoImpCambi));
        $tasso->setTasso(\Trial\Mdm\TipiTassi::ACQUISTO, $this->calcolaTassoMedio($cumulativoImpCambi, $tassiAcq));

        return $tasso;
    }

    /**
     * 
     * @return boolean
     */
    private function registraTassi() {


        foreach ($this->tassi->toArray() as $tipoTasso => $data) {

            $tasso = new \Trial\Db\Tables\TassiEffettivi([
                \Trial\Db\Tables\TassiEffettivi::FK_RENDICONTAZIONI => $this->idRendicontazione,
                \Trial\Db\Tables\TassiEffettivi::FK_VALUTE => $this->valuta,
                \Trial\Db\Tables\TassiEffettivi::FLD_TIPO_TASSO => $tipoTasso]);


            if ($tasso->rowset->count() > 0) {
                $res = $tasso->rowset->current();
            } else {
                unset($tasso);
                $tasso = new \Trial\Db\Tables\TassiEffettivi();
                $res = $tasso->rowset;
            }

            $res[\Trial\Db\Tables\TassiEffettivi::FK_RENDICONTAZIONI] = $this->idRendicontazione;
            $res[\Trial\Db\Tables\TassiEffettivi::FK_VALUTE] = $this->valuta;
            $res[\Trial\Db\Tables\TassiEffettivi::FLD_TIPO_TASSO] = $tipoTasso;
            $res[\Trial\Db\Tables\TassiEffettivi::FLD_TASSO] = $data[\Trial\DbEntity\Tasso::TASSO];

            if (!$res->save()) {
                unset($tasso);
                return FALSE;
            }

            unset($tasso);
        }

        return TRUE;
    }

    /**
     * 
     * @return boolean
     */
    private function calcolaRegistra() {

        $this->tassi = new \Trial\DbEntity\Tassi();
        $this->tassi->aggiungiTassi([$this->tassiAnticipi(), $this->tassiAcquisti()]);
        $this->tassi->setImporto(\Trial\Mdm\TipiTassi::GLOBALE, $this->tassi->getImporto(\Trial\Mdm\TipiTassi::ANTICIPO) + $this->tassi->getImporto(\Trial\Mdm\TipiTassi::ACQUISTO));
        $this->tassi->setTasso(\Trial\Mdm\TipiTassi::GLOBALE, $this->calcolaTassoMedio([$this->tassi->getImporto(\Trial\Mdm\TipiTassi::GLOBALE)], [$this->tassi->getTasso(\Trial\Mdm\TipiTassi::ANTICIPO) + $this->tassi->getTasso(\Trial\Mdm\TipiTassi::ACQUISTO)]));

        if (!$this->registraTassi()) {
            unset($this->tassi);
            return FALSE;
        }

        unset($this->tassi);
        return TRUE;
    }

}
