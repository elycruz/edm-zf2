<?php

namespace Edm\Model;

/**
 * Trait for models who should give access of the fields listed in a 
 * form or fieldset that belong to this model.
 *
 * @author ElyDeLaCruz
 */
trait FieldsInFormAwareTrait {
    
    /**
     * Fields in form.  A list of the fields listed in a form/fieldset that
     * belong to this model.
     * 
     * @var array
     */
    protected $fieldsInForm;
    
    /**
     * Get the fields listed in a form or fieldset for this model.
     * 
     * @return array
     */
    public function getFieldsInForm() {
        return $this->fieldsInForm;
    }

    /**
     * Set the fields in from/fieldset for this model.
     * 
     * @param array $fieldsInForm
     */
    public function setFieldsInForm(array $fieldsInForm) {
        $this->fieldsInForm = $fieldsInForm;
    }
    
    /**
     * Get the values for the fields in form as a key value array.
     * 
     * @return array
     */
    public function getFieldsInFormToArray () {
        // Out value
        $out = array();
        
        // If no fields in form return empty array
        if (!is_array($this->fieldsInForm)) {
            return $out;
        }
        
        // Loop through fields in form
        foreach ($this->fieldsInForm as $key) {
            $out[$key] = $this->{$key};
        }
        
        // Return result
        return $out;
    }

}
