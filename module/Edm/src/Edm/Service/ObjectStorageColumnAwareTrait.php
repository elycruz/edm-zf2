<?php

namespace Edm\Service;

/**
 * Assumes implements DbDataHelperAware interface
 * @author ElyDeLaCruz
 */
trait ObjectStorageColumnAwareTrait {
    /**
     * Un-serializes and un-escapes serialized and escaped string to an array
     * reverse escaped array.
     * @param string $data
     * @return array
     */
    public function unSerializeAndUnescapeArray($data) {
        return $this->getDbDataHelper()->reverseEscapeTuple(
                    unserialize($data));
    }
    
    /**
     * Un serializes and un escapes a string to an array
     * @param array $data
     * @return array
     */
    public function unSerializeAndUnEscapeTuples($data) {
        return $this->getDbDataHelper()->reverseEscapeTuples(
                    unserialize($data));
    }
    
    /**
     * Serializes and escapes an array to string for insertion into db 
     * @param array $data
     * @return string 
     */
    public function serializeAndEscapeArray($data) {
        return serialize($this->getDbDataHelper()->escapeTuple($data));
    }
    
    /**
     * Serializes and escapes tuples into a string
     * @param array $data
     * @return string
     */
    public function serializeAndEscapeTuples($data) {
        return serialize($this->getDbDataHelper()->escapeTuples($data));
    }
    
}
