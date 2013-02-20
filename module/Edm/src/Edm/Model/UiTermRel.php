<?php
/**
 * Our user model for application
 *
 * @author ElyDeLaCruz
 */
class Model_UiTermRel extends Edm_Db_AbstractTable
{
    protected $_name = 'ui_term_relationships';

    public function createUiTermRel(array $data)
    {
        $this->insert($data);
        return $this->getAdapter()->lastInsertId();
    }

    public function updateUiTermRel($id, $objectType, array $data) {
        return $this->update($data,
                'object_id="'. $id .'" AND objectType="'.
                    $objectType .'"');
    }

    public function deleteUiTermRel($id, $objectType) {
        return $this->delete('object_id="'. $id .'" AND ' .
                'objectType="'. $objectType .'"');
    }
    
    public function setListOrder($id, $objectType, $value) {
        return $this->update(array('listOrder' => $value),
                'object_id="'. $id .'" AND ' .
                'objectType="' . $objectType .'"');
    }
    
    public function setTermTaxonomyId($id, $objectType, $termTaxId) {
        return $this->update(array('term_taxonomy_id' => $termTaxId),
                'object_id="'. $id .'" AND ' .
                'objectType="' . $objectType .'"');
    }

    public function setStatus($id, $objectType, $status) {
        return $this->update(array('status' => $status),
                'object_id="'. $id .'" AND ' .
                'objectType="' . $objectType .'"');
    }
    
    public function setAccessGroup($id, $objectType, $value) {
        return $this->update(array('accessGroup' => $value),
                'object_id="'. $id .'" AND ' .
                'objectType="' . $objectType .'"');
    }
    
}


