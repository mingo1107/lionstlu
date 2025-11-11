<?php

namespace ball;


use Exception;

abstract class AttributesRecord implements AttributesInterface
{

    /**
     * @param $name
     * @return null|mixed
     * @throws \Exception
     */
    public function __get($name)
    {
        if (in_array($name, $this->attributes()) || isset($this->attributes()[$name])) {
            if (property_exists($this, $name)) {
                return $this->$name;
            } else {
                return null;
            }
        } else {
            throw new Exception('Getting unknown property: ' . get_class($this) . '::' . $name);
        }
    }
}