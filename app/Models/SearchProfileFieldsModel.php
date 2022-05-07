<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchProfileFieldsModel extends Model
{
    use HasFactory;

    protected $table = 'SearchProfileFields';
    protected $connection = 'sqlite';
}
