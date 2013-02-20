<?php
/**
 * @author ElyDeLaCruz
 */
class Edm_Date_Date extends Zend_Date
{
    public function timestampToMDY($timestamp) {
        return $this->date('m/d/Y', $timestamp);
    }
}