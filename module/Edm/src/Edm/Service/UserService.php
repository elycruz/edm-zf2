<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 11/25/2015
 * Time: 1:42 AM
 */

namespace Edm\Service;

use Zend\Db\ResultSet\ResultSet,
    Zend\Db\Sql\Sql,
    Edm\Db\ResultSet\Proto\UserProto;

class UserService
{
    protected $userTable;
    protected $contactTable;
    protected $userContactRelTable;

    public function __construct () {
        $this->resultSet = new ResultSet();
        $this->resultSet->setArrayObjectPrototype(new UserProto());
    }

    /**
     * Returns our pre-prepared select statement
     * for our term taxonomy model
     * @todo select should include:
     *      parent_name
     *      parent_alias
     *      taxonomy_name
     * @return Zend\Db\Sql\Select
     */
    public function getSelect($sql = null, $includeSensitiveUserColumns = false) {
        $sql = $sql !== null ? $sql : $this->getSql();
        $select = $sql->select();

        // Get required tables for result set
        $userTable = $this->getUserTable();
        $contactUserRelTable = $this->getContactUserRelTable();
        $contactTable = $this->getContactTable();
        $dateInfoTable = $this->getDateInfoTable();

        // User table columns
        $userTableColumns = [
            'user_id', 'role', 'accessGroup', 'status', 'lastLogin', 'date_info_id'
        ];

        // Conditionally include sensitive `user` table columns
        if ($includeSensitiveUserColumns) {
            $userTableColumns[] = 'password';
            $userTableColumns[] = 'activationKey';
        }

        // @todo implement return values only for current role level
        // @todo make password and activationkey optional via flag
        return $select
            // User Contact Rel Table
            ->from(array($contactUserRelTable->alias => $contactUserRelTable->table))

            // User Table
            ->join(array($userTable->alias => $userTable->table),
                $userTable->alias . '.screenName=' . $contactUserRelTable->alias . '.screenName',
                $userTableColumns)

            // Contact Table
            ->join(array($contactTable->alias => $contactTable->table),
                $contactTable->alias '.email=' . $contactUserRelTable->alias . '.email',
                array(
                    'contact_id', 'altEmail', 'name',
                    'type', 'firstName', 'middleName', 'lastName',
                    'userParams'))

            // Date Info Table
            ->join(array($dateInfoTable->alias => $dateInfoTable->table),
                $userTable->alias . '.date_info_id=' . $dateInfoTable->alias . '.date_info_id', array(
                    'createdDate', 'createdById', 'lastUpdated', 'lastUpdatedById'));
    }

    public function getUserTable() {
        if (empty($this->userTable)) {
            $this->userTable = $this->getServiceLocator()
                ->get('Edm\Db\TableGateway\UserTable');
        }
        return $this->userTable;
    }

    public function getContactTable() {
        if (empty($this->contactTable)) {
            $this->contactTable = $this->getServiceLocator()
                ->get('Edm\Db\TableGateway\ContactTable');
        }
        return $this->contactTable;
    }

    public function getContactUserRelTable() {
        if (empty($this->userContactRelTable)) {
            $this->userContactRelTable = $this->getServiceLocator()
                ->get('Edm\Db\TableGateway\UserContactRel');
        }
        return $this->userContactRelTable;
    }
    
}
