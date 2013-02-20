<?php
/**
 * @author ElyDeLaCruz
 */
class Edm_Service_Internal_TermRelService extends Edm_Service_Internal_CrudAbstract
{
    /**
     * Secondary model
     * @var Edm_Db_AbstractTable
     */
    protected $_secondaryModel;
    
    public function __construct($options = null) 
    {
        // Gets and stores item count in _itemCountModel
        $this->getItemCountModel();
        
        // Gets and stores the db it in _db
        $this->getDb();
        
        // Get the primary model
        $this->_primaryModel =
                Edm_Db_Table_ModelBroker::getModel('TermRelationship');
        
        // Get our db data helper
        $this->_dbDataHelper = Edm_Util_DbDataHelper::getInstance();
    }
    
    protected function _getModelName($name)
    {
        if (is_string($name)) {
            $name = strtolower($name);
            $name = str_replace(array('-', '_'), ' ', $name);
            $name = preg_replace('/s$/', '', $name);
            $name = ucwords($name);
            $name = str_replace(' ', '', $name);
            return $name;
        }
        throw new Exception('Only strings are allowed for the term ' .
                'relationship service _getModelName');
    }
    
    protected function _getModelByAlias($alias) {
        $tuple = $this->_itemCountModel->getItemCountByAlias($alias);
        
        // If an entry for this model/table alias exists in the item counts
        // table, begin reflection and abstraction for model
        if (!empty($tuple)) {
            $model = '';
            $className = $this->_getModelName($tuple->tableName);
            $className = 'Model_' . $className;
            $model = new $className();
            return $model;
        }
    }

    protected function _getModelMethodPrefix($alias, $method = null) 
    {
        if (!empty($method)) {
            $alias = $alias . $method;
        }
        
        $alias = $this->_getModelName($alias);
        $alias = str_split($alias);
        $firstLetter = $alias[0];
        unset($alias[0]);
        return strtolower($firstLetter) . implode($alias);
    }
    
    public function getSelect() {
        return parent::getSelect()
                ->from(array('termRel' => $this->_primaryModel->getName()))
                ->join(array('termTax' => 'term_taxonomy'), 
                        'termTax.term_taxonomy_id = termRel.term_taxonomy_id',
                        array('term_alias'))
                ->join(array('term' => 'terms'), 
                        'term.term_Id = termTax.term_alias',
                        array('term_taxonomy_alias' => 'alias'));
    }
    
    public function getBySecondaryTypeAndId($object_id, $objectType1,
            $objectType2 = null, $fetchMode = Zend_Db::FETCH_OBJ, 
            $where = null, $sort = null, $sortBy = null, 
            Zend_Db_Select $select = null)
    {
        // Check object id
        if (!is_numeric($object_id)) {
            throw new Exception('The "object_id" value for the getByIdAndType '.
                    'method of the Term Relationship Service must be numeric.'.
                    '  Value received: ' . $object_id);
        }
        
        // Check object type 1
        if (!is_string($objectType1)) {
            throw new Exception('The "objectType1" value for the '.
                    'getByIdAndType method of the Term Relationship Service '.
                    'must be of type string.  Value received: ' . $objectType1);
        }

        // Append to the current where condition if necessary
        if (!empty($where)) {
            if (!is_string($where)) {
                throw new Exception('The "where" value for the getJoinedRow '.
                    'method of the Term Relationship Service must be of '.
                        'type string.  Value received: ' . $where);
            }
            $where .= ' AND termRel.object_id = '. $object_id .
                ' AND termRel.objectType="'. $objectType1 .'"';
        }
        else {
            $where = 'termRel.object_id = '. $object_id .
                ' AND termRel.objectType="'. $objectType1 .'"';
        }
        
        // Get the select statement
        $select = empty($select) ? $this->getSelect() : $select;
         
        // Get id column name
        $idColumnName = str_replace('-', '_', $objectType1) . '_id';

        // If object type 2 is not null
        if (!empty($objectType2)) {
            // Get tertiary table
            $tertiaryTable = Edm_Db_Table_ModelBroker::getModel($objectType2);
            $select->join(array('tertiary' => $tertiaryTable->getName()),
                    '`tertiary`.`'. $idColumnName .'`='. $object_id);
        }

        // Set where
        $select->where($where);

//        // Order
//        $orderBy = '';
//        if (!empty($sortBy)) {
//            $orderBy .= $sortBy;
//        }
//
//        // Order Direction
//        if (!empty($sort)) {
//            $sort = $sort ? 'ASC' : 'DESC';
//            $orderBy .= ' '. $sort;
//        }
//        
//        // Set order
//        if (!empty($orderBy)) {
//            $select->order($orderBy);
//        }
        
        // Return result
        return $select->query($fetchMode)->fetchAll();
    }
    
    public function setStatus($id, $objectType, $status) {
        return $this->_primaryModel->updateTermRelationship($id, $objectType,
                array('status' => $status));
    }

    public function setAccessGroup($id, $objectType, $accessGroup) {
        return $this->_primaryModel->updateTermRelationship($id, $objectType,
                array('accessGroup' => $accessGroup));
    }
    
    public function setTermTaxonomyId($id, $objectType, $termTaxId) {
        return $this->_primaryModel->updateTermRelationship($id, $objectType,
                array('term_taxonomy_id' => $termTaxId));
    }

    public function setListOrder($id, $objectType, $listOrder) {
        return $this->_primaryModel->updateTermRelationship($id, $objectType,
                array('listOrder' => $listOrder));
    }
    
}
