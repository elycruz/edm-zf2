<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 11/15/2015
 * Time: 8:42 PM
 */

namespace Edm\Db;

use Zend\Db\Adapter\Adapter as DbAdapter;

interface DbAware {
    public function setDb(DbAdapter $db);
    public function getDb();
}
