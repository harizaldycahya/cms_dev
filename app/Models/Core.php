<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Core extends Model
{
    protected $table = 'core';
    protected $fillable = [
        'section_id',
        'segment_id',
        'project_id',
        'tube',
        'core',
        'customers',
        'total_loss_db',
        'end_cable',
        'loss_db_km',
        'remarks',
        'note_spv_mgr',
        'lokasi_otdr',
    ];
    use HasFactory;
}
