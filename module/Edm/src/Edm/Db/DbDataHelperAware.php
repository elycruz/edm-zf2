<?php

namespace Edm\Db;

interface DbDataHelperAware {
    /**
     * @return \Edm\Db\DbDataHelper
     */
    public function getDbDataHelper();

    /**
     * @param DbDataHelperInterface $dbDataHelper
     * @return \Edm\Db\DbDataHelperAware
     */
    public function setDbDataHelper(DbDataHelperInterface $dbDataHelper);
}
