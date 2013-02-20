<?php
/**
 * Description of Edm_Service_Internal_ViewModuleService
 * Secondary db table alias is called 'secondary'
 * @author ElyDeLaCruz
 */
class Edm_Service_Internal_ViewModuleService extends
Edm_Service_Internal_AbstractCrudService
{
    protected $_viewModuleModel;
    
    /**
     * Secondary Model
     * @var Edm_Db_AbstractTable
     */
    protected $_secondaryModel;
    protected $_uiTermRelModel;
    protected $_termTaxonomyModel;

    public function __construct()
    {
        $this->_viewModuleModel = 
                Edm_Db_Table_ModelBroker::getModel('view-module');
        $this->_uiTermRelModel = 
                Edm_Db_Table_ModelBroker::getModel('ui-term-rel');
    }

    public function createViewModule(array $data)
    {
        if (!array_key_exists('view-module', $data) ||
            !array_key_exists('ui-term-rel', $data)) {
            throw new Exception('A key is missing from the '.
                'data array passed into the create module function of the '.
                'module service.');
        }

        // Get data
        $moduleData = $data['view-module'];
        $uiTermRelData = $data['ui-term-rel'];

        // Created by
        $moduleData['createdById'] = 0;

        // Created date
        $moduleData['createdDate'] = Zend_Date::now()->getTimestamp();

        //--------------------------------------------------------------
        // Update term relationship data
        //--------------------------------------------------------------
        // Get item count model and use it's `user` table `itemCount` to
        $uiTermRelData['listOrder'] = 
            $this->getRowCount($this->_viewModuleModel) + 1;

        // Set the `objectType` for term relationship
        $uiTermRelData['objectType'] = 'view-module';
        
        // Check if this module has a secondary table or table that extends it
        if (array_key_exists('secondary', $data)) {
            $secondaryData = $data['secondary'];
            $secondaryModel = 
                Edm_Db_Table_ModelBroker::getModel(
                        $secondaryData['modelAlias']);
            $secondaryData = $secondaryData['data'];
        }

        //--------------------------------------------------------------
        // Begin db transaction
        //--------------------------------------------------------------
        $db = $this->getDb();
        $db->beginTransaction();
        try {
            // Update user table
            $objectId = $this->_viewModuleModel->createViewModule($moduleData);
            
            // Secondary model
            if (!empty($secondaryModel)) {
                $secondaryData['view_module_id'] = $objectId;
                $secondaryModel->insert($secondaryData);
            }

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
            return $e;
        }
    }

    public function getById($id, $fetchMode = Zend_Db::FETCH_ASSOC)
    {
        return $this->getSelect()
                ->where('viewModule.view_module_id=?', $id)
                ->query($fetchMode)->fetch();
    }

    public function getByAlias($alias, $fetchMode = Zend_DB::FETCH_ASSOC) 
    {
        return $this->getSelect()
                ->where('viewModule.alias=?', $alias)->query($fetchMode)->fetch();

    }

    public function getSelect() 
    {
        $select = $this->getDb()->select()
                ->from(array('viewModule' => $this->_viewModuleModel->getName()), array('*'))
                ->join(array('uiTermRel' => $this->_uiTermRelModel->getName()),
                        'uiTermRel.object_id = viewModule.view_module_id AND '.
                        'uiTermRel.objectType = "view-module"');
        
        // If secondary model
        if (!empty($this->_secondaryModel)) {
            $select->join(array(
                'secondary' => $this->_secondaryModel->getName()),
                    'secondary.view_module_id = viewModule.view_module_id');
        }

        // @todo make this table name dynamic
        return $select->join(array('termTax' => 
            $this->getTermTaxonomyModel()->getName()), 
                'termTax.term_taxonomy_id = uiTermRel.term_taxonomy_id',
                array('term_alias'))
        ->join(array('term' => 'terms'), 
                'term.alias = termTax.term_alias',
                array('term_name' => 'name'));    
    }

    public function updateViewModule($id, $data)
    {
        if (array_key_exists('view-module', $data) &&
                array_key_exists('ui-term-rel', $data)) {

            // Get data
            $moduleData = $data['view-module'];
            $uiTermRelData = $data['ui-term-rel'];

            //--------------------------------------------------------------
            // Update module data
            //--------------------------------------------------------------
            $moduleData['lastUpdatedById'] = 0;
            $moduleData['lastUpdated'] = Zend_Date::now()->getTimestamp();

            //--------------------------------------------------------------
            // Update term relationship data
            //--------------------------------------------------------------
            $uiTermRelData['objectType'] = 'view-module';
            
            // Check if this module has a secondary table or table that extends it
            if (array_key_exists('secondary', $data)) {
                $secondaryData = $data['secondary'];
                $secondaryModel = 
                    Edm_Db_Table_ModelBroker::getModel(
                            $secondaryData['modelAlias']);
                $secondaryData = $secondaryData['data'];
            }

            //--------------------------------------------------------------
            // Begin db transaction
            //--------------------------------------------------------------
            $db = $this->getDb();
            $db->beginTransaction();
            try {
                // Update user table
                $this->_viewModuleModel->updateViewModule($id, $moduleData);

                // Update term relationships table
                $this->_uiTermRelModel->updateUiTermRel($id, 'view-module',
                        $uiTermRelData);
                
                // Secondary model
                if (!empty($secondaryModel)) {
                    $secondaryModel->update($secondaryData, 
                            'view_module_id="' . $id .'"');
                }

                // Success, commit to db
                $db->commit();

                // Return true to the user
                return true;
            }
            catch (Exception $e) {
                $db->rollBack();
                return $e;
            }
        }
        else {
            throw new Exception('A key is missing from the '.
                    'data array passed into the update module function of the '.
                    'module service.');
        }
    }

    public function deleteViewModule($id)
    {
        $tupleToDelete = $this->getById($id);
        
        // Throw an error if tuple doesn't exist
        if (empty($tupleToDelete)) {
            throw new Exception('View module with id &quot;'. $id .'&quot;' .
                    ' could not be deleted');
        }
        
        $db = $this->getDb();
        $db->beginTransaction();
        try{
            $this->_viewModuleModel->deleteViewModule($id);
            $this->_uiTermRelModel->deleteUiTermRel($id, 'view-module');
            if (!empty($this->_secondaryModel)) {
                $this->_secondaryModel
                        ->delete($this->_secondaryModel
                                ->getWhereClauseFor($id, 'view_module_id'));
            }
            
            $db->commit();
            return true;
        }
        catch (Exception $e) {
            $db->rollBack();
            return $e->message;
        }
    }
    
    public function setListOrder($id, $listOrder) {
        $viewModule = $this->getById($id);
        if (!empty($viewModule)) {
            return $this->_uiTermRelModel
                    ->setListOrder($id, 'view-module', $listOrder);
        }
        else {
            return false;
        }
    }
    
    public function setTermTaxonomyId($id, $term_taxonomy_id) {
        $viewModule = $this->getById($id);
        if (!empty($viewModule)) {
            return $this->_uiTermRelModel
                    ->setTermTaxonomyId($id, 'view-module', $term_taxonomy_id);
        }
        else {
            return false;
        }
    }
    
    public function setStatus($id, $status) {
        $viewModule = $this->getById($id);
        if (!empty($viewModule)) {
            return $this->_uiTermRelModel
                    ->setStatus($id, 'view-module', $status);
        }
        else {
            return false;
        }
    }
    
    public function getPrimaryModel() 
    {
        return $this->_viewModuleModel;
    }
    
    public function setSecondaryModel(Edm_Db_AbstractTable $model)
    {
        $this->_secondaryModel = $model;;
    }
    
    public function getSecondaryModel() 
    {
        return $this->_secondaryModel;
    }
    
    public function getTermTaxonomyModel() {
        if (empty($this->_termTaxonomyModel)) {
            $this->_termTaxonomyModel = Edm_Db_Table_ModelBroker::getModel('term-taxonomies');
        }
        return $this->_termTaxonomyModel;
    }

}

