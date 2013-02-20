<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Edm for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Edm\Controller;

use Zend\Mvc\Controller\AbstractActionController,
    Zend\View\Model\ViewModel,
    Zend\View\Model\JsonModel,
    Zend\Paginator\Paginator,
    Zend\Paginator\Adapter\DbSelect,
    Zend\Db\Sql\Select,
    Edm\Model\Term,
    Edm\Form\TermForm;

class TermController extends AbstractActionController {

    protected $termModel;

    public function indexAction() {
        // View
        $view = new JsonModel();
        
        // Model
        $model = $this->getTermModel();
        
        // Route match
        $routeMatch = $this->getEvent()->getRouteMatch();
        
        // Page number
        $pageNumber = $routeMatch->getParam('page', 1);
        
        // Items per page
        $itemCountPerPage = $routeMatch->getParam('itemsPerPage', 5);
        
        // Select 
        $select = new Select();
        $select->from($model->getTable());
        
        // Paginator
        $paginator = new Paginator(new DbSelect($select, $model->getAdapter()));
        $paginator  ->setItemCountPerPage($itemCountPerPage)
                    ->setCurrentPageNumber($pageNumber);
        
        // Set actual page (happens to fix exceeded page number set by user)
        $view->page = $paginator->getCurrentPageNumber();
        $view->itemsPerPage = $itemCountPerPage;
        $view->itemsTotal = $paginator->getTotalItemCount();
        
        // Send results
        $view->results = $paginator;
        $view->setTerminal(true);
        return $view;
    }

    public function createAction() {
        $view = new ViewModel();
        $view->setTerminal(true);
        $view->form = new TermForm();

        $request = $this->getRequest();
        if (!$request->isPost()) {
            return $view;
        }

        $term = new Term();
        $view->form->setInputFilter($term->getInputFilter());

        if (!$view->form->isValid()) {
            return $view;
        }

        $term->exchangeArray($form->getData());
        $this->getTermModel()->create($term);
        $view->success = true;
        $view->message = 'Term created successfuly!';
        return $view;
    }

    public function fooAction() {
        // This shows the :controller and :action parameters in default route
        // are working when you browse to /module-specific-root/TermTaxonomy/foo
        return array();
    }

    /**
     * Gets our Term model
     * @return Edm\Model\TermModel
     */
    public function getTermModel() {
        if (empty($this->termModel)) {
            $this->termModel = $this->getServiceLocator()->get('Edm\Model\TermTable');
        }
        return $this->termModel;
    }

}
