<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 8/20/2015
 * Time: 3:35 AM
 */

namespace Edm\Service;

interface EdmCrudServiceInterface {
    public function normalizeSeedOptions ($options = null);
    public function seedOptionsForSelect ($options = null);
    public function read ($options = null);
    public function getSelect ();
    public function sql ();
}