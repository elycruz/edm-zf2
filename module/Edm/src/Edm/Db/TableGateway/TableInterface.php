<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Edm\Db\TableGateway;

/**
 *
 * @author ElyDeLaCruz
 */
interface TableInterface {
    public function setAlias ($alias);
    public function getAlias ();
}
