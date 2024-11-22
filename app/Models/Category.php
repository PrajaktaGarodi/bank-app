<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class Category extends Model
{
    use HasFactory , AsSource;
    protected $fillable = ['name', 'description','meta_title','meta_description','parent_category_id','slug'];
}
