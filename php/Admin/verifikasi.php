<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['nik']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// [LOG] AMBIL ID ADMIN
$nik_admin_session = $_SESSION['nik'];
$q_admin_log = mysqli_query($koneksi, "SELECT id_admin FROM admin WHERE nik = '$nik_admin_session'");
$d_admin_log = mysqli_fetch_array($q_admin_log);
$id_admin_log = $d_admin_log['id_admin'];

// LOGIKA PROSES
if (isset($_POST['aksi'])) {
    $id_pengajuan = mysqli_real_escape_string($koneksi, $_POST['id_pengajuan']);
    $aksi = $_POST['aksi'];

    if ($aksi == 'Setujui') {
        $q_pengajuan = mysqli_query($koneksi, "SELECT * FROM pengajuan_perubahan WHERE id_pengajuan='$id_pengajuan'");
        $data_baru = mysqli_fetch_array($q_pengajuan);

        if ($data_baru) {
            $nik_target = $data_baru['nik'];
            $nama_baru = mysqli_real_escape_string($koneksi, $data_baru['nama_baru']);
            
            // ... (Ambil semua variabel data baru lainnya sama seperti sebelumnya) ...
            $tempat_lahir   = mysqli_real_escape_string($koneksi, $data_baru['tempat_lahir_baru']);
            $tanggal_lahir  = $data_baru['tanggal_lahir_baru'];
            $jenis_kelamin  = $data_baru['jenis_kelamin_baru'];
            $agama          = mysqli_real_escape_string($koneksi, $data_baru['agama_baru']);
            $no_hp          = mysqli_real_escape_string($koneksi, $data_baru['no_hp_baru']);
            $pendidikan     = mysqli_real_escape_string($koneksi, $data_baru['pendidikan_baru']);
            $pekerjaan      = mysqli_real_escape_string($koneksi, $data_baru['pekerjaan_baru']);
            $status_kawin   = $data_baru['status_perkawinan_baru'];
            $status_hub     = $data_baru['status_hub_keluarga_baru'];

            // VALIDASI KEPALA KELUARGA (Sama seperti sebelumnya)
            $validasi_aman = true;
            $no_kk_target = "";

            if($status_hub == 'Kepala Keluarga') {
                $q_warga_lama = mysqli_query($koneksi, "SELECT no_kk FROM warga WHERE nik='$nik_target'");
                $d_warga_lama = mysqli_fetch_array($q_warga_lama);
                $no_kk_target = $d_warga_lama['no_kk'];

                $cek_head = mysqli_query($koneksi, "SELECT nama FROM warga WHERE no_kk = '$no_kk_target' AND status_hub_keluarga = 'Kepala Keluarga' AND nik != '$nik_target'");
                if(mysqli_num_rows($cek_head) > 0) {
                    $existing = mysqli_fetch_assoc($cek_head);
                    $validasi_aman = false;
                    echo "<script>alert('GAGAL: KK ini sudah memiliki Kepala Keluarga (".$existing['nama'].").'); window.location='verifikasi.php';</script>";
                }
            }

            if($validasi_aman) {
                // UPDATE WARGA
                $query_update = "UPDATE warga SET 
                    nama = '$nama_baru',
                    tempat_lahir = '$tempat_lahir',
                    tanggal_lahir = '$tanggal_lahir',
                    jenis_kelamin = '$jenis_kelamin',
                    agama = '$agama',
                    no_hp = '$no_hp',
                    pendidikan = '$pendidikan',
                    pekerjaan = '$pekerjaan',
                    status_perkawinan = '$status_kawin',
                    status_hub_keluarga = '$status_hub'
                    WHERE nik='$nik_target'";

                if (mysqli_query($koneksi, $query_update)) {
                    // Sinkronisasi KK
                    if ($status_hub == 'Kepala Keluarga' && !empty($no_kk_target)) {
                        mysqli_query($koneksi, "UPDATE keluarga SET kepala_keluarga = '$nama_baru' WHERE no_kk = '$no_kk_target'");
                    }

                    // Update Status Pengajuan
                    mysqli_query($koneksi, "UPDATE pengajuan_perubahan SET status='Disetujui' WHERE id_pengajuan='$id_pengajuan'");

                    // [LOG] CATAT PERSETUJUAN
                    $log_detail = "Menyetujui perubahan data warga. NIK: $nik_target, Nama Baru: $nama_baru";
                    mysqli_query($koneksi, "INSERT INTO log_warga (nik, aksi, detail, id_admin) VALUES ('$nik_target', 'Perubahan Disetujui', '$log_detail', '$id_admin_log')");

                    echo "<script>alert('SUKSES! Data diperbarui.'); window.location='verifikasi.php';</script>";
                } else {
                    echo "<script>alert('GAGAL UPDATE: " . mysqli_error($koneksi) . "'); window.location='verifikasi.php';</script>";
                }
            }
        }
    } elseif ($aksi == 'Tolak') {
        // ... (Logika tolak sama) ...
        $alasan_final = ($_POST['alasan_opsi'] == 'Lainnya') ? $_POST['alasan_custom'] : $_POST['alasan_opsi'];
        $alasan_final = mysqli_real_escape_string($koneksi, $alasan_final);
        
        $q_nik = mysqli_query($koneksi, "SELECT nik, nama_baru FROM pengajuan_perubahan WHERE id_pengajuan='$id_pengajuan'");
        $d_nik = mysqli_fetch_array($q_nik);
        $nik_target = $d_nik['nik'];

        $tolak = mysqli_query($koneksi, "UPDATE pengajuan_perubahan SET status='Ditolak', alasan_ditolak='$alasan_final' WHERE id_pengajuan='$id_pengajuan'");
        
        if($tolak) {
            // [LOG] CATAT PENOLAKAN
            $log_detail = "Menolak pengajuan perubahan data NIK: $nik_target. Alasan: $alasan_final";
            mysqli_query($koneksi, "INSERT INTO log_warga (nik, aksi, detail, id_admin) VALUES ('$nik_target', 'Pengajuan Ditolak', '$log_detail', '$id_admin_log')");

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
            <h5 class="text-dark">Daftar Pengajuan</h5>
            <div class="table-responsive p-3">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tgl Request</th>
                            <th>NIK / Nama</th>
                            <th>Detail Perubahan</th>
                            <th width="200">Aksi</th>
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
                                <span class="badge bg-secondary"><?= $row['nik']; ?></span><br>
                                <b><?= $row['nama']; ?></b>
                            </td>
                            <td>
                                <ul class="mb-0 ps-3 small">
                                    <?php 
                                    // Bandingkan Data Lama vs Baru
                                    if($row['nama'] != $row['nama_baru']) 
                                        echo "<li>Nama: <s class='text-muted'>{$row['nama']}</s> <i class='bi bi-arrow-right text-primary'></i> <b>{$row['nama_baru']}</b></li>";
                                    
                                    if($row['pekerjaan'] != $row['pekerjaan_baru']) 
                                        echo "<li>Pekerjaan: <s class='text-muted'>{$row['pekerjaan']}</s> <i class='bi bi-arrow-right text-primary'></i> <b>{$row['pekerjaan_baru']}</b></li>";
                                    
                                    if($row['status_perkawinan'] != $row['status_perkawinan_baru']) 
                                        echo "<li>Status: <s class='text-muted'>{$row['status_perkawinan']}</s> <i class='bi bi-arrow-right text-primary'></i> <b>{$row['status_perkawinan_baru']}</b></li>";
                                    
                                    if($row['status_hub_keluarga'] != $row['status_hub_keluarga_baru'] && !empty($row['status_hub_keluarga_baru'])) 
                                        echo "<li>Hubungan: <s class='text-muted'>{$row['status_hub_keluarga']}</s> <i class='bi bi-arrow-right text-primary'></i> <b>{$row['status_hub_keluarga_baru']}</b></li>";

                                    if($row['no_hp'] != $row['no_hp_baru']) 
                                        echo "<li>No HP: <s class='text-muted'>{$row['no_hp']}</s> <i class='bi bi-arrow-right text-primary'></i> <b>{$row['no_hp_baru']}</b></li>";
                                    
                                    echo "<li class='text-muted fst-italic'>Cek detail lain pada formulir...</li>";
                                    ?>
                                </ul>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <form method="POST">
                                        <input type="hidden" name="id_pengajuan" value="<?= $row['id_pengajuan']; ?>">
                                        <button type="submit" name="aksi" value="Setujui" class="btn btn-success btn-sm" onclick="return confirm('Yakin ingin menyetujui perubahan data ini? Data warga akan langsung berubah.')">
                                            <i class="bi bi-check-lg"></i> Terima
                                        </button>
                                    </form>

                                    <button type="button" class="btn btn-danger btn-sm btn-tolak" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalTolak" 
                                            data-id="<?= $row['id_pengajuan']; ?>">
                                        <i class="bi bi-x-lg"></i> Tolak
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php 
                            } 
                        } else {
                            echo "<tr><td colspan='4' class='text-center text-muted py-4'>Tidak ada pengajuan pending saat ini.</td></tr>";
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
                        <label class="form-label fw-bold">Alasan Penolakan:</label>
                        <select class="form-select" name="alasan_opsi" id="pilihan_alasan" required>
                            <option value="">-- Pilih Alasan --</option>
                            <option value="Data tidak valid / Typo">Data tidak valid / Typo</option>
                            <option value="Dokumen pendukung kurang lengkap">Dokumen pendukung kurang lengkap</option>
                            <option value="Data tidak sesuai dengan KK Fisik">Data tidak sesuai dengan KK Fisik</option>
                            <option value="Lainnya">Lainnya (Tulis Sendiri)</option>
                        </select>
                    </div>

                    <div class="mb-3" id="box_alasan_lain" style="display:none;">
                        <label class="form-label fw-bold">Tulis Alasan Spesifik:</label>
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
    var btnTolak = document.querySelectorAll('.btn-tolak');
    btnTolak.forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = this.getAttribute('data-id');
            document.getElementById('id_tolak_input').value = id;
        });
    });

    document.getElementById('pilihan_alasan').addEventListener('change', function() {
        var boxLain = document.getElementById('box_alasan_lain');
        var inputCustom = document.querySelector('textarea[name="alasan_custom"]');
        if(this.value === 'Lainnya') {
            boxLain.style.display = 'block';
            inputCustom.required = true;
        } else {
            boxLain.style.display = 'none';
            inputCustom.required = false;
        }
    });
</script>

<?php include "footer.php" ?>