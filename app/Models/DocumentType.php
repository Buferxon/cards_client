<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    protected $table = 'document_type';

    protected $fillable = [
        'name',
        'status',
    ];

    public $timestamps = false;
}
