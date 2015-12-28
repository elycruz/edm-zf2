<?php

namespace Edm\Db;

/**
 * Interface for data helpers that escape values for RDBMS and 
 * reverse escapes values from RDBMS.
 * @author ElyDeLaCruz
 */
interface DbDataHelperInterface {
    public function escapeTuple($tuple, array $skipFields = null, array $jsonFields = null);
    public function escapeTuples($tuples, array $skipFields = null, array $jsonFields = null);
    public function reverseEscapeTuple($tuple, array $skipFields = null, array $jsonFields = null);
    public function reverseEscapeTuples($tuples, array $skipFields = null, array $jsonFields = null);
}
