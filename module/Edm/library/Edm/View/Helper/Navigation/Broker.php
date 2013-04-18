<?php

/**
 * @author Ely De La Cruz
 */
class Edm_View_Helper_Navigation_Broker 
implements  Edm_Broker_SimpleBroker
{
    protected static $_stack = null;
    
    /**
     * Gets our stack
     * @return Edm_View_Helper_Navigation_Broker_Stack
     */
    public static function getStack() {
        if (self::$_stack == null) {
            self::$_stack = 
                    new Edm_View_Helper_Navigation_Broker_Stack();
        }
        return self::$_stack;
    }

    /**
     * Adds an item.
     * @param string $itemName
     * @return stdClass 
     */
    public static function addItem($itemName) {
        $stack = self::getStack();
        if (!$stack->offsetExists($itemName)) {
            $item = $stack->createItem();
            $stack->offsetSet($itemName, $item);
        }
        return $stack->{$itemName};
    }
    
    /**
     * Gets an item.
     * @param string $itemName
     * @return stdClass
     */
    public static function getItem($itemName) {
        $stack = self::getStack();
        return $stack->{$itemName};
    }
    
    /**
     * Removes an item.
     * @param string $itemName 
     * @return stdClass
     */
    public static function removeItem($itemName) {
        $stack = self::getStack();
        return $stack->offsetUnset($itemName);
    }
    
    /**
     * Checks if an item is present.
     * @param string $itemName
     * @return boolean
     */
    public static function hasItem($itemName) 
    {
        $stack = self::getStack();
        return $stack->offsetExists($itemName);
    }

    /**
     * Magick method for getting an item.
     * @param string $itemName
     * @return stdClass
     */
    public function __get($itemName)
    {
        return self::getItem($itemName);
    }

}
