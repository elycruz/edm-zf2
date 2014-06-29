<?php

namespace Edm\Controller;

use Edm\Controller\AbstractController,
    Edm\Form\PageForm,
    Edm\Model\Page,
    Edm\Service\AbstractService,
    Edm\Service\PageServiceAware,
    Edm\Service\PageServiceAwareTrait,
    Zend\View\Model\ViewModel,
    Zend\View\Model\JsonModel,
    Zend\Paginator\Paginator,
    Zend\Paginator\Adapter\DbSelect,
    Zend\Debug\Debug;

class PageController extends AbstractController implements PageServiceAware {

    use PageServiceAwareTrait;

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
        $sortBy = $this->getAndSetParam('sortBy', 'alias');

        // Term tax service
        $pageService = $this->getPageService();

        // Select 
        $select = $pageService->getSelect();

        // Where part of query
        $where = array();

        // Page Type
        $postType = $this->getAndSetParam('type', '*');
        if (!empty($postType) && $postType != '*') {
            $where['page.type'] = $postType;
        }

        // Access Group
        $accessGroup = $this->getAndSetParam('accessGroup', '*');
        if (!empty($accessGroup) && $accessGroup != '*') {
            $where['mixedTermRel.accessGroup'] = $accessGroup;
        }

        // Category
        $category = $this->getAndSetParam('term_taxonomy_id', '*');
        if (!empty($category) && $category != '*') {
            $where['mixedTermRel.term_taxonomy_id'] = $category;
        }

        // Parent Id
        $parent_id = $this->getAndSetParam('parent_id', null);
        if (!empty($parent_id)) {
            $where['page.parent_id'] = $parent_id;
        }

        // Where
        if (count($where) > 0) {
            $select->where($where);
        }

        // Order by
        $select->order($sortBy . ' ' . $sort);

        // Paginator $pageService->getDb()
        $paginator = new Paginator(
                new DbSelect($select, $pageService->getPageTable()->getAdapter()));
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
                
        // Set message namespace prefix
        $this->messageNamespacePrefix = 'create-';
        
        // Set up prelims and populate $this -> view for 
        // init flash messenger
        $view =
                $this->view =
                new ViewModel();

        // Let view be terminal in this action
        $view->setTerminal(true);
        $fm = $this->initFlashMessenger();

        // Setup form
        $form = new PageForm('page-form', array(
            'serviceLocator' => $this->getServiceLocator()));
        $form->setAttribute('action', '/edm-admin/page/create');
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
            $fm->setNamespace('create-error')->addMessage('Form validation failed.' .
                    '  Please try again.');
            return $view;
        }

        // Get Page service
        $pageService = $this->getPageService();

        // Get data
        $data = $form->getData();
        $mergedData = array_merge(
                $data['page-fieldset'], 
                $data['mixed-term-rel-fieldset'],
                $data['other-params-fieldset']);
        
        $pageData = new Page($mergedData);
        
        // If emtpy alias populate it
        if (empty($pageData->alias)) {
            $pageData->alias = $this->getDbDataHelper()->getValidAlias($pageData->label);
        }
        // Check if term taxonomy already exists
        $postCheck = $pageService->getByAlias($pageData->alias);
        if (!empty($postCheck)) {
            $fm->setNamespace('create-error')->addMessage('Page with alias "' . $pageData->alias . '" already ' .
                    'exists in the database.  Click here to edit it.');
            return $view;
        }

        // Create term taxonomy
        $rslt = $pageService->createPage($pageData);

        // Send success message to user
        if (is_numeric($rslt) && !empty($rslt) && $rslt instanceof \Exception === false) {
            $fm->setNamespace('create-highlight')
                    ->addMessage('Page "' . $pageData->label . '" added successfully.');
        }
        // send failure message to user 
        else {
            $fm->setNamespace('create-error')
                    ->addMessage('Page "' . $pageData->label . '" failed to be added.');
        }

        // Return message to view
        return $view;
    }

    public function updateAction() {
                        
        // Set message namespace prefix
        $this->messageNamespacePrefix = 'create-';
        
        // Set up prelims and populate $this -> view for 
        // init flash messenger
        $view = $this->view = new ViewModel();
        $view->setTerminal(true);
        $fm = $this->initFlashMessenger();

        // Id
        $id = $this->getParam('itemId');

        // Put data into model
        $pageService = $this->getPageService();

        // Setup form
        $form = new PageForm('page-form', array(
            'serviceLocator' => $this->getServiceLocator()
        ));
        $form->setAttribute('action', '/edm-admin/post/update/id/' . $id);
        $view->form = $form;

        // Check if term already exists if not bail
        $existingPage = $pageService->getById($id, AbstractService::FETCH_FIRST_AS_ARRAY_OBJ);
        if (empty($existingPage)) {
            $fm->setNamespace('create-error')->addMessage('Page with id "'
                    . $id . '" doesn\'t exist in database.');
            return $view;
        }
        
        $userParamsFieldset = null;
        // Resolve user params field
        if (!empty($existingPage->userParams)) {
            $userParamsFieldset = $pageService->unSerializeAndUnEscapeTuples(
                    $existingPage->userParams);
        }
        
        $mvcParamsFieldset = null;
        // Resolve mvc params field
        if (!empty($existingPage->mvcParams)) {
            $mvcParamsFieldset = $pageService->unSerializeAndUnEscapeTuples(
                    $existingPage->mvcParams);
        }

        // Set data
        $form->setData(array(
            'mixed-term-rel-fieldset' => array(
                'term_taxonomy_id' => $existingPage->getMixedTermRelProto()->term_taxonomy_id,
                'status' => $existingPage->status,
                'accessGroup' => $existingPage->accessGroup,
                'type' => $existingPage->type,
            ),
            'page-fieldset' => array(
                'label' => $existingPage->label,
                'alias' => $existingPage->alias,
                'type' => $existingPage->type,
                'uri' => $existingPage->uri,
                'parent_id' => $existingPage->parent_id,
                'acl_resource' => $existingPage->acl_resource,
                'acl_privilege' => $existingPage->acl_privilege,
                'mvc_action' => $existingPage->mvc_action,
                'mvc_controller' => $existingPage->mvc_controller,
                'mvc_module' => $existingPage->mvc_module,
                'mvc_route' => $existingPage->mvc_route,
                'mvc_params' => $existingPage->mvc_params,
                'description' => $existingPage->description
            ),
            'mvc-params-fieldset' => array(
                'userParams' => $mvcParamsFieldset
            ),
            'other-params-fieldset' => array(
                'userParams' => $userParamsFieldset
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
            $fm->setNamespace('create-error')->addMessage('Form validation failed.  ' .
                    'Please review values and try again.');
            return $view;
        }

        // Set data
        $data = $view->form->getData();

        // Allocoate updates
        $mergedData = array_merge(
                $data['page-fieldset'], 
                $data['mixed-term-rel-fieldset'], 
                array('page_id' => $id),
                $data['other-params-fieldset']);
        
        // Create new post model obj
        $pageData = new Page($mergedData);

        // Update term in db
        $rslt = $pageService->updatePage($pageData);

        // Send success message to user
        if ($rslt === true && $rslt instanceof \Exception === false) {
            $fm->setNamespace('create-highlight')
                    ->addMessage('Page "'
                            . $pageData->label . '" in category "' . $pageData->term_taxonomy_id
                            . '" updated successfully.');
        }
        // send failure message to user 
        else {
            $fm->setNamespace('create-error')
                    ->addMessage('Page "'
                            . $pageData->label . '" in category "' . $pageData->term_taxonomy_id
                            . '" failed to be updated.');
        }

        // Return message to view
        return $view;
    }

    public function deleteAction() {
                        
        // Set message namespace prefix
        $this->messageNamespacePrefix = 'delete-';
        
        // Set up prelims and populate $this -> view for 
        $view = $this->view = new JsonModel();
        $view->setTerminal(true);

        // init flash messenger
        $fm = $this->initFlashMessenger();

        // Id
        $id = $this->getParam('itemId');

        // If request is not a get or id is empty return
        if (empty($id)) {
            $fm->setNamespace('delete-error')->addMessage('No `id` was set for ' .
                    'deletion in the query string.');
            return $view;
        }

        // Get term table
        $pageService = $this->getPageService();

        // Check if term already exists
        $page = new Page($pageService->getById($id));
        if (empty($page)) {
            // If not send message and bail
            $fm->setNamespace('delete-error')->addMessage('Page Id "' .
                    $id . '" doesn\'t exist in database.');
            return $view;
        }

        // Delete term in db
        $rslt = $pageService->deletePage($page->page_id);

        // Send success message to user
        if ($rslt instanceof Exception === false) {
            $fm->setNamespace('delete-highlight')
                    ->addMessage('Page "'
                            . $page->label . '" deleted successfully.');
        }
        // send failure message to user 
        else {
            $fm->setNamespace('delete-error')
                    ->addMessage('Page "'
                            . $page->label . '" failed to be deleted.');
        }

        // Return message to view
        return $view;
    }

    public function setListOrderAction() {
                        
        // Set message namespace prefix
        $this->messageNamespacePrefix = 'index-';
        
        $view =
                $this->view =
                new JsonModel();

        // Let view be terminal in this action
        $view->setTerminal(true);

        // Get id of item to update
        $id = $this->getParam('itemId');
        $listOrder = $this->getParam('listOrder');

        // Get term tax
        $pageService = $this->getPageService();
        $post = new Page($pageService->getById($id));
        $fm = $this->initFlashMessenger();

        // Set error message if term tax not found
        if (empty($post)) {
            $fm->setNamespace('index-error')
                    ->addMessage('Page id "' . $id
                            . '" not found in database.  ' .
                            'List order change failed.');
            return $view;
        }
        
        // Set list order
        $post->listOrder = $listOrder;

        // Update listorder
        $rslt = $pageService->setListOrderForPage($post);

        // Send success message to user
        if (!empty($rslt)) {
            $fm->setNamespace('index-highlight')
                    ->addMessage('Page "'
                            . $post->term_name . ' > ' . $post->taxonomy
                            . '" updated successfully.');
        }
        // send failure message to user 
        else {
            $fm->setNamespace('index-error')
                    ->addMessage('Page "'
                            . $post->term_name . ' > ' . $post->taxonomy
                            . '" failed to be updated.');
        }

        // Return message to view
        return $view;
    }

    public function setStatusAction() {
        
    }

    public function setAccessGroupAction() {
        
    }

    public function setTypeAction() {
        
    }

}

