<div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead class="table-danger">
            <tr>
                <th>No</th>
                <th>Kode Peminjaman</th>
                <th>Anggota</th>
                <th>Buku</th>
                <th>Hari Terlambat</th>
                <th>Denda per Hari</th>
                <th>Total Denda</th>
                <th>Status Bayar</th>
                <th>Tanggal Denda</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td><code>{{ $item->peminjaman->kode_peminjaman }}</code></td>
                <td>
                    <div class="fw-bold">{{ $item->peminjaman->anggota->nama_lengkap ?? '-' }}</div>
                    <small class="text-muted">{{ $item->peminjaman->anggota->kelas ?? '-' }}</small>
                </td>
                <td>{{ $item->peminjaman->buku->judul ?? '-' }}</td>
                <td><span class="badge bg-danger">{{ $item->hari_terlambat }} hari</span></td>
                <td>Rp {{ number_format($item->denda_per_hari) }}</td>
                <td class="fw-bold text-danger">Rp {{ number_format($item->total_denda) }}</td>
                <td>
                    @if($item->status_bayar == 'sudah-dibayar')
                        <span class="badge bg-success">Sudah Dibayar</span>
                    @elseif($item->status_bayar == 'belum-dibayar')
                        <span class="badge bg-warning text-dark">Belum Dibayar</span>
                    @else
                        <span class="badge bg-secondary">{{ ucfirst($item->status_bayar) }}</span>
                    @endif
                </td>
                <td>{{ $item->created_at->format('d/m/Y') }}</td>
                <td>
                    @if($item->status_bayar == 'belum-dibayar')
                        <a href="{{ route('denda.bayar', $item->id) }}" class="btn btn-success btn-sm">
                            <i class="fas fa-money-bill me-1"></i>Bayar
                        </a>
                    @elseif($item->status_bayar == 'sudah-dibayar')
                        <span class="text-success"><i class="fas fa-check-circle"></i> Lunas</span>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div> 