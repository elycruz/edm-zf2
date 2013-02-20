<?php

/*
 */
namespace Edm\Controller\Action;
class AbstractController
    extends Zend\Mvc\Controller\Action 
    implements Edm_UserAccess
{
    /**
     * Our current layout object
     * @var Zend_Layout
     */
    protected $_layout;
    
    /**
     * FlashMessenger
     * @var Zend\Mvc\Controller\Action_Helper_FlashMessenger
     */
    protected $_flashMessenger;

    /**
     * Holds a session namespace for this controller's _getAndSetParam function
     * @var Zend_Session_Namespace
     */
    protected $_session_ns;

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
     * Sets the layout variable for this controller if $layout is empty/null
     * then it populates $_layout with the mvc instance of layout
     * @param Zend_Layout $layout
     */
    protected function _getLayout(Zend_Layout $layout = null)
    {
        if (!empty($layout)) {
            $this->_layout = $layout;
        }
        else {
            $this->_layout = Zend_Layout::getMvcInstance();
        }
        return $this->_layout;
    }

    /**
     * Initialize FlashMessenger so that it is available to all actions within
     * the extending controller
     */
    protected function _initFlashMessenger()
    {
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        if ($this->_flashMessenger->setNamespace('highlight')->hasMessages())
        {
            $this->view->messageNamespace = 'highlight';
            $this->view->messages = $this->_flashMessenger->getMessages();
        }
        else if ($this->_flashMessenger->setNamespace('error')->hasMessages())
        {
            $this->view->messageNamespace = 'error';
            $this->view->messages = $this->_flashMessenger
                    ->setNamespace('error')->getMessages();
        }
    }

    /**
     * Sets the session namespace for this controller's _getAndSetParam function
     * @param string $namespace default null; if null is set to this controller'
     * s name.
     * @return Zend_Session_Namespace
     */
    protected function _setSessionNamespace($namespace = null)
    {
        if (empty($namespace)) {
            $request = $this->getRequest();
            $namespace = $request->getModuleName() .'-'.
                $request->getControllerName();
        }
        $this->_session_ns = new Zend_Session_Namespace($namespace);
        return $this->_session_ns;

    }

    /**
     * Gets and sets a url parameter to the view and the session
     * and checks session for value if the value is empty.  Helps keep
     * track of the state of a page according to its parameters.
     * @param string $name
     * @param mixed $defaultValue
     * @return mixed
     */
    protected function _getAndSetParam($name, $defaultValue = null)
    {
        // Get param from request
        $param = $this->_getParam($name);

        // if session_ns is not set, set it to this controller's name
        if (empty($this->_session_ns)) {
            $this->_setSessionNamespace();
        }

        // If empty get param from session
        if (!isset($param)) {
            if (isset($this->_session_ns->$name)) {
                $param = $this->_session_ns->$name;
            }
            else {
                $param = $defaultValue;
            }
        }

        // Set our param to our view and our session namespace
        $this->_session_ns->$name = $this->view->$name = urldecode($param);

        // Return our param
        return $param;
    }

    /**
     * Returns the mvc layout instance
     * @return Zend_Layout
     */
    public function getLayout()
    {
        return $this->_getLayout();
    }

    /**
     * Gets our Flash Messenger
     * @return Zend\Mvc\Controller\Action_Helper_FlashMessenger
     */
    public function getFlashMessenger() 
    {
        // If flashmessenger is empty populate it
        if (empty($this->_flashMessenger)) {
            $this->_initFlashMessenger();
        }
        // return it
        return $this->_flashMessenger;
    }
    
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
    
    public function setAuthAdapter(Zend_Auth $value){
        $this->_authAdapter = $value;
        return $this;
    }
    
    public function setUserService(Edm_Service_Internal_AbstractService $value) {
        $this->_userService = $value;
        return $this;
    }

    /**
     * Returns user service.
     * @return Edm_Service_Internal_UserService
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
    
    public function setUser($value) {
        $this->_user = $value;
        return $this;
    }
    
    /**
     * Gets the logged in user.
     * @return mixed false if no result otherwise an associative array represen-
     * ting the user.
     */
    public function getUser() {
        $user = empty($this->_user) ? 
            ($this->_user = $this->getUserService()->getUser()) : $this->_user;
        return $user;
    }
    
    
}

