<?php

/**
 * @todo modify term taxonomy service to include term term taxonomy
 * @todo Unable to update term taxonomies name error is sent in flash message
 */
namespace Edm\Controller;

use Edm\Controller\AbstractController,
    Edm\Form\TermTaxonomyForm,
    Edm\Model\TermTaxonomy,
    Edm\Service\TermTaxonomyAware,
    Edm\TraitPartials\TermTaxonomyAwareTrait,
    Edm\Service\AbstractService,
    Zend\View\Model\ViewModel,
    Zend\View\Model\JsonModel,
    Zend\Paginator\Paginator,
    Zend\Paginator\Adapter\DbSelect;

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
            return $view;
        }

        // Get Term Taxonomy service
        $termTaxTable = $this->getUserTable();
        $termTaxService = $this->getTermTaxService();

        // Get data
        $data = (object) $view->form->getData();
        $termTaxData = (object) $data->{'term-taxonomy'};
        $termData = (object) $data->term;

        // Check if term taxonomy already exists
        $termTaxCheck = $termTaxService->getByAlias(
                $termData->alias, $termTaxData->taxonomy);
        if (!empty($termTaxCheck)) {
            $fm->setNamespace('error')->addMessage('Term Taxonomy "" already ' .
                    'exists in the database.  Click here to edit it.');
            return $view;
        }

        // Database data helper
        $dbDataHelper = $this->getDbDataHelper();
        
        // Get Term from data 
        $term = $this->getTermFromData($dbDataHelper
                ->escapeTuple($data->term));
        
        // If failed to fetch term
        if (empty($term)) {
            $fm->setNamespace('error')->addMessage('Failed to create term '. 
                    'needed for Term Taxonomy creation.');
            return $view;
        }

        // Normalize description
        $desc = $termTaxData->description;
        $termTaxData->description = $desc ? $desc : '';

        // Normalize parent id
        $parent_id = !empty($termTaxData->parent_id) ? 
                $termTaxData->parent_id : 0;
        
        // Create term taxonomy
        $rslt = $termTaxTable->createItem(array(
            'term_alias' => $term->alias,
            'taxonomy' => $termTaxData->taxonomy,
            'parent_id' => $parent_id,
            'description' => $desc
        ));

        // Send success message to user
        if ($rslt) {
            $fm->setNamespace('highlight')
                    ->addMessage('Term Taxonomy "' . $term->name . ' -> '
                            . $termTaxData->taxonomy . '" added successfully.');
        }
        // send failure message to user 
        else {
            $fm->setNamespace('error')
                    ->addMessage('Term Taxonomy "' . $term->name . ' -> '
                            . $termTaxData->taxonomy . '" failed to be added.');
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
        $form = new TermTaxonomyForm('term-taxonomy-form', array(
            'serviceLocator' => $this->getServiceLocator()
        ));
        $form->setAttribute('action', '/edm-admin/term-taxonomy/update/id/' . $id);
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

    public function setListOrderAction () {
        $view =
            $this->view =
                new JsonModel();

        // Let view be terminal in this action
        $view->setTerminal(true);
        
        // Get id of item to update
        $id = $this->getParam('itemId');
        $listOrder = $this->getParam('listOrder');
        
        // Get term tax
        $termTaxService = $this->getTermTaxService();
        $termTax = new TermTaxonomy ($termTaxService->getById($id));
        $fm = $this->initFlashMessenger();
        
        // Set error message if term tax not found
        if (empty($termTax)) {
            $fm->setNamespace('error')
                    ->addMessage('Term Taxonomy id "' . $id
                            . '" not found in database.  '.
                            'List order change failed.');
            return $view;
        }

        // Update listorder
        $rslt = $termTaxService->setListOrderForId($id, $listOrder);
        
        // Send success message to user
        if (!empty($rslt)) {
            $fm->setNamespace('highlight')
                    ->addMessage('Term Taxonomy "' 
                            . $termTax->term_name . ' > ' . $termTax->taxonomy 
                            . '" updated successfully.');
        }
        // send failure message to user 
        else {
            $fm->setNamespace('error')
                    ->addMessage('Term Taxonomy "' 
                            . $termTax->term_name . ' > ' . $termTax->taxonomy 
                            . '" failed to be updated.');
        }

        // Return message to view
        return $view;
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

    /**
     * Get term from data and create it if it doesn't exists
     * @param mixed [array, object] $termData gets cast as (object) 
     * @return mixed Edm\Model\Term | array
     */
    public function getTermFromData($termData) {
        // Convert from array if necessary
        if (is_array($termData)) {
            $termData = (object) $termData;
        }
        
        // Get term table
        $termTable = $this->getTermModel();
        
        // Check if term already exists
        $term = $termTable->getByAlias((string) $termData->alias);
        
        // Create term if empty
        if (empty($term)) {
            $rslt = $termTable->createItem((array) $termData);
            if (empty($rslt)) {
                return false;
            }
            $term = $termTable->getByAlias((string) $termData->alias);
        }
        // Update term
        else if (!empty($term->name) && !empty($term->term_group_alias)) {
            $termTable->updateItem($term->alias, $term->toArray());
        }
        return $term;
    }
}

