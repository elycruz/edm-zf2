<?php

namespace Edm\Model;

/**
 * Fields In Form Aware Interface
 * @author ElyDeLaCruz
 */
interface FieldsInFormAwareInterface {

    public function getFieldsInForm();

    public function setFieldsInForm(array $fieldsInForm);
}
