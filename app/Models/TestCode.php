<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestCode extends Model
{
    use HasFactory;

    protected $table = 'test_code';

    protected $fillable = [
        'nama',
        'kode',
        'keterangan',
        'status',
    ];
}
