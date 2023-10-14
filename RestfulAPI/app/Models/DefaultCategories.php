<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DefaultCategories extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['type, name'];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'custom_options' => 'json',
        'custom_data' => 'json',
    ];


}
