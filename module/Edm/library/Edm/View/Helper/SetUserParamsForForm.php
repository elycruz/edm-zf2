<?php

/**
 * Description of SetUserParamsForForm
 *
 * @author ElyDeLaCruz
 */
class Edm_View_Helper_SetUserParamsForForm extends Edm_View_Helper_Abstract
{
    protected $_paramLimit = 10;
    
    protected $_paramNamePrefix = 'user_param_';
    
    protected $_fieldNamesPerParam = array(
            'Name' => 'name',
            'Value' => 'value');
    
    public function setUserParamsForForm(Zend_Form $form, array $values) 
    {
        // If get user param limit exists in form use it
        if ($form instanceof Edm_Form_Interface_UserParams) {
            $this->_paramLimit = $form->getUserParamsLimit();
            $this->_fieldNamesPerParam = $form->getFieldNamesPerUserParam();
        }
        
        // Loop through params
        for ($i = 0; $i < $this->_paramLimit; $i += 1) {
            foreach ($this->_fieldNamesPerParam as $fieldName) {

                $index = $i + 1;
                $paramField = $this->_paramNamePrefix . $index . '_' . $fieldName;
                $paramFieldElm = $form->getElement($paramField);

                if (array_key_exists($paramField, $values)
                        && !empty($paramFieldElm)) {
                    $form->setDefault($paramField, $values[$paramField]);
                }
            } // end for each
        } // end for

        return $form;
    }

}
