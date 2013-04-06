<?php

/**
 * Description of Comment
 *
 * @author ElyDeLaCruz
 */
class Model_Comment extends Edm_Db_AbstractTable {

    protected $_name = 'comments';

    public function createComment(array $data) {
        $this->insert($data);
        return $this->getAdapter()->lastInsertId();
    }

    public function updateComment($id, array $data) {
        return $this->update($data, $this->getWhereClauseFor($id, 'comment_id'));
    }

    public function deleteComment($id) {
        return $this->delete(
                        $this->getWhereClauseFor($id, 'comment_id'));
    }

    public function getCompiledComments($post_id, $parent_id = 0, $status = 'approved') 
    {
        // Set up a where caluse 
        $where = 'post_id="' . $post_id . '" AND parent_id="'. $parent_id .'"';

        if (!empty($status)) {
            $where .= ' AND status="' . $status . '"';
        }

        $rslt = $this->getAdapter()->select()
                ->from($this->_name)->where($where)
                ->query(Zend_Db::FETCH_ASSOC)->fetchAll();
        $comments = array();
        
        foreach ($rslt as $comment) {
            $childComments = $this
                ->getCompiledComments($post_id, $comment['comment_id']);
            
            if (!empty($childComments)) {
                $comment['comments'] = $childComments;
            }
            
            $comments[] = $comment;
        }
        
        return $comments;
    }

}
