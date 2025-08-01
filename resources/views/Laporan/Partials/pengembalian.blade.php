<div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead class="table-light">
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Anggota</th>
                <th>Buku</th>
                <th>Tgl Kembali (Aktual)</th>
                <th>Keterlambatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td><code>{{ $item->kode_peminjaman }}</code></td>
                <td>{{ $item->anggota->nama_lengkap ?? '-' }}</td>
                <td>{{ $item->buku->judul ?? '-' }}</td>
                <td>{{ $item->tanggal_kembali_aktual->format('d/m/Y') }}</td>
                <td>
                    @if($item->dendaRecord)
                        {{ $item->dendaRecord->hari_terlambat }} hari
                    @else
                        0 hari
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>