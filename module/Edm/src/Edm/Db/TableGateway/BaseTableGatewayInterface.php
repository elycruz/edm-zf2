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
interface BaseTableGatewayInterface {
    public function setAlias (\string $alias);
    public function getAlias ();
}
