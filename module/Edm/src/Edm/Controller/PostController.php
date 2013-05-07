<?php

/**
 * @todo modify term taxonomy service to include term term taxonomy
 * @todo Unable to update term taxonomies name error is sent in flash message
 */
namespace Edm\Controller;

use Edm\Controller\AbstractController,
    Edm\Form\PostForm,
    Edm\Model\Post,
    Edm\Service\PostServiceAware,
    Edm\Service\PostServiceAwareTrait,
    Zend\View\Model\ViewModel,
    Zend\View\Model\JsonModel,
    Zend\Paginator\Paginator,
    Zend\Paginator\Adapter\DbSelect;

class PostController extends AbstractController 
implements PostServiceAware {

    use PostServiceAwareTrait;

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
        $postService = $this->getPostService();

        // Select 
        $select = $postService->getSelect();

        // Where part of query
        $where = array();

        // Post Type
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
        $category = $this->getAndSetParam('category', '*');
        if (!empty($category) && $category != '*') {
            $where['category'] = $category;
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
                new DbSelect($select, 
                    $postService->getPostTable()->getAdapter()));
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
        $form = new PostForm('post-form', array(
            'serviceLocator' => $this->getServiceLocator() ));
        $form->setAttribute('action', '/edm-admin/post/create');
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

        // Get Post service
        $postService = $this->getPostService();

        // Get data
        $postData = $form->get('post-model');
        $postTermRelData = $form->get('post-term-rel-model');
        
        // Set Post's Term Rel proto
        $postData->setPostTermRelProto($postTermRelData);
        
        // Check if term taxonomy already exists
        $postCheck = $postService->getByAlias($postData->alias);
        if (!empty($postCheck)) {
            $fm->setNamespace('error')->addMessage('Post "'. $postData->title .'" already ' .
                    'exists in the database.  Click here to edit it.');
            return $view;
        }
        
        // Create term taxonomy
        $rslt = $postService->createPost($postData);

        // Send success message to user
        if (is_numeric($rslt) && !empty($rslt) && $rslt instanceof \Exception === false) {
            $fm->setNamespace('highlight')
                    ->addMessage('Post "' . $postData->title . '" added successfully.');
        }
        // send failure message to user 
        else {
            $fm->setNamespace('error')
                    ->addMessage('Post "' . $postData->title . '" failed to be added.');
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
        $postService = $this->getPostService();

        // Setup form
        $form = new PostForm('post-form', array(
            'serviceLocator' => $this->getServiceLocator()
        ));
        $form->setAttribute('action', '/edm-admin/post/update/id/' . $id);
        $view->form = $form;

        // Check if term already exists if not bail
        $existingTermTax = new Post($postService->getById($id));
        if (empty($existingTermTax)) {
            $fm->setNamespace('error')->addMessage('Post with id "'
                    . $id . '" doesn\'t exist in database.');
            return $view;
        }

        // Set data
        $form->setData(array(
            'post-term-rel' => array(
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
        $post = (object) $data['post-term-rel'];
        $term = (object) $data['term'];
        
        // Update term in db
        $rslt = $postService->updateItem($id, $data);
        
        // Send success message to user
        if (is_numeric($rslt)) {
            $fm->setNamespace('highlight')
                    ->addMessage('Post "' 
                            . $term->name . ' > ' . $post->taxonomy 
                            . '" updated successfully.');
        }
        // send failure message to user 
        else {
            $fm->setNamespace('error')
                    ->addMessage('Post "' 
                            . $term->name . ' > ' . $post->taxonomy 
                            . '" failed to be updated.');
        }

//        // Make form blank
//        $view->form->setData(array(
//            'post-term-rel' => array(
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
        $postService = $this->getPostService();

        // Check if term already exists
        $postRslt = $postService->getById($id);
        if (empty($postRslt)) {
            // If not send message and bail
            $fm->setNamespace('error')->addMessage('Post Id "' .
                    $id . '" doesn\'t exist in database.');
            return $view;
        }

        // Post object
        $post = new Post($postRslt);
        
        // Delete term in db
        $rslt = $postService->deleteItem($post->term_taxonomy_id);

        // Send success message to user
        if ($rslt) {
            $fm->setNamespace('highlight')
                    ->addMessage('Post "' 
                            . $post->term_name . ' > ' . $post->term_alias 
                            . '" deleted successfully.');
        }
        // send failure message to user 
        else {
            $fm->setNamespace('error')
                    ->addMessage('Post "' 
                            . $post->term_name . ' > ' . $post->term_alias 
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
        $postService = $this->getPostService();
        $post = new Post ($postService->getById($id));
        $fm = $this->initFlashMessenger();
        
        // Set error message if term tax not found
        if (empty($post)) {
            $fm->setNamespace('error')
                    ->addMessage('Post id "' . $id
                            . '" not found in database.  '.
                            'List order change failed.');
            return $view;
        }

        // Update listorder
        $rslt = $postService->setListOrderForId($id, $listOrder);
        
        // Send success message to user
        if (!empty($rslt)) {
            $fm->setNamespace('highlight')
                    ->addMessage('Post "' 
                            . $post->term_name . ' > ' . $post->taxonomy 
                            . '" updated successfully.');
        }
        // send failure message to user 
        else {
            $fm->setNamespace('error')
                    ->addMessage('Post "' 
                            . $post->term_name . ' > ' . $post->taxonomy 
                            . '" failed to be updated.');
        }

        // Return message to view
        return $view;
    }
    
    public function setStatusAction () {
    }
    
    public function setAccessGroupAction () {
    }
}

