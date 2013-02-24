<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Edm for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Edm\Controller;

use Edm\Controller\AbstractController,
    Edm\Model\Term,
    Edm\Form\TermForm,
    Zend\View\Model\ViewModel,
    Zend\View\Model\JsonModel,
    Zend\Paginator\Paginator,
    Zend\Paginator\Adapter\DbSelect,
    Zend\Db\Sql\Select;

class TermController extends AbstractController {

    protected $termTable;

    public function indexAction () {
        // View
        $view = 
            $this->view = 
                new JsonModel();

        // Model
        $model = $this->getTermModel();

        // Page number
        $pageNumber = $this->getAndSetParam('page', 1);

        // Items per page
        $itemCountPerPage = $this->getAndSetParam('itemsPerPage', 5);

        // Select 
        $select = new Select();
        $select->from($model->getTable());

        // Paginator
        $paginator = new Paginator(new DbSelect($select, $model->getAdapter()));
        $paginator->setItemCountPerPage($itemCountPerPage)
                ->setCurrentPageNumber($pageNumber);

        // Set actual page (happens to fix exceeded page number set by user)
        $view->itemsTotal = $paginator->getTotalItemCount();

        // Send results
        $view->results = $paginator;
        $view->setTerminal(true);
        return $view;
    }

    public function createAction () {
        // Set up prelims and populate $this -> view for 
        // init flash messenger
        $view =
            $this->view =
                new ViewModel();
        $view->setTerminal(true);
        $fm = $this->initFlashMessenger();

        // Setup form
        $form = new TermForm();
        $form->setAttribute('action', '/edm-admin/term/create');
        $view->form = $form;

        // If not post bail
        $request = $this->getRequest();
        if (!$request->isPost()) {
            return $view;
        }

        // Processing request
        $term = new Term();
        $view->form->setInputFilter($term->getInputFilter());
        $view->form->setData($request->getPost());

        // If form not valid return
        if (!$view->form->isValid()) {
            $fm->setNamespace('error')->addMessage('Form validation failed.');
            return $view;
        }

        // Put data into model
        $termTable = $this->getTermModel();
        $term->exchangeArray($view->form->getData());

        // Check if term already exists
        $termExists = $termTable->getByAlias((string) $term->alias);
        if (!empty($termExists)) {
            $fm->setNamespace('error')->addMessage('Term "' . $term->name 
                    . '" with alias "' . $term->alias . '" already exists.');
            return $view;
        }

        // Put term in to db
        $rslt = $termTable->create($term->toArray());

        // Send success message to user
        if ($rslt) {
            $fm->setNamespace('highlight')
                    ->addMessage('Term "' . $term->name . '" with alias "'
                            . $term->alias .'" added successfully.')
                    ->addMessage($term->alias);
        } 
        // send failure message to user 
        else {
            $fm->setNamespace('error')
                    ->addMessage('Term "' . $term->name . '" with alias "' 
                            . $term->alias . '" failed to be added to database.');
        }

        // Return message to view
        return $view;
    }

    public function fooAction() {
        // Render
        $renderer = $this->getServiceLocator()->get('viewrenderer');
        $subView = new ViewModel();
        $subView->setTemplate('edm/partials/message.phtml');
        $view = new JsonModel();
        $view->subView = $renderer->render($subView);

        // 
        $termTable = $this->getTermModel();
        $fm = $this->initFlashMessenger();
        
        // Check if term already exists
        $termExists = $termTable->getByAlias('dd');
        if (!empty($termExists)) {
            $fm->setNamespace('error')->addMessage('Term "hello" already exists.');
            $view->exists = true;
        }
        $this->initFlashMessenger();
        $view->term = $termExists;
        $view->setTerminal(true);
        return $view;
    }

    /**
     * Gets our Term model
     * @return Edm\Model\Term
     */
    public function getTermModel() {
        if (empty($this->termTable)) {
            $this->termTable = $this
                            ->getServiceLocator()->get('Edm\Model\TermTable');
        }
        return $this->termTable;
    }

}
