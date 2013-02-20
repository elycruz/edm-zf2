<?php
/**
 * @author ElyDeLaCruz
 */
interface Edm_Broker_SimpleBroker 
{
    public static function getStack();
    public static function addItem($itemName);
    public static function hasItem($itemName);
    public static function getItem($itemName);
    public static function removeItem($itemName);
    public function __get($itemName);
}