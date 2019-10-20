<?php


namespace Trial\Resources\Pages\Ajax;

/**
 * Description of PageDataLoader
 *
 * @author giacomo.solazzi
 */
class AjaxClassLoader {
    
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
