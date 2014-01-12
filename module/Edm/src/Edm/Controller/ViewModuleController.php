<?php

namespace Edm\Controller;

use Edm\Controller\AbstractController,
    Edm\Form\ViewModuleForm,
    Edm\Model\ViewModule,
    Edm\Controller\SecondaryFieldsetAwareTrait,
    Edm\Service\AbstractService,
    Edm\Service\ViewModuleServiceAware,
    Edm\Service\ViewModuleServiceAwareTrait,
    Edm\Service\TermTaxonomyServiceAware,
    Edm\Service\TermTaxonomyServiceAwareTrait,
    Zend\View\Model\ViewModel,
    Zend\View\Model\JsonModel,
    Zend\Paginator\Paginator,
    Zend\Paginator\Adapter\DbSelect,
    Zend\Debug\Debug;

class ViewModuleController extends AbstractController implements
ViewModuleServiceAware, TermTaxonomyServiceAware {

    use ViewModuleServiceAwareTrait,
        TermTaxonomyServiceAwareTrait,
        SecondaryFieldsetAwareTrait;

    /**
     * The Secondary Fieldset's alias name
     * @var string 
     */
    protected $secondaryFieldsetAlias = 'secondary-fieldset';

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
        $viewModuleService = $this->getViewModuleService();

        // Select 
        $select = $viewModuleService->getSelect();

        // Where part of query
        $where = array();

        // ViewModule Type
        $viewModuleType = $this->getAndSetParam('type', '*');
        if (!empty($viewModuleType) && $viewModuleType != '*') {
            $where['type'] = $viewModuleType;
        }

        // Access Group
        $accessGroup = $this->getAndSetParam('accessGroup', '*');
        if (!empty($accessGroup) && $accessGroup != '*') {
            $where['mixedTermRel.accessGroup'] = $accessGroup;
        }

        // Term Taxonomy Id
        $termTaxId = $this->getAndSetParam('term_taxonomy_id', '*');
        if (!empty($termTaxId) && $termTaxId != '*') {
            $where['mixedTermRel.term_taxonomy_id'] = $termTaxId;
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

        // Paginator $viewModuleService->getDb()
        $paginator = new Paginator(
                new DbSelect($select, $viewModuleService->getViewModuleTable()->getAdapter()));
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

        // Get View Module Type to extend the view module with
        $viewModuleType = $this->getAndSetParam('type', null);

        // Form Action
        $formAction = '/edm-admin/view-module/create';

        // Let view be terminal in this action
        $view->setTerminal(true);
        $fm = $this->initFlashMessenger();

        // Setup form
        $form = new ViewModuleForm('view-module-form', array(
            'serviceLocator' => $this->getServiceLocator()));
        $view->form = $form;

        // If view module type
        if (isset($viewModuleType)) {
            
            $viewModuleTypeData = $this->getSecondaryFieldsetDataModel($viewModuleType);
            $formAction .= $viewModuleTypeData->getFormActionTypeAppendage();

            // Add Secondary Fieldset
            $view->form->add($viewModuleTypeData->getFieldset());
        }

        // Set form action
        $form->setAttribute('action', $formAction);

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
            // Debug::dump($form->getMessages());
            return $view;
        }

        // Get ViewModule service
        $viewModuleService = $this->getViewModuleService();
        $viewModuleService->clearSecondaryTableRelationship();

        // Get data
        $data = $form->getData();
        $mergedData = array_merge(
                $data['view-module-fieldset'], 
                $data['mixed-term-rel-fieldset'], 
                $data['user-params-fieldset']);

        // Get a View Module Model Object
        $viewModuleData = new ViewModule();

        // View Module
        if (isset($viewModuleType)) {

            // Get table name and alias according to view module type
            $termTaxService = $this->getTermTaxService();

            // Fetch table
            $rslt = $termTaxService->getByAlias($viewModuleType . '-alias', 'table-name-by-alias');

            $mergedData = array_merge($mergedData, $data[$viewModuleTypeData->getFieldsetAlias()]);
            $viewModuleData->setSecondaryProtoName($viewModuleTypeData->getModelClassName());
            $viewModuleService->setSecondaryProtoName($viewModuleTypeData->getModelClassName());
            $viewModuleService->setSecondaryTableName($rslt['term_name']);
        }

        // Set view module data
        $viewModuleData->exchangeArray($mergedData);

        // If emtpy alias populate it
        if (empty($viewModuleData->alias)) {
            $viewModuleData->alias =
                    $this->getDbDataHelper()->getValidAlias($viewModuleData->title);
        }
        // Check if term taxonomy already exists
        $viewModuleCheck = $viewModuleService->getByAlias($viewModuleData->alias);
        if (!empty($viewModuleCheck)) {
            $fm->setNamespace('error')->addMessage('View Module with alias "' . $viewModuleData->alias . '" already ' .
                    'exists in the database.  Click here to edit it.');
            return $view;
        }

        // Create term taxonomy
        $rslt = $viewModuleService->createViewModule($viewModuleData);

        // Send success message to user
        if (is_numeric($rslt) && !empty($rslt) && $rslt instanceof \Exception === false) {
            $fm->setNamespace('highlight')
                    ->addMessage('View Module "' . $viewModuleData->title .
                            '" added successfully.');
        }
        // send failure message to user 
        else {
            $fm->setNamespace('error')
                    ->addMessage('View Module "' . $viewModuleData->title .
                            '" failed to be added.  Errors: <br />'
                            . '<pre>' . $rslt->getTraceAsString()
                            . '</pre><br />'
                            . $rslt->getMessage());
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

        // View Module Type
        $viewModuleType = $this->getParam('type');

        // Put data into model
        $viewModuleService = $this->getViewModuleService();

        // Setup form
        $formAction = '/edm-admin/view-module/update/id/' . $id;
        $form = new ViewModuleForm('view-module-form', array(
            'serviceLocator' => $this->getServiceLocator()
        ));
        $form->setAttribute('action', $formAction);
        $view->form = $form;
        
        // Blank Data For 
        $dataForForm = array();

        // If view module type
        if (isset($viewModuleType)) {
            $classFormattedName = $this->normalizeAliasToClassCase($viewModuleType);
            $secondaryFieldsetName = '\\Edm\\Form\\' . $classFormattedName . 'Fieldset';
            $secondaryModelName = '\\Edm\\Model\\' . $classFormattedName;
            $formAction .= '/type/' . $viewModuleType;

            // Add Secondary Fieldset
            $view->form->add(new $secondaryFieldsetName(
                    $this->secondaryFieldsetAlias));
        }

        // Get Existing View Module
        $existingViewModule =
                $viewModuleService->getById($id, 
                        AbstractService::FETCH_FIRST_AS_ARRAY_OBJ);
        
        // Check if term already exists if not bail
        if (empty($existingViewModule)) {
            $fm->setNamespace('error')->addMessage('ViewModule with id "'
                    . $id . '" doesn\'t exist in database.');
            return $view;
        }

        $userParamsFieldset = null;
        // Resolve user params field
        if (!empty($existingViewModule->userParams)) {
            $userParamsFieldset = $viewModuleService->unSerializeAndUnEscapeTuples(
                    $existingViewModule->userParams);
        }

        $allowedOnPages = null;
        // Resolve allowed on pages field
        if (!empty($existingViewModule->allowedOnPages)) {
            $allowedOnPages = $viewModuleService->unSerializeAndUnEscapeArray(
                    $existingViewModule->allowedOnPages);
        }

        // Mixed Term Rel Proto
        $mixedTermRel = $existingViewModule->getMixedTermRelProto();

        // Data for Form
        $dataForForm = array_merge($dataForForm, array(
            'mixed-term-rel-fieldset' => array(
                'term_taxonomy_id' => $mixedTermRel->term_taxonomy_id,
                'status' => $mixedTermRel->status,
                'accessGroup' => $mixedTermRel->accessGroup,
            ),
            'view-module-fieldset' => array(
                'title' => $existingViewModule->title,
                'alias' => $existingViewModule->alias,
                'type' => $existingViewModule->type,
                'partialScript' => $existingViewModule->partialScript,
                'content' => $existingViewModule->content,
                'allowedOnPages' => $allowedOnPages
            ),
            'user-params-fieldset' => array(
                'userParams' => $userParamsFieldset
            )
        ));
        
        // Merge Secondary Data if any
        $secondaryProto = $existingViewModule->getSecondaryProto();
        if ($secondaryProto) {
            $dataForForm[$secondaryFieldsetName] = 
                    $secondaryProto->getFieldsInFormToArray();
        }
        
        // Set data
        $form->setData($dataForForm);

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
        $mergedData = array_merge(
                $data['view-module-fieldset'], 
                $data['mixed-term-rel-fieldset'], 
                array('view_module_id' => $id), 
                $data['user-params-fieldset']);

        // Create new post model obj
        $viewModuleData = new ViewModule($mergedData);

        // Update term in db
        $rslt = $viewModuleService->updateViewModule($viewModuleData);

        // Send success message to user
        if ($rslt === true && $rslt instanceof \Exception === false) {
            $fm->setNamespace('highlight')
                    ->addMessage('ViewModule "'
                            . $viewModuleData->title . '" in category "' . $viewModuleData->term_taxonomy_id
                            . '" updated successfully.');
        }
        // send failure message to user 
        else {
            $fm->setNamespace('error')
                    ->addMessage('ViewModule "'
                            . $viewModuleData->title . '" in category "' . $viewModuleData->term_taxonomy_id
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
        $viewModuleService = $this->getViewModuleService();

        // Check if term already exists
        $viewModuleRslt = $viewModuleService->getById($id);
        if (empty($viewModuleRslt)) {
            // If not send message and bail
            $fm->setNamespace('error')->addMessage('ViewModule Id "' .
                    $id . '" doesn\'t exist in database.');
            return $view;
        }

        // ViewModule object
        $viewModule = new ViewModule($viewModuleRslt);
        $mixedTermRel = $viewModule->getMixedTermRelProto();

        // Delete term in db
        $rslt = $viewModuleService->deleteViewModule($viewModule->post_id);

        // Send success message to user
        if ($rslt) {
            $fm->setNamespace('highlight')
                    ->addMessage('ViewModule "'
                            . $viewModule->title . '" in category "' . $mixedTermRel->term_taxonomy_id
                            . '" deleted successfully.');
        }
        // send failure message to user 
        else {
            $fm->setNamespace('error')
                    ->addMessage('ViewModule "'
                            . $viewModule->title . '" in category "' . $mixedTermRel->term_taxonomy_id
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
        $viewModuleService = $this->getViewModuleService();
        $viewModule = new ViewModule($viewModuleService->getById($id));
        $fm = $this->initFlashMessenger();

        // Set error message if term tax not found
        if (empty($viewModule)) {
            $fm->setNamespace('error')
                    ->addMessage('ViewModule id "' . $id
                            . '" not found in database.  ' .
                            'List order change failed.');
            return $view;
        }

        // Set list order
        $viewModule->listOrder = $listOrder;

        // Update listorder
        $rslt = $viewModuleService->setListOrderForViewModule($viewModule);

        // Send success message to user
        if (!empty($rslt)) {
            $fm->setNamespace('highlight')
                    ->addMessage('ViewModule "'
                            . $viewModule->term_name . ' > ' . $viewModule->taxonomy
                            . '" updated successfully.');
        }
        // send failure message to user 
        else {
            $fm->setNamespace('error')
                    ->addMessage('ViewModule "'
                            . $viewModule->term_name . ' > ' . $viewModule->taxonomy
                            . '" failed to be updated.');
        }

        // Return message to view
        return $view;
    }

}
