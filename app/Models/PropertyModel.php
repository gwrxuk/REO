<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyModel extends Model
{
    use HasFactory;

    protected $table = 'Property';
    protected $primaryKey = 'propertyId';
    protected $keyType = 'string';
    protected $connection = 'sqlite';
}
