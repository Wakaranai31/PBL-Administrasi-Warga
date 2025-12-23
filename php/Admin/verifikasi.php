<?php
session_start();
include '../koneksi.php';
// LOGIKA PROSES (SETUJUI / TOLAK)
if (isset($_POST['aksi'])) {
    $id_pengajuan = $_POST['id_pengajuan'];
    $aksi = $_POST['aksi'];

    if ($aksi == 'Setujui') {
        // --- LOGIKA SETUJUI (SAMA SEPERTI SEBELUMNYA) ---
        $q_pengajuan = mysqli_query($koneksi, "SELECT * FROM pengajuan_perubahan WHERE id_pengajuan='$id_pengajuan'");
        $data_baru = mysqli_fetch_array($q_pengajuan);
        $nik_target = $data_baru['nik'];

        $update_warga = mysqli_query($koneksi, "UPDATE warga SET 
            nama = '$data_baru[nama_baru]',
            tempat_lahir = '$data_baru[tempat_lahir_baru]',
            tanggal_lahir = '$data_baru[tanggal_lahir_baru]',
            jenis_kelamin = '$data_baru[jenis_kelamin_baru]',
            agama = '$data_baru[agama_baru]',
            no_hp = '$data_baru[no_hp_baru]',
            pendidikan = '$data_baru[pendidikan_baru]',
            pekerjaan = '$data_baru[pekerjaan_baru]',
            status_perkawinan = '$data_baru[status_perkawinan_baru]'
            WHERE nik='$nik_target'");

        if ($update_warga) {
            mysqli_query($koneksi, "UPDATE pengajuan_perubahan SET status='Disetujui', alasan_ditolak=NULL WHERE id_pengajuan='$id_pengajuan'");
            echo "<script>alert('Berhasil! Data warga telah diperbarui.'); window.location='verifikasi.php';</script>";
        }

    } elseif ($aksi == 'Tolak') {
        // --- LOGIKA TOLAK (BARU) ---
        // Tangkap alasan dari form modal
        $alasan_opsi = $_POST['alasan_opsi'];
        $alasan_custom = $_POST['alasan_custom'];
        
        // Gabungkan alasan (Jika pilih 'Lainnya', pakai input manual)
        $alasan_final = ($alasan_opsi == 'Lainnya') ? $alasan_custom : $alasan_opsi;
        
        // Simpan status Ditolak beserta Alasannya
        $tolak = mysqli_query($koneksi, "UPDATE pengajuan_perubahan SET status='Ditolak', alasan_ditolak='$alasan_final' WHERE id_pengajuan='$id_pengajuan'");
        
        if($tolak) {
            echo "<script>alert('Pengajuan berhasil ditolak.'); window.location='verifikasi.php';</script>";
        }
    }
}
?>

<?php include "header.php" ?>
<?php include "sidebar.php" ?>

<div class="content">
    <div class="page">
        <h2 class="mb-4">Verifikasi Perubahan Data</h2>
        
        <div class="card">
            <h5 class="card-header bg-warning text-dark">Daftar Pengajuan (Pending)</h5>
            <div class="table-responsive p-3">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Tgl Request</th>
                            <th>NIK / Nama</th>
                            <th>Perubahan Diajukan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = mysqli_query($koneksi, "SELECT * FROM pengajuan_perubahan JOIN warga ON pengajuan_perubahan.nik = warga.nik WHERE status='Pending' ORDER BY tanggal_request ASC");
                        
                        if(mysqli_num_rows($query) > 0) {
                            while($row = mysqli_fetch_array($query)) {
                        ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($row['tanggal_request'])); ?></td>
                            <td>
                                <b><?= $row['nik']; ?></b><br>
                                <?= $row['nama']; ?>
                            </td>
                            <td>
                                <ul class="mb-0 ps-3">
                                    <li><b>Nama Baru:</b> <?= $row['nama_baru']; ?></li>
                                    <li><b>Pekerjaan:</b> <?= $row['pekerjaan_baru']; ?></li>
                                    <li><b>Status:</b> <?= $row['status_perkawinan_baru']; ?></li>
                                </ul>
                            </td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="id_pengajuan" value="<?= $row['id_pengajuan']; ?>">
                                    <button type="submit" name="aksi" value="Setujui" class="btn btn-success btn-sm" onclick="return confirm('Setujui perubahan ini?')">
                                        <i class="bi bi-check-lg"></i> Setujui
                                    </button>
                                </form>

                                <button type="button" class="btn btn-danger btn-sm btn-tolak" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalTolak" 
                                        data-id="<?= $row['id_pengajuan']; ?>">
                                    <i class="bi bi-x-lg"></i> Tolak
                                </button>
                            </td>
                        </tr>
                        <?php 
                            } 
                        } else {
                            echo "<tr><td colspan='4' class='text-center text-muted py-4'>Tidak ada pengajuan pending.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTolak" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Tolak Pengajuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id_pengajuan" id="id_tolak_input">
                    
                    <div class="mb-3">
                        <label class="form-label">Pilih Alasan Penolakan:</label>
                        <select class="form-select" name="alasan_opsi" id="pilihan_alasan" required>
                            <option value="">-- Pilih Alasan --</option>
                            <option value="Data tidak valid/typo">Data tidak valid / Typo</option>
                            <option value="Dokumen pendukung kurang">Dokumen pendukung kurang</option>
                            <option value="Data tidak sesuai dengan KK">Data tidak sesuai dengan KK</option>
                            <option value="Lainnya">Lainnya (Tulis Sendiri)</option>
                        </select>
                    </div>

                    <div class="mb-3" id="box_alasan_lain" style="display:none;">
                        <label class="form-label">Tulis Alasan Spesifik:</label>
                        <textarea class="form-control" name="alasan_custom" rows="3" placeholder="Jelaskan alasan penolakan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="aksi" value="Tolak" class="btn btn-danger">Kirim Penolakan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
    // 1. Saat tombol Tolak di tabel diklik
    var btnTolak = document.querySelectorAll('.btn-tolak');
    btnTolak.forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = this.getAttribute('data-id');
            // Masukkan ID ke dalam input hidden di modal
            document.getElementById('id_tolak_input').value = id;
        });
    });

    // 2. Tampilkan Textarea jika pilih "Lainnya"
    document.getElementById('pilihan_alasan').addEventListener('change', function() {
        var boxLain = document.getElementById('box_alasan_lain');
        if(this.value === 'Lainnya') {
            boxLain.style.display = 'block';
            document.querySelector('textarea[name="alasan_custom"]').required = true;
        } else {
            boxLain.style.display = 'none';
            document.querySelector('textarea[name="alasan_custom"]').required = false;
        }
    });
</script>

<?php include "footer.php" ?>