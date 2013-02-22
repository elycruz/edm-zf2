<?php
/**
 * Description of AbstractController
 *
 * @author ElyDeLaCruz
 */
namespace Edm\Controller;
use Zend\Mvc\Controller\AbstractActionController;


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
}
