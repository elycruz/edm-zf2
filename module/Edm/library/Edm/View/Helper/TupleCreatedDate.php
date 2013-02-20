<?php

/**
 * TupleCreatedDate.php
 * Edm_View_Helper_TupleCreatedDate
 * Takes createdDate timestamp and converts it to a human readable format
 *
 * @author ElyDeLaCruz
 */
class Edm_View_Helper_TupleCreatedDate
extends Zend_View_Helper_Abstract
{
    public function tupleCreatedDate($createdDate = null)
    {
        /**
         * Get the last updated field
         */
        if(!empty($createdDate) &&
                is_numeric($createdDate)){
            return new Zend_Date($createdDate, Zend_Date::TIMESTAMP);
        }
        return;
    }
}
