<?php
session_start();
include '../koneksi.php'; 

if (!isset($_SESSION['nik']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$nik_admin_session = $_SESSION['nik'];
$q_admin_log = mysqli_query($koneksi, "SELECT id_admin FROM admin WHERE nik = '$nik_admin_session'");
$d_admin_log = mysqli_fetch_array($q_admin_log);
$id_admin_log = $d_admin_log['id_admin'];

$list_kk = [];
$query_kk_list = mysqli_query($koneksi, "SELECT no_kk, kepala_keluarga FROM keluarga ORDER BY no_kk ASC");
while($row_kk = mysqli_fetch_assoc($query_kk_list)) {
    $list_kk[] = $row_kk;
}

if (isset($_POST['tambah_warga'])) {
    $nik            = mysqli_real_escape_string($koneksi, $_POST['nik']);
    $nama           = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $no_kk_input    = $_POST['no_kk'];
    $no_kk          = !empty($no_kk_input) ? "'".mysqli_real_escape_string($koneksi, $no_kk_input)."'" : "NULL";
    $tempat_lahir   = mysqli_real_escape_string($koneksi, $_POST['tempat_lahir']);
    $tanggal_lahir  = mysqli_real_escape_string($koneksi, $_POST['tanggal_lahir']);
    $jenis_kelamin  = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin']);
    $status_hub     = mysqli_real_escape_string($koneksi, $_POST['status_hub_keluarga']);
    $status_kawin   = mysqli_real_escape_string($koneksi, $_POST['status_perkawinan']);
    $pendidikan     = mysqli_real_escape_string($koneksi, $_POST['pendidikan']);
    $pekerjaan      = mysqli_real_escape_string($koneksi, $_POST['pekerjaan']);
    $agama          = mysqli_real_escape_string($koneksi, $_POST['agama']);
    $no_hp          = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    $password_input = $_POST['password']; 
    $password_hash  = password_hash($password_input, PASSWORD_DEFAULT);
    $cek_nik = mysqli_query($koneksi, "SELECT nik FROM warga WHERE nik = '$nik'");

    $validasi_kk_aman = true;
    if ($status_hub == 'Kepala Keluarga' && !empty($no_kk_input)) {
        $cek_head = mysqli_query($koneksi, "SELECT nama FROM warga WHERE no_kk = '$no_kk_input' AND status_hub_keluarga = 'Kepala Keluarga'");
        if (mysqli_num_rows($cek_head) > 0) {
            $existing_head = mysqli_fetch_assoc($cek_head);
            $validasi_kk_aman = false;
            echo "<script>alert('GAGAL: KK ini sudah memiliki Kepala Keluarga (".$existing_head['nama'].").');</script>";
        }
    }

    if (mysqli_num_rows($cek_nik) > 0) {
        echo "<script>alert('Gagal: NIK sudah terdaftar!');</script>";
    } elseif (!$validasi_kk_aman) {

    } else {
        $query_tambah = "INSERT INTO warga (
            nik, no_kk, nama, password, tempat_lahir, tanggal_lahir, jenis_kelamin, 
            status_hub_keluarga, status_perkawinan, pendidikan, pekerjaan, agama, no_hp
        ) VALUES (
            '$nik', $no_kk, '$nama', '$password_hash', '$tempat_lahir', '$tanggal_lahir', '$jenis_kelamin', 
            '$status_hub', '$status_kawin', '$pendidikan', '$pekerjaan', '$agama', '$no_hp'
        )";
        if (mysqli_query($koneksi, $query_tambah)) {
            if ($status_hub == 'Kepala Keluarga' && !empty($no_kk_input)) {
                mysqli_query($koneksi, "UPDATE keluarga SET kepala_keluarga = '$nama' WHERE no_kk = '$no_kk_input'");
            }
            $log_detail = "Menambahkan warga baru. NIK: $nik, Nama: $nama";
            mysqli_query($koneksi, "INSERT INTO log_warga (nik, aksi, detail, id_admin) VALUES ('$nik', 'Tambah Warga', '$log_detail', '$id_admin_log')");
            echo "<script>alert('Data Warga Berhasil Ditambahkan!'); window.location='data_wrg.php';</script>";
        } else {
            echo "<script>alert('Gagal Tambah: " . mysqli_error($koneksi) . "');</script>";
        }
    }
}

if (isset($_POST['update_warga'])) {
    $nik_lama       = $_POST['nik']; 
    $no_kk_input    = $_POST['no_kk'];
    $no_kk_update   = !empty($no_kk_input) ? "'".mysqli_real_escape_string($koneksi, $no_kk_input)."'" : "NULL";
    $nama           = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $tempat_lahir   = mysqli_real_escape_string($koneksi, $_POST['tempat_lahir']);
    $tanggal_lahir  = mysqli_real_escape_string($koneksi, $_POST['tanggal_lahir']);
    $jenis_kelamin  = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin']);
    $status_hub     = mysqli_real_escape_string($koneksi, $_POST['status_hub_keluarga']);
    $status_kawin   = mysqli_real_escape_string($koneksi, $_POST['status_perkawinan']);
    $pendidikan     = mysqli_real_escape_string($koneksi, $_POST['pendidikan']);
    $pekerjaan      = mysqli_real_escape_string($koneksi, $_POST['pekerjaan']);
    $agama          = mysqli_real_escape_string($koneksi, $_POST['agama']);
    $no_hp          = mysqli_real_escape_string($koneksi, $_POST['no_hp']);

    $validasi_kk_aman = true;
    if ($status_hub == 'Kepala Keluarga' && !empty($no_kk_input)) {
        $cek_head = mysqli_query($koneksi, "SELECT nama FROM warga WHERE no_kk = '$no_kk_input' AND status_hub_keluarga = 'Kepala Keluarga' AND nik != '$nik_lama'");
        if (mysqli_num_rows($cek_head) > 0) {
            $existing_head = mysqli_fetch_assoc($cek_head);
            $validasi_kk_aman = false;
            echo "<script>alert('GAGAL UPDATE: KK ini sudah memiliki Kepala Keluarga.');</script>";
        }
    }

    if ($validasi_kk_aman) {
        $query_update = "UPDATE warga SET 
                        no_kk = $no_kk_update, 
                        nama = '$nama',
                        tempat_lahir = '$tempat_lahir',
                        tanggal_lahir = '$tanggal_lahir',
                        jenis_kelamin = '$jenis_kelamin',
                        status_hub_keluarga = '$status_hub',
                        status_perkawinan = '$status_kawin',
                        pendidikan = '$pendidikan',
                        pekerjaan = '$pekerjaan',
                        agama = '$agama',
                        no_hp = '$no_hp'
                        WHERE nik = '$nik_lama'";

        if (mysqli_query($koneksi, $query_update)) {
            if ($status_hub == 'Kepala Keluarga' && !empty($no_kk_input)) {
                mysqli_query($koneksi, "UPDATE keluarga SET kepala_keluarga = '$nama' WHERE no_kk = '$no_kk_input'");
            }

            $log_detail = "Mengubah data profil warga. NIK: $nik_lama, Nama: $nama";
            mysqli_query($koneksi, "INSERT INTO log_warga (nik, aksi, detail, id_admin) VALUES ('$nik_lama', 'Edit Warga', '$log_detail', '$id_admin_log')");
            echo "<script>alert('Data Berhasil Diperbarui!'); window.location='data_wrg.php';</script>";
        } else {
            echo "<script>alert('Gagal Update: " . mysqli_error($koneksi) . "');</script>";
        }
    }
}

if (isset($_GET['hapus'])) {
    $nik_hapus = $_GET['hapus'];
    $cek_hapus = mysqli_query($koneksi, "SELECT nama, no_kk, status_hub_keluarga FROM warga WHERE nik = '$nik_hapus'");
    $data_hapus = mysqli_fetch_assoc($cek_hapus);
    $nama_dihapus = $data_hapus['nama'];
    $query_hapus = "DELETE FROM warga WHERE nik = '$nik_hapus'";
    
    if (mysqli_query($koneksi, $query_hapus)) {
        if ($data_hapus['status_hub_keluarga'] == 'Kepala Keluarga' && !empty($data_hapus['no_kk'])) {
            $kk_target = $data_hapus['no_kk'];
            mysqli_query($koneksi, "UPDATE keluarga SET kepala_keluarga = NULL WHERE no_kk = '$kk_target'");
        }
        $log_detail = "Menghapus data warga. NIK: $nik_hapus, Nama: $nama_dihapus";
        mysqli_query($koneksi, "INSERT INTO log_warga (nik, aksi, detail, id_admin) VALUES (NULL, 'Hapus Warga', '$log_detail', '$id_admin_log')");
        echo "<script>alert('Data Berhasil Dihapus'); window.location='data_wrg.php';</script>";
    } else {
        echo "<script>alert('Gagal Hapus: " . mysqli_error($koneksi) . "');</script>";
    }
}

$data_warga = [];
$query = "SELECT warga.*, keluarga.alamat FROM warga 
          LEFT JOIN keluarga ON warga.no_kk = keluarga.no_kk 
          ORDER BY warga.nama ASC";
$result = mysqli_query($koneksi, $query);
while($row = mysqli_fetch_assoc($result)) {
    $data_warga[] = $row;
}
?>

<?php include "header.php" ?>
<?php include "sidebar.php" ?>

<div class="content">
    <div id="warga" class="page">
        <h2>Data Warga</h2>
        
        <div class="card">
            <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="bi bi-person-plus-fill"></i> Tambah Warga
            </button>

            <div class="table-responsive p-0">
                <table class="table table-hover">
                    <thead> 
                        <tr>
                            <th>No</th>
                            <th>NIK</th>
                            <th>No KK</th> <th>Nama</th>
                            <th>J. Kelamin</th>
                            <th>No. HP</th>
                            <th class="sticky-aksi text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        foreach($data_warga as $row): 
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $row['nik']; ?></td>
                            <td><span class="badge bg-info text-dark"><?php echo $row['no_kk'] ? $row['no_kk'] : 'Non-KK'; ?></span></td>
                            <td><?php echo $row['nama']; ?></td>
                            <td><?php echo $row['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?></td>
                            <td><?php echo $row['no_hp']; ?></td>
                            <td class="sticky-aksi text-center">
                                <button class="btn btn-lihat btn-sm" data-bs-toggle="modal" data-bs-target="#modalDetail<?php echo $row['nik']; ?>"> 
                                    <i class="bi bi-eye-fill"></i> Detail
                                </button>
                                
                                <button class="btn btn-edit btn-sm" data-bs-toggle="modal" data-bs-target="#modalEdit<?php echo $row['nik']; ?>">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </button> 
                                
                                <a href="data_wrg.php?hapus=<?php echo $row['nik']; ?>" class="btn btn-hapus btn-sm" onclick="return confirm('Hapus data <?php echo $row['nama']; ?>?')">
                                    <i class="bi bi-trash-fill"></i> Hapus
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-dark">
                <h5 class="modal-title fw-bold">Tambah Data Warga Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">NIK (Wajib)</label>
                            <input type="text" name="nik" class="form-control" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nomor KK (Keluarga)</label>
                            <select name="no_kk" class="form-select">
                                <option value="">-- Pilih Kartu Keluarga --</option>
                                <?php foreach($list_kk as $kk): ?>
                                    <option value="<?php echo $kk['no_kk']; ?>">
                                        <?php echo $kk['no_kk']; ?> - <?php echo $kk['kepala_keluarga']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Password Akun</label>
                            <input type="text" name="password" class="form-control" placeholder="Buat password untuk login" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Agama</label>
                            <select name="agama" class="form-select">
                                <option value="Islam">Islam</option>
                                <option value="Kristen">Kristen</option>
                                <option value="Katolik">Katolik</option>
                                <option value="Hindu">Hindu</option>
                                <option value="Buddha">Buddha</option>
                                <option value="Konghucu">Konghucu</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Status Hubungan Keluarga</label>
                            <select name="status_hub_keluarga" class="form-select">
                                <option value="Kepala Keluarga">Kepala Keluarga</option>
                                <option value="Istri">Istri</option>
                                <option value="Anak">Anak</option>
                                <option value="Famili Lain">Famili Lain</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Status Perkawinan</label>
                            <select name="status_perkawinan" class="form-select">
                                <option value="Belum Kawin">Belum Kawin</option>
                                <option value="Kawin">Kawin</option>
                                <option value="Cerai Hidup">Cerai Hidup</option>
                                <option value="Cerai Mati">Cerai Mati</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Pendidikan</label>
                            <input type="text" name="pendidikan" class="form-control">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Pekerjaan</label>
                            <input type="text" name="pekerjaan" class="form-control">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">No. HP (WA)</label>
                            <input type="text" name="no_hp" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah_warga" class="btn btn-primary">Simpan Data Baru</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php foreach($data_warga as $row): ?>

<div class="modal fade" id="modalDetail<?php echo $row['nik']; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Detail Data Warga</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-2"><label class="fw-bold">NIK:</label> <p><?php echo $row['nik']; ?></p></div>
                    <div class="col-md-6 mb-2"><label class="fw-bold">Nama:</label> <p><?php echo $row['nama']; ?></p></div>
                    <div class="col-md-6 mb-2"><label class="fw-bold">No KK:</label> <p><?php echo $row['no_kk']; ?></p></div>
                    <div class="col-md-6 mb-2"><label class="fw-bold">Alamat:</label> <p><?php echo $row['alamat']; ?></p></div>
                    <div class="col-md-6 mb-2"><label class="fw-bold">TTL:</label> <p><?php echo $row['tempat_lahir'] . ", " . $row['tanggal_lahir']; ?></p></div>
                    <div class="col-md-6 mb-2"><label class="fw-bold">Gender:</label> <p><?php echo $row['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?></p></div>
                    <div class="col-md-6 mb-2"><label class="fw-bold">Agama:</label> <p><?php echo $row['agama']; ?></p></div>
                    <div class="col-md-6 mb-2"><label class="fw-bold">Status Hub:</label> <p><?php echo $row['status_hub_keluarga']; ?></p></div>
                    <div class="col-md-6 mb-2"><label class="fw-bold">Status Kawin:</label> <p><?php echo $row['status_perkawinan']; ?></p></div>
                    <div class="col-md-6 mb-2"><label class="fw-bold">Pendidikan:</label> <p><?php echo $row['pendidikan']; ?></p></div>
                    <div class="col-md-6 mb-2"><label class="fw-bold">Pekerjaan:</label> <p><?php echo $row['pekerjaan']; ?></p></div>
                    <div class="col-md-6 mb-2"><label class="fw-bold">No HP:</label> <p><?php echo $row['no_hp']; ?></p></div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                
                <button type="button" class="btn btn-info text-white fw-bold" data-bs-toggle="modal" data-bs-target="#modalRiwayat<?php echo $row['nik']; ?>">
                    <i class="bi bi-clock-history"></i> Lihat Riwayat Perubahan
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalRiwayat<?php echo $row['nik']; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable"> <div class="modal-content">
            <div class="modal-header text-dark">
                <h5 class="modal-title fw-bold"><i class="bi bi-clock-history me-2"></i>Riwayat Aktivitas: <?php echo $row['nama']; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light">
                
                <?php
                $nik_target_log = $row['nik'];
                $q_log = mysqli_query($koneksi, "SELECT log_warga.*, admin.nama as nama_admin 
                                                 FROM log_warga 
                                                 LEFT JOIN admin ON log_warga.id_admin = admin.id_admin
                                                 WHERE log_warga.nik = '$nik_target_log' 
                                                 ORDER BY log_warga.waktu DESC");
                if(mysqli_num_rows($q_log) > 0) {
                    echo '<div class="list-group">';
                    while($log = mysqli_fetch_array($q_log)) {
                        $waktu = date('d M Y, H:i', strtotime($log['waktu']));
                        $badge_color = 'bg-secondary';
                        if($log['aksi'] == 'Tambah Warga') $badge_color = 'bg-success';
                        if($log['aksi'] == 'Edit Warga') $badge_color = 'bg-warning text-dark';
                        if($log['aksi'] == 'Hapus Warga') $badge_color = 'bg-danger';
                        if($log['aksi'] == 'Perubahan Disetujui') $badge_color = 'bg-primary';
                        if($log['aksi'] == 'Pengajuan Ditolak') $badge_color = 'bg-danger';
                        echo '
                        <div class="list-group-item list-group-item-action mb-2 border rounded shadow-sm">
                            <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                                <h6 class="mb-1 fw-bold text-primary">'.$log['aksi'].'</h6>
                                <small class="text-muted"><i class="bi bi-calendar-event me-1"></i>'.$waktu.'</small>
                            </div>
                            <p class="mb-1 text-dark">'.$log['detail'].'</p>
                            <small class="text-muted fst-italic">
                                <i class="bi bi-person-gear me-1"></i>Oleh: '.($log['nama_admin'] ? $log['nama_admin'] : 'Sistem/Warga').'
                            </small>
                        </div>';
                    }
                    echo '</div>';
                } else {
                    echo '
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-clipboard-x display-4 mb-3 d-block"></i>
                        <p>Belum ada riwayat perubahan untuk warga ini.</p>
                    </div>';
                }
                ?>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modalDetail<?php echo $row['nik']; ?>">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Detail
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEdit<?php echo $row['nik']; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"> 
                <h5 class="modal-title text-dark">Edit Data Warga</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">NIK (Tidak bisa diubah)</label>
                            <input type="text" name="nik" class="form-control bg-light" value="<?php echo $row['nik']; ?>" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nomor KK (Keluarga)</label>
                            <select name="no_kk" class="form-select">
                                <option value="">-- Pilih Kartu Keluarga --</option>
                                <?php foreach($list_kk as $kk): ?>
                                    <option value="<?php echo $kk['no_kk']; ?>" <?php if($row['no_kk'] == $kk['no_kk']) echo 'selected'; ?>>
                                        <?php echo $kk['no_kk']; ?> - <?php echo $kk['kepala_keluarga']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" value="<?php echo $row['nama']; ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control" value="<?php echo $row['tempat_lahir']; ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control" value="<?php echo $row['tanggal_lahir']; ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select">
                                <option value="L" <?php if($row['jenis_kelamin'] == 'L') echo 'selected'; ?>>Laki-laki</option>
                                <option value="P" <?php if($row['jenis_kelamin'] == 'P') echo 'selected'; ?>>Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Agama</label>
                            <select name="agama" class="form-select">
                                <option value="Islam" <?php if($row['agama'] == 'Islam') echo 'selected'; ?>>Islam</option>
                                <option value="Kristen" <?php if($row['agama'] == 'Kristen') echo 'selected'; ?>>Kristen</option>
                                <option value="Katolik" <?php if($row['agama'] == 'Katolik') echo 'selected'; ?>>Katolik</option>
                                <option value="Hindu" <?php if($row['agama'] == 'Hindu') echo 'selected'; ?>>Hindu</option>
                                <option value="Buddha" <?php if($row['agama'] == 'Buddha') echo 'selected'; ?>>Buddha</option>
                                <option value="Konghucu" <?php if($row['agama'] == 'Konghucu') echo 'selected'; ?>>Konghucu</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Status Hubungan</label>
                            <select name="status_hub_keluarga" class="form-select">
                                <option value="Kepala Keluarga" <?php if($row['status_hub_keluarga'] == 'Kepala Keluarga') echo 'selected'; ?>>Kepala Keluarga</option>
                                <option value="Suami" <?php if($row['status_hub_keluarga'] == 'Suami') echo 'selected'; ?>>Suami</option>
                                <option value="Istri" <?php if($row['status_hub_keluarga'] == 'Istri') echo 'selected'; ?>>Istri</option>
                                <option value="Anak" <?php if($row['status_hub_keluarga'] == 'Anak') echo 'selected'; ?>>Anak</option>
                                <option value="Famili Lain" <?php if($row['status_hub_keluarga'] == 'Famili Lain') echo 'selected'; ?>>Famili Lain</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Status Perkawinan</label>
                            <select name="status_perkawinan" class="form-select">
                                <option value="Belum Kawin" <?php if($row['status_perkawinan'] == 'Belum Kawin') echo 'selected'; ?>>Belum Kawin</option>
                                <option value="Kawin" <?php if($row['status_perkawinan'] == 'Kawin') echo 'selected'; ?>>Kawin</option>
                                <option value="Cerai Hidup" <?php if($row['status_perkawinan'] == 'Cerai Hidup') echo 'selected'; ?>>Cerai Hidup</option>
                                <option value="Cerai Mati" <?php if($row['status_perkawinan'] == 'Cerai Mati') echo 'selected'; ?>>Cerai Mati</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Pendidikan</label>
                            <input type="text" name="pendidikan" class="form-control" value="<?php echo $row['pendidikan']; ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Pekerjaan</label>
                            <input type="text" name="pekerjaan" class="form-control" value="<?php echo $row['pekerjaan']; ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">No. HP</label>
                            <input type="text" name="no_hp" class="form-control" value="<?php echo $row['no_hp']; ?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="update_warga" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>
<?php include "footer.php" ?>