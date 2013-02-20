<?php
/**
 * Description of Abstract
 * @author ElyDeLaCruz
 */
abstract class Edm_Db_AbstractTable extends Zend_Db_Table_Abstract
{
    /**
     * Returns the table name
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }
    
    /**
     * Get a where clause for a column
     * @param mixed $colValue
     * @param string $colName
     * @return
     */
    public function getWhereClauseFor($colValue, $colName)
    {
        return $this->getAdapter()->quoteInto($colName .' = ?', $colValue);
    }

    /**
     * Get row by column name
     * @param mixed $colValue
     * @param string $colName
     * @param int $fetchMode default Zend_Db::FETCH_ASSOC
     * @return Zend_Db::FETCH_*
     */
    public function getByColumn($colValue, $colName,
            $fetchMode = Zend_Db::FETCH_ASSOC)
    {
        return 
            $this->select()->where(
                    $this->getWhereClauseFor($colValue, $colName))
            ->query($fetchMode)->fetch();
    }
}