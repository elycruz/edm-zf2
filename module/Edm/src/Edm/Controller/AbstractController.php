<?php

/**
 * Description of AbstractController
 *
 * @author ElyDeLaCruz
 */

namespace Edm\Controller;

use Zend\Mvc\Controller\AbstractActionController,
 Zend\View\Model\JsonModel,
 Edm\Db\DatabaseDataHelper,
 Edm\Db\DbDataHelperAccess,
 Edm\Db\DbDataHelper;

class AbstractController extends AbstractActionController 
implements DbDataHelperAccess {

    /**
     *
     * @var mixed [ViewModel, JsonViewModel, etc.]
     */
    protected $view;

    protected function getParam($key, $default = null) {
        // Route match
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

    public function setDbDataHelper(DbDataHelper $dbDataHelper) {
        $this->dbDataHelper = $dbDataHelper;
    }

    public function getDbDataHelper() {
        if (empty($this->dbDataHelper)) {
            $this->dbDataHelper = new DatabaseDataHelper();
//                    $this->getServiceLocator()->get('Edm\Db\DatabaseDataHelper');
        }
        return $this->dbDataHelper;
    }

}
