<?php
/**
 * @author ElyDeLaCruz
 */
class Edm_Controller_Plugin_CookieSession
extends Zend_Controller_Plugin_Abstract
{
    /**
     * Global Session namespace
     * @var Zend_Session_Namespace
     */
    public static $_global_ns;

    /**
     * Initiate our session here
     * @param Zend_Controller_Request_Abstract $request
     */
    public function routeStartup( Zend_Controller_Request_Abstract $request )
    {
        // Get the session.ini options
        $session_options = new Zend_Config_INI(
                APPLICATION_PATH .'/configs/session.ini', APPLICATION_ENV);

        // Set the session.ini options to the Zend_Session object
        Zend_Session::setOptions( $session_options->toArray() );

        //  Start our session
        Zend_Session::start();

        // Set global namespace
        self::$_global_ns =  new Zend_Session_Namespace('global');

        // Make sure we created our session
        // if not regenrate id and create a global
        // namespace and set a created variable to
        // true
        if( !self::$_global_ns->created )
        {
            Zend_Session::regenerateId();
            self::$_global_ns =  new Zend_Session_Namespace('global');
            self::$_global_ns->created = true;
            self::$_global_ns->count = 0;
        }
        else {
            self::$_global_ns->count += 1;
        }

        // Check to see if we have a user_agent variable in our
        // global namespace.
        if( !isset( self::$_global_ns->user_agent ))
        {
            // Store user_agent hash in our session and in a cookie
            self::$_global_ns->user_agent =
                    md5( SALT . $_SERVER['HTTP_USER_AGENT'] . PEPPER );
            setcookie('user_agent',
                    self::$_global_ns->user_agent, 0 );
        }
        // Else verify user_agent hash is legit
        else {
            if( self::$_global_ns->user_agent !=
                    md5( SALT . $_SERVER['HTTP_USER_AGENT'] . PEPPER ) ||
                    ( isset( $_COOKIE['user_agent'] ) &&
                    $_COOKIE['user_agent'] != self::$_global_ns->user_agent )){
                // Possible Security Violation.  Tell the user what has
                // happened and redirect them to more friendly pastures.
                Zend_Session::regenerateId();
                self::$_global_ns =  new Zend_Session_Namespace('global');
                self::$_global_ns->created = true;
                self::$_global_ns->count = 0;
                throw new Exception(
                        '<p>Your session has ended.  Please visit the'.
                        'link below to start a new session.</p><br />'.
                        '<a href="/">Site root</a>'
                    );
            }
        }
        //Zend_Debug::dump(self::$_global_ns); exit();

    }
}