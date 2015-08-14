<?php

namespace Edm\Utils;

trait GetPublicVarsTrait {
    public function getPublicVars () {
        $class = $this; 
        $vars = function() use ($class) { 
            return get_object_vars($class); 
        }; 
        return $vars(); 
    }
}
