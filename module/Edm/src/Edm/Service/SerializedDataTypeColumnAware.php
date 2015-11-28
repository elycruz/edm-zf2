<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 11/27/2015
 * Time: 8:04 PM
 */

namespace Edm\Service;


interface SerializedDataTypeColumnAware
{
    public function unserializeAndUnescapeArray (array $data);
    public function unserializeAndUnescapeTuples (array $data);
    public function serializeAndEscapeArray (array $data);
    public function serializeAndEscapeTuples (array $data);
}
