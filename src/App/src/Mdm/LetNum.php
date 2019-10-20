<?php

namespace App\Mdm;

/**
 * Description of LetNum
 *
 * @author giacomo.solazzi
 */
class LetNum {

    protected static $letNum = [
        ['a', 'j', 's'],
        ['b', 'k', 't'],
        ['c', 'l', 'u'],
        ['d', 'm', 'v'],
        ['e', 'n', 'w'],
        ['f', 'o', 'x'],
        ['g', 'p', 'y'],
        ['h', 'q', 'z'],
        ['i', 'r']];

    /**
     * 
     * @return array
     */
    public static function getLetters() {
        
        $ret = array();
        
        foreach (self::$letNum as $grLet) {            
            $ret = array_merge($ret, array_values($grLet));            
        }        
        
        return $ret;
    }

    /**
     * 
     * @param string $string
     * @return int
     */
    public static function matchStringNumber($string) {

        $sum = 0;

        foreach (str_split(strtolower($string)) as $char) {

            if ($char == ' ') {
                continue;
            }

            foreach (self::$letNum as $index => $chars) {

                if (in_array($char, $chars)) {

                    $sum += intval($index) + 1;
                }
            }
        }

        return RedNum::shrink($sum);
    }

    /**
     * 
     * @param int $num
     * @return array
     */
    public static function matchNumberChars($num) {

        if ($num > 0) {
            return self::$letNum[--$num];
        } else {
            return [];
        }
    }

    

}
