<?php

/**
 * Description of AbstractController
 *
 * @author ElyDeLaCruz
 */

namespace Edm\Controller;

use Zend\Mvc\Controller\AbstractActionController,
 Zend\View\Model\JsonModel,
 Edm\Db\DbDataHelperAware,
 Edm\Db\DbDataHelperAwareTrait;

class AbstractController extends AbstractActionController 
implements DbDataHelperAware {
    
    use DbDataHelperAwareTrait;
    
    /**
     * @var mixed [ViewModel, JsonViewModel, etc.]
     */
    protected $view;
    
    /**
     * Highlight Message Namespace
     * @var {string} - default 'highglight'
     */
    protected $highlightMessageNamespace = 'highlight';
    
    /**
     * @var {string} - default 'error'
     */
    protected $errorMessageNamespace = 'error';
    
    /**
     * @var {string} - default ''
     */
    protected $messageNamespacePrefix = '';
    
    /**
     * @var {string} - default ''
     */
    protected $messageNamespaceSuffix = '';
    
    protected function getParam($key, $default = null) {
        $routeMatch = $this->getEvent()->getRouteMatch();
        return $routeMatch->getParam($key, $default);
    }

    protected function getAndSetParam($key, $default) {
        return $this->view->$key = $this->getParam($key, $default);
    }

    protected function initFlashMessenger() {
        $fm = $this->flashMessenger();
        $highlight = $this->getMessageNamespaceString($this->highlightMessageNamespace);
        $error = $this->getMessageNamespaceString($this->errorMessageNamespace);
        if ($fm->setNamespace($highlight)->hasMessages()) {
            $this->view->messagesNamespace = $highlight;
            $this->view->messages = $fm->getMessages();
        } else if ($fm->setNamespace($error)->hasMessages()) {
            $this->view->messagesNamespace = $error;
            $this->view->messages = $fm->setNamespace($error)->getMessages();
        }
        return $fm;
    }

    public function flashMessagesToJsonAction() {
        $this->messageNamespacePrefix = $this->getParam('prefix');
        $view = $this->view = new JsonModel();
        $view->setTerminal(true);
        $this->initFlashMessenger();
        return $view;
    }
    
    public function getMessageNamespaceString ($namespace = 'highlight') {
            return $this->messageNamespacePrefix 
                    . $this->{$namespace .'MessageNamespace'} 
                    . $this->messageNamespaceSuffix;
    }

}
