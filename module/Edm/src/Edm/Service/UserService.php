<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 11/25/2015
 * Time: 1:42 AM
 */

namespace Edm\Service;


class UserService
{
    protected $userTable;
    protected $contactTable;
    protected $userContactRelTable;

    /**
     * Returns our pre-prepared select statement
     * for our term taxonomy model
     * @todo select should include:
     *      parent_name
     *      parent_alias
     *      taxonomy_name
     * @return Zend\Db\Sql\Select
     */
    public function getSelect($sql = null) {
        $sql = $sql !== null ? $sql : $this->getSql();
        $select = $sql->select();
        // @todo implement return values only for current role level
        // @todo make password and activationkey optional via flag
        return $select
            // User Contact Rel Table
            ->from(array('userContactRel' => $this->getContactUserRelTable()->table))

            // User Table
            ->join(array('user' => $this->getUserTable()->table),
                'user.screenName=userContactRel.screenName',
                array(
                    'user_id', 'password', 'role',
                    'accessGroup', 'status', 'lastLogin',
                    'activationKey', 'date_info_id'))

            // Contact Table
            ->join(array('contact' => $this->getContactTable()->table),
                'contact.email=userContactRel.email',
                array(
                    'contact_id', 'altEmail', 'name',
                    'type', 'firstName', 'middleName', 'lastName',
                    'userParams'))

            // Date Info Table
            ->join(array('dateInfo' => $this->getDateInfoTable()->table),
                'user.date_info_id=dateInfo.date_info_id', array(
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
