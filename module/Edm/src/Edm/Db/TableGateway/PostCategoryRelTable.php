<?php

namespace Edm\Db\TableGateway;

class PostCategoryRelTable extends BaseTableGateway {

    protected $alias = 'postCategoryRel';
    protected $table = 'post_category_relationships';
    protected $modelClass = 'Edm\Db\ResultSet\Proto\PostCategoryRelProto';

}