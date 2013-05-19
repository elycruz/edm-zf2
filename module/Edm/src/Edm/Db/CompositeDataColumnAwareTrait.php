<?php

namespace Edm\Db;

/**
 * Assumes class using trait implements DbDataHelperAware interface
 * @author ElyDeLaCruz
 */
trait CompositeDataColumnAwareTrait {
    /**
     * Un-serializes and un-escapes serialized and escaped string to an array
     * reverse escaped array.
     * @param string $data
     * @return array $user
     */
    public function unSerializeAndUnEscapeArray(array $data) {
        return $this->getDbDataHelper()->reverseEscapeTuple(
                    unserialize($data));
    }
    
    /**
     * Un serializes and un escapes a string to an array
     * @param array $data
     * @return array
     */
    public function unSerializeAndUnEscapeTuples(array $data) {
        return $this->getDbDataHelper()->reverseEscapeTuples(
                    unserialize($data));
    }
    
    /**
     * Serializes and escapes an array to string for insertion into db 
     * @param array $data
     * @return string 
     */
    public function serializeAndEscapeArray(array $data) {
        return serialize($this->getDbDataHelper()->escapeTuple($data));
    }
    
    /**
     * Serializes and escapes tuples into a string
     * @param array $data
     * @return string
     */
    public function serializeAndEscapeTuples(array $data) {
        return serialize($this->getDbDataHelper()->escapeTuples($data));
    }
    
}
