@extends('laporan.pdf.layout')

@section('content')
    <h2>Analisis Nilai</h2>
    <div class="meta">
        Kelas {{ $kelas->nama_kelas }} &middot; Tahun Ajaran {{ $tahun->tahun_ajaran }} Semester {{ ucfirst($tahun->semester) }}
    </div>

    <h2>Perbandingan Rata-rata per Mata Pelajaran</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 40px">No</th>
                <th>Mata Pelajaran</th>
                <th class="center">Rata-rata</th>
                <th class="center">Jumlah Nilai</th>
                <th class="center">Status vs KKM {{ $kkm }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($analisisMapel as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['mapel'] }}</td>
                    <td class="center">{{ number_format($item['rata'], 1) }}</td>
                    <td class="center">{{ $item['jumlah'] }}</td>
                    <td class="center {{ $item['rata'] >= $kkm ? 'tuntas' : 'belum' }}">
                        {{ $item['rata'] >= $kkm ? 'Tuntas' : 'Belum Tuntas' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="center muted">Belum ada nilai yang tercatat untuk kelas dan tahun ajaran terpilih.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h2>Sebaran Nilai &mdash; {{ $mapelNama }}</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 40%">Rentang Nilai</th>
                <th class="center">Jumlah Siswa</th>
                <th class="center">Persentase</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sebaranNilai as $item)
                <tr>
                    <td>{{ $item['label'] }}</td>
                    <td class="center">{{ $item['jumlah'] }}</td>
                    <td class="center">{{ $item['persen'] }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Siswa Perlu Perhatian (Rata-rata di bawah KKM {{ $kkm }})</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 40px">No</th>
                <th>Nama Siswa</th>
                <th>NISN</th>
                <th class="center">Rata-rata</th>
                <th class="center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($siswaPerhatian as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['siswa']->nama }}</td>
                    <td>{{ $item['siswa']->nisn }}</td>
                    <td class="center belum">{{ number_format($item['rata'], 1) }}</td>
                    <td class="center belum">Belum Tuntas</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="center muted">Semua siswa telah mencapai KKM.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
