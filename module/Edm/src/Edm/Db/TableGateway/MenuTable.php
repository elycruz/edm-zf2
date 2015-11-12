<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 11/12/2015
 * Time: 1:15 PM
 */

namespace Edm\Db\TableGateway;

class MenuTable extends BaseTableGateway {

    protected $alias = 'media';
    protected $table = 'media';
    protected $modelClass = \Edm\Model\Menu;

}