<?php

namespace Edm\Controller;

use Edm\Controller\AbstractController,
    Edm\Form\TermTaxonomyForm,
    Edm\Model\Term,
    Edm\Service\TermTaxonomyAware,
    Edm\TraitPartials\TermTaxonomyAwareTrait,
    Edm\Service\AbstractService,
    Zend\View\Model\ViewModel,
    Zend\View\Model\JsonModel,
    Zend\Paginator\Paginator,
    Zend\Paginator\Adapter\DbSelect,
    Zend\Db\Sql\Select;

class TermTaxonomyController extends AbstractController implements TermTaxonomyAware {

    use TermTaxonomyAwareTrait;

    protected $termTaxTable;
    protected $termTable;

    public function indexAction() {
        // View
        $view =
                $this->view =
                new JsonModel();

        // Model
        $model = $this->getTermTaxonomyModel();

        // Page number
        $pageNumber = $this->getAndSetParam('page', 1);

        // Items per page
        $itemCountPerPage = $this->getAndSetParam('itemsPerPage', 5);

        // Select 
//        $select = new Select();
        $select = $this->getTermTaxService()->getSelect();
//        $select->from($model->getTable());
//        var_dump($select->getSqlString()); exit();  
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

        // Let view be terminal in this action
        $view->setTerminal(true);
        $fm = $this->initFlashMessenger();

        // Setup form
        $form = new TermTaxonomyForm('term-taxonomy-form', array(
            'serviceLocator' => $this->getServiceLocator()
        ));
        $form->setAttribute('action', '/edm-admin/term-taxonomy/create');
        $view->form = $form;

        // If not post bail
        $request = $this->getRequest();
        if (!$request->isPost()) {
            return $view;
        }

        // Set form data
        $view->form->setData($request->getPost());

        // If form not valid return
        if (!$view->form->isValid()) {
            $fm->setNamespace('error')->addMessage('Form validation failed.' .
                    '  Please try again.');
//                    'data: ' . var_dump($form->getData()));
            return $view;
        }

        // Put data into model
        $termTaxTable = $this->getTermTaxonomyModel();
        $termTable = $this->getTermModel();
        $data = (object) $view->form->getData();
        // Check if term already exists
        $term = $termTable->getByAlias((string) $data->term['alias']);
        if (empty($term)) {
            $rslt = $termTable->createItem($data->term);
            if (empty($rslt)) {
                $fm->setNamespace('error')->addMessage('Failed to create term "'
                        . $term->name . '".');
                return $view;
            }
            $term = $termTable->getByAlias((string) $data->term['alias']);
        }

        // Put term in to db
        $desc = $data->{'term-taxonomy'}['description'];
        $data->{'term-taxonomy'}['description'] = $desc ? $desc : '';

        $rslt = $termTaxTable->createItem(array(
            'term_alias' => $term->alias,
            'taxonomy' => 'taxonomy',
            'description' => $desc
        ));

        // Send success message to user
        if ($rslt) {
            $fm->setNamespace('highlight')
                    ->addMessage('Term Taxonomy "' . $term->name . '" with alias "'
                            . $term->alias . '" added successfully.');
        }
        // send failure message to user 
        else {
            $fm->setNamespace('error')
                    ->addMessage('Term Taxonomy "' . $term->name . '" with alias "'
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
        $id = $this->getParam('id');

        // Put data into model
        $termTaxTable = $this->getTermTaxonomyModel();

        // Check if term already exists
        try {
            $existingTerm = new Term((array) $termTaxTable->getByAlias($id));
        } catch (\Exception $e) {
            $fm->setNamespace('error')->addMessage('Term '
                    . 'doesn\t exist in database.');
            return $view;
        }

        // Setup form
        $form = new TermForm();
        $form->setAttribute('action', '/edm-admin/term/update?id=' . $id);
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
        $rslt = $termTaxTable->updateItem($term->alias, $term->toArray());

        // Send success message to user
        if ($rslt) {
            $fm->setNamespace('highlight')
                    ->addMessage('Term "' . $term->name . '" with alias "'
                            . $term->alias . '" updated successfully.')
                    ->addMessage($term->alias);
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

        // Id
        $id = $this->getParam('id');

        // Request
        $request = $this->getRequest();

        // If request is not a get or id is empty return
        if (empty($id) || !$request->isGet()) {
            $fm->setNamespace('error')->addMessage('No `id` was set for ' .
                    'deletion in the query string.');
            return $view;
        }

        // Get term table
        $termTaxTable = $this->getTermTaxonomyModel();

        try {
            // Check if term already exists
            $term = new Term((array) $termTaxTable->getByAlias($id));
        } catch (\Exception $e) {
            // If not send message and bail
            $fm->setNamespace('error')->addMessage('Term alias "' .
                    $id . '" doesn\t exist in database.');
            $view->error = $e;
            return $view;
        }

        // Delete term in db
        $rslt = $termTaxTable->deleteItem($term->alias);

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

    public function getTermTaxonomyModel() {
        if (empty($this->termTaxTable)) {
            $locator = $this->getServiceLocator();
            $this->termTaxTable = $locator->get('Edm\Db\Table\TermTaxonomyTable');
            $this->termTaxTable->setServiceLocator($locator);
        }
        return $this->termTaxTable;
    }

    public function getTermModel() {
        if (empty($this->termTable)) {
            $locator = $this->getServiceLocator();
            $this->termTable = $locator->get('Edm\Db\Table\TermTable');
            $this->termTable->setServiceLocator($locator);
        }
        return $this->termTable;
    }

    public function fooAction() {
        $view = new JsonModel();
        $termTaxService = $this->getTermTaxService();
//        $rslt = $termTaxService->getByAlias('taxonomy');
//        $rslt = $termTaxService->getByTaxonomy('taxonomy');
        $rslt = $termTaxService->getByTaxonomy('taxonomy', array(
            'fetchMode' => AbstractService::FETCH_RESULT_SET_TO_ARRAY,
            'nestedResults' => true,
            'order' => 'term_name ASC',
//            'where' => array('term_alias' => 'testing-9')
        ));

        $view->result = $rslt;
        return $view;
    }

}

