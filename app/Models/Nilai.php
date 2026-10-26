<?php

namespace App\Models;

use Database\Factories\NilaiFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['siswa_id', 'mapel_id', 'tahun_id', 'formatif', 'sumatif', 'nilai_akhir', 'status_kelulusan'])]
class Nilai extends Model
{
    /** @use HasFactory<NilaiFactory> */
    use HasFactory;

    /**
     * Kriteria ketuntasan minimal (KKM) untuk status kelulusan.
     */
    public const KKM = 75;

    protected $table = 'nilai';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'formatif' => 'decimal:2',
            'sumatif' => 'decimal:2',
            'nilai_akhir' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Nilai $nilai): void {
            $nilai->hitungNilaiAkhir();
        });
    }

    /**
     * Hitung nilai akhir (0,7 x formatif + 0,3 x sumatif) dan status kelulusan.
     */
    public function hitungNilaiAkhir(): void
    {
        if ($this->formatif !== null && $this->sumatif !== null) {
            $this->nilai_akhir = round(0.7 * (float) $this->formatif + 0.3 * (float) $this->sumatif, 2);
            $this->status_kelulusan = $this->nilai_akhir >= self::KKM ? 'Lulus' : 'Tidak Lulus';
        }
    }

    /**
     * @return BelongsTo<Siswa, $this>
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    /**
     * @return BelongsTo<MataPelajaran, $this>
     */
    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class, 'mapel_id');
    }

    /**
     * @return BelongsTo<TahunAjaran, $this>
     */
    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_id');
    }
}
