<?php

namespace App\Components;

class SearchProfile
{

    private $name;
    private $propertyType;
    private $searchFields;

    public function __construct($name, $propertyType, $searchFields)
    {
        $this->name = $name;
        $this->propertyType = $propertyType;
        foreach ($searchFields as $field) {
            $this->searchFields[$field["fieldName"]] = json_decode($field["fieldValue"]);
        }
    }

    public function getSearchProfilePropertyType()
    {
        return $this->propertyType;
    }

    public function getSearchProfileFields()
    {
        return $this->searchFields;
    }

}
