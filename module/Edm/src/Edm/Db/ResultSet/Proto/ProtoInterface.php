<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 8/21/2015
 * Time: 2:31 AM
 */

namespace Edm\Db\ResultSet\Proto;

interface ProtoInterface {

    /**
     * To array modes:
     */
    const TO_ARRAY_SHALLOW = 0;
    const TO_ARRAY_FLATTENED = 1;
    const TO_ARRAY_NESTED = 2;

    /**
     * To array for operations:
     */
    const FOR_OPERATION_DB = 0;
    const FOR_OPERATION_DB_INSERT = 1;
    const FOR_OPERATION_DB_UPDATE = 2;
    const FOR_OPERATION_FORM = 3;

    public function has($key);
    public function toArray();
    public function getAllowedKeysForProto();
    public function getNotAllowedKeysForInsert();
    public function getNotAllowedKeysForUpdate();
    public function setAllowedKeysOnProto($inputData, $proto);
}
