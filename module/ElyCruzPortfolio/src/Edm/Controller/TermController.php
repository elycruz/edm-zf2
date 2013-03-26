<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Edm for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
/**
 * @todo Implement DatabaseDataHelper
 */

namespace Edm\Controller;

use Edm\Controller\AbstractController,
    Edm\Model\Term,
    Edm\Form\TermForm,
    Zend\View\Model\ViewModel,
    Zend\View\Model\JsonModel,
    Zend\Paginator\Paginator,
    Zend\Paginator\Adapter\DbSelect,
    Zend\Db\Sql\Select,
    Zend\Debug\Debug;

class TermController extends AbstractController {

    protected $termTable;

    public function indexAction() {
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
        $view->results = $this->getDbDataHelper()->reverseEscapeTuples(
                $paginator->getCurrentItems()->toArray());
        $view->setTerminal(true);
        return $view;
    }

    public function createAction() {
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
        $rslt = $termTable->createItem($term->toArray());

        // Send success message to user
        if ($rslt) {
            $fm->setNamespace('highlight')
                    ->addMessage('Term "' . $term->name . '" with alias "'
                            . $term->alias . '" added successfully.')
                    ->addMessage($term->alias);
            $view->form->setData(array(
                'name' => '',
                'alias' => '',
                'term_group_alias' => ''
            ));
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

    public function updateAction() {
        // Set up prelims and populate $this -> view for 
        // init flash messenger
        $view =
                $this->view =
                new ViewModel();
        $view->setTerminal(true);
        $fm = $this->initFlashMessenger();

        // Id
        $id = $this->getParam('itemId');

        // Put data into model
        $termTable = $this->getTermModel();

        // Check if term already exists
        try {
            $existingTerm = new Term((array) $termTable->getByAlias($id));
        } catch (\Exception $e) {
            $fm->setNamespace('error')->addMessage('Term '
                    . 'doesn\t exist in database.');
            return $view;
        }
        
        // Setup form
        $form = new TermForm();
        $form->setAttribute('action', '/edm-admin/term/update/id/' . $id);
        $form->setData($existingTerm->toArray());
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

        // Generate alias if empty
//        $alias = $view->form->getValue('alias');
//        if (empty($alias)) {
//            $term->alias = $this->getDbDataHelper()->getValidAlias($term->name);
//        }
//        
        // Set data
        $data = $view->form->getData();
//        $data['alias'] = $alias;
        // Put data into term object
        $term->exchangeArray($data);

        // Update term in db
        $rslt = $termTable->updateItem($term->alias, $term->toArray());

        // Send success message to user
        if ($rslt) {
            $fm->setNamespace('highlight')
                    ->addMessage('Term "' . $term->name . '" with alias "'
                            . $term->alias . '" updated successfully.');
            $view->form->setData(array(
                'name' => '',
                'alias' => '',
                'term_group_alias' => ''
            ));
        }
        // send failure message to user 
        else {
            $fm->setNamespace('error')
                    ->addMessage('Term "' . $term->name . '" with alias "'
                            . $term->alias . '" failed to be updated.');
        }

        // Return message to view
        return $view;
    }

    public function deleteAction() {
        // Set up prelims and populate $this -> view for 
        $view =
                $this->view =
                new ViewModel();
        $view->setTerminal(true);
        
        // init flash messenger
        $fm = $this->initFlashMessenger();

        // No deletion for demo
        $fm->setNamespace('error')->addMessage('Deleting terms is not ' .
                'allowed in this demo.');
        
        // init flash messenger
        $fm = $this->initFlashMessenger();
        
        // Bail
        return $view;

        // Id
        $id = $this->getParam('itemId');

        // Request
        $request = $this->getRequest();

        // If request is not a get or id is empty return
        if (empty($id) || !$request->isGet()) {
            $fm->setNamespace('error')->addMessage('No `id` was set for ' .
                    'deletion in the query string.');
            return $view;
        }

        // Get term table
        $termTable = $this->getTermModel();

        try {
            // Check if term already exists
            $term = new Term((array) $termTable->getByAlias($id));
        } catch (\Exception $e) {
            // If not send message and bail
            $fm->setNamespace('error')->addMessage('Term alias "' .
                    $id . '" doesn\t exist in database.');
            $view->error = $e;
            return $view;
        }

        // Delete term in db
        $rslt = $termTable->deleteItem($term->alias);

        // Send success message to user
        if ($rslt) {
            $fm->setNamespace('highlight')
                    ->addMessage('Term deleted successfully. '
                            . 'Term name: "' . $term->name . '"'
                            . 'Term alias: "' . $term->alias . '"');
        }
        // send failure message to user 
        else {
            $fm->setNamespace('error')
                    ->addMessage('Term "' . $term->name . '" with alias "'
                            . $term->alias . '" failed to be deleted.');
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

        $termTable = $this->getTermModel();
        $fm = $this->initFlashMessenger();

        // Check if term already exists
        $termExists = $termTable->getByAlias('dd');
        if (!empty($termExists)) {
            $fm->setNamespace('error')->addMessage('Term "' . $termExists->name
                    . '" already exists in database.');
            $view->exists = true;
        }
        $this->initFlashMessenger();
        $view->term = $termExists;
        $view->setTerminal(true);
        return $view;
    }

    /**
     * Gets our Term model
     * @return Edm\Db\Table\TermTable
     */
    public function getTermModel() {
        if (empty($this->termTable)) {
            $locator = $this->getServiceLocator();
            $this->termTable = $this->getServiceLocator()
                    ->get('Edm\Db\Table\TermTable');
            $this->termTable->setServiceLocator($locator);
        }
        return $this->termTable;
    }

}
