<?php

declare(strict_types=1);

namespace Edm\Service;

use 
    Zend\Db\ResultSet\ResultSet,
    Zend\Db\Sql\Sql,
    Edm\Db\ResultSet\Proto\PostProto,
    Edm\Db\TableGateway\DateInfoTableAware,
    Edm\Db\TableGateway\DateInfoTableAwareTrait;

class PostService extends AbstractCrudService
    implements DateInfoTableAware {

    use DateInfoTableAwareTrait;

    /**
     * @var \Edm\Db\TableGateway\PostTale
     */
    protected $postTable;
    
    /**
     * @var \Edm\Db\TableGateway\PostCategoryRelTable
     */
    protected $postCategoryRelTable;

    public function __construct () {
        $this->resultSet = new ResultSet();
        $this->resultSet->setArrayObjectPrototype(new PostProto());
    }
    
    public function getSelect(Sql $sql = null, array $options = null) {
        return $sql;
    }
    
    public function createPost (Post $post) {
        return 1;
    }
    
    public function updatePost (Post $post) {
        return true;
    }
    
    public function deletePost (Post $post) {
        return true;
    }
    
}
