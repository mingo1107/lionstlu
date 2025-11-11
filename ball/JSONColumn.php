<?php

namespace ball;


use JsonSerializable;

abstract class JSONColumn extends AttributesRecord implements JsonSerializable
{
    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @param bool $filterEmpty
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize(bool $filterEmpty = false)
    {
        $json = [];
        foreach ($this->attributes() as $f) {
            if ($filterEmpty && $this->$f == "") {
                // filter empty
            } else {
                $json[$f] = $this->$f;
            }
        }
        return $json;
    }

    /**
     * @param string $json
     * @return static[]
     */
    public static function toArray($json)
    {
        if (empty($json)) {
            return [];
        }
        $data = [];
        $jsonArray = json_decode($json);
        foreach ($jsonArray as $array) {
            $o = new static();
            foreach ($o->attributes() as $f) {
                if (isset($array->$f)) {
                    $o->$f = $array->$f;
                }
            }
            array_push($data, $o);
        }
        return $data;
    }


    /**
     * @param string $json
     * @return static
     */
    public static function toObject(string $json)
    {
        $jsonArray = json_decode($json);
        $o = new static();
        foreach ($o->attributes() as $f) {
            if (isset($jsonArray->$f)) {
                $o->$f = $jsonArray->$f;
            }
        }
        return $o;
    }

    public function apply(string $json)
    {
        $jsonArray = json_decode($json);
        foreach ($this->attributes() as $f) {
            if (isset($jsonArray->$f)) {
                $this->$f = $jsonArray->$f;
            }
        }
    }

}