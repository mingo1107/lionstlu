<?php
namespace ball\file\aws;

use ball\AttributesRecord;


/**
 * @property string $Body
 * @property string $Bucket
 * @property string $Key
 * @property string $ContentType
 */
class S3 extends AttributesRecord
{

    /**
     * @return array
     */
    public function attributes(): array
    {
        $fields = [
            'Body',
            'Bucket',
            'Key',
            'ContentType'
        ];
        $obj = [];

        foreach ($fields as $f) {
            if (isset($this->$f)) {
                $obj[$f] = $this->$f;
            }
        }
        return $obj;
    }
}