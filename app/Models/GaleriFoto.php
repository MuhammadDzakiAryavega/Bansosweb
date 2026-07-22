<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GaleriFoto extends Model
{
    protected $table = 'galeri_fotos';
    protected $primaryKey = 'id_foto';

    protected $fillable = [
        'galeri_id',
        'path',
        'urutan',
    ];

    public function galeri(): BelongsTo
    {
        return $this->belongsTo(Galeri::class, 'galeri_id', 'id_galeri');
    }
}
