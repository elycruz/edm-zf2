<?php

namespace Edm\Service;

use Edm\Service\AbstractService,
    Edm\Model\ViewModule,
    Edm\Service\TermTaxonomyServiceAware,
    Edm\Service\TermTaxonomyServiceAwareTrait,
    Zend\Db\ResultSet\ResultSet,
    Zend\Db\Sql\Sql,
    Zend\Db\TableGateway\Feature\FeatureSet,
    Zend\Db\TableGateway\Feature\GlobalAdapterFeature,
    Zend\Stdlib\DateTime,
    Zend\Debug\Debug;

/**
 * @todo fix composite data column aware interface and trait to use the 
 * @todo start using the db\table->alias for aliases to avoid conflicts and 
 * maintain readability
 * "tuple" language instead of the array language
 * @author ElyDeLaCruz
 */
class ViewModuleService extends AbstractService 
implements \Edm\UserAware,
        \Edm\Db\CompositeDataColumnAware,
        TermTaxonomyServiceAware {
    
    use \Edm\UserAwareTrait,
        \Edm\Db\CompositeDataColumnAwareTrait,
        TermTaxonomyServiceAwareTrait;

    protected $viewModuleTable;
    protected $mixedTermRelTable;
    protected $resultSet;
    protected $notAllowedForUpdate = array(
        'view_module_id',
        'object_id',
        'objectType'
    );

    public function __construct() {
        $this->sql = new Sql($this->getDb());
        $this->resultSet = new ResultSet();
        $this->resultSet->setArrayObjectPrototype(new ViewModule());
    }

    /**
     * Creates a viewModule and it's constituants 
     *  (viewModule and viewModule relationship)
     * @param ViewModule $viewModule
     * @return mixed int | boolean | \Exception
     */
    public function createViewModule(ViewModule $viewModule) {

        // Get current user
        $user = $this->getUser();
        
        // Bail if no user
        if (empty($user)) {
            return false;
        }
        
        // Get some help for cleaning data to be submitted to db
        $dbDataHelper = $this->getDbDataHelper();
        
        // ViewModule Term Rel
        $mixedTermRel = $viewModule->getMixedTermRelProto();
        
        // Created Date
        $today = new DateTime();
        $viewModule->createdDate = $today->getTimestamp();
        
        // Created by
        $viewModule->createdById = $user->user_id;
        
        // If empty alias
        if (empty($viewModule->alias)) {
            $viewModule->alias = $dbDataHelper->getValidAlias($viewModule->title);
        }
        
        // Escape tuples 
        $cleanViewModule = $dbDataHelper->escapeTuple($viewModule->toArray());
        $cleanMixedTermRel = $dbDataHelper->escapeTuple($mixedTermRel->toArray());
        if (is_array($cleanViewModule['userParams'])) {
            $cleanViewModule['userParams'] = 
                    $this->serializeAndEscapeTuples($cleanViewModule['userParams']);
        }

        // Get database platform object
        $driver = $this->getDb()->getDriver();
        $conn = $driver->getConnection();
        
        // Begin transaction
        $conn->beginTransaction();
        try {
            // Create viewModule
            $this->getViewModuleTable()->insert($cleanViewModule);
            $retVal = $view_module_id = $driver->getLastGeneratedValue();
            
            // Create viewModule mixedTermRel rel
            $cleanMixedTermRel['view_module_id'] = $view_module_id;
            $this->getMixedTermRelTable()->insert($cleanMixedTermRel);

            // Commit and return true
            $conn->commit();
        } catch (\Exception $e) {
            $conn->rollback();
            Debug::dump($e->getMessage());
            $retVal = $e;
        }
        return $retVal;
    }

    /**
     * Updates a viewModule and it's constituants
     *   (mixedTermRel and viewModule mixedTermRel relationship).  
     * @todo There are no safety checks being done in this method
     * @param int $id
     * @param ViewModule $viewModule
     * @return mixed boolean | Exception
     */
    public function updateViewModule(ViewModule $viewModule) {
        
        $id = $viewModule->view_module_id;
//        Debug::dump($viewModule);
        // Get Db Data Helper
        $dbDataHelper = $this->getDbDataHelper();
        
        // If empty alias
        if (empty($viewModule->alias)) {
            $viewModule->alias = $dbDataHelper->getValidAlias($viewModule->title);
        }
        
        // Escape tuples 
        $viewModuleData = $dbDataHelper->escapeTuple($this->ensureOkForUpdate($viewModule->toArray()));
        $mixedTermRelData = $dbDataHelper->escapeTuple(
                $this->ensureOkForUpdate($viewModule->getMixedTermRelProto()->toArray()));
        
        // If is array user params serialize it to string
        if (is_array($viewModuleData['userParams'])) {
            $viewModuleData['userParams'] = $this->serializeAndEscapeTuples($viewModuleData['userParams']);
        }
        
        // Get database platform object
        $conn = $this->getDb()->getDriver()->getConnection();
        
        // Begin transaction
        $conn->beginTransaction();
        try {

            // Update mixedTermRel
            if (is_array($mixedTermRelData) && count($mixedTermRelData) > 0) {
                $this->getMixedTermRelTable()
                        ->update($mixedTermRelData, array('view_module_id' => $id));
            }

            // Update viewModule
            $this->getViewModuleTable()->update($viewModuleData, array('view_module_id' => $id));

            // Commit and return true
            $conn->commit();
            $retVal = true;
        } catch (\Exception $e) {
            $conn->rollback();
            $retVal = $e;
        }
        return $retVal;
    }

    /**
     * Deletes a viewModule and depends on RDBMS triggers and cascade rules to delete
     * it's related tables (mixedTermRel and viewModule mixedTermRel rels)
     * @param int $id
     * @return boolean
     */
    public function deleteViewModule($id) {
        // Get database platform object
        $conn = $this->getDb()->getDriver()->getConnection();

        // Begin transaction
        $conn->beginTransaction();
        try {
            // Create viewModule
            $this->getViewModuleTable()->delete(array('view_module_id' => $id));

            // Commit and return true
            $conn->commit();
            $retVal = true;
        } catch (\Exception $e) {
            $conn->rollback();
            $retVal = $e;
        }
        return $retVal;
    }

    /**
     * Gets a viewModule by id
     * @param integer $id
     * @param integer $fetchMode
     * @return mixed array | boolean
     */
    public function getById($id, $fetchMode = AbstractService::FETCH_FIRST_AS_ARRAY) {
        return $this->read(array(
                    'fetchMode' => $fetchMode,
                    'where' => array('viewModule.view_module_id' => $id)));
    }

    /**
     * Fetches a viewModule by screen name
     * @param string $alias
     * @param int $fetchMode
     * @return mixed array | boolean
     */
    public function getByAlias($alias, $fetchMode = AbstractService::FETCH_FIRST_AS_ARRAY) {
        return $this->read(array(
                    'fetchMode' => $fetchMode,
                    'where' => array('viewModule.alias' => $alias)));
    }

    /**
     * Returns our pre-prepared select statement
     * @todo select should include:
     *      parent_name
     *      parent_alias
     *      taxonomy_name
     * @return Zend\Db\Sql\Select
     */
    public function getSelect($sql = null) {
        $sql = $sql !== null ? $sql : $this->getSql();
        $select = $sql->select();
        $termTaxService = $this->getTermTaxService();
        // @todo implement return values only for current role level
        return $select
                
            // View Module 
            ->from(array('viewModule' => $this->getViewModuleTable()->getTable(table)))

            // Mixed Term Rel
            ->join(array('mixedTermRel' => $this->getMixedTermRelTable()->table), 
                    'mixedTermRel.view_module_id=viewModule.view_module_id')

            // Term Taxonomy
            ->join(array('termTax' => $termTaxService->getTermTaxonomyTable()->table),
                    'termTax.term_taxonomy_id=mixedTermRel.term_taxonomy_id',
                    array('term_alias'))

            // Term
            ->join(array('term' => $termTaxService->getTermTable()->table), 
                    'term.alias=termTax.term_alias', array('term_name' => 'name'));
    }

    public function getViewModuleTable() {
        if (empty($this->viewModuleTable)) {
            $feature = new FeatureSet();
            $feature->addFeature(new GlobalAdapterFeature());
            $this->viewModuleTable =
                    new \Zend\Db\TableGateway\TableGateway(
                    'view_modules', $this->getServiceLocator()
                            ->get('Zend\Db\Adapter\Adapter'), $feature);
        }
        return $this->viewModuleTable;
    }
    
    public function getMixedTermRelTable() {
        if (empty($this->mixedTermRelTable)) {
            $feature = new FeatureSet();
            $feature->addFeature(new GlobalAdapterFeature());
            $this->mixedTermRelTable =
                    new \Zend\Db\TableGateway\TableGateway(
                    'mixed_term_relationships', $this->getServiceLocator()
                            ->get('Zend\Db\Adapter\Adapter'), $feature);
        }
        return $this->mixedTermRelTable;
    }

    /**
     * Checks if an alias already exists for a viewModule
     * @param string $alias
     * @return boolean 
     */
    public function checkViewModuleAliasExistsInDb($alias) {
        $rslt = $this->getMixedTermRelTable()->select(
                        array('alias' => $alias))->current();
        if (empty($rslt)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Remove any empty keys and ones in the not ok for update list
     * @param array $data
     * @return array
     */
    public function ensureOkForUpdate(array $data) {
        foreach ($this->notAllowedForUpdate as $key) {
            if (array_key_exists($key, $data) ||
                    (array_key_exists($key, $data) && !isset($data[$key]))) {
                unset($data[$key]);
            }
        }
        return $data;
    }
    
    public function setListOrderForViewModule (ViewModule $viewModule) {
        if (!is_numeric($viewModule->listOrder) || !is_numeric($viewModule->view_module_id)) {
            throw new \Exception('Only numeric values are accepted for ' .
                    __CLASS__ .' -> '. __FUNCTION__ . '.');
        }
        return $this->getViewModuleTable()->update(
                array('listOrder' => $viewModule->listOrder), 
                array('view_module_id' => $viewModule->view_module_id));
    }
    
    public function setTermTaxonomyForViewModule (ViewModule $viewModule, $taxonomyAlias, $value) {
        
        // If input filter is not valid (data in viewModule is not valid) then
        // throw an exception
        if (!$viewModule->getInputFilter()->isValid()) {
            throw new \Exception('ViewModule object received in ' .
                    __CLASS__ .'->'. __FUNCTION__ . ' is invalid.');
            // @todo spit out error messages here
        }
        
        // If taxonomy alias is not valid
        if (!in_array($taxonomyAlias, $viewModule->getValidKeys())) {
            throw new \Exception('"'. $taxonomyAlias . '" is not a valid ' .
                    'field of the viewModule model in "' . 
                    __CLASS__ . '->' . __FUNCTION__ . '"');
        }
        
        // If viewModule id is not set
        if (!is_numeric($viewModule->view_module_id)) {
            throw new \Exception('Only numeric values are accepted for ' .
                    __CLASS__ .'->'. __FUNCTION__ . '\'s $viewModule->view_module_id param.');
        }

        // Check if taxonomy alias indeed has $value else throw error
        $allowedCheck = $this->getTermTaxService()->getByAlias($value, $taxonomyAlias);
        if (empty($allowedCheck)) {
            throw new \Exception('One of the values passed into "' .
                    __CLASS__ .'->'. __FUNCTION__ . '" are not allowed.');
        }
        
        // Update term taxonomy value and return outcome
        return $this->getViewModuleTable()->update(
                array($taxonomyAlias => $value), 
                array('view_module_id' => $viewModule->view_module_id));
    }

}