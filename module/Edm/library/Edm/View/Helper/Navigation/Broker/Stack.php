<?php
/**
 * Provides a stack for our nav broker.
 * @author ElyDeLaCruz
 */
class Edm_View_Helper_Navigation_Broker_Stack
    implements Edm_Broker_SimpleStack
{
    /**
     * Holds our items
     * @var array
     */
    protected $_itemsByName = array();
    
    /**
     * Holds our regular expression which is used to validate item name
     * @var string 
     */
    protected $_itemNamePattern = '/^[^a-z0-9\-\_]$/i';

    public function __constructor() {}
	
    /**
     * Gets an entry from our items array if it exists otherwise lazy loads it.
     * @param string $itemName
     * @return stdClass 
     */
    public function __get($itemName)
    {
        $itemName = $this->normalizeName($itemName);
        if (!array_key_exists($itemName, $this->_itemsByName)) {
            $this->_itemsByName[$itemName] = $this->createItem();
        }
        return $this->_itemsByName[$itemName];
    }
    
    /**
     * Magick method for checking if an entry is set.
     * @param string $itemName
     * @return boolean 
     */
    public function __isset($itemName)
    {
        $itemName = $this->normalizeName($itemName);
        return array_key_exists($itemName, $this->_itemsByName);
    }

    /**
     * Unsets an item.
     * @param string $itemName
     * @return void 
     */
    public function __unset($itemName)
    {
        $itemName = $this->normalizeName($itemName);
        return $this->offsetUnset($itemName);
    }

    /**
     * Gets an array object which we can use as an iterator.
     * @return ArrayObject 
     */
    public function getIterator()
    {
        return new ArrayObject($this->_itemsByName);
    }
    
    /**
     * Pushes an item into our item array.
     * @param type $item
     * @return void
     */
    public function push($item) {
        return $this->_itemsByName[] = $item;
    }

    /**
     * Checks if our offset exists.
     * @param string $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        $itemName = $this->normalizeName($offset);
        return array_key_exists($itemName,
                $this->_itemsByName);
    }

    /**
     * Throws and exception if offset is not set otherwise 
     * returns entry at offset $offset.
     * @param type $offset
     * @return stdClass
     */
    public function offsetGet($offset)
    {
        $itemName = $this->normalizeName($offset);
        if (!isset($this->_itemsByName[$itemName])) {
            throw new Exception('Offset '. $itemName 
                    .' does not exists in Navigation Stack');
        }
        return $this->{$itemName};
    }

    /**
     * Sets an offset to value.
     * @param string $offset
     * @param stdClass $value
     * @return Edm_View_Helper_Navigation_Broker_Stack 
     */
    public function offsetSet($offset, $value)
    {
        if (!is_object($value)) {
            throw new Exception('OffsetSet\'s \$value must be an object');
        }
        $itemName = $this->normalizeName($offset);
        $this->_itemsByName[$itemName] = $value;
        return $this;
    }

    /**
     * Unsets a value from our item array.
     * @param string $itemName
     * @return Edm_View_Helper_Navigation_Broker_Stack 
     */
    public function offsetUnset($itemName)
    {
        $itemName = $this->normalizeName($itemName);
        if (!$this->offsetExists($itemName)) {
            throw new Exception('An entry for navigation stdclass ' .
                    $itemName . ' does not exist in the Navigation Stack.');
        }
        unset($this->_itemsByName[$itemName]);
        return $this;
    }

    /**
     * Returns the count of our item array.
     * @return int
     */
    public function count()
    {
        return count($this->_itemsByName);
    }
 
    /**
     * Returns a normalized item name string.  Throws an Exception on if item 
     * name is not string.
     * @param string $itemName
     * @return string
     */
    public function normalizeName($itemName) 
    {
        if (is_string($itemName)) {
            $itemName = preg_replace($this->_itemNamePattern, '-', $itemName);
            return $itemName;
        }
        else {
            throw new Exception('Cannot not normalize Navigation Stack '.
                    'Item name/offset.  Offset must be of type "string".');
        }
    }
    
    /**
     * Creates an item which can then be passed into our item array.
     * @param array $options default null
     * @return stdClass 
     */
    public function createItem($options = null) {
        $item = new stdClass();
        $item->navigation = new Zend_Navigation($options);
        return $item;
    }

}