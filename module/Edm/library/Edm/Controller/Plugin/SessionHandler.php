<?php

/**
 * SessionHandler.php
 * Edm_Controller_Plugin_SessionHandler
 *
 * @author Ely De La Cruz
 * @created 3/08/2010
 */
class Edm_Controller_Plugin_SessionHandler 
extends Zend_Controller_Plugin_Abstract 
{

    /**
     * Global Session namespace
     * @var Zend_Session_Namespace
     */
    public static $_global_ns;
    
    private $_maxNumRequestsPerSession = 1000;
    
    private $_incidentMax = 10;

    /**
     * Sudo code for enforcing max num requests
     * If (numReq > mnr && sessDura < sessMaxDura)
     *       Put users IP in server (apache) block list and send email about block
     *       And record incident into database
     *           If incident occurs more than incident max put user on perm block list
     */

    /**
     * Initiate our session here
     * @param Zend_Controller_Request_Abstract $request
     */
    public function routeStartup(Zend_Controller_Request_Abstract $request) 
    {
        // Get the session.ini options
        $session_options = new Zend_Config_INI(
                        APPLICATION_PATH . '/configs/global.session.ini', 'development');

        // Set the session.ini options to the Zend_Session object
        Zend_Session::setOptions($session_options->toArray());

        //setup your DB connection like before
        //NOTE: this config is also passed to Zend_Db_Table so anything specific
        //to the table can be put in the config as well
        $config = array(
            'name' => 'sessions', //table name as per Zend_Db_Table
            'primary' => array(
                'id', //the sessionID given by PHP
                'savePath', //session.save_path
                'name', //session name
            ),
            'primaryAssignment' => array(
                //you must tell the save handler which columns you
                //are using as the primary key. ORDER IS IMPORTANT
                'sessionId', //first column of the primary key is sessionID
                'sessionSavePath', //second column of the primary key save path
                'sessionName', //third column of the primary key session name
            ),
            'modifiedColumn' => 'modified', //time session should expire
            'dataColumn' => 'data', //serialized data
            'lifetimeColumn' => 'lifetime', //end of life for record
        );

        //Tell Zend_Session to use your Save Handler
        Zend_Session::setSaveHandler(
                new Zend_Session_SaveHandler_DbTable($config));

        //  Start our session
        Zend_Session::start();

        // Set global namespace
        self::$_global_ns = new Zend_Session_Namespace('global');

        // Make sure we created our session if not regenrate id and create a 
        // global namespace and set a created variable to true
        if (!self::$_global_ns->created) {
            Zend_Session::regenerateId();
            self::$_global_ns = new Zend_Session_Namespace('global');
            self::$_global_ns->created = true;
            self::$_global_ns->count = 0;
        } 
        else {
            self::$_global_ns->count += 1;
            
        }

        // Check to see if we have a user_agent variable in our global namespace
        if (!isset(self::$_global_ns->user_agent)) {
            // Store user_agent hash in our session and in a cookie
            self::$_global_ns->user_agent =
                    md5(SALT . $_SERVER['HTTP_USER_AGENT'] . PEPPER);
            setcookie('user_agent', self::$_global_ns->user_agent, 0);
        } 
        else if (self::$_global_ns->user_agent !=
                md5(SALT . $_SERVER['HTTP_USER_AGENT'] . PEPPER) ||
                ( isset($_COOKIE['user_agent']) &&
                $_COOKIE['user_agent'] != self::$_global_ns->user_agent )) {

            // Destroy session
            // Regenerate id
            Zend_Session::regenerateId();
            self::$_global_ns = new Zend_Session_Namespace('global');
            self::$_global_ns->created = true;

            // @todo if count exceeds 1000 destroy session and make entry into db.  If this occurs 3 times for the same visitor ban their ip address till further notice
            self::$_global_ns->count = 0;

            // Set user agent
            self::$_global_ns->user_agent =
                    md5(SALT . $_SERVER['HTTP_USER_AGENT'] . PEPPER);
            setcookie('user_agent', self::$_global_ns->user_agent, 0);

            // Throw exception
            throw new Exception(
                    '<p>Your session has ended.  Please visit the' .
                    'link below to start a new session.</p><br />' .
                    '<a href="/">Site root</a>'
            );
        }
    }

}