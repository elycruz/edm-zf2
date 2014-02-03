<?php

namespace Edm\Service;

use Edm\Service\AbstractService,
    Edm\Model\Post,
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
class PostService extends AbstractService 
implements \Edm\UserAware,
        \Edm\Db\CompositeDataColumnAware,
        TermTaxonomyServiceAware {
    
    use \Edm\UserAwareTrait,
        \Edm\Db\CompositeDataColumnAwareTrait,
        \Edm\Db\Table\DateInfoTableAwareTrait,
        TermTaxonomyServiceAwareTrait;

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
//        if (empty($user)) {
//            return false;
//        }
        
        // Get some help for cleaning data to be submitted to db
        $dbDataHelper = $this->getDbDataHelper();
        
        // Post Term Rel
        $postTermRel = $post->getPostTermRelProto();
               
        // If empty user params
        if (!isset($post->userParams)) {
            $post->userParams = '';
        }

        // If empty alias
        if (empty($post->alias)) {
            $post->alias = $dbDataHelper->getValidAlias($post->title);
        }
        
        // Escape tuples 
        $cleanPost = $dbDataHelper->escapeTuple($post->toArray());
        $cleanPostTermRel = $dbDataHelper->escapeTuple($postTermRel->toArray());
        if (is_array($cleanPost['userParams'])) {
            $cleanPost['userParams'] = $this->serializeAndEscapeTuples($cleanPost['userParams']);
        }

        // Get database platform object
        $driver = $this->getDb()->getDriver();
        $conn = $driver->getConnection();
        
        // Begin transaction
        $conn->beginTransaction();
        try {
            // Insert date info
            $today = new \DateTime();
            $this->getDateInfoTable()->insert(
                    array('createdDate' => $today->getTimestamp(), 
                          'createdById' => '0'));
            
            // Get date_info_id for post
            $cleanPost['date_info_id'] = $driver->getLastGeneratedValue();
            
            // Create post
            $this->getPostTable()->insert($cleanPost);
            $retVal = $post_id = $driver->getLastGeneratedValue();
            
            // Create post postTermRel rel
            $cleanPostTermRel['post_id'] = $post_id;
            $this->getPostTermRelTable()->insert($cleanPostTermRel);

            // Commit and return true
            $conn->commit();
        } 
        catch (\Exception $e) {
            $conn->rollback();
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
    public function updatePost(Post $post) {
        
        $id = $post->post_id;

        // Get Db Data Helper
        $dbDataHelper = $this->getDbDataHelper();
        
        // If empty alias
        if (empty($post->alias)) {
            $post->alias = $dbDataHelper->getValidAlias($post->title);
        }
        
        // Escape tuples 
        $postData = $dbDataHelper->escapeTuple($this->ensureOkForUpdate($post->toArray()));
        $postTermRelData = $dbDataHelper->escapeTuple(
                $this->ensureOkForUpdate($post->getPostTermRelProto()->toArray()));
        
        // If is array user params serialize it to string
        if (is_array($postData['userParams'])) {
            $postData['userParams'] = $this->serializeAndEscapeTuples($postData['userParams']);
        }
        
        // Db driver
        $driver = $this->getDb()->getDriver();
        
        // Get database platform object
        $conn = $driver->getConnection();
        
        // Begin transaction
        $conn->beginTransaction();
        try {
            // Insert date info
            $today = new \DateTime();
            $this->getDateInfoTable()->insert(
                    array('lastUpdated' => $today->getTimestamp(), 
                          'lastUpdatedById' => '0'));
            
            // Get date_info_id for post
            $cleanPost['date_info_id'] = $driver->getLastGeneratedValue();
            
            // Update postTermRel
            if (is_array($postTermRelData) && count($postTermRelData) > 0) {
                $this->getPostTermRelTable()
                        ->update($postTermRelData, array('post_id' => $id));
            }

            // Update post
            $this->getPostTable()->update($postData, array('post_id' => $id));

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
        $termTaxService = $this->getTermTaxService();
        // @todo implement return values only for current role level
        return $select
                ->from(array('post' => $this->getPostTable()->table))
                ->join(array('postTermRel' => 
                    $this->getPostTermRelTable()->table), 
                        'postTermRel.post_id=post.post_id',
                        array('term_taxonomy_id'))
        
            // Date Info Table
            ->join(array('dateInfo' => $this->getDateInfoTable()->table),
                'post.date_info_id=dateInfo.date_info_id', array(
                    'createdDate', 'createdById', 'lastUpdated', 'lastUpdatedById'))
                
            // Term Taxonomy
            ->join(array('termTax' => $termTaxService->getTermTaxonomyTable()->table),
                    'termTax.term_taxonomy_id=postTermRel.term_taxonomy_id',
                    array('term_alias'))
                
            // Term
            ->join(array('term' => $termTaxService->getTermTable()->table), 
                    'term.alias=termTax.term_alias', array('term_name' => 'name'));
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
                    (array_key_exists($key, $data) && !isset($data[$key]))) {
                unset($data[$key]);
            }
        }
        return $data;
    }
    
    public function setListOrderForPost (Post $post) {
        if (!is_numeric($post->listOrder) || !is_numeric($post->post_id)) {
            throw new \Exception('Only numeric values are accepted for ' .
                    __CLASS__ .' -> '. __FUNCTION__ . '.');
        }
        return $this->getPostTable()->update(
                array('listOrder' => $post->listOrder), 
                array('post_id' => $post->post_id));
    }
    
    public function setTermTaxonomyForPost (Post $post, $taxonomyAlias, $value) {
        
        // If input filter is not valid (data in post is not valid) then
        // throw an exception
        if (!$post->getInputFilter()->isValid()) {
            throw new \Exception('Post object received in ' .
                    __CLASS__ .'->'. __FUNCTION__ . ' is invalid.');
            // @todo spit out error messages here
        }
        
        // If taxonomy alias is not valid
        if (!in_array($taxonomyAlias, $post->getValidKeys())) {
            throw new \Exception('"'. $taxonomyAlias . '" is not a valid ' .
                    'field of the post model in "' . 
                    __CLASS__ . '->' . __FUNCTION__ . '"');
        }
        
        // If post id is not set
        if (!is_numeric($post->post_id)) {
            throw new \Exception('Only numeric values are accepted for ' .
                    __CLASS__ .'->'. __FUNCTION__ . '\'s $post->post_id param.');
        }

        // Check if taxonomy alias indeed has $value else throw error
        $allowedCheck = $this->getTermTaxService()->getByAlias($value, $taxonomyAlias);
        if (empty($allowedCheck)) {
            throw new \Exception('One of the values passed into "' .
                    __CLASS__ .'->'. __FUNCTION__ . '" are not allowed.');
        }
        
        // Update term taxonomy value and return outcome
        return $this->getPostTable()->update(
                array($taxonomyAlias => $value), 
                array('post_id' => $post->post_id));
    }

}