<?php

declare(strict_types=1);

namespace Edm\Service;

use 
    Zend\Db\ResultSet\ResultSet,
    Zend\Db\Sql\Sql,
    Edm\Db\ResultSet\Proto\PostProto,
    Edm\Db\TableGateway\DateInfoTableAware,
    Edm\Db\TableGateway\DateInfoTableAwareTrait,
    Edm\Service\TermTaxonomyServiceAware,
    Edm\Service\TermTaxonomyServiceAwareTrait,
    Edm\Filter\Slug,
    Edm\UserAware,
    Edm\UserAwareTrait;

class PostService extends AbstractCrudService
    implements DateInfoTableAware, TermTaxonomyServiceAware, UserAware {

    use DateInfoTableAwareTrait,
        TermTaxonomyServiceAwareTrait,
        UserAwareTrait;

    /**
     * @var \Edm\Db\TableGateway\PostTale
     */
    protected $_postTable;
    protected $_postTableName;
    protected $_postTableAlias;
    
    /**
     * @var \Edm\Db\TableGateway\PostCategoryRelTable
     */
    protected $_postCategoryRelTable;
    
    /**
     * @var string
     */
    protected $_postCategoryRelTableName;
    
    /**
     * @var string
     */
    protected $_postCategoryRelTableAlias;
    
    /**
     * @var string
     */
    protected $_termTableName;
    
    /**
     * @var string
     */
    protected $_termTableAlias;
    
    /**
     * @var string
     */
    protected $_termTaxonomyTableName;
    
    /**
     * @var string
     */
    protected $_termTaxonomyTableAlias;
    
    /**
     * @var string
     */
    protected $_dateInfoTableName;
    
    /**
     * @var string
     */
    protected $_dateInfoTableAlias;
    
    /**
     * @var bool
     */
    protected $_populatedTableNamesAndAliases = false;

    public function __construct () {
        // Customize Result Set instance
        $this->resultSet = new ResultSet();
        $this->resultSet->setArrayObjectPrototype(new PostProto());
    }
    
     /**
     * Creates a post and it's constituants (post and post relationship)
     * @param Post $post
     * @return mixed int | boolean | \Exception
     */
    public function createPost(PostProto $post) {
        // Get current user
//        $user = $this->getUser();
        
        // Bail if no user
//        if (empty($user)) {
//            return false;
//        }
        
        // Get slugger
        $slugger = new Slug();
                
        // Post Category Rel
        $postCategoryRel = $post->getPostCategoryRelProto();
        
        // If empty alias
        if (empty($post->alias)) {
            $post->alias = $slugger($post->title);
        }
        
        // Get some help for cleaning data to be submitted to db
        $this->getDbDataHelper()->escapeTuple($post);
        
//        if (is_array($cleanPost['userParams'])) {
//            $cleanPost['userParams'] = $this->serializeAndEscapeTuples($cleanPost['userParams']);
//        }
        
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
                          'createdById' => 0)); //$user->user_id));
            
            // Get date_info_id for post
            $post->date_info_id = $driver->getLastGeneratedValue();
            
            // Create post
            $this->getPostTable()->insert($post->toArray(
                    PostProto::FOR_OPERATION_DB_INSERT));
            
            // Get last inserted id
            $post_id = $driver->getLastGeneratedValue();
            
            // Create post category rel entry
            $postCategoryRel->post_id = $post_id;
            $this->getPostCategoryRelTable()->insert($postCategoryRel->toArray(
                    PostProto::FOR_OPERATION_DB_INSERT));
            
            // Return post id
            $retVal = (int) $post_id;
            
            // Commit and return true
            $conn->commit();
        } 
        catch (\Exception $e) {
            $conn->rollback();
            $retVal = $e;
        }
        
        // Return result
        return $retVal;
    }
    
    
    /**
     * Updates a post and optionally it's related rows.
     * @todo There are no safety checks being done in this method
     * @param Post $post
     * @return mixed boolean | Exception
     */
    public function updatePost(PostProto $post) {
                
        // Get current user
//        $user = $this->getUser();
//        
//        // Bail if no user
//        if (empty($user)) {
//            return false;
//        }
        
        // If empty alias
        if (empty($post->alias)) {
            $slugger = new Slug();
            $post->alias = $slugger($post->title);
        }
        
        // If is array user params serialize it to string
        if (is_array($post->userParams)) {
            $post->userParams = $this->serializeAndEscapeTuples($post->userParams);
        }
        
        // Get post category rel proto
        $postCategoryRel = $post->getPostCategoryRelProto();
        $oldTermTaxonomyId = $postCategoryRel->getStoredSnapshotValues()['term_taxonomy_id'];
        
        // Check if category changed
        $changedTermTaxonomyId = $postCategoryRel->term_taxonomy_id !== $oldTermTaxonomyId 
                ? $postCategoryRel->term_taxonomy_id : null;
                
        // Escape post and post category rel data
        $escapedPost = $this->getDbDataHelper()->escapeTuple($post);
        
        // Db driver
        $driver = $this->getDb()->getDriver();
        
        // Get database platform object
        $conn = $driver->getConnection();
        
        // Begin transaction
        $conn->beginTransaction();
        
        // Try db transaction(s)
        try {
            // Insert date info
            $today = new \DateTime();
            
            // Update Date Info
            $this->getDateInfoTable()->update([
                            'lastUpdated' => $today->getTimestamp(), 
                            'lastUpdatedById' => 0, //$user->user_id
                        ], [
                            'date_info_id' => $post->date_info_id
                        ]);
            
            if (isset($changedTermTaxonomyId)) {
                $this->getPostCategroyRelTable()->update(
                        ['term_taxonomy_id' => $changedTermTaxonomyId],  
                        ['post_id' => $escapedPost->post_id] 
                    );
            }

            // Update post
            $this->getPostTable()->update(
                $post->toArray(PostProto::FOR_OPERATION_DB_INSERT), 
                array('post_id' => $escapedPost->post_id));
            
            // Return true to the user
            $retVal = true;
            
            // Commit and return true
            $conn->commit();
        } 
        catch (\Exception $e) {
            $conn->rollback();
            $retVal = $e;
        }
        
        // Return result to user
        return $retVal;
    }
    
//    /**
//     * Deletes a post and depends on RDBMS triggers and cascade rules to delete
//     * it's related tables (postTermRel and post postTermRel rels)
//     * @param int $id
//     * @return boolean
//     */
//    public function deletePost($id) {
//        // Get database platform object
//        $conn = $this->getDb()->getDriver()->getConnection();
//        // Begin transaction
//        $conn->beginTransaction();
//        try {
//            // Create post
//            $this->getPostTable()->delete(array('post_id' => $id));
//            // Commit and return true
//            $conn->commit();
//            $retVal = true;
//        } catch (\Exception $e) {
//            $conn->rollback();
//            $retVal = $e;
//        }
//        return $retVal;
//    }
//    
    /**
     * @param Sql $sqlObj
     * @param array $options - Default null.
     * @return \Zend\Db\Sql\Select
     */
    public function getSelect(Sql $sqlObj = null, array $options = null) {
        // Sql objects
        $sql = $sqlObj !== null ? $sqlObj : $this->getSql();
        
        // Select object
        $select = $sql->select();

        // @todo implement return values only for current role level
        return $select
            
            // Post Table
            ->from([$this->_postTableAlias => $this->_postTableName])
            
            // Post Category Rel Table
            ->join([$this->_postCategoryRelTableAlias => $this->_postCategoryRelTableName], 
                    $this->_postCategoryRelTableAlias . '.post_id=' . 
                    $this->_postTableAlias . '.post_id', 
                    ['term_taxonomy_id'])
        
            // Date Info Table
            ->join([$this->_dateInfoTableAlias => $this->_dateInfoTableName],
                    $this->_postTableAlias . '.date_info_id=' . 
                    $this->_dateInfoTableAlias .'.date_info_id', [
                        'createdDate', 'createdById', 
                        'lastUpdated', 'lastUpdatedById'])
                
            // Term Taxonomy
            ->join([$this->_termTaxonomyTableAlias => $this->_termTaxonomyTableName],
                    $this->_termTaxonomyTableAlias . 
                    '.term_taxonomy_id=' . 
                    $this->_postCategoryRelTableAlias . '.term_taxonomy_id',
                    ['term_alias'])
                
            // Term
            ->join([$this->_termTableAlias => $this->_termTableName],
                    $this->_termTableAlias . '.alias=' . 
                    $this->_termTaxonomyTableAlias . '.term_alias', 
                    ['term_name' => 'name']);
        
    }
    
    /**
     * Gets a post by id.
     * @param int $post_id
     * @return mixed array | boolean
     */
    public function getPostById($post_id) {
        return $this->read(array('where' => array('post.post_id' => $post_id)))
                ->current();
    }
    
//    /**
//     * Fetches a post by screen name
//     * @param string $alias
//     * @param int $fetchMode
//     * @return mixed array | boolean
//     */
//    public function getPostByAlias($alias, $fetchMode = AbstractService::FETCH_FIRST_AS_ARRAY) {
//        return $this->read(array(
//                    'fetchMode' => $fetchMode,
//                    'where' => array('post.alias' => $alias)));
//    }
//    
//    public function checkPostAliasExistsInDb($alias) {
//        $rslt = $this->getPostTermRelTable()->select(
//                        array('alias' => $alias))->current();
//        if (empty($rslt)) {
//            return false;
//        } else {
//            return true;
//        }
//    }
//    
//    public function setListOrderForPost (Post $post) {
//        if (!is_numeric($post->listOrder) || !is_numeric($post->post_id)) {
//            throw new \Exception('Only numeric values are accepted for ' .
//                    __CLASS__ .' -> '. __FUNCTION__ . '.');
//        }
//        return $this->getPostTable()->update(
//                array('listOrder' => $post->listOrder), 
//                array('post_id' => $post->post_id));
//    }
//    
//    public function setTermTaxonomyForPost (Post $post, $taxonomyAlias, $value) {
//        
//        // If input filter is not valid (data in post is not valid) then
//        // throw an exception
//        if (!$post->getInputFilter()->isValid()) {
//            throw new \Exception('Post object received in ' .
//                    __CLASS__ .'->'. __FUNCTION__ . ' is invalid.');
//            // @todo spit out error messages here
//        }
//        
//        // If taxonomy alias is not valid
//        if (!in_array($taxonomyAlias, $post->getValidKeys())) {
//            throw new \Exception('"'. $taxonomyAlias . '" is not a valid ' .
//                    'field of the post model in "' . 
//                    __CLASS__ . '->' . __FUNCTION__ . '"');
//        }
//        
//        // If post id is not set
//        if (!is_numeric($post->post_id)) {
//            throw new \Exception('Only numeric values are accepted for ' .
//                    __CLASS__ .'->'. __FUNCTION__ . '\'s $post->post_id param.');
//        }
//        // Check if taxonomy alias indeed has $value else throw error
//        $allowedCheck = $this->termTaxonomyService()->getByAlias($value, $taxonomyAlias);
//        if (empty($allowedCheck)) {
//            throw new \Exception('One of the values passed into "' .
//                    __CLASS__ .'->'. __FUNCTION__ . '" are not allowed.');
//        }
//        
//        // Update term taxonomy value and return outcome
//        return $this->getPostTable()->update(
//                array($taxonomyAlias => $value), 
//                array('post_id' => $post->post_id));
//    }
    
    public function ensureTableNamesAndAliases() {
        // Bail if names already populated
        if ($this->_populatedTableNamesAndAliases) {
            return $this;
        }
        
        // Term Taxonomy Service
        $termTaxonomyService = $this->getTermTaxonomyService();
        
        // Term Table
        $termTable = $termTaxonomyService->getTermTable();
        $this->_termTableName = $termTable->table;
        $this->_termTableAlias = $termTable->alias;
        
        // Term Taxonomy Table
        $termTaxonomyTable = $termTaxonomyService->getTermTaxonomyTable();
        $this->_termTaxonomyTableName = $termTaxonomyTable->table;
        $this->_termTaxonomyTableAlias = $termTaxonomyTable->alias;
        
        // Post Table
        $postTable = $this->getPostTable();
        $this->_postTableName = $postTable->table;
        $this->_postTableAlias = $postTable->alias;
        
        // Post Category Rel Table
        $postCategoryRelTable = $this->getPostCategoryRelTable();
        $this->_postCategoryRelTableName = $postCategoryRelTable->table;
        $this->_postCategoryRelTableAlias = $postCategoryRelTable->alias;
                
        // Date Info Table
        $dateInfoTable = $this->getDateInfoTable();
        $this->_dateInfoTableName = $dateInfoTable->table;
        $this->_dateInfoTableAlias = $dateInfoTable->alias;
        
        // Set populated flag to true
        $this->_populatedTableNamesAndAliases = true;
        
        // Return self
        return $this;
    }
    
    public function getPostTable() {
        if (empty($this->_postTable)) {
            $this->_postTable = 
                    $this->getServiceLocator()->get('Edm\Db\TableGateway\PostTable');
        }
        return $this->_postTable;
    }
    
    public function getPostCategoryRelTable() {
        if (empty($this->_postCategoryRelTable)) {
            $this->_postCategoryRelTable =
                    $this->getServiceLocator()
                        ->get('Edm\Db\TableGateway\PostCategoryRelTable');     
        }
        return $this->_postCategoryRelTable;
    }
    
}
