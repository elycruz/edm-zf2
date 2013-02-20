<?php
/**
 * HumanBoolean.php
 * Simple encapsulation of custom markup for boolean values within app
 * Helps making site wide changes easier.  Accepts True || False || Null etc.
 * Returns human readable string 'Yes' or 'No' encapsulated within some
 * hard coded custom markup
 *
 * @author ElyDeLaCruz
 */
class Edm_View_Helper_HumanBoolean
    extends Zend_View_Helper_Abstract
{
    protected $_valid_value_keys = array(
        'boolean', 'label', 'separator', 'field_separator'
    );

    public function humanBoolean(array $values = null)
    {
        /**
         * Validate the values
         */
        Edm_Util_ArrayHelper::compareKeys(
            $values, $this->_valid_value_keys);

        $output = '<div class="form-item human-bln">';

        /**
         * Create label if necessary
         */
        if(key_exists('label', $values)){
            $output .= '<label class="tbld tsml">'. $values['label'];
            $fieldSeparator = key_exists('field_separator', $values) ? 
                    $values['field_separator'] : ':';
            $output .= $fieldSeparator .'&nbsp;</label>';
        }
        /**
         * Boolean
         */
        if(key_exists('boolean', $values)){
            $output .= '<span class="tsml">'. ($values['boolean'] ? 'Yes' : 'No') .'</span>;';
        }
        /**
         * Separator
         */
        if(key_exists('separator', $values)){
            $output .= $values['separator'];
        }
        
        $output .= '</div><!--/.form-item .human-bln-->';
        
        return $output;
    }
}