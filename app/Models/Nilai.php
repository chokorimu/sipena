<?php

namespace App\Models;

use Database\Factories\NilaiFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

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
     * Predikat huruf standar berdasarkan nilai akhir (untuk tampilan rekap/laporan).
     */
    public function predikat(): ?string
    {
        if ($this->nilai_akhir === null) {
            return null;
        }

        return match (true) {
            (float) $this->nilai_akhir >= 90 => 'A',
            (float) $this->nilai_akhir >= 80 => 'B',
            (float) $this->nilai_akhir >= 70 => 'C',
            (float) $this->nilai_akhir >= 60 => 'D',
            default => 'E',
        };
    }

    /**
     * Peringkat siswa berdasarkan nilai akhir (standard competition: 1, 2, 2, 4).
     * Siswa tanpa nilai akhir tidak diberi peringkat.
     *
     * @param  Collection<int, Nilai>  $nilai  koleksi nilai ter-key oleh siswa_id
     * @return array<int, int> map siswa_id => peringkat
     */
    public static function hitungRanking(Collection $nilai): array
    {
        $urut = $nilai
            ->filter(fn (Nilai $item): bool => $item->nilai_akhir !== null)
            ->sortByDesc(fn (Nilai $item): float => (float) $item->nilai_akhir);

        $rankings = [];
        $peringkat = 0;
        $peringkatSebelumnya = 0;
        $nilaiSebelumnya = null;

        foreach ($urut as $siswaId => $item) {
            $peringkat++;
            $nilaiAkhir = (float) $item->nilai_akhir;

            if ($nilaiSebelumnya !== null && $nilaiAkhir === $nilaiSebelumnya) {
                $rankings[(int) $siswaId] = $peringkatSebelumnya;
            } else {
                $rankings[(int) $siswaId] = $peringkat;
                $peringkatSebelumnya = $peringkat;
                $nilaiSebelumnya = $nilaiAkhir;
            }
        }

        return $rankings;
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
