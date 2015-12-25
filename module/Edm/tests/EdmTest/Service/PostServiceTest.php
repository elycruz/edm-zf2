<?php
/**
 * Created by IntelliJ IDEA.
 * Post: Ely
 * Date: 11/28/2015
 * Time: 12:34 AM
 */

namespace EdmTest\Service;

use EdmTest\Bootstrap,
    Edm\Db\ResultSet\Proto\PostProto;

class PostServiceTest extends \PHPUnit_Framework_TestCase
{
    
    /**
     * @var \Edm\Service\TermTaxonomyService
     */
    public static $postService;
    
    /**
     * @var \Edm\Service\TermTaxonomyService
     */
    public static $termTaxonomyService;

    public static $qualifyingPostData = [
        'parent_id' => 0,
        'title' => 'Some Title',
        'alias' => 'some-slug',
        'content' => 'Some content.',
        'excerpt' => 'Some exceprt.',
        'hits' => 0,
        'listOrder' => 1,
        'commenting' => 'enabled',
        'commentCount' => 0,
        'type' => 'post',
        'accessGroup' => 'guest',
        'status' => 'published',
        'userParams' => '{}',
    ];

    /**
     * Ids that are created throughout tests that should not be left in database.
     * @var array
     */
    public static $postProtosToDelete = [];

    public static function setUpBeforeClass () {
        self::$postService = Bootstrap::getServiceManager()->get('Edm\Service\PostService');
        self::$termTaxonomyService = Bootstrap::getServiceManager()->get('Edm\Service\TermTaxonomyService');
        self::$postService->ensureTableNamesAndAliases();
    }
    
    /**
     * @var Int
     */
    public static $createdPostId = null;

    public function truthyCreationProvider () {
        return [
            [self::$qualifyingPostData]
        ];
    }

    /**
     * @dataProvider truthyCreationProvider
     * @param array $postData
     */
    public function testCreatePost ($postData) {
        // Get term tax service
        $termTaxService = $this->termTaxonomyService();
        
        // Get post service
        $postService = $this->postService();
        
        // Get 'unpublished' term taxonomy
        $termTaxonomy = $termTaxService->getByAlias('unpublished', 'post-status');
        $postObj = new PostProto($postData);
        $postObj->getPostCategoryRelProto()->term_taxonomy_id = 
                $termTaxonomy->term_taxonomy_id;

        // Get post id
        $id = $postService->createPost($postObj);

        // Assert id returned
        $this->assertInternalType('int', $id);

        self::$createdPostId = $id;
    }

    public function testRead () {
        // Set id to search for
        $id = self::$createdPostId;
        
        // Get service
        $service = $this->postService();
        
        $postTableAlias = $service->getPostTable()->alias;

        // Get read result
        $rslt = $service->read(['where' => [$postTableAlias . '.post_id' => $id]]);

        // Get row
        $proto = $rslt->current();

        // Assert correct result set type
        $this->assertInstanceOf('Zend\Db\ResultSet\ResultSet', $rslt);

        // Assert only one item with current id
        $this->assertEquals(1, $rslt->count());

        // Assert correct proto class was returned by `read`
        $this->assertInstanceOf('Edm\Db\ResultSet\Proto\PostProto', $proto);
    }
    
    public function testUpdatePost () {
//        $id = self::$createdPostId;
//        
//        // Get service
//        $service = $this->postService();
//        
//        // Get post
//        $postProto = $service->getPostById($id);
//
//        // Clone post proto
//        $unchangedData = $this->postProtoFromNestedArray($postProto->toNestedArray());
//
//        // Get contact
//        $contact = $postProto->getContactProto();
//
//        // Update row
//        $contact->firstName = 'Rice';
//        $contact->lastName = 'Krispies';
//        $contact->middleName = 'Bob';
//        $postProto->role = 'guest';
//
//        // Update row
//        $rslt = $service->updatePost($postProto->post_id, $postProto, $unchangedData);
//
//        // Assert post was updated successfully
//        $this->assertEquals(true, $rslt);
//
//        // Get updated row
//        $updatedPostProto = $service->getPostById($postProto->post_id);
//        $contact = $updatedPostProto->getContactProto();
//
//        // Assert updates were made successfully
//        $this->assertEquals('Rice', $contact->firstName);
//        $this->assertEquals('Krispies', $contact->lastName);
//        $this->assertEquals('Bob', $contact->middleName);
//        $this->assertEquals('guest', $updatedPostProto->role);
//        
//        self::$createdPostId = $updatedPostProto->post_id;
    }

    public function testDeletePost () {
//        // Get service
//        $postService = $this->postService();
//        
//        $postProto = $postService->getPostById(self::$createdPostId);
//
//        // Delete post
//        $rslt = $postService->deletePost($postProto);
//
//        // Test return value
//        $this->assertEquals(true, $rslt);
    }

    public function testPostServiceClass () {
        $service = $this->postService();
        $this->assertInstanceOf('Edm\Service\PostService', $service);
        $this->assertInstanceOf('Edm\Service\AbstractCrudService', $service);
    }

    public function testGetSelect () {
//        $this->assertInstanceOf('Zend\Db\Sql\Select', $this->postService()->getSelect());
    }

    public function testGetPostById () {
//        $id = 1;
//        $service = $this->postService();
//        $proto = $service->getPostById($id);
//        $this->assertInstanceOf('Edm\Db\ResultSet\Proto\PostProto', $proto);
    }

    /**
     * @dataProvider truthyCreationProvider
     * @param array $postData
     */
    public function testGetPostByScreenName ($postData) {
//        $postService = $this->postService();
//        $originalPostProto = $this->postProtoFromNestedArray($postData);
//        $screenName = $originalPostProto->screenName;
//        $post_id = $postService->createPost($originalPostProto);
//        $proto = $postService->getPostByScreenName($screenName);
//        $this->assertInstanceOf('Edm\Db\ResultSet\Proto\PostProto', $proto);
//        $this->assertEquals($screenName, $proto->screenName);
//        $this->assertEquals($post_id, $proto->post_id);
//        $postService->deletePost($proto);
    }

    /**
     * @return \Edm\Service\PostService
     */
    public function postService () {
        return self::$postService;
    }

    /**
     * @return \Edm\Service\TermTaxonomyService
     */
    public function termTaxonomyService () {
        return self::$termTaxonomyService;
    }

    public function postProtoFromNestedArray ($nestedArray) {
        $proto = new PostProto();
        $proto->exchangeNestedArray($nestedArray);
        return $proto;
    }

    public static function tearDownAfterClass () {
        self::$postService->getPostTable()->delete([1]);
        self::$postService->getPostCategoryRelTable()->delete([1]);
    }

}
