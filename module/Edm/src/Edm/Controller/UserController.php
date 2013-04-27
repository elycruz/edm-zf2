<?php

/**
 * @todo modify term taxonomy service to include term term taxonomy
 * @todo Unable to update term taxonomies name error is sent in flash message
 */
namespace Edm\Controller;

use Edm\Controller\AbstractController,
    Edm\Form\UserForm,
    Edm\Service\TermTaxonomyServiceAware,
    Edm\Service\TermTaxonomyServiceAwareTrait,
    Zend\View\Model\ViewModel,
    Zend\View\Model\JsonModel,
    Zend\Paginator\Paginator,
    Zend\Paginator\Adapter\DbSelect;

class UserController extends AbstractController implements TermTaxonomyServiceAware {

    use TermTaxonomyServiceAwareTrait;

    protected $termTaxTable;
    protected $termTable;

    public function indexAction() {
        // View
        $view = 
                $this->view =
                new JsonModel();

        // Model
        $model = $this->getUserTable();

        // Page number
        $pageNumber = $this->getAndSetParam('page', 1);

        // Items per page
        $itemCountPerPage = $this->getAndSetParam('itemsPerPage', 5);

        // Select 
        $select = $this->getTermTaxService()->getSelect();

        // Where part of query
        $where = null;

        // Taxonomy
        $taxonomy = $this->getAndSetParam('taxonomy', '*');
        if (!empty($taxonomy) && $taxonomy != '*') {
            $where = 'taxonomy="' . $taxonomy . '"';
        }

        // Parent Id
        $parent_id = $this->getAndSetParam('parent_id', null);
        if (!empty($parent_id)) {
            $where .= isset($parent_id) ? ' AND ' : '';
            $where .= 'parent_id="' . $parent_id . '"';
        }

        // Where
        if (isset($where)) {
            $select->where($where);
        }
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
        $form = new UserForm('user-form', array(
            'serviceLocator' => $this->getServiceLocator()
        ));
        $form->setAttribute('action', '/edm-admin/user/create');
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
            return $view;
        }

        // Get User Service

        // Get data
        $data = (object) $view->form->getData();
        $userData = (object) $data->user;
        $contactData = (object) $data->contact;

        // Check if term taxonomy already exists
        $termTaxCheck = $termTaxService->getByAlias(
                $contactData->alias, $userData->taxonomy);
        if (!empty($termTaxCheck)) {
            $fm->setNamespace('error')->addMessage('Term Taxonomy "" already ' .
                    'exists in the database.  Click here to edit it.');
            return $view;
        }

        // Database data helper
        $dbDataHelper = $this->getDbDataHelper();
        
        // Get Term from data 
        $term = $this->getTermFromData($dbDataHelper
                ->escapeTuple($data->contact));
        
        // If failed to fetch term
        if (empty($term)) {
            $fm->setNamespace('error')->addMessage('Failed to create term '. 
                    'needed for Term Taxonomy creation.');
            return $view;
        }

        // Normalize description
        $desc = $userData->description;
        $userData->description = $desc ? $desc : '';

        // Normalize parent id
        $parent_id = !empty($userData->parent_id) ? 
                $userData->parent_id : 0;
        
        // Create term taxonomy
        $rslt = $termTaxTable->createItem(array(
            'term_alias' => $term->alias,
            'taxonomy' => $userData->taxonomy,
            'parent_id' => $parent_id,
            'description' => $desc
        ));

        // Send success message to user
        if ($rslt) {
            $fm->setNamespace('highlight')
                    ->addMessage('Term Taxonomy "' . $term->name . ' -> '
                            . $userData->taxonomy . '" added successfully.');
        }
        // send failure message to user 
        else {
            $fm->setNamespace('error')
                    ->addMessage('Term Taxonomy "' . $term->name . ' -> '
                            . $userData->taxonomy . '" failed to be added.');
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
        $termTaxTable = $this->getUserTable();
        $termTaxService = $this->getTermTaxService();

        // Setup form
        $form = new UserForm('user-form', array(
            'serviceLocator' => $this->getServiceLocator()
        ));
        $form->setAttribute('action', '/edm-admin/user/update/id/' . $id);
        $view->form = $form;

        // Check if term already exists
        try {
            $existingTermTax = new TermTaxonomy((array) $termTaxService->getById($id));
        } catch (\Exception $e) {
            $fm->setNamespace('error')->addMessage('Term Taxonomy with id "'
                    . $id . '" doesn\'t exist in database.');
            return $view;
        }

        // Set data
        $form->setData(array(
            'term-taxonomy' => array(
                'taxonomy' => $existingTermTax->taxonomy,
                'parent_id' => $existingTermTax->parent_id,
                'description' => $existingTermTax->description
            ),
            'term' => array(
                'name' => $existingTermTax->term_name,
                'alias' => $existingTermTax->term_alias,
                'term_group_alias' => $existingTermTax->term_group_alias
            )
        ));

        // If not post bail
        $request = $this->getRequest();
        if (!$request->isPost()) {
            return $view;
        }

        // Processing request
        $view->form->setData($request->getPost());

        // If form not valid return
        if (!$view->form->isValid()) {
            $fm->setNamespace('error')->addMessage('Form validation failed.  ' .
                    'Please review values and try again.');
            return $view;
        }

        // Set data
        $data = $view->form->getData();
        
        // Allocoate updates
        $term = $this->getTermFromData($data['term']); //->exchangeArray($data);
        $termTax = (object) $data['term-taxonomy'];

        // Normalize description
        $desc = $termTax->description;
        $termTax->description = $desc ? $desc : '';

        // Normalize parent id
        $parent_id = !empty($termTax->parent_id) ? 
                $termTax->parent_id : 0;
        
        $data = array(
            'term_alias' => $term->alias,
            'taxonomy' => $termTax->taxonomy,
            'parent_id' => $parent_id,
            'description' => $termTax->description
        );
        
        // Update term in db
        $rslt = $termTaxTable->updateItem($id, $data);
        
        // Send success message to user
        if (!empty($rslt)) {
            $fm->setNamespace('highlight')
                    ->addMessage('Term Taxonomy "' 
                            . $term->name . ' > ' . $termTax->taxonomy 
                            . '" updated successfully.');
        }
        // send failure message to user 
        else {
            $fm->setNamespace('error')
                    ->addMessage('Term Taxonomy "' 
                            . $term->name . ' > ' . $termTax->taxonomy 
                            . '" failed to be updated.');
        }

        // Return message to view
        return $view;
    }

    public function deleteAction() {
        // Set up prelims and populate $this -> view for 
        $view =
                $this->view =
                new JsonModel();
        $view->setTerminal(true);

        // init flash messenger
        $fm = $this->initFlashMessenger();

        // Id
        $id = $this->getParam('itemId');

        // If request is not a get or id is empty return
        if (empty($id)) {
            $fm->setNamespace('error')->addMessage('No `id` was set for ' .
                    'deletion in the query string.');
            return $view;
        }

        // Get term table
        $termTaxTable = $this->getUserTable();

        try {
            // Check if term already exists
            $term = new TermTaxonomy($this->getTermTaxService()->getById($id));
        } 
        catch (\Exception $e) {
            // If not send message and bail
            $fm->setNamespace('error')->addMessage('Term Taxonomy Id "' .
                    $id . '" doesn\'t exist in database.');
            $view->error = $e;
            return $view;
        }

        // Delete term in db
        $rslt = $termTaxTable->deleteItem($term->term_taxonomy_id);

        // Send success message to user
        if ($rslt) {
            $fm->setNamespace('highlight')
                    ->addMessage('Term Taxonomy "' 
                            . $term->term_name . ' > ' . $term->term_alias 
                            . '" deleted successfully.');
        }
        // send failure message to user 
        else {
            $fm->setNamespace('error')
                    ->addMessage('Term Taxonomy "' 
                            . $term->term_name . ' > ' . $term->term_alias 
                            . '" failed to be deleted.');
        }

        // Return message to view
        return $view;
    }
    
    public function loginAction () {
        
    }
    
    public function logoutAction () {
        
    }
    
    public function sendActivationAction () {
        
    }
    
    public function activtionAction () {
        
    }
    
    public function sendActivationEmail () {
        
    }
    
    
}
