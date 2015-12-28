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
     * Operations for `toArray` method
     * or use cases for `toArray` method:
     ********************************************/
    const FOR_OPERATION_DB_INSERT = 'Insert';
    const FOR_OPERATION_DB_UPDATE = 'Update';

    public function has($key);
    public function toArray($operation = null);
    public function toNestedArray($operation = null);
    public function getAllowedKeysForProto();
    public function getNotAllowedKeysForInsert();
    public function getNotAllowedKeysForUpdate();
    public function getSubProtoGetters();
    public function setAllowedKeysOnProto($inputData, $proto);
    public function filterArrayBasedOnOp ($array, $operation = null);
    public function forEachInSubProtos (callable $callback);
    // An opportunity to enforce business rules on row objects (protos) before performing 
    // further CRUD operations on them.
    public function enforceBusinessRules (); 

}
