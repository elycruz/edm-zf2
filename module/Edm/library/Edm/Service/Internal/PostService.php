<?php
/**
 * Description of Edm_Service_Internal_PostService
 * @author ElyDeLaCruz
 */
class Edm_Service_Internal_PostService extends
Edm_Service_Internal_AbstractCrudService
{
    /**
     * Post Model
     * @var Edm_Db_AbstractTable
     */
    protected $_postModel;
    
    /**
     * Post Term Relationship model
     * @var Edm_Db_AbstractTable
     */
    protected $_postTermRelModel;
    
    
    /**
     * Post Index Service
     * @var Edm_Service_Internal_PostIndexService
     */
    protected $_postIndexService;

    public function __construct() {
        $this->_postModel =
                Edm_Db_Table_ModelBroker::getModel('post');
        $this->_postTermRelModel =
                Edm_Db_Table_ModelBroker::getModel('post-term-rel');
//        $this->_postIndexService = 
//                new Edm_Service_Internal_PostIndexService( );
//        $this->_postIndexService->buildInitialIndex();
    }

    /**
     * Creates a Post
     * @param array $data
     * @return Boolean
     */
    public function createPost(array $data)
    {
        if (!array_key_exists('post', $data) &&
            !array_key_exists('post-term-rel', $data)) {
            throw new Exception('A key is missing from the '.
                    'data array passed into the create post function of the '.
                    'post service.');
        }
            // Get data
            $postData = (object) $data['post'];
            $postTermRelData = (object) $data['post-term-rel'];

            //--------------------------------------------------------------
            // Update posts data
            //--------------------------------------------------------------
            
            $postData->userParams = '';
            
            // Created by
            $user = $this->getUser();
            $postData->createdById = $user->user_id;

            // Created date
            $postData->createdDate = Zend_Date::now()->getTimestamp();
            
            if (empty($postData->alias)) {
                $this->postData->alias = 
                        $this->generateValidAlias($postData->title);
            }
            
            // Auto fill excerpt if it is blank
//            if (empty($postData->excerpt)) {
//                
//                // Get excerpt length
//                $postData->autoExcerptLength = $postData->autoExcerptLength ? 
//                        $postData->autoExcerptLength : 1024;
//                
//                // Get the excerpt's contents
//                $postData->excerpt = substr($postData->content, 0,
//                        $postData->autoExcerptLength);
//                
//                // Try to repiar any broken markup within excerpt
//                $tidy = tidy_parse_string($postData->excerpt, 
//                        array('indent' => false, 'output-xhtml' => true, 
//                            'preserve-entities' => false, 
//                            'input-encoding' => 'utf-8',
//                            'wrap' => false, 'show-body-only' => true));
//                
//                // Set new excerpt markup
//                $postData->excerpt = (string) $tidy;
//            }
                unset($postData->autoExcerptLength);
            
            // Auto generate alias if necessary
            if (empty($postData->alias)) {
                $postData->alias = $this->generateValidAlias($postData->title);
            }
            
            // If no comment status
            if (empty($postData->commentStatus)) {
                $postData->commentStatus = 'disabled';
            }
            
            //--------------------------------------------------------------
            // Update post-type data if necessary
            //--------------------------------------------------------------
//            if ($postData->type != 'post' && 
//                    array_key_exists($postData->type, $data)) {
//                $postTypeModel = Edm_Db_Table_ModelBroker::getModel(
//                        $postData->type);
//                $postTypeData = (object) $data[$postData->type];
//            }
            
            //--------------------------------------------------------------
            // Update post-type data if necessary
            //--------------------------------------------------------------
            
            // Get item count model and use it's `user` table `itemCount` to
            $postData->listOrder = $this
                    ->getRowCount($this->_postModel) + 1;
            
            //--------------------------------------------------------------
            // Begin db transaction
            //--------------------------------------------------------------
            $db = $this->getDb();
            $db->beginTransaction();
            try {
                // Update user table
                $postId = $this->_postModel->createPost((array) $postData);
                
                // If post type data
                if (!empty($postTypeData)) {
                    $postTypeData->post_id = $postId;
                    $postTypeModel->insert((array) $postTypeData);
                }

                // Update term relationships table
                $postTermRelData->post_id = $postId;
                $this->_postTermRelModel
                        ->createPostTermRel((array) $postTermRelData);

                // Success, commit to db
                $db->commit();

                //$this->sendTupleToSearchIndex($tuple);
                
                // Return true to the user
                return true;
            }
            catch (Exception $e) {
                $db->rollBack();
                return $e;
            }
    }

    public function getPostById($id, $fetchMode = Zend_Db::FETCH_OBJ) {
        return $this->getSelect()->where('post.post_id = ?', $id)
                    ->query($fetchMode)->fetch();
    }

    public function getPostByAlias($alias, $fetchMode = Zend_DB::FETCH_OBJ) {
        return $this->getSelect()->where('post.alias=?', $alias)
                    ->query($fetchMode)->fetch();
    }

    public function getSelect() 
    {
        return $this->getDb()->select()
                ->from(array('post' => $this->_postModel->getName()), '*')
                ->join(array('postTermRel' => $this->_postTermRelModel->getName()),
                        'postTermRel.post_id = post.post_id')
                ->join(array('termTax' => 'term_taxonomies'), 
                        'termTax.term_taxonomy_id = postTermRel.term_taxonomy_id',
                        array('term_alias'))
                ->join(array('term' => 'terms'), 
                        'term.alias = termTax.term_alias',
                        array('term_name' => 'name', 'category' => 'name'))
                ->join(array('user' => 'users'), 
                        'user.user_id = post.createdById',
                        array('contact_id'))
                ->join(array('contact' => 'contacts'), 
                        'contact.contact_id = user.contact_id',
                        array('authorName' => 
                            'CONCAT(firstName, " ", lastName)'));
    }
    
    /**
     * Update a Post
     * @param int $id
     * @param array $data
     * @return Boolean
     */
    public function updatePost($id, $data)
    {
        if (!array_key_exists('post', $data) &&
            !array_key_exists('post-term-rel', $data)) {
            throw new Exception('A key is missing from the '.
                    'data array passed into the update post function of the '.
                    'post service.');
        }

        // Get data
        $postData = (object) $data['post'];
        $postTermRelData = (object) $data['post-term-rel'];

        //--------------------------------------------------------------
        // Update post data
        //--------------------------------------------------------------
        $postData->lastUpdatedById = $this->getUser()->user_id;
        $postData->lastUpdated = Zend_Date::now()->getTimestamp();

        // Auto fill excerpt if it is blank
//        if (empty($postData->excerpt)) {
//            $postData->autoExcerptLength = $postData->autoExcerptLength ? 
//                    $postData->autoExcerptLength : 1024;
//
//            $postData->excerpt = substr($postData->content, 0,
//                    $postData->autoExcerptLength);
//
//            // Repiar any broken markup
//            $tidy = tidy_parse_string($postData->excerpt, 
//                    array('indent' => false, 'output-xhtml' => true, 
//                        'preserve-entities' => false,
//                        'input-encoding' => 'utf-8',
//                        'wrap' => false, 'show-body-only' => true));
//            $postData->excerpt = (string) $tidy;
//        }

        // Unset uneeded vars
        unset($postData->autoExcerptLength);
        
        // Auto generate alias if necessary
        if (empty($postData->alias)) {
            $postData->alias = $this->generateValidAlias($postData->title);
        }

        // If no comment status
        if (empty($postData->commentStatus)) {
            $postData->commentStatus = 'disabled';
        }

        //--------------------------------------------------------------
        // Update post-type data if necessary
        //--------------------------------------------------------------
//            if ($postData->type != 'post' && 
//                    array_key_exists($postData->type, $data)) {
//                $postTypeModel = Edm_Db_Table_ModelBroker::getModel(
//                        $postData->type);
//                $postTypeData = (object) $data[$postData->type];
//            }

        //--------------------------------------------------------------
        // Begin db transaction
        //--------------------------------------------------------------
        $db = $this->getDb();
        $db->beginTransaction();
        try {
            // Update user table
            $this->_postModel->updatePost($id, (array) $postData);

//                // If post type data
//                if (!empty($postTypeData)) {
//                    $postTypeData->post_id = $id;
//                    $postTypeModel->update((array) $postTypeData, 
//                            'post_id="'. $id .'"');
//                }

            // Update term relationships table
            $this->_postTermRelModel
                    ->updatePostTermRel($id, (array) $postTermRelData);

            // Success, commit to db
            $db->commit();

            // Return true to the user
            return true;
        }
        catch (Exception $e) {
            $db->rollBack();
            return $e;
        }
    } // end update
    
    public function deletePost($post)
    {
        $this->getDb()->beginTransaction();
        try {

            // Delete row from whatever table extends this post
            if ($post->type != 'post') {
                $postTypeModel = Edm_Db_Table_ModelBroker::getModel(
                        $post->type);

                // Delete extended row
                $postTypeModel->delete(
                        $postTypeModel
                            ->getWhereClauseFor($post->post_id, 'post_id'));
            }
            
            // Delete post row
            $this->_postModel->delete(
                    $this->_postModel->getWhereClauseFor(
                            $post->post_id, 'post_id'));
            
            // Delete term relationship row
            $this->_postTermRelModel
                    ->delete('post_id="'. $post->post_id .'" AND type="post"');
            
            // Commit 
            $this->_db->commit();
            
            // Return
            return true;
            
        }
        catch( Exception $e) 
        {
            $this->_db->rollBack();
            return $e;
        }
    }
    
    public function setListOrder($post_id, $listOrder) {
        $post = $this->getPostById($post_id);
        if (!empty($post)) {
            return $this->getPostModel()
                    ->setListOrder($post_id, $listOrder);
        }
        else {
            return false;
        }
    }
    
    public function setCategory($post_id, $category_id) {
        $post = $this->getPostById($post_id);
        if (!empty($post)) {
            return $this->_postTermRelModel
                    ->setTermTaxonomyId($post_id, $category_id);
        }
        else {
            return false;
        }
    }
    
    public function setStatus($post_id, $status) {
        $post = $this->getPostById($post_id);
        if (!empty($post)) {
            return $this->_postTermRelModel
                    ->setStatus($post_id, $status);
        }
        else {
            return false;
        }
    }
    
    public function getPostModel() {
        if (empty($this->_postModel)) {
            $this->_postModel = Edm_Db_Table_ModelBroker::getModel('post');
        }
        return $this->_postModel;
    }
    
}
