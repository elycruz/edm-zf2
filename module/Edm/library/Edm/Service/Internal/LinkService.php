<?php
/**
 * Description of Edm_Service_LinkService
 * @author ElyDeLaCruz
 */
class Edm_Service_Internal_LinkService extends
Edm_Service_Internal_AbstractCrudService
{
    protected $_linkModel;
    protected $_uiTermRelModel;
    protected $_menuModel;
    protected $_viewModuleModel;

    public function __construct()
    {
        $this->_linkModel = Edm_Db_Table_ModelBroker::getModel('link');
        $this->_uiTermRelModel = Edm_Db_Table_ModelBroker::getModel('ui-term-rel');
        $this->_menuModel = Edm_Db_Table_ModelBroker::getModel('menu');
        $this->_viewModuleModel = Edm_Db_Table_ModelBroker::getModel('view-module');
    }

    public function createLink(array $data)
    {
        if (!array_key_exists('link', $data) &&
            !array_key_exists('ui-term-rel', $data)) {
            throw new Exception('A key is missing from the '.
                    'data array passed into the create link function of the '.
                    'link service.');
        }

        // Get data
        $linkData = $data['link'];
        $uiTermRelData = $data['ui-term-rel'];

        //--------------------------------------------------------------
        // Update term relationship data
        //--------------------------------------------------------------
        // Get item count model and use it's `user` table `itemCount` to
        $uiTermRelData['listOrder'] = 
            $this->getRowCount($this->_linkModel) + 1;

        // Set the `objectType` for term relationship
        $uiTermRelData['objectType'] = 'link';

        //--------------------------------------------------------------
        // Begin db transaction
        //--------------------------------------------------------------
        $db = $this->getDb();
        $db->beginTransaction();
        try {
            // Update user table
            $objectId = $this->_linkModel->createLink($linkData);

            // Update term relationships table
            $uiTermRelData['object_id'] = $objectId;
            $this->_uiTermRelModel->createUiTermRel($uiTermRelData);

            // Success, commit to db
            $db->commit();

            // Return true to the user
            return true;
        }
        catch (Exception $e) {
            $db->rollBack();
            return $e->getMessage();
        }
    }
    
    public function getLinkById($id, $fetchMode = Zend_Db::FETCH_OBJ)
    {
        return $this->getSelect()
                ->where('link.link_id=?', $id)->query($fetchMode)->fetch();
    }

    public function getLinkByAlias($alias, $fetchMode = Zend_DB::FETCH_OBJ) {
        return $this->getSelect()
                ->where('link.alias=?', $alias)->query($fetchMode)->fetch();

    }
    
    public function getSelect() 
    {
        return $this->getDb()->select()
                ->from(array('link' => $this->_linkModel->getName()), '*')
                ->join(array('menu' => $this->_menuModel->getName()), 
                        'menu.menu_id=link.menu_id', 
                        array('menu_module_id' => 'view_module_id'))
                ->join(array('viewModule' => $this->_viewModuleModel->getName()), 
                        'viewModule.view_module_id=menu.view_module_id',
                        array('menu_title' => 'title'))
                ->join(array('uiTermRel' => $this->_uiTermRelModel->getName()),
                        'uiTermRel.object_id = link.link_id AND '.
                        'uiTermRel.objectType = "link"')
                ->join(array('termTax' => 'term_taxonomies'), 
                        'termTax.term_taxonomy_id = uiTermRel.term_taxonomy_id',
                        array('term_alias'))
                ->join(array('term' => 'terms'), 
                        'term.alias = termTax.term_alias',
                        array('term_name' => 'name'));
    }
    
    public function updateLink($id, $data)
    {
        if (array_key_exists('link', $data) &&
                array_key_exists('ui-term-rel', $data)) {

            // Get data
            $linkData = $data['link'];
            $uiTermRelData = $data['ui-term-rel'];

            //--------------------------------------------------------------
            // Update term relationship data
            //--------------------------------------------------------------
            $uiTermRelData['objectType'] = 'link';

            // Do alterations to data
            $uiTermRelModel = new Model_UiTermRel();

            //--------------------------------------------------------------
            // Begin db transaction
            //--------------------------------------------------------------
            $this->_db->beginTransaction();
            try {
                // Update user table
                $this->_linkModel->updateLink($id, $linkData);

                // Update term relationships table
                $uiTermRelModel->updateUiTermRel($id,
                        'link', $uiTermRelData);

                // Success, commit to db
                $this->_db->commit();

                // Return true to the user
                return true;
            }
            catch (Exception $e) {
                $this->_db->rollBack();
                return $e->getMessage();
            }
        }
        else {
            throw new Exception('A key is missing from the '.
                    'data array passed into the update link function of the '.
                    'link service.');
        }

    }

    /**
     * Deletes a Link
     * @param Zend_Db::FETCH_MODE object $linkObj
     * @return int
     */
    public function deleteLink($id)
    {
        $tupleToDelete = $this->getLinkById($id);
        $this->_db->beginTransaction();
        try{
            $this->_linkModel->update(array('parent_id' => '0'),
                    'parent_id="'. $id .'"');
            $this->_linkModel->deleteLink($id);
            $this->_uiTermRelModel->deleteUiTermRel($id, 'link');
            $this->_db->commit();
            return true;
        }
        catch (Exception $e) {
            $this->_db->rollBack();
            return $e->message;
        }
    }

    public function getPrimaryModel() {
        return $this->_linkModel;
    }
    
    /**
     * Returns a cv string of the link in the form: "id|/uri/link"
     * @param mixed $link  object|array
     * @return string
     */
    public function linkToCvString($link) {
        
        // Cast to object if necessary
        if (is_array($link)) {
            $link = (object) $link;
        }
        
        // On a per type basis generation
        if ($link->type == 'uri') {
            $link = $link->link_id .'|'. $link->uri;
        }
        else if ($link->type == 'mvc') {
            $urlHelper = 
                Zend\Mvc\Controller\Action_HelperBroker::getStaticHelper('url');
            $urlOptions = array();

            $routeName = (string) $link->mvc_route;
            unset($link->mvc_route);
            unset($link->mvc_resetParamsOnRender);

            $urlOptions = array('module' => $link->mvc_module,
                'controller' => $link->mvc_controller, 
                'action' => $link->mvc_action);

            $uri = $urlHelper->url($urlOptions, $routeName);
            $link = $link->link_id .'|'. $uri;
        }
        
        return $link;
    }
    
    protected function updateViewModules($id) {
        $vms = $this->getViewModuleService();
        $rslt = $vms->read(array('where' => array(
            'allowedPages LIKE "%'. $id .'%"')));
        
        // If empty bail
        if (empty($rslt)) {
            return;
        }
        
        foreach($rslt as $tuple) {
            // Update each one
        }
    }

}

