<?php

namespace Edm\Db;

/**
 * Interface for data helpers that escape values for db and 
 * reverse escapes them from db.
 * @author ElyDeLaCruz
 */
interface DbDataHelperInterface {
    public function escapeTuple($tuple, $skipFields = null);
    public function escapeTuples($tuples, $skipFields = null);
    public function reverseEscapeTuple($tuple, $skipFields = null);
    public function reverseEscapeTuples($tuples, $skipFields = null);
}
