<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyncItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sync_id',
        'file_url'
    ];
}
