<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

class Directory extends Model
{
    use HasFactory, NodeTrait;

    protected $fillable = [
        'name',
        '_lft',
        '_rgt',
        'parent_id',
        'syncItem_id'
    ];
}
