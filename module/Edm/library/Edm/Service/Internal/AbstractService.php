<?php
/**
 * @author ElyDeLaCruz
 */
abstract class Edm_Service_Internal_AbstractService
    implements Edm_UserAccess, Edm_Injectable
{
    /**
     * The Zend Auth Adapter used for authenticating our user.
     * @var Zend_Auth
     */
    protected $_authAdapter;
    
    /**
     * Our user service.
     * @var Edm_Service_Internal_FrontEndUserService
     */
    protected $_userService;

    /**
     * Our user object.
     * @var StdClass
     */
    protected $_user;

    /**
     * Gets our Auth Adapter.
     * @return Zend_Auth
     */
    public function getAuthAdapter()
    {
        $auth = $this->_authAdapter;
        if (empty($auth)) {
            if (Zend_Registry::isRegistered('edm-auth-adapter')) {
                $auth = Zend_Registry::get('edm-auth-adapter');
            }
            else {
                $auth = Zend_Auth::getInstance();
            }
        }
        return $this->_authAdapter = $auth;
    }
    
    /**
     * Sets the auth adapter
     * @param type $adapter
     * @return Edm_Service_Internal_Abstract 
     */
    public function setAuthAdapter(Zend_Auth $adapter) 
    {
        $this->_authAdapter = $adapter;
        Zend_Registry::set('edm-auth-adapter');
        return $this;
    }

    /**
     * Returns our user service.
     * @return Edm_Service_Internal_FrontEndUserService
     */
    public function getUserService()
    {
        $us = $this->_userService;
        if (empty($us)) {
            if (Zend_Registry::isRegistered('edm-user-service')) {
                $us = Zend_Registry::get('edm-user-service');
            }
            else {
                $us = new Edm_Service_Internal_UserService();
                Zend_Registry::set('edm-user-service', $us);
            }
        }
        return $this->_userService = $us;
    }
    
    /**
     * Sets the user service
     * @param type $userService
     * @return Edm_Service_Internal_Abstract 
     */
    public function setUserService(Edm_Service_Internal_AbstractService $value) 
    {
        $this->_userService = $value;
        Zend_Registry::set('edm-user-service', $this->_userService);
        return $this;
    }
    
    /**
     * Gets the logged in user.
     * @return mixed false if no result otherwise an associative array represen-
     * ting the user.
     */
    public function getUser()
    {
        $user = $this->_user;
        if (empty($user)) {
            if (Zend_Registry::isRegistered('edm-user')) {
                $user = Zend_Registry::get('edm-user');
            }
            else {
                $auth = $this->getAuthAdapter();
                if ($auth->hasIdentity()) {
                     $user = $this->getUserService()->getUserById(
                                $auth->getIdentity()->user_id);
                }
                else {
                    return false;
                }
            }
        }
        return $user;
    }
    
    /**
     * Sets the user.
     * @param type $user
     * @return Edm_Service_Internal_Abstract 
     */
    public function setUser($user) 
    {
        $this->_user = $user;
        Zend_Registry::set('edm-user', $user);
        return $this;
    }
    
    ############################################################################
    # INJECTIBLE INTERFACE
    ############################################################################
    
    /**
     * Set element attribute
     *
     * @param  string $name
     * @param  mixed $value
     * @return Edm_Service_Internal_Abstract
     * @throws Exception for invalid $name values
     */
    public function setAttrib($name, $value)
    {
        $name = (string) $name;
        if ('_' == $name[0]) {
            throw new Exception(sprintf('Invalid attribute "%s"; must not '.
                    'contain a leading underscore', $name));
        }

        if (null === $value) {
            unset($this->$name);
        } 
        else if (method_exists($this, 'set' . ucfirst($name))) {
            // Setter exists; use it
            $method = 'set' . ucfirst($key);
            $this->$method($value);
        }
        else {
            $this->$name = $value;
        }
        

        return $this;
    }

    /**
     * Set multiple attributes at once
     *
     * @param  array $attribs
     * @return Edm_Service_Internal_Abstract
     */
    public function setAttribs(array $attribs)
    {
        foreach ($attribs as $key => $value) {
            $this->setAttrib($key, $value);
        }

        return $this;
    }

    /**
     * Retrieve an attribute 
     * Note ** If a getter exists for this property the 
     * getter will be used to retreive the property instead.
     *
     * @param  string $name
     * @return mixed
     */
    public function getAttrib($name)
    {
        $name = (string) $name;
        if (isset($this->$name)) {
            return $this->$name;
        }
        else if (method_exists($this, 'get' . ucfirst($name))) {
            $method = 'get' . ucfirst($name);
            return $this->$method();
        }

        return null;
    }

    /**
     * Return all attributes
     * @return array
     */
    public function getAttribs()
    {
        $attribs = get_object_vars($this);
        foreach ($attribs as $key => $value) {
            // Check for getters for private and protected properties
            if ('_' == substr($key, 0, 1)) {
                unset($attribs[$key]);
                
                // Use getters if they exists otherwise 
                // unset the property
                $method = 'get' . ucfirst($key);
                if (method_exists($this, $method)) {
                    $newVal = $this->$method();
                    $newKey = substr($key, 1);
                    $attribs[$newKey] = $newVal;
                }
            }
        }

        return $attribs;
    }
    
    /**
     * Set object state from options array
     * @param  array $options
     * @return Edm_Service_Internal_Abstract
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $this->setAttrib($key, $value);
        }
        return $this;
    }

    
}