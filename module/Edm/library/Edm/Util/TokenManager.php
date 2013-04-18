<?php

// @todo refactor this to be just a token and/or extend Zend_Session instead
defined('DEFAULT_EDM_TOKEN_SEED')
        || define('DEFAULT_EDM_TOKEN_SEED', 'edm-util-tokenmanager');

class Edm_Util_TokenManager {

    /**
     * Session namespace for token application
     * @var Zend_Session_Namespace
     */
    private $_session_ns;
    /**
     * Session namespace name
     * @var string
     */
    private $_namespace;
    /**
     * Single instance flag
     * @var boolean
     */
    private $_singleInstance;


    public function __construct($namespace = null, $singleInstance = false) {
        $this->_namespace = $namespace;
        $this->_singleInstance = $singleInstance;
        $namespace = $namespace ? $namespace : 'edm';
        $this->setSessionNamespace($namespace, $singleInstance);
    }

    /**
     * Set the Session namespace
     * @param $value
     * @return unknown_type
     */
    public function setSessionNamespace($value, $singleInstance = false) {
        $this->_session_ns = new Zend_Session_Namespace(
                $value, $singleInstance);
        return $this->_session_ns;
    }

    /**
     * Takes an alphanumeric string as seed and generates a unique token
     * @param $seed the seed used to generate our token default null
     * @param $withTimeStamp flag for checking whether to append time
     *  stamp to token
     * @param $salt prefix for the seed default null
     * @param $pepper suffix for the seed default null
     * @return String
     */
    public function generateToken($seed = null, $withTimeStamp = true, 
            $salt = null, $pepper = null) {
        //  Get seed
        $seed = $seed ? uniqid($seed) : uniqid(DEFAULT_EDM_TOKEN_SEED);

        // Check if we have to add timestamp to our seed
        if ($withTimeStamp) {
            $seed = $seed . time();
        }

        // Check if we have to add salt
        if ($salt) {
            $seed = $salt . $seed;
        }

        // Check if we have to add pepper
        if ($pepper) {
            $seed = $seed . $pepper;
        }

        // Set and return the token
        $this->_session_ns->token = md5($seed);
        return $this->_session_ns->token;
    }

    /**
     * Takes a token value and checks it versus the current token
     * within the session namespace passed to the constructor.
     * @param $tokenToCheck
     * @return Boolean
     */
    public function checkToken($tokenToCheck) {
        return (!empty($tokenToCheck) &&
        $tokenToCheck === $this->_session_ns->token );
    }

}