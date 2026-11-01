@extends('laporan.pdf.layout')

@section('content')
    <h2>Laporan Akademik &mdash; Rekap Kelas</h2>
    <div class="meta">
        Kelas {{ $kelas->nama_kelas }} &middot; Tahun Ajaran {{ $tahun->tahun_ajaran }} Semester {{ ucfirst($tahun->semester) }}
    </div>

    <h2>Statistik Kelas</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 60%">Keterangan</th>
                <th class="center">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total Siswa</td>
                <td class="center">{{ $statistik['totalSiswa'] }}</td>
            </tr>
            <tr>
                <td>Tuntas (rata-rata &gt;= KKM {{ $kkm }})</td>
                <td class="center tuntas">{{ $statistik['tuntas'] }}</td>
            </tr>
            <tr>
                <td>Belum Tuntas</td>
                <td class="center belum">{{ $statistik['belumTuntas'] }}</td>
            </tr>
            <tr>
                <td>Rata-rata Kelas</td>
                <td class="center">{{ $statistik['rataRata'] !== null ? number_format($statistik['rataRata'], 1) : '-' }}</td>
            </tr>
        </tbody>
    </table>

    <h2>Ringkasan Nilai per Mata Pelajaran</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 40px">No</th>
                <th>Mata Pelajaran</th>
                <th class="center">Rata-rata</th>
                <th class="center">Tertinggi</th>
                <th class="center">Terendah</th>
                <th class="center">Tuntas</th>
                <th class="center">Belum Tuntas</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($ringkasanMapel as $index => $mapel)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $mapel['mapel'] }}</td>
                    <td class="center">{{ number_format($mapel['rata'], 1) }}</td>
                    <td class="center">{{ number_format($mapel['tertinggi'], 1) }}</td>
                    <td class="center">{{ number_format($mapel['terendah'], 1) }}</td>
                    <td class="center tuntas">{{ $mapel['tuntas'] }}</td>
                    <td class="center {{ $mapel['belum'] > 0 ? 'belum' : 'muted' }}">{{ $mapel['belum'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="center muted">Belum ada nilai yang tercatat untuk kelas dan tahun ajaran terpilih.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h2>Distribusi Predikat</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 40%">Predikat</th>
                <th class="center">Jumlah Nilai</th>
                <th class="center">Persentase</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($distribusiPredikat as $item)
                <tr>
                    <td>{{ $item['label'] }}</td>
                    <td class="center">{{ $item['jumlah'] }}</td>
                    <td class="center">{{ $item['persen'] }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
