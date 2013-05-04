<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Edm for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Edm\Controller;

use Zend\View\Model\ViewModel,
    Edm\Controller\AbstractController,
    Edm\Service\UserServiceAwareTrait,
    Edm\Form\UserLoginForm;

class IndexController extends AbstractController {

    use UserServiceAwareTrait;

    public function indexAction() {
        // Get our view
        $view = $this->view = new ViewModel();

        // Init flash messenger
        $fm = $this->initFlashMessenger();

        // Get service
        $userService = $this->getUserService();
        
        if ($userService->getAuthService()->hasIdentity()) {
            return $this->redirect('/edm-admin/ajax-ui');
        }

        // Setup form
        $form = new UserLoginForm('login-form');
        $form->setAttribute('action', '/edm-admin/index');
        $view->form = $form;

        // If not post bail
        $request = $this->getRequest();
        if (!$request->isPost()) {
            return $view;
        }

        // Processing request
        $view->form->setData($request->getPost());

        // If form not valid return
        if (!$view->form->isValid()) {
            $fm->setNamespace('error')->addMessage('Login attempt failed. ' .
                    'Please try again.');
            return $view;
        }

        // Get data
        $data = $view->form->getData();

        // Update user in db
        $rslt = $userService->loginUser($data);

        // Login success message to user
        if ($rslt === true) {
            $fm->setNamespace('highlight')
                    ->addMessage('You\'ve been logged in successfully.');
        }
        // Login failure message to user 
        else {
            $fm->setNamespace('error')
                    ->addMessage('Login attempt failed!  Please try again.');
        }
        
        $view->messages = $fm->getMessages();
        
        // Return message to view
        return $view;
    }

    public function logoutAction() {
        $this->view = new ViewModel();
        $this->getUserService()->logoutUser();
        return $this->view;
    }
}
