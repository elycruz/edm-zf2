<?php

/**
 * @todo modify term taxonomy service to include term term taxonomy
 * @todo Unable to update term taxonomies name error is sent in flash message
 */

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
        $postService = $this->getPageService();

        // Select 
        $select = $postService->getSelect();

        // Where part of query
        $where = array();

        // Page Type
        $postType = $this->getAndSetParam('type', '*');
        if (!empty($postType) && $postType != '*') {
            $where['type'] = $postType;
        }

        // Access Group
        $accessGroup = $this->getAndSetParam('accessGroup', '*');
        if (!empty($accessGroup) && $accessGroup != '*') {
            $where['accessGroup'] = $accessGroup;
        }

        // Category
        $category = $this->getAndSetParam('term_taxonomy_id', '*');
        if (!empty($category) && $category != '*') {
            $where['term_taxonomy_id'] = $category;
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

        // Paginator $postService->getDb()
        $paginator = new Paginator(
                new DbSelect($select, $postService->getPageTable()->getAdapter()));
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
        $view->form->setData($request->getPage());

        // If form not valid return
        if (!$view->form->isValid()) {
            $fm->setNamespace('error')->addMessage('Form validation failed.' .
                    '  Please try again.');
            // Debug::dump($form->getMessages());
            return $view;
        }

        // Get Page service
        $postService = $this->getPageService();

        // Get data
        $data = $form->getData();
        $mergedData = array_merge(
                $data['page-fieldset'], 
                $data['page-term-rel-fieldset'],
                $data['user-params-fieldset']);
        
        $postData = new Page($mergedData);
        
        // If emtpy alias populate it
        if (empty($postData->alias)) {
            $postData->alias = $this->getDbDataHelper()->getValidAlias($postData->title);
        }
        // Check if term taxonomy already exists
        $postCheck = $postService->getByAlias($postData->alias);
        if (!empty($postCheck)) {
            $fm->setNamespace('error')->addMessage('Page with alias "' . $postData->alias . '" already ' .
                    'exists in the database.  Click here to edit it.');
            return $view;
        }

        // Create term taxonomy
        $rslt = $postService->createPage($postData);

        // Send success message to user
        if (is_numeric($rslt) && !empty($rslt) && $rslt instanceof \Exception === false) {
            $fm->setNamespace('highlight')
                    ->addMessage('Page "' . $postData->title . '" added successfully.');
        }
        // send failure message to user 
        else {
            $fm->setNamespace('error')
                    ->addMessage('Page "' . $postData->title . '" failed to be added.');
        }

        // Return message to view
        return $view;
    }

    public function updateAction() {
        // Set up prelims and populate $this -> view for 
        // init flash messenger
        $view = $this->view = new ViewModel();
        $view->setTerminal(true);
        $fm = $this->initFlashMessenger();

        // Id
        $id = $this->getParam('itemId');

        // Put data into model
        $postService = $this->getPageService();

        // Setup form
        $form = new PageForm('page-form', array(
            'serviceLocator' => $this->getServiceLocator()
        ));
        $form->setAttribute('action', '/edm-admin/post/update/id/' . $id);
        $view->form = $form;

        // Check if term already exists if not bail
        $existingPage = $postService->getById($id, AbstractService::FETCH_FIRST_AS_ARRAY_OBJ);
        if (empty($existingPage)) {
            $fm->setNamespace('error')->addMessage('Page with id "'
                    . $id . '" doesn\'t exist in database.');
            return $view;
        }
        
        $userParamsFieldset = null;
        // Resolve user params field
        if (!empty($existingPage->userParams)) {
            $userParamsFieldset = $postService->unSerializeAndUnEscapeTuples(
                    $existingPage->userParams);
        }

        // Set data
        $form->setData(array(
            'page-term-rel-fieldset' => array(
                'term_taxonomy_id' => $existingPage->getPageTermRelProto()->term_taxonomy_id,
            ),
            'page-fieldset' => array(
                'title' => $existingPage->title,
                'alias' => $existingPage->alias,
                'content' => $existingPage->content,
                'excerpt' => $existingPage->excerpt,
                'commenting' => $existingPage->commenting,
                'status' => $existingPage->status,
                'accessGroup' => $existingPage->accessGroup,
                'type' => $existingPage->type,
            ),
            'user-params-fieldset' => array(
                'userParams' => $userParamsFieldset
                )
        ));
        
        // If not post bail
        $request = $this->getRequest();
        if (!$request->isPost()) {
            return $view;
        }

        // Processing request
        $view->form->setData($request->getPage());

        // If form not valid return
        if (!$view->form->isValid()) {
            $fm->setNamespace('error')->addMessage('Form validation failed.  ' .
                    'Please review values and try again.');
            return $view;
        }

        // Set data
        $data = $view->form->getData();

        // Allocoate updates
        $mergedData = array_merge(
                $data['page-fieldset'], 
                $data['page-term-rel-fieldset'], 
                array('page_id' => $id),
                $data['user-params-fieldset']);
        
        // Create new post model obj
        $postData = new Page($mergedData);

        // Update term in db
        $rslt = $postService->updatePage($postData);

        // Send success message to user
        if ($rslt === true && $rslt instanceof \Exception === false) {
            $fm->setNamespace('highlight')
                    ->addMessage('Page "'
                            . $postData->title . '" in category "' . $postData->term_taxonomy_id
                            . '" updated successfully.');
        }
        // send failure message to user 
        else {
            $fm->setNamespace('error')
                    ->addMessage('Page "'
                            . $postData->title . '" in category "' . $postData->term_taxonomy_id
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
        $postService = $this->getPageService();

        // Check if term already exists
        $postRslt = $postService->getById($id);
        if (empty($postRslt)) {
            // If not send message and bail
            $fm->setNamespace('error')->addMessage('Page Id "' .
                    $id . '" doesn\'t exist in database.');
            return $view;
        }

        // Page object
        $post = new Page($postRslt);
        $postTermRel = $post->getPageTermRelProto();

        // Delete term in db
        $rslt = $postService->deletePage($post->post_id);

        // Send success message to user
        if ($rslt) {
            $fm->setNamespace('highlight')
                    ->addMessage('Page "'
                            . $post->title . '" in category "' . $postTermRel->term_taxonomy_id
                            . '" deleted successfully.');
        }
        // send failure message to user 
        else {
            $fm->setNamespace('error')
                    ->addMessage('Page "'
                            . $post->title . '" in category "' . $postTermRel->term_taxonomy_id
                            . '" failed to be deleted.');
        }

        // Return message to view
        return $view;
    }

    public function setListOrderAction() {
        $view =
                $this->view =
                new JsonModel();

        // Let view be terminal in this action
        $view->setTerminal(true);

        // Get id of item to update
        $id = $this->getParam('itemId');
        $listOrder = $this->getParam('listOrder');

        // Get term tax
        $postService = $this->getPageService();
        $post = new Page($postService->getById($id));
        $fm = $this->initFlashMessenger();

        // Set error message if term tax not found
        if (empty($post)) {
            $fm->setNamespace('error')
                    ->addMessage('Page id "' . $id
                            . '" not found in database.  ' .
                            'List order change failed.');
            return $view;
        }
        
        // Set list order
        $post->listOrder = $listOrder;

        // Update listorder
        $rslt = $postService->setListOrderForPage($post);

        // Send success message to user
        if (!empty($rslt)) {
            $fm->setNamespace('highlight')
                    ->addMessage('Page "'
                            . $post->term_name . ' > ' . $post->taxonomy
                            . '" updated successfully.');
        }
        // send failure message to user 
        else {
            $fm->setNamespace('error')
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

