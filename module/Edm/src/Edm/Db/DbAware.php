<?php

namespace Edm\Db;

use Zend\Db\Adapter\Adapter as DbAdapter;

interface DbAware {
    public function setDb(DbAdapter $db);
    public function getDb();
}

