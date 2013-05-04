<?php

namespace Edm\Db;

/**
 * Assumes class using trait implements DbDataHelperAware interface
 * @author ElyDeLaCruz
 */
trait CompositeDataColumnAwareTrait {
    /**
     * Un-serializes and un-escapes serialized and escaped data fetched from db
     * reverse escaped array.
     * @param string $data
     * @return array $user
     */
    public function unSerializeAndUnEscapeArray(array $data) {
        return $this->getDbDataHelper()->reverseEscapeTupleFromDb(
                    unserialize($data));
    }
    
    /**
     * Serializes and escapes an array to string for insertion into db 
     * @param array $data
     * @return string 
     */
    public function serializeAndEscapeArray(array $data) {
        return serialize($this->getDbDataHelper()->escapeTupleForDb($data));
    }
}
