<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TupleListOrderControl
 *
 * @author ElyDeLaCruz
 */
class Edm_View_Helper_ReverseEscapeTupleFromDb
    extends Zend_View_Helper_Abstract
{
    /**
     * Returns the db $tuple unescaped
     * @param string $tuple
     * @param boolean $decodeHtml decodes any html data within tuple using
     * html_entity_decode()
     * @return string
     */
    public function reverseEscapeTupleFromDb( $tuple = null, $decodeHtml = false )
    {
        return Edm_Util_DbDataHelper::getInstance()
                ->reverseEscapeTupleFromDb($tuple);
    }
}
