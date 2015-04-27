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
    Edm\Service\AbstractService,
    Edm\Service\UserAware,
    Edm\Service\UserAwareTrait,
    Edm\Model\User,
    Zend\View\Model\ViewModel,
    Zend\View\Model\JsonModel,
    Zend\Paginator\Paginator,
    Zend\Paginator\Adapter\DbSelect;

class UserController extends AbstractController implements TermTaxonomyServiceAware, UserAware {

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
        $data = $view->form->getData();
        $userData = new User(array_merge($data['user'], $data['contact']));
        $contactData = $userData->getContactProto();

        // Check if user exists by email
        $email = $contactData->email;
        $emailCheck = $userService->checkEmailExistsInDb($email);
        if (!empty($emailCheck)) {
            $fm->setNamespace('error')->addMessage('A user with email "' . $email
                    . '" already exists in the database.  Click here to edit it.');
            return $view;
        }

        // Check if user exists by screen name
        $screenName = $userData->screenName;
        $screenNameCheck = $userService->checkScreenNameExistsInDb($screenName);
        if (!empty($screenNameCheck)) {
            $fm->setNamespace('error')->addMessage('A user with screenName "' . $screenName
                    . '" already exists in the database.  Click here to edit it.');
            return $view;
        }

        // Create term taxonomy
        $rslt = $userService->createUser($userData);

        // Send success message to user
        if (!empty($rslt) && $rslt instanceof \Exception === false) {
            $fm->setNamespace('highlight')
                    ->addMessage('User with email "' . $email . '" added successfully.');
        }
        // send failure message to user 
        else {
//            var_dump($rslt->getMessage()); exit();
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

        // Get service
        $userService = $this->getUserService();

        // Setup form
        $form = new UserForm('user-form', array(
            'serviceLocator' => $this->getServiceLocator()));
        $form->setAttribute('action', '/edm-admin/user/update/id/' . $id);
        $view->form = $form;

        // Check if user already exists
        $userCheck = $userService->getById($id, AbstractService::FETCH_FIRST_AS_ARRAY_OBJ);
        if (empty($userCheck)) {
            $fm->setNamespace('error')->addMessage('User with id "'
                    . $id . '" doesn\'t exist in database.');
            return $view;
        }

        // Get contact data
        $contactData = $userCheck->getContactProto();

        // Set data
        $form->setData(array(
            'user' => array(
                'screenName' => $userCheck->screenName,
                'status' => $userCheck->status,
                'role' => $userCheck->role,
                'accessGroup' => $userCheck->accessGroup,
            ),
            'contact' => array(
                'firstName' => $contactData->firstName,
                'lastName' => $contactData->lastName,
                'email' => $contactData->email,
                'altEmail' => $contactData->altEmail,
            )
        ));
        
        // Make password optional
        // @todo make password optional and add password verification field
//        $form->get('user')->get('password')->setAttribute('required', false);

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

        $data = $view->form->getData();
        $user = (object) $data['user'];
        $contact = (object) $data['contact'];
        
        // Update user in db
        $rslt = $userService->updateUser($id, $userCheck->contact_id, array(
            'user' => array(
                'screenName' => $user->screenName,
                'status' => $user->status,
                'role' => $user->role,
                'accessGroup' => $user->accessGroup,
            ),
            'contact' => array(
                'firstName' => $contact->firstName,
                'lastName' => $contact->lastName,
                'email' => $contact->email,
                'altEmail' => $contact->altEmail,
            ),
            'originalContact' => $contactData->toArray()
        ));

        // Send success message to user
        if ($rslt instanceof \Exception === false) {
            $fm->setNamespace('highlight')
                    ->addMessage('User "'
                            . $contact->firstName . ', '
                            . $contact->lastName . '" '
                            . 'with email "' . $contact->email . '" '
                            . 'updated successfully.');
        }
        // send failure message to user 
        else {
            $fm->setNamespace('error')
                    ->addMessage('User "'
                            . $contact->firstName . ', '
                            . $contact->lastName . '" '
                            . 'with email "' . $contact->email . '" '
                            . '" failed to be updated. <br />' . $rslt);
        }

        // Return message to view
        return $view;
    }

    public function deleteAction() {
        // Set up prelims and populate $this -> view for 
        $view = $this->view = new JsonModel();
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
        $userService = $this->getUserService();

        try {
            // Check if term already exists
            $user = $userService->getById($id, AbstractService::FETCH_FIRST_AS_ARRAY_OBJ);
            $contact = $user->getContactProto();
        } catch (\Exception $e) {
            // If not send message and bail
            $fm->setNamespace('error')->addMessage('User Id "' .
                    $id . '" doesn\'t exist in database.');
            return $view;
        }

        // Delete term in db
        $rslt = $userService->deleteUser($user->user_id);

        // Send success message to user
        if ($rslt) {
            $fm->setNamespace('highlight')
                    ->addMessage('User "'
                            . $contact->firstName . ', '
                            . $contact->lastName . '" '
                            . 'with email "' . $contact->email . '" '
                            . '" deleted successfully.');
        }
        // send failure message to user 
        else {
            $fm->setNamespace('error')
                    ->addMessage('User "'
                            . $contact->firstName . ', '
                            . $contact->lastName . '" '
                            . 'with email "' . $contact->email . '" '
                            . '" failed to be deleted.');
        }

        // Return message to view
        return $view;
    }

//    
//    public function sendActivationAction () {
//        
//    }
//    
//    public function activtionAction () {
//        
//    }
//    
//    public function sendActivationEmail () {
//        
//    }
}
