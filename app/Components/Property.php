<?php

namespace App\Components;

class Property
{

    private $name;
    private $address;
    private $propertyType;
    private $fields;

    public function __construct($name, $address, $propertyType, $fields)
    {
        $this->name = $name;
        $this->address = $address;
        $this->propertyType = $propertyType;
        foreach ($fields as $field) {
            $this->fields[$field["fieldName"]] = json_decode($field["fieldValue"]);
        }
    }

    public function getPropertyType()
    {
        return $this->propertyType;
    }

    public function getPropertyFields()
    {
        return $this->fields;
    }

}

