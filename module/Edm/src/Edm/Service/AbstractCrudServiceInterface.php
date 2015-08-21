<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 8/21/2015
 * Time: 2:27 AM
 */

namespace Edm\Service;


interface AbstractCrudServiceInterface {
    public function create (AbstractModel $model);   // :Edm\Service\AbstractInternalCrudService
	public function read (array $where);   			// :Edm\Service\AbstractInternalCrudService
	public function update (AbstractModel $model);   // :Edm\Service\AbstractInternalCrudService
	public function delete (AbstractModel $model);   // :Edm\Service\AbstractInternalCrudService
	public function select (AbstractModel $select);  // :Zend\Db\Sql\Select
}