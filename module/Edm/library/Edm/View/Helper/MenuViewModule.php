<?php
/**
 * @author ElyDeLaCruz
 */
class Edm_View_Helper_MenuViewModule extends
Edm_View_Helper_Abstract
{
    /**
     * Zend Navigation Container
     * @var Zend_Navigation_Container
     */
    protected $_navigation;
    
    /**
     * Link Service
     * @var Edm_Service_Internal_LinkService
     */
    protected $_linkService;
    
    /**
     * View Module Service
     * @var Edm_Service_Internal_AbstractCrudService
     */
    protected $_viewModuleService;
    
    /**
     * Sets up this view helper for output
     * @param array $options
     * @param Zend_View_Abstract $view
     * @return Edm_View_Helper_MenuModule
     */
    public function menuViewModule(array $options,
            Zend_View_Abstract $view)
    {
        
        // Set valid option key names
        $this->setValidKeyNames(array('tuple',
            'attributes', 'injectAcl', 'render', 'viewModuleService',
            'useModuleHelper', 'linkService'));
        
        // Get necessary services
        $this->_linkService = $this->getLinkService();
        $this->_viewModuleService = $this->getViewModuleService();
        $this->_viewModuleService
                ->setSecondaryModel(Edm_Db_Table_ModelBroker::getModel('menu'));

        // Validate the option key names
        $this->validateKeyNames($options);

        // Set the options so that they are accessible via $this->
        $this->setOptions($options);

        // Set the tuple columns to options for this view helper
        $this->setOptions((array)$this->tuple);

        // Check whether we have to set the view
        if (!empty($view)) {
            $this->setView($view);
        }
        
//        return $this->test()->render();
        // Get our navigation object
        $nav = $this->_getNavigationObject();
        
        // Resolve menu helper name
        $this->menuHelperName = empty($this->menuHelperName) ? 'EdmMenu' : 
                $this->menuHelperName;

        // Active branch only
        $this->onlyActiveBranch = (bool) $this->onlyActiveBranch;

        // Render parents
        $this->renderParents = (bool) $this->renderParents;
//            ;

        // Acl set acl to this view helper's navigation object
        if (!empty($this->injectAcl)) {
            $this->_injectAcl();
        }
        
        // Render menu
        $this->content = $this->view->navigation()
                ->findHelper($this->menuHelperName)
                ->setMinDepth(0)
                ->setMaxDepth(5)
                ->setUlClass('horiz-links-left')
//                ->setRenderParents($this->renderParents)
//                ->setOnlyActiveBranch($this->onlyActiveBranch)
                ->renderMenu();
            
        if (!empty($this->menuPartialScript)) {
            // Render Partial
            $this->content = $this->view->partial($this->menuPartialScript, $this);
        }

        if (!empty($this->useModuleHelper)) {
            // @todo Render content into view module's helper
            // $this->content = $this->view->helperName($moduleHelperOptions);
        }
        
        // Return content
        return $this->content;
    }
    
    /**
     * Returns a Zend_Navigation object with its pages already populated
     * with links that have a menu_id of $menu_id
     * @param uint $menu_id
     * @return Zend_Navigation 
     */
    protected function _getNavigationObject()
    {   
        // If inherits from another menu
        if (!empty($this->parent_id)) 
        {
            // Get Parent
            $parent = $this->_viewModuleService
                    ->getById($this->parent_id);
            
            // If parent exists
            if (!empty($parent)) {
                $parentStackItem = 
                        Edm_View_Helper_Navigation_Broker::getItem(
                                $parent->alias);

                // if parent doesn't have pages set pages 
                // then get pages.
                if (!$parentStackItem->navigation->hasPages()) {
                    $parentPages =  $this->dbDataHelper->reverseEscapeTuplesFromDb(
                            $this->_linkService->read(array(
                                'where' => 'link.menu_id="' . $this->parent_id .
                    '" AND uiTermRel.status="published" AND link.parent_id="0"',
                                'sortBy' => 'uiTermRel.listOrder')));
                    $parentStackItem->navigation->setPages($parentPages);
                }
                    
                // Set the main navigation item
                $this->_navigation = $parentStackItem->navigation;
            }
            else {
                // @todo finish error message
                throw new Exception('Parent does\'nt exists for ...menu module');
            }// if parent exists
        } // If inherits from menu id
        
        else {

            // Get Navigation Stack Item for this nav
            $currStackItem = 
                    Edm_View_Helper_Navigation_Broker::getItem($this->alias);

            // If current stack item doens't have pages then get them
            if (!$currStackItem->navigation->hasPages()) {
                // Get all top level links of this menu that are published
                $rslt = $this->_linkService->read(array(
                                'where' => 'link.menu_id="' . $this->menu_id .
                    '" AND uiTermRel.status="published" AND link.parent_id="0"',
                                'sortBy' => 'uiTermRel.listOrder'));

                // Loop through top level link results 
                // and create a pages array for our
                // Zend Navigation object.
                $pages = array();
                $dbDataHelper = $this->getDbDataHelper();
                foreach($rslt as $link) {
                    $link = (object) $dbDataHelper->reverseEscapeTupleFromDb($link);
                    $pages[] = $this->_createPage($link);
                }
                
                // Set curr navs pages
                $currStackItem->navigation->setPages($pages);
            } 
            
            // Set this' navigation object
            $this->_navigation = $currStackItem->navigation;
        }
        
        // @todo fix this hack;  I.e., not sure if this should be here
//        $urlHelper = Zend\Mvc\Controller\Action_HelperBroker::getStaticHelper('url');
//        $uri = $urlHelper->url();
//        $activeNav = $this->view->navigation($this->_navigation)->findByUri($uri);
//        $activeNav->active = true; 
        
        // Return our nav
        return $this->_navigation;
    }


    /**
     * Puts the pertinent link information needed to create a Zend_Nav..._Page
     * within an array.
     * @param object $link
     * @return array
     */
    protected function _createPage( stdClass $link)
    {
        // Start setting up page/link
        $page = new stdClass();
        
        // Label
        $page->label = $link->label;
        
        // Id
        if (!empty($link->html_id)) {
            $page->id = $link->html_id;
        }

        // Class
        if (!empty($link->html_class)) {
            $page->class = $link->html_class;
        }

        // Title
        if (!empty($link->html_title)) {
            $page->title = $link->html_title;
        }
        else {
            $page->title = $link->label;
        }

        // Target
        if (!empty($link->html_target)) {
            $page->target = $link->html_target;
        }
        
        // Rel
        if (!empty($link->html_rel)) {
            $page->rel = $link->html_rel;
        }
        
        // Hidden
        // @todo fix hidden attribute to generate html 5 hidden attribute on link
        if (!empty($link->html_hidden)) {
            $page->hidden = 'hidden';
        }
        
        // Visibile
        if (empty($link->visible)) {
            $page->visible = false;
        }
        
        // Resource
        if (!empty($link->acl_resource)) {
            $page->resource = $link->acl_resource;
        }

        // Privilege
        if (!empty($link->acl_privilege)) {
            $page->privilege = $link->acl_privilege;
        }

        // If "Uri" link;  I.e. Zend_Navigation_Page_Uri
        if ($link->type == 'uri') {
            $page->uri = $link->uri;
        }
        
        // If "Mvc" link; I.e. Zend_Navigation_Page_Mvc
        else if ($link->type == 'mvc') {
            
            // Route
            if (!empty($link->mvc_route)) {
                $page->route = $link->mvc_route;
            }

            // Module
            if (!empty($link->mvc_module)) {
                $page->module = $link->mvc_module;
            }

            // Controller
            if (!empty($link->mvc_controller)) {
                $page->controller = $link->mvc_controller;
            }

            // Action
            if (!empty($link->mvc_action)) {
                $page->action = $link->mvc_action;
            }

            // Reset Params on Render
//            if (!empty($link->mvc_resetParamsOnRender)) {
//                $page->reset_paramsbool = $link->mvc_resetParamsOnRender;
//            }
            
            // Mvc Params
            if (!empty($link->mvc_params)) {
                $mvc_params = Zend_Json::decode($link->mvc_params);
                if ($mvc_params) {
                    
                    // Process mvc params (gets rid of extraneous names used for json array
                    $processed_mvc_params = $this->_getParamsForMvcLink($mvc_params);
                    
                    // @todo Eliminate hardcoding options for mvc params functionality in link classes
                    if (count($processed_mvc_params)) {
                        $page->params = $processed_mvc_params;
                    }

                }
                
            } // end if mvc params
        }

        // Order
        if (!empty($link->listOrder)) {
            $page->order = $link->listOrder;
        }
        
        // Children
        $children = $this->_getLinkPages($link);
        if ($children !== false) {
            $page->pages = $children;
        }
        
        return (array) $page;
    }

    protected function _getLinkPages(stdClass  $link)
    {
        //Zend_Debug::dump($link);
        // Get all sub pages for this $link/page
        $rslt = $this->_dbDataHelper->reverseEscapeTuplesFromDb(
            $this->_linkService->read(array('where' => 'link.menu_id="'. 
                $link->menu_id .
                '" AND uiTermRel.status="published" AND link.parent_id="'.
                $link->link_id .'"',
                'sortBy' => 'uiTermRel.listOrder')));

        // Loop through results
        $pages = array();
        foreach($rslt as $child) {
            
            // Cast child tuple as object
            $child = (object) $child;
            
            // add Child to pages array
            $pages[] = $this->_createPage($child);
        }

        return (count($pages) > 0 ? $pages : false);
    }
    
    /**
     * Injects the front acl into this view helpers navigation object.
     * @return Zend_Navigation
     */
    protected function _injectAcl()
    {
//        if (Zend_Registry::isRegistered('edm-acl')) {
//            $acl = Zend_Registry::get('edm-acl');
//        }
//        else {
            $acl_config = new Zend_Config_Ini(
                        APPLICATION_PATH .
                        '/configs/edm-default/acl.ini',
                        APPLICATION_ENV);
            $acl = new Edm_Acl($acl_config);
            Zend_Registry::set('edm-acl', $acl);
//        }
        
        $auth = Zend_Auth::getInstance();
        $role = $auth->hasIdentity() ?
                (string)($auth->getIdentity()->role) : 'guest';
        return $this->view->navigation($this->_navigation)
                ->setAcl($acl)->setRole($role);
    }
    
    protected function _getParamsForMvcLink(array $params) 
    {
        $output = array();
        $limit = 10; $i = 0;
        $prefix = 'mvc_param_';
        
        for ($i = 0; $i < $limit; $i += 1) {
            $paramFieldName = $prefix . ($i + 1) .'_name';
            $paramValueName = $prefix . ($i + 1) .'_value';

            if (!empty($params[$paramFieldName])) {
                $output[$params[$paramFieldName]] = 
                    $params[$paramValueName] ? $params[$paramValueName] : '';
            }
            
        }// end for

        return $output;
    }    


    /**
     * View Module Service
     * @return Edm_Service_Internal_AbstractCrudService
     */
    public function getViewModuleService() {
        $vm = $this->_viewModuleService;
        if (empty($vm)) {
            if (Zend_Registry::isRegistered('edm-viewModule-service')) {
                $vm = Zend_Registry::get('edm-viewModule-service');
            } else {
                $vm = new Edm_Service_Internal_ViewModuleService();
                Zend_Registry::set('edm-viewModule-service', $vm);
            }
        }
        return $vm;
    }
    
    /**
     * Link Service
     * @return Edm_Service_Internal_LinkService 
     */
    public function getLinkService() {
        $ls = $this->_linkService;
        if (empty($ls)) {
            if (Zend_Registry::isRegistered('edm-link-service')) {
                $ls = Zend_Registry::get('edm-link-service');
            }
            else {
                $ls = new Edm_Service_Internal_LinkService();
                Zend_Registry::set('edm-link-service', $ls);
            }
        }
        $this->_linkService = $ls;
        return $ls;
    }
    
    
    public function test($pages = null) {
        // Get navigation
        $nav_config = new Zend_Config_Xml(APPLICATION_PATH
                .'/configs/edm-admin/navigation.xml', 'navigation');
        $nav = new Zend_Navigation($nav_config);
        
        // Pass acl to navigation
//        if (Zend_Registry::isRegistered('edm-acl')) {
//            $acl = Zend_Registry::get('edm-acl');
//        }
//        else {
            $acl = new Edm_Acl(new Zend_Config_Ini(
                    APPLICATION_PATH .'/configs/edm-admin/acl.ini', 'production'));
            Zend_Registry::set('edm-acl', $acl);
//        }
        
        $auth = Zend_Auth::getInstance();
        $role = $auth->hasIdentity() ? $auth->getIdentity()->role : 'guest';
        return $this->view->navigation($nav)->setAcl($acl)->setRole($role)->findHelper('EdmMenu');
    }
}
