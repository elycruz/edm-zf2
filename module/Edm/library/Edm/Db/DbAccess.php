<?php

interface Edm_Db_DbAccess {
    public function setDb(Zend_Db_Adapter_Abstract $db);
    public function getDb();
}

