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
    Zend\Paginator\Adapter\DbSelect,
        
    Edm\Service\UserAware,
    Edm\Service\UserAwareTrait;

class UserController extends AbstractController 
implements TermTaxonomyServiceAware, UserAware {

    use TermTaxonomyServiceAwareTrait,
            UserAwareTrait;

    public function indexAction() {
        // View
        $view = 
                $this->view =
                new JsonModel();

        // Page number
        $pageNumber = $this->getAndSetParam('page', 1);

        // Items per page
        $itemCountPerPage = $this->getAndSetParam('itemsPerPage', 5);

        // Get User Service
        $userService = $this->getUserService();
        
        // Select 
        $select = $userService->getSelect();
        
        // Where part of query
        $where = array();

        // Status
        $status = $this->getAndSetParam('status', '*');
        if (!empty($status) && $status != '*') {
            $where['user.status'] = $status;
        }

        // Role
        $role = $this->getAndSetParam('role', '*');
        if (!empty($role) && $role != '*') {
            $where['user.role'] = $role;
        }

        // Access Group
        $accessGroup = $this->getAndSetParam('accessGroup', '*');
        if (!empty($accessGroup) && $accessGroup != '*') {
            $where['user.accessGroup'] = $accessGroup;
        }

        // Where
        if (count($where) > 0) {
            $select->where($where);
        }
        
        // Paginator
        $paginator = new Paginator(new DbSelect($select, $userService->getDb()));
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
        $userService = $this->getUserService();

        // Get data
        $data = (object) $view->form->getData();
        $userData = (object) $data->user;
        $contactData = (object) $data->contact;

        // Check if user exists by email
        $email = $contactData->email;
        $emailCheck = $userService->checkEmailExistsInDb($email);
        if (!empty($emailCheck)) {
            $fm->setNamespace('error')->addMessage('A user with email "'. $email 
                    .'" already exists in the database.  Click here to edit it.');
            return $view;
        }
        
        // Check if user exists by screen name
        $screenName = $userData->screenName;
        $screenNameCheck = $userService->checkScreenNameExistsInDb($screenName);
        if (!empty($screenNameCheck)) {
            $fm->setNamespace('error')->addMessage('A user with screenName "'. $screenName 
                    .'" already exists in the database.  Click here to edit it.');
            return $view;
        }
        
        // Create term taxonomy
        $rslt = $userService->createUser($view->form->getData());

        // Send success message to user
        if ($rslt) {
            $fm->setNamespace('highlight')
                    ->addMessage('User with email "' . $email . '" added successfully.');
        }
        // send failure message to user 
        else {
            var_dump($rslt);
            $fm->setNamespace('error')
                    ->addMessage('User with email "' . $email . '" failed to be added.');
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
        $userTable = $this->getUserTable();
        $userService = $this->getTermTaxService();

        // Setup form
        $form = new UserForm('user-form', array(
            'serviceLocator' => $this->getServiceLocator()
        ));
        $form->setAttribute('action', '/edm-admin/user/update/id/' . $id);
        $view->form = $form;

        // Check if term already exists
        try {
            $existingTermTax = new TermTaxonomy((array) $userService->getById($id));
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
        $user = (object) $data['term-taxonomy'];

        // Normalize description
        $desc = $user->description;
        $user->description = $desc ? $desc : '';

        // Normalize parent id
        $parent_id = !empty($user->parent_id) ? 
                $user->parent_id : 0;
        
        $data = array(
            'term_alias' => $term->alias,
            'taxonomy' => $user->taxonomy,
            'parent_id' => $parent_id,
            'description' => $user->description
        );
        
        // Update term in db
        $rslt = $userTable->updateItem($id, $data);
        
        // Send success message to user
        if (!empty($rslt)) {
            $fm->setNamespace('highlight')
                    ->addMessage('Term Taxonomy "' 
                            . $term->name . ' > ' . $user->taxonomy 
                            . '" updated successfully.');
        }
        // send failure message to user 
        else {
            $fm->setNamespace('error')
                    ->addMessage('Term Taxonomy "' 
                            . $term->name . ' > ' . $user->taxonomy 
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
        $userTable = $this->getUserTable();

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
        $rslt = $userTable->deleteItem($term->term_taxonomy_id);

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
