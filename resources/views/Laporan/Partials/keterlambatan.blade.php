<div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead class="table-warning">
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Anggota</th>
                <th>Buku</th>
                <th>Tgl Pinjam</th>
                <th>Tgl Kembali (Rencana)</th>
                <th>Hari Terlambat</th>
                <th>Denda yang Akan Dikenakan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
            @php
                $hariTerlambat = Carbon\Carbon::now()->diffInDays($item->tanggal_kembali_rencana);
                $dendaAkanDikenakan = $hariTerlambat * 1000; // Rp 1.000 per hari
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td><code>{{ $item->kode_peminjaman }}</code></td>
                <td>
                    <div class="fw-bold">{{ $item->anggota->nama_lengkap ?? '-' }}</div>
                    <small class="text-muted">{{ $item->anggota->kelas ?? '-' }}</small>
                </td>
                <td>{{ $item->buku->judul ?? '-' }}</td>
                <td>{{ $item->tanggal_pinjam->format('d/m/Y') }}</td>
                <td>{{ $item->tanggal_kembali_rencana->format('d/m/Y') }}</td>
                <td><span class="badge bg-warning text-dark">{{ $hariTerlambat }} hari</span></td>
                <td class="fw-bold text-warning">Rp {{ number_format($dendaAkanDikenakan) }}</td>
                <td>
                    <a href="{{ route('pengembalian.create', $item->id) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-undo me-1"></i>Kembalikan
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div> 