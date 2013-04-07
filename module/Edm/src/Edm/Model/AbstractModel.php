<?php

namespace Edm\Model;

/**
 * Abstract Model
 * @author ElyDeLaCruz
 */
class AbstractModel {

    /**
     * Valid keys for model
     * @var array
     */
    protected $validKeys;

    /**
     * Constructor
     * @param array $data
     */
    public function __construct(array $data = null) {
        if (!empty($data)) {
            $this->exchangeArray($data);
        }
    }

    /**
     * Exchange array
     * @param array $data
     * @return \Edm\Model\AbstractModel
     */
    public function exchangeArray(array $data) {
        foreach ($data as $key => $val) {
            if (in_array($key, $this->validKeys)) {
                $this->{$key} = $val;
            }
        }
        return $this;
    }

    /**
     * Setter
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value) {
        $this->{$name} = $value;
    }

    /**
     * Getter
     * @param string $name
     */
    public function __get($name) {
        if (!isset($this->{$name})) {
            $this->{$name} = null;
        }
        return $this->{$name};
    }

    /**
     * Returns model as array
     * @return array
     */
    public function toArray() {
        $retVal = array();
        foreach ($this->validKeys as $key) {
            $retVal[$key] = $this->{$key};
        }
        return $retVal;
    }
    
}
