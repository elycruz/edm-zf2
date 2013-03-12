<?php
/**
 * Describes a class that inherits the 
 * Edm global dependency injection model.
 *
 * @author ElyDeLaCruz
 */
namespace Edm;

interface Injectable 
{
    public function setOptions(array $options);
    public function setAttrib($key, $value);
    public function getAttrib($name);
    public function setAttribs(array $attribs);
    public function getAttribs();
}

