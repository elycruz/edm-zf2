<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 8/20/2015
 * Time: 3:35 AM
 */

namespace Edm\Service;

interface EdmServiceInterface {
    public function normalizeMethodOptions ($options = null);
    public function seedOptionsForSelect ($options = null);
    public function read ($options = null);
    public function sql ($sql = null);
    public function cleanResultSetToArray (ResultSet $rsltSet);
    public function fetchFromResult (ResultSet $rsltSet, $fetchMode = self::FETCH_RESULT_SET_TO_ARRAY);
}