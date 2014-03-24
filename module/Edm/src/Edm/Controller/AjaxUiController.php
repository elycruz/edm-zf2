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
    Zend\View\Model\ViewModel;

class AjaxUiController extends AbstractActionController
{
    public function indexAction()
    {
        $this->view = new ViewModel(array('key' => 'value'));
        $this->view->setTerminal(true);
        $this->view->setTemplate('layout/edm-admin-ajax-ui');
        $this->view->navigation_json = 
                json_encode($this->getServiceLocator()
                        ->get('edm-navigation')->toArray());
        return $this->view;
    }
}
