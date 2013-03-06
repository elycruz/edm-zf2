<?php

namespace Edm\Db;

use Zend\Db\Adapter\Adapter as DbAdapter;

interface DbAccess {
    public function setDb(DbAdapter $db);
    public function getDb();
}

