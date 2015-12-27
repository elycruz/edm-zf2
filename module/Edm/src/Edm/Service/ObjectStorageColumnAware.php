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
    public function serializeAndEscapeTuple ($data);
    public function serializeAndEscapeTuples ($data);
    public function unSerializeAndUnEscapeTuple ($data);
    public function unSerializeAndUnEscapeTuples ($data);
}