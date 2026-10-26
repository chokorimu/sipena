<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\Pivot;

#[Fillable(['siswa_id', 'kelas_id', 'tahun_id'])]
class KelasSiswa extends Pivot
{
    protected $table = 'kelas_siswa';

    public $incrementing = true;
}
