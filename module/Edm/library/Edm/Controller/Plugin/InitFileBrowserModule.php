<?php
/**
 * Description of InitAdminPlugins
 * @author ElyDeLaCruz
 */
class Edm_Controller_Plugin_InitFileBrowserModule
    extends Zend_Controller_Plugin_Abstract
{
    public function dispatchLoopStartup(
            Zend_Controller_Request_Abstract $request)
    {
        // Continue only if current module is being requested
        $module = $request->getModuleName();
        if ($module != 'edm-file-browser') {
            return;
        }

        // Define the sites default title
        defined('DEFAULT_SITE_TITLE') ||
            define( 'DEFAULT_SITE_TITLE', 'Edm Cms - File Browser' );

        /**********************************************************************
         * LAYOUT and VIEW
         **********************************************************************/
        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout('index');
        $layout->setLayoutPath(APPLICATION_PATH
                .'/modules/edm-file-browser/views/scripts' .
                '/layout');
        $view = $layout->getView();

        // Customzie the top of the document
        $view->headTitle( DEFAULT_SITE_TITLE, 'SET' );
        
        // Style sheets
        $view->headLink()->appendStylesheet(
            '/module-templates/edm-file-browser/css/main.css', 'all');
			
        // Admin View Helpers
        $view->addHelperPath('Edm/View/Helper', 'Edm_View_Helper');
        $view->addHelperPath('Edm/View/Helper/Navigation',  'Edm_View_Helper_Navigation');
        $view->addHelperPath(APPLICATION_PATH . '/modules/edm-admin/views' .
                '/helpers', 'EdmAdmin_View_Helper');
        
        // File Browser View Helpers
        $view->addHelperPath(APPLICATION_PATH . '/modules/edm-file-browser/views' .
                '/helpers', 'EdmFileBrowser_View_Helper');

        // If not index page
        if ($request->getControllerName() == 'index') {
            return;
        }
        
//        // Get navigation
//        $nav_config = new Zend_Config_Xml(APPLICATION_PATH
//                .'/configs/edm-file-browser/navigation.xml', 'navigation');
//        $nav = new Zend_Navigation($nav_config);
//
//        // Pass acl to navigation
//        if (Zend_Registry::isRegistered('edm-acl')) {
//            $acl = Zend_Registry::get('edm-file-browser-acl');
//        }
//        else {
//            
//            $acl = new Edm_Acl(new Zend_Config_Ini(
//                    APPLICATION_PATH .'/configs/edm-file-browser/acl.ini', 'production'));
//            Zend_Registry::set('edm-file-browser-acl', $acl);
//        }
//        
//        $auth = Zend_Auth::getInstance();
//        $role = $auth->hasIdentity() ? $auth->getIdentity()->role : 'guest';
//        $view->navigation($nav)->setAcl($acl)->setRole($role);
//
//        $uri = '/'. $request->getModuleName() .'/'.
//                $request->getControllerName() .'/'.
//                $request->getActionName();
//        //$uri = $request->getPathInfo();
//
//        $activeNav = $view->navigation()->findByUri($uri);
//        $activeNav->active = true;
    }
}
