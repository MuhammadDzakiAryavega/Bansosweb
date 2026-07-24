<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubKriteria extends Model
{
    protected $table = 'pkh_sub_kriteria';

    protected $fillable = [
        'kriteria',
        'nama',
        'nilai',
    ];

    protected function casts(): array
    {
        return [
            'nilai' => 'integer',
        ];
    }

    public function penilaian(): HasMany
    {
        return $this->hasMany(Penilaian::class, 'sub_kriteria_id');
    }
}
