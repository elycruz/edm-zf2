<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 8/21/2015
 * Time: 2:31 AM
 */

namespace Edm\Db\ResultSet\Proto;

interface ProtoInterface {
    public function has($key);
    public function toArray();
}