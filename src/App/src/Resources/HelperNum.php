<?php

namespace App\Resources;

use App\Mdm\DatiPersona;
use App\Mdm\RedNum;
use App\Mdm\LetNum;

/**
 * Description of class HelperNum
 *
 * @author giacomo.solazzi
 */
class HelperNum {

    protected static $letPianoMentale = ['a', 'j', 's', 'h', 'q', 'z'];
    protected static $letPianoEmotivo = ['b', 'k', 't', 'c', 'l', 'u', 'f', 'o', 'x'];
    protected static $letPianoFisico = ['d', 'm', 'v', 'e', 'n', 'w'];
    protected static $letPianoSpirituale = ['g', 'p', 'y', 'i', 'r'];
    protected static $vocali = ['a', 'e', 'i', 'o', 'u'];

    /**
     * I numeri associati al piano mentale sono 1 e 8
     * conta quante volte sono presenti nel nome, 
     * cognome e data di nascita della persona
     * 
     * @param DatiPersona $dp
     * @return int
     */
    public static function freqPianoMentale(DatiPersona $dp) {

        $cntPiano = 0;

        foreach (count_chars($dp->nome, 1) as $i => $val) {

            if (in_array(chr($i), self::$letPianoMentale)) {
                $cntPiano = $cntPiano + $val;
            }
        }

        foreach (count_chars($dp->cognome, 1) as $i => $val) {

            if (in_array(chr($i), self::$letPianoMentale)) {
                $cntPiano = $cntPiano + $val;
            }
        }

        foreach (count_chars($dp->getNumeriNascita(), 1) as $i => $val) {

            $chars = LetNum::matchNumberChars(chr($i));

            if (is_array($chars) && !empty($chars)) {

                foreach ($chars as $char) {

                    if (in_array($char, self::$letPianoMentale)) {
                        $cntPiano = $cntPiano + $val;
                    }
                }
            }
        }

        return $cntPiano;
    }

    /**
     * I numeri associati al piano emotivo sono 2, 3, 6
     * conta quante volte sono presenti nel nome, 
     * cognome e data di nascita della persona
     * 
     * @param DatiPersona $dp
     * @return int
     */
    public static function freqPianoEmotivo(DatiPersona $dp) {

        $cntPiano = 0;

        foreach (count_chars($dp->nome, 1) as $i => $val) {

            if (in_array(chr($i), self::$letPianoEmotivo)) {
                $cntPiano = $cntPiano + $val;
            }
        }

        foreach (count_chars($dp->cognome, 1) as $i => $val) {

            if (in_array(chr($i), self::$letPianoEmotivo)) {
                $cntPiano = $cntPiano + $val;
            }
        }

        foreach (count_chars($dp->getNumeriNascita(), 1) as $i => $val) {

            $chars = LetNum::matchNumberChars(chr($i));

            if (is_array($chars) && !empty($chars)) {

                foreach ($chars as $char) {

                    if (in_array($char, self::$letPianoEmotivo)) {
                        $cntPiano = $cntPiano + $val;
                    }
                }
            }
        }

        return $cntPiano;
    }

    /**
     * I numeri associati al piano fisico sono 4, 5
     * conta quante volte sono presenti nel nome, 
     * cognome e data di nascita della persona
     * 
     * @param DatiPersona $dp
     * @return int
     */
    public static function freqPianoFisico(DatiPersona $dp) {

        $cntPiano = 0;

        foreach (count_chars($dp->nome, 1) as $i => $val) {

            if (in_array(chr($i), self::$letPianoFisico)) {
                $cntPiano = $cntPiano + $val;
            }
        }

        foreach (count_chars($dp->cognome, 1) as $i => $val) {

            if (in_array(chr($i), self::$letPianoFisico)) {
                $cntPiano = $cntPiano + $val;
            }
        }

        foreach (count_chars($dp->getNumeriNascita(), 1) as $i => $val) {

            $chars = LetNum::matchNumberChars(chr($i));

            if (is_array($chars) && !empty($chars)) {

                foreach ($chars as $char) {

                    if (in_array($char, self::$letPianoFisico)) {
                        $cntPiano = $cntPiano + $val;
                    }
                }
            }
        }

        return $cntPiano;
    }

    /**
     * I numeri associati al piano spirituale sono 7, 9
     * conta quante volte sono presenti nel nome, 
     * cognome e data di nascita della persona
     * 
     * @param DatiPersona $dp
     * @return int
     */
    public static function freqPianoSpirituale(DatiPersona $dp) {

        $cntPiano = 0;

        foreach (count_chars($dp->nome, 1) as $i => $val) {

            if (in_array(chr($i), self::$letPianoSpirituale)) {
                $cntPiano = $cntPiano + $val;
            }
        }

        foreach (count_chars($dp->cognome, 1) as $i => $val) {

            if (in_array(chr($i), self::$letPianoSpirituale)) {
                $cntPiano = $cntPiano + $val;
            }
        }

        foreach (count_chars($dp->getNumeriNascita(), 1) as $i => $val) {

            $chars = LetNum::matchNumberChars(chr($i));

            if (is_array($chars) && !empty($chars)) {

                foreach ($chars as $char) {

                    if (in_array($char, self::$letPianoSpirituale)) {
                        $cntPiano = $cntPiano + $val;
                    }
                }
            }
        }

        return $cntPiano;
    }

    /**
     * 
     * La riduzione numerologica a 9 delle vocali
     * nel nome e nel cognome sia associa all'archetipo
     * che rappresenta la nostra interiorità
     * 
     * 
     * @param DatiPersona $dp
     * @return int
     */
    
    public static function archetipoVocali(DatiPersona $dp) {

        $sumVocali = 0;
        
        foreach (str_split($dp->nome) as $char) {

            if (in_array($char, self::$vocali)) {
                $sumVocali = $sumVocali + intval(LetNum::matchStringNumber($char));
            }
        }

        foreach (str_split($dp->cognome) as $char) {

            if (in_array($char, self::$vocali)) {
                $sumVocali = $sumVocali + intval(LetNum::matchStringNumber($char));
            }
        }
        
        return RedNum::shrink($sumVocali);
        
    }
    
    /**
     * 
     * La riduzione numerologica a 9 delle consonanti
     * nel nome e nel cognome sia associa all'archetipo
     * che rappresenta la nostra immagine esteriore, ciò
     * che la gente vede di noi
     * 
     * 
     * @param DatiPersona $dp
     * @return int
     */
    
    public static function archetipoConsonanti(DatiPersona $dp) {

        $sumCons = 0;
        
        foreach (str_split($dp->nome) as $char) {

            if (!in_array($char, self::$vocali) && in_array($char, LetNum::getLetters())) {
                $sumCons = $sumCons + intval(LetNum::matchStringNumber($char));
            }
        }

        foreach (str_split($dp->cognome) as $char) {

            if (!in_array($char, self::$vocali) && in_array($char, LetNum::getLetters())) {
                $sumCons = $sumCons + intval(LetNum::matchStringNumber($char));
            }
        }
        
        return RedNum::shrink($sumCons);
        
        
    }

}
