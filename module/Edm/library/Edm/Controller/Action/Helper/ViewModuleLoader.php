<?php
/**
 * @author ElyDeLaCruz
 */
namespace Edm\Controller\Action\Helper;
use Edm\Controller\Action\AbstractHelper;
class Helper_ViewModuleLoader 
extends AbstractHelper {

    private $_dbDataHelper;
    private $_termTaxService;
    private $_viewModuleService;
    private $_view;

    public function preDispatch() {
        
        $dbDataHelper = $this->getDbDataHelper();
        // Get the current controller
        $controller = $this->getActionController();

        // Get the current view
        $this->_view = $controller->view;

        // Populate layout positions
        $termTaxServ = $this->termTaxonomyService();
        $positions = $termTaxServ->getTermTaxonomiesByAlias('position');
        foreach ($positions as $pos) {
            $this->_getLayout()->{$pos->term_alias} = '';
        }

        // Get module service
        $moduleService = $this->getViewModuleService();

        // Get all modules with their type models (if necessary)
        $rslt = $moduleService->read(array(
            'where' => 'uiTermRel.status <> "unpublished" ' .
            'AND uiTermRel.status <> "archived"',
            'sortBy' => 'listOrder',
            'sort' => 1));
        
//        Zend_Debug::dump($rslt); exit();

        // Loop through results and apply to the layout
        foreach ($rslt as $viewModule) {
            
            // Merge secondary output if necessary
            $secondaryModel = $this->getSecondaryModel($viewModule['type']);
            if (!empty($secondaryModel)) {
                $moduleService->setSecondaryModel($secondaryModel);
                $viewModule = $dbDataHelper->reverseEscapeTupleFromDb(
                        $moduleService->getById($viewModule['view_module_id']));
            }
            
            // Cast and cleanse tuple 
            $viewModule = (object) $dbDataHelper
                ->reverseEscapeTupleFromDb($viewModule);
            
            if (!$this->allowedOnPage($viewModule)) {
                continue;
            }

            // Get the positon value
            $position = $viewModule->term_alias;

            // Append view helper results to the layout position
            // Switch helper type
            $helperType = strtolower($viewModule->helperType);
            if (!empty($viewModule->helperName)) {
                $helperFuncName = $viewModule->helperName;
            }
            else {
                $helperFuncName = $this->normalizeHelperName($viewModule->type);
            }
            
            switch ($helperType) {
                case 'action':
                    // $output = $this->_view->_helper();
                    break;
                case 'view':
                // Fall through
                default:
                    $output = $this->_view->$helperFuncName(array(
                        'tuple' => $viewModule), $this->_view);
                    break;
            }

            $this->_getLayout()->{$position} .= $output;
        }
    }

    /**
     * Returns whether or not module is allowed on current page
     * @param array $module tuple
     * @return boolean 
     * @todo make this functionality more elegant and effecient
     */
    private function allowedOnPage($module) 
    {
        // Return value
        $retVal = false;

        // Get allowed pages
        $allowedPages = (array) Zend_Json::decode($module->allowedPages);

        // Get uri
        $uri = $_SERVER['REQUEST_URI'];

        // Loop through the allowed pages and try to match the current link.
        foreach ($allowedPages as $value) {
            $value = explode('|', $value);
            $value = $value[1];
            if (empty($value)) {
                return true;
            }
            if (strlen($value) == 1) {
                if ($uri == $value) {
                    $retVal = true;
                    break;
                }
            } else {
                if (strpos($uri, $value) !== false) {
                    $retVal = true;
                    break;
                }
            }
        }
        return $retVal;
    }

    /**
     * View Module Service
     * @return Edm_Service_Internal_ViewModuleService
     */
    public function getViewModuleService() {
        if (empty($this->_viewModuleService)) {
            if (Zend_Registry::isRegistered('edm-viewModule-service')) {
                $vm = Zend_Registry::get('edm-viewModule-service');
            } else {
                $vm = new Edm_Service_Internal_ViewModuleService();
            }
            $this->_viewModuleService = $vm;
        }
        return $this->_viewModuleService;
    }

    public function getDbDataHelper() {
        if (empty($this->_dbDataHelper)) {
            if (Zend_Registry::isRegistered('edm-dbDataHelper')) {
                $dbDataHelper = Zend_Registry::get('edm-dbDataHelper');
            } else {
                $dbDataHelper = new Edm_Db_DatabaseDataHelper();
                Zend_Registry::set('edm-dbDataHelper', $dbDataHelper);
            }
            $this->_dbDataHelper = $dbDataHelper;
        }
        return $this->_dbDataHelper;
    }

    public function termTaxonomyService() {
        if (empty($this->_termTaxService)) {
            if (Zend_Registry::isRegistered('edm-termTax-service')) {
                $_termTaxService = Zend_Registry::get('edm-termTax-service');
            } else {
                $this->_termTaxService =
                        $_termTaxService =
                        new Edm_Service_Internal_TermTaxonomyService();
                Zend_Registry::set('edm-termTax-service', $_termTaxService);
            }
        }
        return $this->_termTaxService;
    }

    private function normalizeHelperName($name) {
        $name = explode('-', $name);
        $newName = '';
        foreach ($name as $key => $val) {
            if ((int) $key == 0) {
                $newName .= lcfirst($val);

                continue;
            }
            $newName .= ucfirst($val);
        }
        return $newName . 'ViewModule';
    }
    
    /**
     * Returns the model or false if it can't be found/loaded
     * @param type $alias
     * @return mixed Edm_Db_AbstractTable || false
     */
    private function getSecondaryModel($alias) {
        return Edm_Db_Table_ModelBroker::modelClassExists($alias);
    }

}
