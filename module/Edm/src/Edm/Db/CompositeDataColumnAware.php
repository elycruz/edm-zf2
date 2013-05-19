<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Edm\Db;

/**
 * @author ElyDeLaCruz
 */
interface CompositeDataColumnAware {
    public function serializeAndEscapeArray (array $data);
    public function serializeAndEscapeTuples (array $data);
    public function unSerializeAndUnEscapeArray ($data);
    public function unSerializeAndUnEscapeTuples ($data);
}
