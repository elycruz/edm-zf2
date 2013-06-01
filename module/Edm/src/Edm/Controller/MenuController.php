<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Edm\Controller;

use Edm\Controller\AbstractController,
    Edm\Form\ViewModuleForm,
    Edm\Model\ViewModule,
    Edm\Form\MenuFieldset,
    Edm\Service\AbstractService,
    Edm\Service\ViewModuleServiceAware,
    Edm\Service\ViewModuleServiceAwareTrait,
    Zend\View\Model\ViewModel,
    Zend\View\Model\JsonModel,
    Zend\Paginator\Paginator,
    Zend\Paginator\Adapter\DbSelect,
    Zend\Debug\Debug;

/**
 * Description of MenuController
 *
 * @author ElyDeLaCruz
 */
class MenuController extends AbstractController implements ViewModuleServiceAware {

    use ViewModuleServiceAwareTrait;
    
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
        $form = new ViewModuleForm('view-module-form', array(
            'serviceLocator' => $this->getServiceLocator()));
        $form->setAttribute('action', '/edm-admin/menu/create');
        
        // Add Menu Fieldset
        $form->add(new MenuFieldset('menu-fieldset'));
        
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
            Debug::dump($form->getMessages());
            return $view;
        }
        
        // Get ViewModule service
        $viewModuleService = $this->getViewModuleService();

        // Get data
        $data = $form->getData();
        $mergedData = array_merge(
                $data['view-module-fieldset'], 
                $data['menu-fieldset'], 
                $data['mixed-term-rel-fieldset'], 
                $data['user-params-fieldset']);

        // Get view module data
        $viewModuleData = new ViewModule();
        $viewModuleData
                ->setSecondaryModelName('Edm\Model\Menu')
                ->exchangeArray($mergedData);

        // If emtpy alias populate it
        if (empty($viewModuleData->alias)) {
            $viewModuleData->alias =
                    $this->getDbDataHelper()->getValidAlias($viewModuleData->title);
        }
        // Check if term taxonomy already exists
        $viewModuleCheck = $viewModuleService->getByAlias($viewModuleData->alias);
        if (!empty($viewModuleCheck)) {
            $fm->setNamespace('error')->addMessage('Menu with alias "' . $viewModuleData->alias . '" already ' .
                    'exists in the database.  Click here to edit it.');
            return $view;
        }

        // Create term taxonomy
        $rslt = $viewModuleService->createViewModule($viewModuleData);

        // Send success message to user
        if (is_numeric($rslt) && !empty($rslt) && $rslt instanceof \Exception === false) {
            $fm->setNamespace('highlight')
                    ->addMessage('Menu "' . $viewModuleData->title . 
                            '" added successfully.');
        }
        // send failure message to user 
        else {
            $fm->setNamespace('error')
                    ->addMessage('Menu "' . $viewModuleData->title . 
                            '" failed to be added.  Errors: <br />' 
                            . '<pre>' . $rslt->getTraceAsString() 
                            . '</pre><br />'
                            . $rslt->getMessage());
        }

        // Return message to view
        return $view;
    }
}
