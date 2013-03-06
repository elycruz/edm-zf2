<?php
namespace Edm\Model;

/**
 * @author ElyDeLaCruz
 */
class AbstractModel {
    
    public function exchangeArray(array $data) {
        foreach ($data as $key => $val) {
            if (property_exists($this, $key)) {
                $this->{$key} = $val;
            }
        }
    }
    
    public function toArray () {
        $retVal = array();
        foreach($this->validKeys as $key) {
            $retVal[$key] = $this->{$key};
        }
        return $retVal;
    }
    
}
