<?php

namespace Edm\InputFilter;
use Zend\Config\Config;

/**
 * Gateway to default shared input options
 * @author ElyDeLaCruz
 */
class DefaultInputOptions extends Config {
    public function __construct(array $array = null, $allowModifications = false) {
        if ($array === null) {
            $array = include APP_PATH .'/module/Edm/configs/default.input.options.php';
        }
        parent::__construct($array, $allowModifications);
    }
}
