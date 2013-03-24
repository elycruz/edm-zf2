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
 Edm\TraitPartials\DbDataHelperAwareTrait;

class AbstractController extends AbstractActionController 
implements DbDataHelperAware {
    
    use DbDataHelperAwareTrait;
    
    /**
     *
     * @var mixed [ViewModel, JsonViewModel, etc.]
     */
    protected $view;

    protected function getParam($key, $default = null) {
        $routeMatch = $this->getEvent()->getRouteMatch();
        return $routeMatch->getParam($key, $default);
    }

    protected function getAndSetParam($key, $default) {
        return $this->view->$key = $this->getParam($key, $default);
    }

    protected function initFlashMessenger() {
        $fm = $this->flashMessenger();
        if ($fm->setNamespace('highlight')->hasMessages()) {
            $this->view->messagesNamespace = 'highlight';
            $this->view->messages = $fm->getMessages();
        } else if ($fm->setNamespace('error')->hasMessages()) {
            $this->view->messagesNamespace = 'error';
            $this->view->messages = $fm
                            ->setNamespace('error')->getMessages();
        }
        return $fm;
    }

    public function flashMessagesToJsonAction() {
        $view = $this->view = new JsonModel();
        $view->setTerminal(true);
        $this->initFlashMessenger();
        return $view;
    }

}
