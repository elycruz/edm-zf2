<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Edm\Service;

/**
 * @author ElyDeLaCruz
 */
interface ObjectStorageColumnAware {
    public function serializeAndEscapeArray ($data);
    public function serializeAndEscapeTuples ($data);
    public function unSerializeAndUnEscapeArray ($data);
    public function unSerializeAndUnEscapeTuples ($data);
}