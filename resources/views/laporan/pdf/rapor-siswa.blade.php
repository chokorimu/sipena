@extends('laporan.pdf.layout')

@section('content')
    <h2>Rapor Siswa</h2>
    <div class="meta">
        Kelas {{ $kelas->nama_kelas }} &middot; Tahun Ajaran {{ $tahun->tahun_ajaran }} Semester {{ ucfirst($tahun->semester) }}
    </div>

    <table>
        <tbody>
            <tr>
                <th style="width: 30%">Nama Siswa</th>
                <td>{{ $rapor['siswa']->nama }}</td>
            </tr>
            <tr>
                <th>NISN</th>
                <td>{{ $rapor['siswa']->nisn }}</td>
            </tr>
        </tbody>
    </table>

    <h2>Nilai per Mata Pelajaran</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 40px">No</th>
                <th>Mata Pelajaran</th>
                <th class="center">Formatif</th>
                <th class="center">Sumatif</th>
                <th class="center">Nilai Akhir</th>
                <th class="center">Predikat</th>
                <th class="center">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rapor['nilai'] as $index => $nilai)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $nilai->mataPelajaran->nama_mapel }}</td>
                    <td class="center">{{ number_format((float) $nilai->formatif, 1) }}</td>
                    <td class="center">{{ number_format((float) $nilai->sumatif, 1) }}</td>
                    <td class="center">{{ number_format((float) $nilai->nilai_akhir, 1) }}</td>
                    <td class="center">{{ $nilai->predikat() }}</td>
                    <td class="center {{ $nilai->status_kelulusan === 'Lulus' ? 'tuntas' : 'belum' }}">
                        {{ $nilai->status_kelulusan }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="center muted">Belum ada nilai yang tercatat untuk siswa ini pada tahun ajaran terpilih.</td>
                </tr>
            @endforelse
        </tbody>
        @if ($rapor['nilai']->isNotEmpty())
            <tfoot>
                <tr>
                    <td colspan="4"><strong>Rata-rata</strong></td>
                    <td class="center"><strong>{{ number_format($rapor['rataRata'], 1) }}</strong></td>
                    <td class="center"><strong>{{ $rapor['predikat'] }}</strong></td>
                    <td class="center {{ $rapor['status'] === 'Tuntas' ? 'tuntas' : 'belum' }}">
                        <strong>{{ $rapor['status'] }}</strong>
                    </td>
                </tr>
            </tfoot>
        @endif
    </table>

    @if ($evaluasi !== null)
        <h2>Catatan Evaluasi</h2>
        <table>
            <tbody>
                <tr>
                    <td>{{ $evaluasi->catatan }}</td>
                </tr>
            </tbody>
        </table>
    @endif
@endsection
