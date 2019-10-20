<?php


namespace Trial\Resources\Pages;

/**
 * Description of PageDataLoader
 *
 * @author giacomo.solazzi
 */
class PageDataLoader {
    
    /**
     * 
     * @param string $pageClassName
     * @return mixed string|null
     */
    
    public static function getClass($pageClassName){        
        
        $class = __NAMESPACE__.'\\'.ucfirst($pageClassName); 
        
        if(class_exists($class)){
            return $class;
        }else{
            return NULL;
        }
        
        
        
    }
    
}
