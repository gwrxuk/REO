<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchProfileModel extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $table = 'SearchProfile';
    protected $primaryKey = 'searchProfileId';
    protected $connection = 'sqlite';
}
