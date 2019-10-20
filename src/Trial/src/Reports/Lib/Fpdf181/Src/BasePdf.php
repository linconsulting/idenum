<?php

namespace Trial\Reports\Lib\Fpdf181\Src;

/**
 * Description of BasePdf
 *
 * @author giacomo.solazzi
 */
class BasePdf extends Fpdf {

    public $hSmall;
    public $hMed;
    public $lSmall;
    public $lMed;
    public $lBig;
    public $lRigaIntera;
    public $fileName;
    public $headers;

    public function __construct() {

        parent::__construct();

        $this->hSmall = 3;
        $this->hMed = 5.5;
        $this->lSmall = 30;
        $this->lMed = 50;
        $this->lBig = 100;
        $this->lRigaIntera = 188;
        $this->fontpath = realpath(__DIR__ . '/..') . '/font/';
        $this->fileName = 'report.pdf';
        $this->headers = ['Content-Type' => ['application/pdf'],
            'Content-Disposition' => ['attachment; ' . $this->_httpencode('filename', $this->fileName, FALSE)],
            'Cache-Control' => ['private, max-age=0, must-revalidate'],
            'Pragma' => 'public'];
    }

    public function getTopMargin() {
        return $this->tMargin;
    }

    public function getHeaders($fileName = '') {

        if (!empty($fileName)) {
            $this->fileName = $fileName;
            $this->headers['Content-Disposition'] = ['attachment; ' . $this->_httpencode('filename', $this->fileName, FALSE)];
        }

        return $this->headers;
    }

    /**
     * Imposta il font per lo stile H1
     * 
     */
    public function setFontH1() {

        $this->SetFont('Helvetica', 'B', 11);
    }

    /**
     * Imposta il font per lo stile H2
     * 
     */
    public function setFontH2() {

        $this->SetFont('Helvetica', 'B', 10);
    }

    /**
     * Imposta il font per lo stile H3
     * 
     */
    public function setFontH3() {

        $this->SetFont('Helvetica', '', 10);
    }

    /**
     * Imposta il font per il corpo del documento
     * 
     */
    public function setFontBody() {

        $this->SetFont('Helvetica', '', 9);
    }

    /**
     * Imposta il font per il corpo del documento in grassetto
     * 
     */
    public function setFontBodyBold() {

        $this->SetFont('Helvetica', 'B', 9);
    }

    /**
     * Imposta il font per il corpo del documento in corsivo
     * 
     */
    public function setFontBodyItalic() {

        $this->SetFont('Helvetica', 'I', 9);
    }

    /**
     * Inserisce n carriage return (paragrafi vuoti)
     * 
     * @param type $n
     */
    public function insertCarriageReturn($n = 1) {

        for ($index = 0; $index < $n; $index++) {

            $this->Ln();
        }
    }

    /**
     * 
     * Stampa la prima riga di una tabella: instestazione
     * centra i testi e li borda
     * 
     *      
     * @param array $txtCols    --  stringhe da stampare, una x colonna
     * @param array $aWCols      --  larghezza cella, una x colonna
     * @param int $hCols      --  altezza cella
     */
    public function printHeaderTable($txtCols, $aWCols, $hCols) {

        foreach ($txtCols as $idCols => $vCol) {

            $this->printTextBox($vCol, $aWCols[$idCols], $hCols, $hCols, 'C');
        }
    }

    /**
     * 
     * Stampa una riga di una tabella
     * applica a tutte le celle lo stesso allineamento     
     * 
     *      
     * @param array $txtCols    --  stringhe da stampare, una x colonna
     * @param array $aWCols      --  larghezza cella, una x colonna
     * @param array $hCols      --  altezza cella, una x colonna
     * @param array $align     -- L, C, R, J , una x colonna
     * @param array $border    -- LTBR , una x colonna
     * @param array $styleFont -- array con nome Font, stile: 'B' bold, 'I' italic, '' normal e dimensione. Es: array(array('Helvetica','B',10))
     *                              
     * 
     */
    public function printRowTable($txtCols, $aWCols, $hCols, $align, $border, $styleFont = NULL) {

        foreach ($txtCols as $idCols => $vCol) {

            if (is_null($styleFont)) {

                $this->printTextBox($vCol, $aWCols[$idCols], $hCols[$idCols], $hCols[$idCols], $align[$idCols], $border[$idCols]);
            } else {
                $this->printTextBox($vCol, $aWCols[$idCols], $hCols[$idCols], $hCols[$idCols], $align[$idCols], $border[$idCols], TRUE, $styleFont[$idCols]);
            }


            //$this->pdf->printTextBox($strSpesa, $this->pdf->lRigaIntera, $this->pdf->hMed, $this->pdf->hMed, 'L', '', TRUE);
        }
    }

    /**
     * 
     * Stampa un'area di testo
     *      
     * @param string $strTxt   -- la stringa da scrivere
     * @param int $lCel    -- larghezza della cella
     * @param int $hRiga   -- altezza cella per riga
     * @param int $hMax    -- altezza max della cella     
     * @param string $align   -- allineamento L, R, C, J
     * @param bool $oneLine -- True x stampare la stringa su un'unica riga
     *  
     */
    public function printTextBox($strTxt, $lCel, $hRiga, $hMax, $align = 'J', $border = 'TRBL', $oneLine = FALSE, $styleFont = NULL) {

        /* prima stampa il riquadro con il bordo */
        $yi = $this->GetY();
        $xi = $this->GetX();

        $this->Cell($lCel, $hMax, "", $border);
        $this->SetX($xi);


        /* stile attuale del font */
        if (!is_null($styleFont)) {

            $fontFamily = $this->FontFamily;
            $fontStyle = $this->FontStyle;
            $fontSize = $this->FontSizePt;

            $this->SetFont($styleFont[0], $styleFont[1], $styleFont[2]);
        }


        /* poi il testo senza il bordo */

        $nRigheStr = 1;
        if ($oneLine === FALSE) {
            $nRigheStr = $this->calcolaRigheStringa($strTxt, $lCel);
        }


        if ($nRigheStr > 1) {

            $nRigheStr = $this->MultiCellRowCount($lCel, $strTxt, $align);

            $padding_top = ($hMax - ($nRigheStr * $hRiga)) / 2;
            $this->SetY($yi + $padding_top);
            $this->SetX($xi);

            $this->MultiCell($lCel, $hRiga, $strTxt, 0, $align, 0, $nRigheStr);
        } else {
            $this->Cell($lCel, $hMax, $strTxt, 0, $nRigheStr, $align);
        }

        /* impostare prima la y xché resetta anche la x */
        $this->SetY($yi);
        $this->SetX($xi + $lCel);


        /* ripristina il font precedente */
        if (!is_null($styleFont)) {

            $this->SetFont($fontFamily, $fontStyle, $fontSize);
        }
    }

    /**
     *      
     * @param float $hItem  -->altezza blocco
     * @param int $nIter    -->ripetizioni blocco
     * @return bool         -->TRUE se è necessaria una nuova pagina
     * 
     */
    public function contentInNewPage($hItem = 0, $nIter = 1) {

        $yi = $this->GetY();

        $hMax = $hItem * $nIter;

        if (($yi + $hMax) >= $this->PageBreakTrigger) {

            return TRUE;
        } else {

            return FALSE;
        }
    }

    /**
     * 
     * Dato un array di stringhe e di rispettive larghezze,
     * calcola il numero massimo di righe
     * che serve per ospitarle tutte
     * 
     * 
     * @param array $arStringhe
     * @param array $arLargh
     * @return int
     */
    public function calcolaNrMax($arStringhe, $arLargh) {

        $arHMax = array();

        foreach ($arStringhe as $k => $v) {

            $nr = $this->calcolaRigheStringa($v, $arLargh[$k]);

            if ($nr > 1) {
                $arHMax[] = $this->MultiCellRowCount($arLargh[$k], $v);
            } else {
                $arHMax[] = $nr;
            }
        }

        sort($arHMax);

        return end($arHMax);
    }

    /**
     * Data una stringa ed una larghezza calcola il numero di
     * righe che occorrono
     * 
     * @param string $strTxt
     * @param decimal $lCella
     * @return int
     */
    public function calcolaRigheStringa($strTxt, $lCella = 0) {

        if ($lCella == 0) {
            $wp = $this->w - ( $this->lMargin + $this->rMargin );
        } else {
            $wp = $lCella;
        }

        $nRigheToPrint = 0;
        $nRigheDecimale = $this->GetStringWidth($strTxt) / $wp;
        $nRigheIntere = (int) $nRigheDecimale;
        $resto = $nRigheDecimale - $nRigheIntere;


        if ($resto > 0 && $resto <= 0.93) {
            $nRigheToPrint = $nRigheIntere + 1;
        } elseif ($resto > 0.93) {
            $nRigheToPrint = $nRigheIntere + 2;
        } else {
            $nRigheToPrint = $nRigheDecimale;
        }

        return $nRigheToPrint;
    }

    /**
     * override del metodo MultiCell
     * 
     * questa variante tronca il testo per farlo rientrare in $maxline
     * 
     * @param type $w
     * @param type $h
     * @param type $txt
     * @param string $border
     * @param type $align
     * @param type $fill
     * @param type $maxline
     * @return string
     */
    public function MultiCell($w, $h, $txt, $border = 0, $align = 'J', $fill = false, $maxline = 0) {

        //Output text with automatic or explicit line breaks, maximum of $maxlines
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 and $s[$nb - 1] == "\n")
            $nb--;
        $b = 0;
        if ($border) {
            if ($border == 1) {
                $border = 'LTRB';
                $b = 'LRT';
                $b2 = 'LR';
            } else {
                $b2 = '';
                if (is_int(strpos($border, 'L')))
                    $b2 .= 'L';
                if (is_int(strpos($border, 'R')))
                    $b2 .= 'R';
                $b = is_int(strpos($border, 'T')) ? $b2 . 'T' : $b2;
            }
        }
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $ns = 0;
        $nl = 1;
        while ($i < $nb) {
            //Get next character
            $c = $s[$i];
            if ($c == "\n") {
                //Explicit line break
                if ($this->ws > 0) {
                    $this->ws = 0;
                    $this->_out('0 Tw');
                }
                $this->Cell($w, $h, substr($s, $j, $i - $j), $b, 2, $align, $fill);
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $ns = 0;
                $nl++;
                if ($border and $nl == 2)
                    $b = $b2;
                if ($maxline && $nl > $maxline)
                    return substr($s, $i);
                continue;
            }
            if ($c == ' ') {
                $sep = $i;
                $ls = $l;
                $ns++;
            }
            $l += $cw[$c];
            if ($l > $wmax) {
                //Automatic line break
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                    if ($this->ws > 0) {
                        $this->ws = 0;
                        $this->_out('0 Tw');
                    }
                    $this->Cell($w, $h, substr($s, $j, $i - $j), $b, 2, $align, $fill);
                } else {
                    if ($align == 'J') {
                        $this->ws = ($ns > 1) ? ($wmax - $ls) / 1000 * $this->FontSize / ($ns - 1) : 0;
                        $this->_out(sprintf('%.3f Tw', $this->ws * $this->k));
                    }
                    $this->Cell($w, $h, substr($s, $j, $sep - $j), $b, 2, $align, $fill);
                    $i = $sep + 1;
                }
                $sep = -1;
                $j = $i;
                $l = 0;
                $ns = 0;
                $nl++;
                if ($border and $nl == 2)
                    $b = $b2;
                if ($maxline && $nl > $maxline)
                    return substr($s, $i);
            } else
                $i++;
        }
        //Last chunk
        if ($this->ws > 0) {
            $this->ws = 0;
            $this->_out('0 Tw');
        }
        if ($border and is_int(strpos($border, 'B')))
            $b .= 'B';
        $this->Cell($w, $h, substr($s, $j, $i - $j), $b, 2, $align, $fill);
        $this->x = $this->lMargin;
        return '';
    }

    /**
     * 
     * conta quante righe sono necessarie
     * per la stampa multicell di una stringa
     * 
     * @param float $w - larghezza cella
     * @param string $txt - stringa contenuto     
     * @param type $align - allineamento     
     * @return int
     */
    public function MultiCellRowCount($w, $txt, $align = 'J') {

        //Output text with automatic or explicit line breaks, maximum of $maxlines

        $nRet = 1;

        $cw = &$this->CurrentFont['cw'];
        if ($w == 0) {
            $w = $this->w - $this->rMargin - $this->x;
        }

        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);

        if ($nb > 0 and $s[$nb - 1] == "\n") {
            $nb--;
        }


        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $ns = 0;
        $nl = 1;
        while ($i < $nb) {
            //Get next character
            $c = $s[$i];
            if ($c == "\n") {

                //Explicit line break
                if ($this->ws > 0) {
                    $this->ws = 0;
                }

                $nRet++;

                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $ns = 0;
                $nl++;
                continue;
            }

            if ($c == ' ') {
                $sep = $i;
                $ls = $l;
                $ns++;
            }

            $l += $cw[$c];

            if ($l > $wmax) {

                //Automatic line break
                if ($sep == -1) {

                    if ($i == $j) {
                        $i++;
                    }

                    if ($this->ws > 0) {
                        $this->ws = 0;
                    }
                } else {

                    if ($align == 'J') {
                        $this->ws = ($ns > 1) ? ($wmax - $ls) / 1000 * $this->FontSize / ($ns - 1) : 0;
                    }
                    $i = $sep + 1;
                }

                $nRet++;

                $sep = -1;
                $j = $i;
                $l = 0;
                $ns = 0;
                $nl++;
            } else {
                $i++;
            }
        }
        //Last chunk
        if ($this->ws > 0) {
            $this->ws = 0;
        }

        return $nRet;
    }

}
