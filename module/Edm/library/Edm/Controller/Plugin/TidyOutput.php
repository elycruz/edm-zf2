<?php

class Edm_Controller_Plugin_TidyOutput
extends Zend_Controller_Plugin_Abstract {

    /**
     * @var tidy|null
     */
    protected $_tidy;
    
    /**
     * @var array
     */
    protected static $_tidyConfig = array(
        'indent' => true,
        'indent-attributes' => false,
        'vertical-space' => false,
        'hide-comments' => true,
        'output-xhtml' => true,
        //'preserve-entities' => true,
        //'drop-proprietary-attributes' => true,
        'wrap' => 0,
            //'tidy-mark' => false,
            //'output-bom' => false
    );
    
    /**
     * Whether or not to diagnose tidy to the log
     * @var boolean
     */
    protected static $_diagnose = false;
    
    /**
     * @var string
     */
    protected static $_tidyEncoding = 'UTF8';

    /**
     * Switch diagnosing HTML mode
     */
    public static function setDiagnose($diagnose = true) {
        self::$_diagnose = (bool) $diagnose;
    }

    public static function setConfig(array $config) {
        self::$_tidyConfig = $config;
    }

    public static function setEncoding($encoding) {
        if (!is_string($encoding)) {
            throw new InvalidArgumentException('Encoding must be a string');
        }
        self::$_tidyEncoding = $encoding;
    }

    protected function getTidy($string = null) {
        if (null === $this->_tidy) {
            if (null === $string) {
                $this->_tidy = new tidy();
            } else {
                $this->_tidy = tidy_parse_string($string,
                        self::$_tidyConfig, self::$_tidyEncoding);
            }
        }
        return $this->_tidy;
    }

    public function dispatchLoopShutdown()
    {
        // Get response
        $response = $this->getResponse();
        
        // Get tidy
        $tidy = $this->getTidy($response->getBody());

        
//        if ('development' === APPLICATION_ENV) {
//            if (true === self::$_diagnose) {
//                $tidy->diagnose();
//                $lines = array_reverse(explode("\n", $tidy->errorBuffer));
//                array_shift($lines);
//                foreach ($lines as $line) {
//                    Lupi_Logger::log($line, Zend_Log::INFO);
//                }
//            }
//        }
        
        // Run tidy
        $tidy->cleanRepair();
        
        // Remove white space
        //$tidy = preg_replace("/\t*\r*\n/m", "", $tidy);
        
        // Set cleaned body
        $response->setBody((string) $tidy);
    }

}