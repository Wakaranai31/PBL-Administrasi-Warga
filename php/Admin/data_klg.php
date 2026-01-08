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

if (isset($_POST['tambah_kk'])) {
    $no_kk      = mysqli_real_escape_string($koneksi, $_POST['no_kk']);
    $kepala     = mysqli_real_escape_string($koneksi, $_POST['kepala_keluarga']);
    $alamat     = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $rt         = mysqli_real_escape_string($koneksi, $_POST['rt']);
    $rw         = mysqli_real_escape_string($koneksi, $_POST['rw']);
    $kode_pos   = mysqli_real_escape_string($koneksi, $_POST['kode_pos']);
    $kelurahan  = mysqli_real_escape_string($koneksi, $_POST['kelurahan']);
    $kecamatan  = mysqli_real_escape_string($koneksi, $_POST['kecamatan']);
    $kota       = mysqli_real_escape_string($koneksi, $_POST['kota']);
    $provinsi   = mysqli_real_escape_string($koneksi, $_POST['provinsi']);
    $cek = mysqli_query($koneksi, "SELECT no_kk FROM keluarga WHERE no_kk = '$no_kk'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Gagal: Nomor KK sudah terdaftar!');</script>";
    } else {
        $query = "INSERT INTO keluarga (no_kk, kepala_keluarga, alamat, rt, rw, kode_pos, kelurahan, kecamatan, kota, provinsi)
                  VALUES ('$no_kk', '$kepala', '$alamat', '$rt', '$rw', '$kode_pos', '$kelurahan', '$kecamatan', '$kota', '$provinsi')";
        
        if (mysqli_query($koneksi, $query)) {
            $log_detail = "Menambahkan Kartu Keluarga Baru. No KK: $no_kk, Kepala: $kepala";
            mysqli_query($koneksi, "INSERT INTO log_warga (nik, aksi, detail, id_admin) VALUES (NULL, 'Tambah KK', '$log_detail', '$id_admin_log')");

            echo "<script>alert('Data KK Berhasil Ditambahkan!'); window.location='data_klg.php';</script>";
        } else {
            echo "<script>alert('Gagal Tambah: " . mysqli_error($koneksi) . "');</script>";
        }
    }
}

if (isset($_POST['update_kk'])) {
    $no_kk_lama = $_POST['no_kk_lama']; 
    $no_kk_baru = mysqli_real_escape_string($koneksi, $_POST['no_kk']);
    $kepala     = mysqli_real_escape_string($koneksi, $_POST['kepala_keluarga']);
    $alamat     = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $rt         = mysqli_real_escape_string($koneksi, $_POST['rt']);
    $rw         = mysqli_real_escape_string($koneksi, $_POST['rw']);
    $kode_pos   = mysqli_real_escape_string($koneksi, $_POST['kode_pos']);
    $kelurahan  = mysqli_real_escape_string($koneksi, $_POST['kelurahan']);
    $kecamatan  = mysqli_real_escape_string($koneksi, $_POST['kecamatan']);
    $kota       = mysqli_real_escape_string($koneksi, $_POST['kota']);
    $provinsi   = mysqli_real_escape_string($koneksi, $_POST['provinsi']);

    $query = "UPDATE keluarga SET 
            no_kk = '$no_kk_baru',
            kepala_keluarga = '$kepala',
            alamat = '$alamat',
            rt = '$rt',
            rw = '$rw',
            kode_pos = '$kode_pos',
            kelurahan = '$kelurahan',
            kecamatan = '$kecamatan',
            kota = '$kota',
            provinsi = '$provinsi'
            WHERE no_kk = '$no_kk_lama'";

    if (mysqli_query($koneksi, $query)) {
        $cek_warga_kepala = mysqli_query($koneksi, "SELECT nik FROM warga WHERE no_kk = '$no_kk_baru' AND status_hub_keluarga = 'Kepala Keluarga'");
        if(mysqli_num_rows($cek_warga_kepala) > 0) {
            // Update nama warga tersebut agar sama dengan inputan baru
            mysqli_query($koneksi, "UPDATE warga SET nama = '$kepala' WHERE no_kk = '$no_kk_baru' AND status_hub_keluarga = 'Kepala Keluarga'");
        }
        $log_detail = "Memperbarui Data KK No: $no_kk_lama. Kepala Keluarga: $kepala (Data warga terkait juga disinkronisasi)";
        mysqli_query($koneksi, "INSERT INTO log_warga (nik, aksi, detail, id_admin) VALUES (NULL, 'Edit KK', '$log_detail', '$id_admin_log')");

        echo "<script>alert('Data KK Berhasil Diperbarui & Sinkron dengan Data Warga!'); window.location='data_klg.php';</script>";
    } else {
        echo "<script>alert('Gagal Update: " . mysqli_error($koneksi) . "');</script>";
    }
}

if (isset($_GET['hapus'])) {
    $kk_hapus = $_GET['hapus'];
    $q_cek = mysqli_query($koneksi, "SELECT kepala_keluarga FROM keluarga WHERE no_kk='$kk_hapus'");
    $d_cek = mysqli_fetch_array($q_cek);
    $nama_kepala = $d_cek['kepala_keluarga'];

    $query = "DELETE FROM keluarga WHERE no_kk = '$kk_hapus'";
    if (mysqli_query($koneksi, $query)) {
        $log_detail = "Menghapus KK No: $kk_hapus ($nama_kepala) beserta seluruh anggota keluarga di dalamnya.";
        mysqli_query($koneksi, "INSERT INTO log_warga (nik, aksi, detail, id_admin) VALUES (NULL, 'Hapus KK', '$log_detail', '$id_admin_log')");
        echo "<script>alert('Data KK Berhasil Dihapus!'); window.location='data_klg.php';</script>";
    } else {
        echo "<script>alert('Gagal Hapus: " . mysqli_error($koneksi) . "');</script>";
    }
}

$data_keluarga = [];
$result = mysqli_query($koneksi, "SELECT * FROM keluarga ORDER BY kepala_keluarga ASC");
while($row = mysqli_fetch_assoc($result)) {
    $data_keluarga[] = $row;
}
?>

<?php include "header.php" ?>
<?php include "sidebar.php" ?>

<div class="content">
    <div id="keluarga" class="page">
        <h2>Data Keluarga (Kartu Keluarga)</h2>
        
        <div class="card">
            <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambahKK">
                <i class="bi bi-plus-circle-fill"></i> Tambah KK Baru
            </button>
            <div class="table-responsive p-0">
                <table class="table table-hover">
                    <thead> 
                        <tr>
                            <th>No</th>
                            <th>Nomor KK</th>
                            <th>Kepala Keluarga</th>
                            <th>Alamat Singkat</th>
                            <th class="text-center">RT / RW</th>
                            <th class="sticky-aksi text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        foreach($data_keluarga as $row): 
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><span class="badge bg-secondary"><?php echo $row['no_kk']; ?></span></td>
                            <td><b><?php echo $row['kepala_keluarga']; ?></b></td>
                            <td><?php echo $row['alamat']; ?></td>
                            <td class="text-center"><?php echo $row['rt']; ?> / <?php echo $row['rw']; ?></td>
                            <td class="sticky-aksi text-center">
                                <button class="btn btn-lihat btn-sm" data-bs-toggle="modal" data-bs-target="#modalDetailKK<?php echo $row['no_kk']; ?>">
                                    <i class="bi bi-eye-fill"></i> Lihat
                                </button>
                                <button class="btn btn-edit btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditKK<?php echo $row['no_kk']; ?>">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </button> 
                                
                                <a href="data_klg.php?hapus=<?php echo $row['no_kk']; ?>" class="btn btn-hapus btn-sm" onclick="return confirm('PERINGATAN: Menghapus KK ini akan MENGHAPUS SELURUH DATA WARGA yang terdaftar di dalamnya.\n\nYakin ingin menghapus KK Bpk. <?php echo $row['kepala_keluarga']; ?>?')">
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

<div class="modal fade" id="modalTambahKK" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-dark">
                <h5 class="modal-title fw-bold">Tambah Kartu Keluarga Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Nomor KK</label>
                            <input type="text" name="no_kk" class="form-control" placeholder="16 Digit No. KK" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Kepala Keluarga</label>
                            <input type="text" name="kepala_keluarga" class="form-control" placeholder="Nama Kepala Keluarga" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="fw-bold">Alamat Lengkap</label>
                            <textarea name="alamat" class="form-control" rows="2" placeholder="Nama Jalan, Blok, No. Rumah" required></textarea>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="fw-bold">RT</label>
                            <input type="text" name="rt" class="form-control" value="003" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="fw-bold">RW</label>
                            <input type="text" name="rw" class="form-control" value="005" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Kode Pos</label>
                            <input type="text" name="kode_pos" class="form-control" value="29400">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Kelurahan</label>
                            <input type="text" name="kelurahan" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Kecamatan</label>
                            <input type="text" name="kecamatan" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Kota/Kabupaten</label>
                            <input type="text" name="kota" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Provinsi</label>
                            <input type="text" name="provinsi" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah_kk" class="btn btn-primary">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php foreach($data_keluarga as $row): ?>

<div class="modal fade" id="modalDetailKK<?php echo $row['no_kk']; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered"> <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Detail Kartu Keluarga</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    
                    <div class="col-lg-7 border-end">
                        <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">
                            <i class="bi bi-info-circle-fill me-2"></i>Informasi Kartu Keluarga
                        </h6>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="small text-muted fw-bold d-block">Nomor KK</label>
                                <span class="fs-5 fw-bold text-dark"><?php echo $row['no_kk']; ?></span>
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted fw-bold d-block">Kepala Keluarga</label>
                                <span class="fw-bold text-dark"><?php echo $row['kepala_keluarga']; ?></span>
                            </div>
                            
                            <div class="col-12">
                                <label class="small text-muted fw-bold d-block">Alamat Lengkap</label>
                                <span class="text-dark"><?php echo $row['alamat']; ?></span>
                                <span class="text-dark">
                                    Kel. <?php echo $row['kelurahan']; ?>, <?php echo $row['kecamatan']; ?>,
                                    <?php echo $row['kota']; ?>, <?php echo $row['provinsi']; ?>
                                </span>
                            </div>
                            <div class="col-md-3">
                                <label class="small text-muted fw-bold d-block">RT</label>
                                <span class="text-dark"><?php echo $row['rt']; ?></span>
                            </div>
                            <div class="col-md-3">
                                <label class="small text-muted fw-bold d-block">RW</label>
                                <span class="text-dark"><?php echo $row['rw']; ?></span>
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted fw-bold d-block">Kode Pos</label>
                                <span class="text-dark"><?php echo $row['kode_pos']; ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <h6 class="fw-bold text-success mb-3 border-bottom pb-2">
                            <i class="bi bi-people-fill me-2"></i>Anggota Keluarga Terdaftar
                        </h6>
                        <div class="list-group list-group-flush border rounded" style="max-height: 350px; overflow-y: auto;">
                            <?php
                            $no_kk_detail = $row['no_kk'];
                            $query_anggota = mysqli_query($koneksi, "SELECT nama, status_hub_keluarga FROM warga WHERE no_kk = '$no_kk_detail' ORDER BY FIELD(status_hub_keluarga, 'Kepala Keluarga', 'Istri', 'Anak') ASC");
                            
                            if(mysqli_num_rows($query_anggota) > 0){
                                while($anggota = mysqli_fetch_assoc($query_anggota)){
                                    
                                    $badge_bg = 'bg-secondary';
                                    if($anggota['status_hub_keluarga'] == 'Kepala Keluarga') $badge_bg = 'bg-primary';
                                    if($anggota['status_hub_keluarga'] == 'Istri') $badge_bg = 'bg-success';
                                    if($anggota['status_hub_keluarga'] == 'Anak') $badge_bg = 'bg-info text-dark';
                                    
                                    echo '<div class="list-group-item d-flex justify-content-between align-items-center list-group-item-action">';
                                    echo '<div>';
                                    echo '<i class="bi bi-person-circle me-2 text-muted"></i>';
                                    echo '<span class="fw-bold">' . $anggota['nama'] . '</span>';
                                    echo '</div>';
                                    echo '<span class="badge '.$badge_bg.' rounded-pill" style="font-size: 0.7rem;">' . $anggota['status_hub_keluarga'] . '</span>';
                                    echo '</div>';
                                }
                            } else {
                                echo '<div class="text-center p-4 text-muted">';
                                echo '<i class="bi bi-person-x display-6 d-block mb-2"></i>';
                                echo '<small>Belum ada warga terdaftar di KK ini.</small>';
                                echo '</div>';
                            }
                            ?>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditKK<?php echo $row['no_kk']; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-dark fw-bold">Edit Data Keluarga</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="no_kk_lama" value="<?php echo $row['no_kk']; ?>">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Nomor KK</label>
                            <input type="text" name="no_kk" class="form-control" value="<?php echo $row['no_kk']; ?>" required>
                            <small class="text-danger" style="font-size: 0.7rem;">*Hati-hati, mengubah No KK akan mengubah data warga terkait.</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Kepala Keluarga</label>
                            <input type="text" name="kepala_keluarga" class="form-control" value="<?php echo $row['kepala_keluarga']; ?>" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="fw-bold">Alamat Lengkap</label>
                            <textarea name="alamat" class="form-control" rows="2" required><?php echo $row['alamat']; ?></textarea>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="fw-bold">RT</label>
                            <input type="text" name="rt" class="form-control" value="<?php echo $row['rt']; ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="fw-bold">RW</label>
                            <input type="text" name="rw" class="form-control" value="<?php echo $row['rw']; ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Kode Pos</label>
                            <input type="text" name="kode_pos" class="form-control" value="<?php echo $row['kode_pos']; ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Kelurahan</label>
                            <input type="text" name="kelurahan" class="form-control" value="<?php echo $row['kelurahan']; ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Kecamatan</label>
                            <input type="text" name="kecamatan" class="form-control" value="<?php echo $row['kecamatan']; ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Kota/Kabupaten</label>
                            <input type="text" name="kota" class="form-control" value="<?php echo $row['kota']; ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Provinsi</label>
                            <input type="text" name="provinsi" class="form-control" value="<?php echo $row['provinsi']; ?>">
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="update_kk" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?php include "footer.php" ?>