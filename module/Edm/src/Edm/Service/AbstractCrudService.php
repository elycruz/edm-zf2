<?php
/**
 * @author ElyDeLaCruz
 */
abstract class Edm_Service_Internal_AbstractCrudService
extends Edm_Service_Internal_AbstractService
implements Edm_Service_Internal_CrudInterface
{
    /**
     * Get the default db adapter
     * @var Zend_Db_Adatper
     */
    protected $db;
    
    /**
     * Database helper
     * @var Edm_Util_DbDataHelper
     */
    protected $dbDataHelper;
    
    /**
     * Info schema adapter
     * @var Zend_Db_Adapter_Abstract
     */
    protected $infoSchema;
    
    /**
     * Alias pattern
     * for aliases which are used throughout our application;  Url aliases; 
     * Item aliases;  User aliases; etc..
     * @var string regular expression
     */
    protected $aliasRegex = '/[^\-\w\d_]/i';
    
    /**
     * Select statement
     * @var Zend_Db_Select
     */
    protected $select;
    
    /**
     * Reads the service using the return value of getSelect() as its default 
     * select object.
     * @param array options {
     *      string $where,
     *      int $fetchMode default Zend_Db::FETCH_*,
     *      int $sort,
     *      string $sortBy,
     *      Zend_Db_Select $select,
     * }
     * @return array
     */
    public function read(array $options = null) {
        $options = $this->getCompiledReadOptions($options);
        return $options->select->query($options->fetchMode)->fetchAll();
    }
    
    /**
     * Returns a tuple by column name and value.
     * @param type $columnValue
     * @param type $columnName
     * @param type $tableAlias
     * @param type $fetchMode
     * @return Zend_Db_Table_Row
     */
    public function getByColumn($columnName, $columnValue,
            $tableAlias, $fetchMode = Zend_Db::FETCH_ASSOC) 
    {
        return $this->getSelect()
            ->where($tableAlias .'.'. 
                    $columnName .'=?', $columnValue)
                ->query($fetchMode)->fetch();
    }
    
    /**
     * Returns a select object from this services _db object (usually overridden
     * by extending class).
     * @return Zend_Db_Table_Select 
     */
    public function getSelect() 
    {
        return $this->getDb()->select();
    }
    
    /**
     * Gets our db instance
     * @return Zend_Db 
     */
    public function getDb() 
    {
        $db = $this->db;
        if (empty($db)) {
            if (Zend_Registry::isRegistered('edm-db')) {
                $db = Zend_Registry::get('edm-db');
            }
            else {
                $db = Zend_Db_Table::getDefaultAdapter();
                Zend_Registry::set('edm-db', $db);
            }
        }
        return $this->db = $db;;
    }

    /**
     * Sets our db and returns this service
     * @param type $db
     * @return Edm_Service_Internal_CrudAbstract 
     */
    public function setDb(Zend_Db_Adapter_Abstract $db) 
    {
        $this->db = $db;
        Zend_Registry::set('edm-db', $db);
        return $this;
    }
    
    public function getDbDataHelper() {
        if (empty($this->dbDataHelper)) {
            if (Zend_Registry::isRegistered('edm-dbDataHelper')) {
                $dbDataHelper = Zend_Registry::get('edm-dbDataHelper');
            }
            else {
                $dbDataHelper = new Edm_Db_DatabaseDataHelper();
                Zend_Registry::set('edm-dbDataHelper', $dbDataHelper);
            }
            $this->dbDataHelper = $dbDataHelper;
        }
        return $this->dbDataHelper;
    }

    public function setDbDataHelper(Edm_Db_DbDataHelper $dbDataHelper) {
        $this->dbDataHelper = $dbDataHelper;
        Zend_Registry::set('edm-dbDataHelper', $dbDataHelper);
    }
    
    public function setAliasPattern($value) {
        $this->aliasRegex = $value;
        return $this;
    }
    
    public function getAliasPattern() {
        return $this->aliasRegex;
    }
    
    public function generateValidAlias($str) 
    {
        $str = trim($str);
        $str = substr($str, 0, 255);
        $str = preg_replace($this->aliasRegex, '-', $str);
        $str = strtolower($str);
        return $str;
    }
    
    /**
     * Compiles the order by and where parts of the select (common to all 
     * edm crud service classes as well as provides common defaults for them)
     * put here for ease of use.
     * @param array $options default stdClass if null
     *          - fetchMode default Zend_Db::FETCH_ASSOC
     *          - sortBy default ''
     *          - sort default 'DESC'
     *          - where default ''
     * @return stdClass 
     */
    protected function getCompiledReadOptions(array $options = null)
    {
        $options = !empty($options) ? (object) $options : new stdClass();
        $select = empty($options->select) ?  
                $this->getSelect() : $options->select;
        
        // Get our values
        $sortBy = isset($options->sortBy) ? $options->sortBy : '';
        $sort = !isset($options->sort) ? 'DESC' : 'ASC';
        $where = isset($options->where) ? $options->where : '';
        
        // Fetch mode
        if (!isset($options->fetchMode) || 
                !is_numeric($options->fetchMode)) {
            $options->fetchMode = Zend_Db::FETCH_ASSOC;
        }   
        
        // If where
        if (!empty($where)) {
            $select->where($where);
        }
        
        // If order by
        $orderBy = '';
        if (!empty($sortBy)) {
            $orderBy .= $sortBy;
        }
        
        // If sort
        if (!isset($sort)) {
            $orderBy .= ' '. $sort;
        }
        
        // Order by
        if (!empty($orderBy)) {
            $select->order($orderBy);
        }
        
        $options->select = $select;
        return $options;
    }
    
    /**
     * Returns the count for the model using the information schema
     * @param Edm_Db_AbstractTable $model
     * @return int 
     */
    public function getRowCount(Edm_Db_AbstractTable $model) {
        $rslt = $this->getInfoSchema()->select()->from('tables', 'TABLE_ROWS')
                ->where('TABLE_NAME="'. $model->getName() .'" AND ' .
                        'TABLE_SCHEMA="'. DB_NAME .'"')
                ->query(Zend_Db::FETCH_OBJ)->fetch();
        
        if (empty($rslt)) {
            return 0;
        }
        
        return ((int) $rslt->TABLE_ROWS);
    }
    
    public function getInfoSchema() {
        if (empty($this->infoSchema)) {
            if (Zend_Registry::isRegistered('edm-infoSchema')) {
                $this->infoSchema = Zend_Registry::get('edm-infoSchema');
            } else {
                $config = new Zend_Config_Ini(APPLICATION_PATH .'/configs/' .
                        'edm-admin/infoschema.ini', APPLICATION_ENV);
                $this->infoSchema = Zend_Db::factory($config);
                Zend_Registry::set('edm-infoSchema', $this->infoSchema);
            }
        }
        return $this->infoSchema;
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
    
}