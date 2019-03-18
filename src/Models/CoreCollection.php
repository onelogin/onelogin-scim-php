<?php

namespace App\Models;

class CoreCollection
{
    public $schemas = ['urn:scim:schemas:core:1.0'];

    private $scimItems;

    public function __construct($scimItems = [])
    {
        $this->scimItems = $scimItems;
    }

    public function toSCIM($encode = true)
    {
        $data = [
            'totalResults' => count($this->scimItems),
            'startIndex' => 1,
            'schemas' => $this->schemas,
            'Resorces' => $this->scimItems
        ];

        if ($encode) {
            $data = json_encode($data);
        }

        return $data;
    }
}
