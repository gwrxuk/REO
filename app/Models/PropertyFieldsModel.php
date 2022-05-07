<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyFieldsModel extends Model
{
    use HasFactory;

    protected $table = 'PropertyFields';
    protected $connection = 'sqlite';
}
