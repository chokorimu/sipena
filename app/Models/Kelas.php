<?php

namespace App\Models;

use Database\Factories\KelasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama_kelas', 'tingkat', 'wali_kelas_id'])]
class Kelas extends Model
{
    /** @use HasFactory<KelasFactory> */
    use HasFactory;

    protected $table = 'kelas';

    /**
     * @return BelongsTo<Guru, $this>
     */
    public function waliKelas(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'wali_kelas_id');
    }

    /**
     * @return BelongsToMany<Siswa, $this>
     */
    public function siswa(): BelongsToMany
    {
        return $this->belongsToMany(Siswa::class, 'kelas_siswa', 'kelas_id', 'siswa_id')
            ->using(KelasSiswa::class)
            ->withPivot('tahun_id')
            ->withTimestamps();
    }

    /**
     * @return HasMany<Jadwal, $this>
     */
    public function jadwal(): HasMany
    {
        return $this->hasMany(Jadwal::class, 'kelas_id');
    }
}
