<?php

/**
 * @todo modify term taxonomy service to include term term taxonomy
 * @todo Unable to update term taxonomies name error is sent in flash message
 */
namespace Edm\Controller;

use Edm\Controller\AbstractController,
    Edm\Form\TermTaxonomyForm,
    Edm\Model\TermTaxonomy,
    Edm\Service\TermTaxonomyServiceAware,
    Edm\Service\TermTaxonomyServiceAwareTrait,
    Zend\View\Model\ViewModel,
    Zend\View\Model\JsonModel,
    Zend\Paginator\Paginator,
    Zend\Paginator\Adapter\DbSelect;

class TermTaxonomyController extends AbstractController 
implements TermTaxonomyServiceAware {

    use TermTaxonomyServiceAwareTrait;

    public function indexAction() {
        // View
        $view =
                $this->view =
                new JsonModel();

        // Page number
        $pageNumber = $this->getAndSetParam('page', 1);

        // Items per page
        $itemCountPerPage = $this->getAndSetParam('itemsPerPage', 5);
        
        // Sort
        $sort = $this->getAndSetParam('sort', 'ASC');
        
        // Sort by
        $sortBy = $this->getAndSetParam('sortBy', 'term_alias');
        
        // Term tax service
        $termTaxService = $this->getTermTaxService();

        // Select 
        $select = $termTaxService->getSelect();

        // Where part of query
        $where = array();

        // Taxonomy
        $taxonomy = $this->getAndSetParam('taxonomy', '*');
        if (!empty($taxonomy) && $taxonomy != '*') {
            $where['taxonomy'] = $taxonomy;
        }

        // Access Group
        $accessGroup = $this->getAndSetParam('accessGroup', '*');
        if (!empty($accessGroup) && $accessGroup != '*') {
            $where['accessGroup'] = $accessGroup;
        }

        // Parent Id
        $parent_id = $this->getAndSetParam('parent_id', null);
        if (!empty($parent_id)) {
            $where['parent_id'] = $parent_id;
        }

        // Where
        if (count($where) > 0) {
            $select->where($where);
        }
        
        // Order by
        $select->order($sortBy . ' ' . $sort);
        
        // Paginator $termTaxService->getDb()
        $paginator = new Paginator(
                new DbSelect($select, 
                    $termTaxService->getTermTaxonomyTable()->getAdapter()));
        $paginator->setItemCountPerPage($itemCountPerPage)
                ->setCurrentPageNumber($pageNumber);

        // Set actual page (happens to fix exceeded page number set by user)
        $view->itemsTotal = $paginator->getTotalItemCount();

        // Send results
        $view->results = $this->getDbDataHelper()->reverseEscapeTuples(
                $paginator->getCurrentItems());
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
        $termTaxService = $this->getTermTaxService();

        // Get data
        $data = $view->form->getData();
        $termTaxData = (object) $data['term-taxonomy'];
        $termData = (object) $data['term'];

        // Check if term taxonomy already exists
        $termTaxCheck = $termTaxService->getByAlias(
                $termData->alias, $termTaxData->taxonomy);
        if (!empty($termTaxCheck)) {
            $fm->setNamespace('error')->addMessage('Term Taxonomy "" already ' .
                    'exists in the database.  Click here to edit it.');
            return $view;
        }
        
        // Create term taxonomy
        $rslt = $termTaxService->createItem($data);

        // Send success message to user
        if (is_numeric($rslt) && !empty($rslt)) {
            $fm->setNamespace('highlight')
                    ->addMessage('Term Taxonomy "' . $termData->name . ' -> '
                            . $termTaxData->taxonomy . '" added successfully.');
        }
        // send failure message to user 
        else {
            $fm->setNamespace('error')
                    ->addMessage('Term Taxonomy "' . $termData->name . ' -> '
                            . $termTaxData->taxonomy . '" failed to be added.');
        }
        
        // Make form blank
//        $view->form->setData(array(
//            'term-taxonomy' => array(
//                'taxonomy' => '',
//                'parent_id' => '',
//                'description' => ''
//            ),
//            'term' => array(
//                'name' => '',
//                'alias' => '',
//                'term_group_alias' => ''
//            )
//        ));

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
        $termTaxService = $this->getTermTaxService();

        // Setup form
        $form = new TermTaxonomyForm('term-taxonomy-form', array(
            'serviceLocator' => $this->getServiceLocator()
        ));
        $form->setAttribute('action', '/edm-admin/term-taxonomy/update/id/' . $id);
        $view->form = $form;

        // Check if term already exists if not bail
        $existingTermTax = new TermTaxonomy($termTaxService->getById($id));
        if (empty($existingTermTax)) {
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
        $termTax = (object) $data['term-taxonomy'];
        $term = (object) $data['term'];
        
        // Update term in db
        $rslt = $termTaxService->updateItem($id, $data);
        
        // Send success message to user
        if (is_numeric($rslt)) {
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

//        // Make form blank
//        $view->form->setData(array(
//            'term-taxonomy' => array(
//                'taxonomy' => '',
//                'parent_id' => '',
//                'description' => ''
//            ),
//            'term' => array(
//                'name' => '',
//                'alias' => '',
//                'term_group_alias' => ''
//            )
//        ));

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
        $termTaxService = $this->getTermTaxService();

        // Check if term already exists
        $termTaxRslt = $termTaxService->getById($id);
        if (empty($termTaxRslt)) {
            // If not send message and bail
            $fm->setNamespace('error')->addMessage('Term Taxonomy Id "' .
                    $id . '" doesn\'t exist in database.');
            return $view;
        }

        // Term Taxonomy object
        $termTax = new TermTaxonomy($termTaxRslt);
        
        // Delete term in db
        $rslt = $termTaxService->deleteItem($termTax->term_taxonomy_id);

        // Send success message to user
        if ($rslt) {
            $fm->setNamespace('highlight')
                    ->addMessage('Term Taxonomy "' 
                            . $termTax->term_name . ' > ' . $termTax->term_alias 
                            . '" deleted successfully.');
        }
        // send failure message to user 
        else {
            $fm->setNamespace('error')
                    ->addMessage('Term Taxonomy "' 
                            . $termTax->term_name . ' > ' . $termTax->term_alias 
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
    
}

