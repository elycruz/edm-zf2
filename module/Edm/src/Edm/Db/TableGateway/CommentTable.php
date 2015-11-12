<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 11/12/2015
 * Time: 1:15 PM
 */

namespace Edm\Db\TableGateway;

class CommentTable extends BaseTableGateway {

    protected $alias = 'comment';
    protected $table = 'comments';
    protected $modelClass = \Edm\Model\Comment;

}