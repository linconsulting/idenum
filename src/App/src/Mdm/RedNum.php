<?php

namespace App\Mdm;

/**
 * Description of LetNum
 *
 * @author giacomo.solazzi
 */
class RedNum {

    public static function shrink($num) {
        
        if(is_int($num) && $num > 9) {
            
            return self::shrink(self::splitSum($num));
            
        } elseif(is_int($num) && $num >= 0 && $num <= 9) {
            
            return $num;
            
        } else {
            return NULL;
        }
    }
    
    
    private static function splitSum($num){
        
        $aSum = 0;
        
        foreach (str_split($num) as $value) {
            
            $aSum += intval($value);
            
        }
        
        return $aSum;
        
    }

}
