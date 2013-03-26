<?php

/**
 * Description of Post
 *
 * @author ElyDeLaCruz
 */
class Model_Post
    extends Edm_Db_AbstractTable
{
    protected $_name = 'posts';

    /**
     * Creates a post entry in our db
     * @param array $data
     * @return integer
     */
    public function createPost(array $data) {
        $this->insert($data);
        return $this->getAdapter()->lastInsertId();
    }

    public function updatePost($id, array $data) {
        return $this->update($data,
                $this->getWhereClauseFor($id, 'post_id'));
    }

    public function deletePost($id) {
        return $this->delete(
                $this->getWhereClauseFor($id, 'post_id'));
        
    }
    
    public function setListOrder($id, $listOrder) {
        return $this->update(array('listOrder' => $listOrder),
                $this->getWhereClauseFor($id, 'post_id'));
    }
}
