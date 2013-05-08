<?php

namespace Edm\Service;

use Edm\Service\AbstractService,
    Edm\Model\Post,
    Zend\Db\ResultSet\ResultSet,
    Zend\Db\Sql\Sql,
    Zend\Db\TableGateway\Feature\FeatureSet,
    Zend\Db\TableGateway\Feature\GlobalAdapterFeature,
    Zend\Stdlib\DateTime,
        Zend\Debug\Debug;

/**
 * @author ElyDeLaCruz
 */
class PostService extends AbstractService 
implements \Edm\UserAware,
        \Edm\Db\CompositeDataColumnAware {
    
    use \Edm\UserAwareTrait,
            \Edm\Db\CompositeDataColumnAwareTrait;

    protected $postTable;
    protected $postTermRelTable;
    protected $resultSet;
    protected $notAllowedForUpdate = array(
        'post_id',
    );

    public function __construct() {
        $this->sql = new Sql($this->getDb());
        $this->resultSet = new ResultSet();
        $this->resultSet->setArrayObjectPrototype(new Post());
    }

    /**
     * Creates a post and it's constituants 
     *  (post and post relationship)
     * @param Post $post
     * @return mixed int | boolean | \Exception
     */
    public function createPost(Post $post) {

        // Get current user
        $user = $this->getUser();
        
        // Bail if no user
        if (empty($user)) {
            return false;
        }
        
        // Post Term Rel
        $postTermRel = $post->getPostTermRelProto();
        
        // Created Date
        $today = new DateTime();
        $post->createdDate = $today->getTimestamp();
        
        // Created by
        $post->createdById = $user->user_id;
        $post->userParams = '';

        // Escape tuples 
        $dbDataHelper = $this->getDbDataHelper();
        $cleanPost = $dbDataHelper->escapeTuple($post->toArray());
        $cleanPostTermRel = $dbDataHelper->escapeTuple($postTermRel->toArray());

        // Get database platform object
        $driver = $this->getDb()->getDriver();
        $conn = $driver->getConnection();
        
        var_dump($cleanPost);

        // Begin transaction
        $conn->beginTransaction();
        try {
            // Create post
            $this->getPostTable()->insert($cleanPost);
            $retVal = $post_id = $driver->getLastGeneratedValue();
            
            // Create post postTermRel rel
            $cleanPostTermRel['post_id'] = $post_id;
            $this->getPostTermRelTable()->insert($cleanPostTermRel);

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
     * Updates a post and it's constituants
     *   (postTermRel and post postTermRel relationship).  
     * @todo There are no safety checks being done in this method
     * @param int $id
     * @param Post $post
     * @return mixed boolean | Exception
     */
    public function updatePost($id, Post $post) {

        // Escape tuples 
        $dbDataHelper = $this->getDbDataHelper();
        $post = $dbDataHelper->escapeTuple($this->ensureOkForUpdate($post->toArray()));
        $postTermRel = $dbDataHelper->escapeTuple(
                $this->ensureOkForUpdate($post->getPostTermRelProto()->toArray()));

        // Get database platform object
        $conn = $this->getDb()->getDriver()->getConnection();

        // Begin transaction
        $conn->beginTransaction();

        try {

            // Update postTermRel
            if (is_array($postTermRel) && count($postTermRel) > 0) {
                $this->getPostTermRelTable()
                        ->update($postTermRel, array('post_id' => $id));
            }

            // Update post
            $this->getPostTable()->update($post, array('post_id' => $id));

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
     * Deletes a post and depends on RDBMS triggers and cascade rules to delete
     * it's related tables (postTermRel and post postTermRel rels)
     * @param int $id
     * @return boolean
     */
    public function deletePost($id) {
        // Get database platform object
        $conn = $this->getDb()->getDriver()->getConnection();

        // Begin transaction
        $conn->beginTransaction();
        try {
            // Create post
            $this->getPostTable()->delete(array('post_id' => $id));

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
     * Gets a post by id
     * @param integer $id
     * @param integer $fetchMode
     * @return mixed array | boolean
     */
    public function getById($id, $fetchMode = AbstractService::FETCH_FIRST_AS_ARRAY) {
        return $this->read(array(
                    'fetchMode' => $fetchMode,
                    'where' => array('post.post_id' => $id)));
    }

    /**
     * Fetches a post by screen name
     * @param string $alias
     * @param int $fetchMode
     * @return mixed array | boolean
     */
    public function getByAlias($alias, $fetchMode = AbstractService::FETCH_FIRST_AS_ARRAY) {
        return $this->read(array(
                    'fetchMode' => $fetchMode,
                    'where' => array('post.alias' => $alias)));
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
        // @todo implement return values only for current role level
        return $select
                ->from(array('post' => $this->getPostTable()->table))
                ->join(array('postTermRel' => 
                    $this->getPostTermRelTable()->table), 
                        'postTermRel.post_id=post.post_id');
    }

    public function getPostTable() {
        if (empty($this->postTable)) {
            $locator = $this->getServiceLocator();
            $this->postTable = $locator->get('Edm\Db\Table\PostTable');
        }
        return $this->postTable;
    }

    public function getPostTermRelTable() {
        if (empty($this->postTermRelTable)) {
            $feature = new FeatureSet();
            $feature->addFeature(new GlobalAdapterFeature());
            $this->postTermRelTable =
                    new \Zend\Db\TableGateway\TableGateway(
                    'post_category_relationships', $this->getServiceLocator()
                            ->get('Zend\Db\Adapter\Adapter'), $feature);
        }
        return $this->postTermRelTable;
    }

    /**
     * Checks if an alias already exists for a post
     * @param string $alias
     * @return boolean 
     */
    public function checkPostAliasExistsInDb($alias) {
        $rslt = $this->getPostTermRelTable()->select(
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
                    (array_key_exists($key, $data) && empty($data[$key]))) {
                unset($data[$key]);
            }
        }
        return $data;
    }

}