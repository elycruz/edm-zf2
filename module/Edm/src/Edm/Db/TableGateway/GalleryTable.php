<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 11/12/2015
 * Time: 1:15 PM
 */

namespace Edm\Db\TableGateway;

class GalleryTable extends BaseTableGateway {

    protected $alias = 'gallery';
    protected $table = 'galleries';
    protected $modelClass = \Edm\Model\Gallery;

}