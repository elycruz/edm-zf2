<?php

namespace Edm\Db;

interface DbDataHelperAware {
    /**
     * @return \Edm\Db\DbDataHelper
     */
    public function getDbDataHelper();

    /**
     * @param DbDataHelper $dbDataHelper
     * @return \Edm\Db\DataHelperAware
     */
    public function setDbDataHelper(DbDataHelper $dbDataHelper);
}
