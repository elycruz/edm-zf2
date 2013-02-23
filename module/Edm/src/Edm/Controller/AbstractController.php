<?php
/**
 * Description of AbstractController
 *
 * @author ElyDeLaCruz
 */
namespace Edm\Controller;
use Zend\Mvc\Controller\AbstractActionController,
        Zend\View\Model\JsonModel;


class AbstractController extends AbstractActionController {

    /**
     *
     * @var mixed [ViewModel, JsonViewModel, etc.]
     */
    protected $view;
    
    protected function initFlashMessenger() {
        $fm = $this->flashMessenger();
        if ($fm->setNamespace('highlight')->hasMessages()) {
            $this->view->messageNamespace = 'highlight';
            $this->view->messages = $fm->getMessages();
        } else if ($fm->setNamespace('error')->hasMessages()) {
            $this->view->messageNamespace = 'error';
            $this->view->messages = $fm
                            ->setNamespace('error')->getMessages();
        }
        return $fm;
    }
    
    public function flashMessagesToJsonAction () {
        $view = $this->view = new JsonModel();
        $view->setTerminal(true);
        $this->initFlashMessenger();
        return $view;
    }
}
