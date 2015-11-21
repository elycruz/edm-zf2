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
     * Modes for `toArray` method:
     ********************************************/
    const TO_ARRAY_SHALLOW = 0;
    const TO_ARRAY_FLATTENED = 1;
    const TO_ARRAY_NESTED = 2;

    /**
     * Operations for `toArray` method
     * or use cases for `toArray` method:
     ********************************************/
    const FOR_OPERATION_DB = 'Db';
    const FOR_OPERATION_DB_INSERT = 'Insert';
    const FOR_OPERATION_DB_UPDATE = 'Update';
    const FOR_OPERATION_FORM = 'Form';

    public function has($key);
    public function toArray($operation = null, $mode = AbstractProto::TO_ARRAY_SHALLOW);
    public function getAllowedKeysForProto();
    public function getNotAllowedKeysForInsert();
    public function getNotAllowedKeysForUpdate();
    public function setAllowedKeysOnProto($inputData, $proto);
}
