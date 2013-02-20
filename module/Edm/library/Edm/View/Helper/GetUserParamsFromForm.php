<?php
/**
 * Description of GetUserParamsFromForm
 *
 * @author ElyDeLaCruz
 */
class Edm_View_Helper_GetUserParamsFromForm 
extends Edm_View_Helper_Abstract {
    
    protected $_paramLimit = 10;
    
    protected $_paramNamePrefix = 'user_param_';
    
    protected $_fieldNamesPerParam = array(
            'Name' => 'name',
            'Value' => 'value');
    
    /**
     * Returns a forms user params as an array
     * @param Zend_Form $form must implement Edm_Form_Interface_UserParams
     * @return array
     */
    public function getUserParamsFromForm(Zend_Form $form)
    {
        // Output var
        $output = array();

        // If get user param limit exists in form use it
        if ($form instanceof Edm_Form_Interface_UserParams) {
            $this->_paramLimit = $form->getUserParamsLimit();
            $this->_fieldNamesPerParam = $form->getFieldNamesPerUserParam();
        }
        
        // Loop through params
        for ($i = 0; $i < $this->_paramLimit; $i += 1) {
            foreach ($this->_fieldNamesPerParam as
            $humanReadable => $formName) {

                $index = $i + 1;

                $paramFieldName = $this->_paramNamePrefix .
                                        $index . '_' . $formName;
                $paramField = $form->getValue($paramFieldName);

                if (!empty($paramField)) {
                    $output[$paramFieldName] = $paramField;
                }
            } // end for each
        } // end for

        return $output;
    }

}
