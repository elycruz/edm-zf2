<?php
/**
 * Description of Uri
 *
 * @author ElyDeLaCruz
 */
class Edm_Validate_Uri extends Zend_Validate_Abstract {
    const INVALID_URL = 'invalidurl';

    protected $_message_templates = array(
        self::INVALID_URL => '%value% is not a valid URI.'
    );

    public function isValid($value) {
        $valueString = (string) $value;
        $this->_setValue($valueString);

        if (!Zend_Uri::check($value)) {
            $this->_error(self::INVALID_URL);
            return false;
        } else {
            return true;
        }
    }
}
