<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Alternatif extends Model
{
    protected $table = 'pkh_alternatif';

    protected $fillable = [
        'user_id',
        'desa',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function penilaian(): HasMany
    {
        return $this->hasMany(Penilaian::class, 'alternatif_id');
    }

    /** Nilai crisp terpilih per kriteria: [slug => nilai]. */
    public function nilaiPerKriteria(): array
    {
        return $this->penilaian
            ->filter(fn ($p) => $p->subKriteria !== null)
            ->mapWithKeys(fn ($p) => [$p->kriteria => $p->subKriteria->nilai])
            ->all();
    }
}
