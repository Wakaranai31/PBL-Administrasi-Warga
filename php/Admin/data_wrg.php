<?php
session_start();
// [UBAH DISINI] Path koneksi saya perbaiki jadi ../../ sesuai gambar struktur foldermu
include '../koneksi.php'; 

if (!isset($_SESSION['nik']) || $_SESSION['role'] != 'admin') {
    // [UBAH DISINI] Path login juga diperbaiki
    header("Location: ../login.php");
    exit();
}

// [UBAH DISINI] BAGIAN 1: LOGIKA UPDATE DATA (PHP)
// Ini menangkap data saat tombol "Simpan Perubahan" ditekan
if (isset($_POST['update_warga'])) {
    $nik_lama       = $_POST['nik']; // NIK dijadikan kunci pencarian
    $nama           = $_POST['nama'];
    $tempat_lahir   = $_POST['tempat_lahir'];
    $tanggal_lahir  = $_POST['tanggal_lahir'];
    $jenis_kelamin  = $_POST['jenis_kelamin'];
    $status_hub     = $_POST['status_hub_keluarga'];
    $pendidikan     = $_POST['pendidikan'];
    $pekerjaan      = $_POST['pekerjaan'];
    $no_hp          = $_POST['no_hp'];

    // Query Update ke Database
    $query_update = "UPDATE warga SET 
                     nama = '$nama',
                     tempat_lahir = '$tempat_lahir',
                     tanggal_lahir = '$tanggal_lahir',
                     jenis_kelamin = '$jenis_kelamin',
                     status_hub_keluarga = '$status_hub',
                     pendidikan = '$pendidikan',
                     pekerjaan = '$pekerjaan',
                     no_hp = '$no_hp'
                     WHERE nik = '$nik_lama'";

    if (mysqli_query($koneksi, $query_update)) {
        echo "<script>alert('Data Berhasil Diperbarui!'); window.location='data_wrg.php';</script>";
    } else {
        echo "<script>alert('Gagal Update: " . mysqli_error($koneksi) . "');</script>";
    }
}
// [AKHIR BAGIAN 1]

if (isset($_GET['hapus'])) {
    $nik_hapus = $_GET['hapus'];
    $query_hapus = "DELETE FROM warga WHERE nik = '$nik_hapus'";
    mysqli_query($koneksi, $query_hapus);
    echo "<script>alert('Data Berhasil Dihapus'); window.location='data_wrg.php';</script>";
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
            <div class="table-responsive">
                <table>
                    <thead> 
                        <tr>
                            <th>No</th>
                            <th>NIK</th>
                            <th>No. KK</th>
                            <th>Nama</th>
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
                            <td><?php echo $row['no_kk']; ?></td>
                            <td><?php echo $row['nama']; ?></td>
                            <td><?php echo $row['no_hp']; ?></td>
                            <td class="sticky-aksi text-center">
                                <button class="btn btn-lihat" data-bs-toggle="modal" data-bs-target="#modalDetail<?php echo $row['nik']; ?>">
                                    <i class="bi bi-eye-fill"></i> Lihat
                                </button>
                                
                                <button class="btn btn-edit" data-bs-toggle="modal" data-bs-target="#modalEdit<?php echo $row['nik']; ?>">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </button> 
                                
                                <a href="data_wrg.php?hapus=<?php echo $row['nik']; ?>" class="btn btn-hapus" onclick="return confirm('Hapus data <?php echo $row['nama']; ?>?')">
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

<?php foreach($data_warga as $row): ?>

<div class="modal fade" id="modalDetail<?php echo $row['nik']; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Data Warga</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 mb-5"><label class="fw-bold">NIK:</label><p><?php echo $row['nik']; ?></p></div>
                    <div class="col-md-4 mb-5"><label class="fw-bold">Nama:</label><p><?php echo $row['nama']; ?></p></div>
                    <div class="col-md-4 mb-5"><label class="fw-bold">Tempat Lahir:</label><p><?php echo $row['tempat_lahir']; ?></p></div>
                    <div class="col-md-4 mb-5"><label class="fw-bold">Tanggal Lahir:</label><p><?php echo $row['tanggal_lahir']; ?></p></div>
                    <div class="col-md-4 mb-5"><label class="fw-bold">Jenis Kelamin:</label><p><?php echo $row['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?></p></div>
                    <div class="col-md-4 mb-5"><label class="fw-bold">Status Hubungan:</label><p><?php echo $row['status_hub_keluarga']; ?></p></div>
                    <div class="col-md-4 mb-5"><label class="fw-bold">Alamat:</label><p><?php echo $row['alamat'] ? $row['alamat'] : 'Data KK belum ada'; ?></p></div>
                    <div class="col-md-4 mb-5"><label class="fw-bold">Pendidikan:</label><p><?php echo $row['pendidikan']; ?></p></div>
                    <div class="col-md-4 mb-5"><label class="fw-bold">Pekerjaan:</label><p><?php echo $row['pekerjaan']; ?></p></div>
                    <div class="col-md-4 mb-5"><label class="fw-bold">No. HP:</label><p><?php echo $row['no_hp']; ?></p></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEdit<?php echo $row['nik']; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"> 
                <h5 class="modal-title">Edit Data Warga</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">NIK</label>
                            <input type="text" name="nik" class="form-control" value="<?php echo $row['nik']; ?>">
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
                            <label class="form-label fw-bold">Status Hubungan</label>
                            <select name="status_hub_keluarga" class="form-select">
                                <option value="Kepala Keluarga" <?php if($row['status_hub_keluarga'] == 'Kepala Keluarga') echo 'selected'; ?>>Kepala Keluarga</option>
                                <option value="Suami" <?php if($row['status_hub_keluarga'] == 'Suami') echo 'selected'; ?>>Suami</option>
                                <option value="Istri" <?php if($row['status_hub_keluarga'] == 'Istri') echo 'selected'; ?>>Istri</option>
                                <option value="Anak" <?php if($row['status_hub_keluarga'] == 'Anak') echo 'selected'; ?>>Anak</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Pendidikan</label>
                            <input type="text" name="pendidikan" class="form-control" value="<?php echo $row['pendidikan']; ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Pekerjaan</label>
                            <input type="text" name="pekerjaan" class="form-control" value="<?php echo $row['pekerjaan']; ?>">
                        </div>

                        <div class="col-md-6 mb-3">
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

<script src="../../bootstrap/js/bootstrap.bundle.min.js"></script>