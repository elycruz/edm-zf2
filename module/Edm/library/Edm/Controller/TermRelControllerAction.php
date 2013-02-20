<?php
/**
 * @author ElyDeLaCruz
 */

namespace Edm\Controller;
use Edm\Controller\Action\AbstractController;
class Edm_Controller_TermRelControllerAction
extends AbstractController
{
    /**
     * Primary service for our data model
     * @var Edm_Service_Internal_Abstract
     */
    protected $_primaryService;

    public function setStatusAction()
    {
        $id = $this->_getParam('id');
        $objectType = $this->_getParam('objectType');
        $value = $this->_getParam('status');

        // Consider checking existence of tuple before updating it.
        // Update listOrder for id
        $rslt = $this->_primaryService
                ->setStatus($id, $objectType, $value);

        // Send process outcome message to the user
        if ($rslt == true) {
            $this->_flashMessenger->setNamespace('highlight')
                    ->addMessage('Object id:'. $id .'&rsquo;s status '.
                            'id value '.
                            'has been updated.');
        }
        else {
            $this->_flashMessenger->setNamespace('error')
                    ->addMessage('There has been an error in updating the '.
                            'Object id:'. $id .'&rsquo;s status '.
                            'value.');
        }

        // Redirect back to the index page
        $this->_redirect('/admin/'. $this->getRequest()
                ->getControllerName() . '/index');

    }

    public function setAccessGroupAction()
    {
        $id = $this->_getParam('id');
        $objectType = $this->_getParam('objectType');
        $value = $this->_getParam('accessGroup');

        // Consider checking existence of tuple before updating it.
        // Update listOrder for id
        $rslt = $this->_primaryService
                ->setAccessGroup($id, $objectType, $value);

        // Send process outcome message to the user
        if ($rslt == true) {
            $this->_flashMessenger->setNamespace('highlight')
                    ->addMessage('Object id:'. $id .'&rsquo;s accessGroup '.
                            'id value '.
                            'has been updated.');
        }
        else {
            $this->_flashMessenger->setNamespace('error')
                    ->addMessage('There has been an error in updating the '.
                            'Object id:'. $id .'&rsquo;s accessGroup '.
                            'value.');
        }

        // Redirect back to the index page
        $this->_redirect('/admin/'. $this->getRequest()
                ->getControllerName() . '/index');
    }
    
    public function setTermTaxonomyAction()
    {
        $id = $this->_getParam('id');
        $objectType = $this->_getParam('objectType');
        $value = $this->_getParam('termTaxonomyId');

        // Consider checking existence of tuple before updating it.
        // Update listOrder for id
        $rslt = $this->_primaryService
                ->setTermTaxonomyId($id, $objectType, $value);

        // Send process outcome message to the user
        if ($rslt == true) {
            $this->_flashMessenger->setNamespace('highlight')
                    ->addMessage('Object id:'. $id .'&rsquo;s term taxonomy '.
                            'id value '.
                            'has been updated.');
        }
        else {
            $this->_flashMessenger->setNamespace('error')
                    ->addMessage('There has been an error in updating the '.
                            'Object id:'. $id .'&rsquo;s term taxonomy id '.
                            'value.');
        }

        // Redirect back to the index page
        $this->_redirect('/admin/'. $this->getRequest()
                ->getControllerName() . '/index');
    }

    public function setListOrderAction()
    {
        $id = $this->_getParam('id');
        $objectType = $this->_getParam('objectType');
        $listOrder = $this->_getParam('listOrder');

        // Consider checking existence of tuple before updating it.
        // Update listOrder for id
        $rslt = $this->_primaryService
                ->setListOrder($id, $objectType, $listOrder);

        // Send process outcome message to the user
        if ($rslt == true) {
            $this->_flashMessenger->setNamespace('highlight')
                    ->addMessage('Item Info&rsquo;s list order value has '.
                            'been updated for id');
        }
        else {
            $this->_flashMessenger->setNamespace('error')
                    ->addMessage('There has been an error in updating the '.
                            'the Item Info&rsquo;s list order value.');
        }

        // Redirect back to the index page
        $this->_redirect('/admin/'. $this->getRequest()
                ->getControllerName() . '/index');
    }

    
}