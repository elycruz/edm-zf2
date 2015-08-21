<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 8/21/2015
 * Time: 2:31 AM
 */

namespace Edm\Model;


interface ModelInterface {
    public function exchangeArray (array $data = null);
    public function getArrayCopy (array $keys = null);
    public function getValidKeys ();
}