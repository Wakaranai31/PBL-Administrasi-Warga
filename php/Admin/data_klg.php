<?php
session_start();
// Mundur 2 langkah cari koneksi
include '../koneksi.php';

// 1. CEK KEAMANAN
if (!isset($_SESSION['nik']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// 2. LOGIKA TAMBAH DATA KELUARGA
if (isset($_POST['tambah_kk'])) {
    $no_kk      = $_POST['no_kk'];
    $kepala     = $_POST['kepala_keluarga'];
    $alamat     = $_POST['alamat'];
    $rt         = $_POST['rt'];
    $rw         = $_POST['rw'];
    $kode_pos   = $_POST['kode_pos'];
    // Default data wilayah (bisa diubah sesuai kebutuhan atau ditambah input form)
    $kelurahan  = "Sukamaju"; 
    $kecamatan  = "Batam Kota";
    $kota       = "Batam";
    $provinsi   = "Kepulauan Riau";

    // Cek duplikat No KK
    $cek = mysqli_query($koneksi, "SELECT no_kk FROM keluarga WHERE no_kk = '$no_kk'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Gagal: Nomor KK sudah terdaftar!');</script>";
    } else {
        $query = "INSERT INTO keluarga (no_kk, kepala_keluarga, alamat, rt, rw, kode_pos, kelurahan, kecamatan, kota, provinsi)
                  VALUES ('$no_kk', '$kepala', '$alamat', '$rt', '$rw', '$kode_pos', '$kelurahan', '$kecamatan', '$kota', '$provinsi')";
        
        if (mysqli_query($koneksi, $query)) {
            echo "<script>alert('Data KK Berhasil Ditambahkan!'); window.location='data_klg.php';</script>";
        } else {
            echo "<script>alert('Gagal Tambah: " . mysqli_error($koneksi) . "');</script>";
        }
    }
}

// 3. LOGIKA UPDATE DATA KELUARGA
if (isset($_POST['update_kk'])) {
    $no_kk_lama = $_POST['no_kk_lama']; // Kunci untuk mencari data lama
    $no_kk_baru = $_POST['no_kk'];      // Data baru (bisa sama atau beda jika ada typo)
    $kepala     = $_POST['kepala_keluarga'];
    $alamat     = $_POST['alamat'];
    $rt         = $_POST['rt'];
    $rw         = $_POST['rw'];
    $kode_pos   = $_POST['kode_pos'];

    // Update query (Karena ada ON UPDATE CASCADE di database, mengubah No KK aman dan akan merubah data di tabel warga juga)
    $query = "UPDATE keluarga SET 
              no_kk = '$no_kk_baru',
              kepala_keluarga = '$kepala',
              alamat = '$alamat',
              rt = '$rt',
              rw = '$rw',
              kode_pos = '$kode_pos'
              WHERE no_kk = '$no_kk_lama'";

    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data KK Berhasil Diperbarui!'); window.location='data_klg.php';</script>";
    } else {
        echo "<script>alert('Gagal Update: " . mysqli_error($koneksi) . "');</script>";
    }
}

// 4. LOGIKA HAPUS DATA
if (isset($_GET['hapus'])) {
    $kk_hapus = $_GET['hapus'];
    // Karena ON DELETE CASCADE, menghapus KK akan menghapus semua warga di dalamnya (Hati-hati!)
    $query = "DELETE FROM keluarga WHERE no_kk = '$kk_hapus'";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data KK Berhasil Dihapus!'); window.location='data_klg.php';</script>";
    } else {
        echo "<script>alert('Gagal Hapus: " . mysqli_error($koneksi) . "');</script>";
    }
}

// 5. AMBIL DATA KELUARGA
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
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKK">
                <i class="bi bi-plus-circle-fill"></i> Tambah KK Baru
            </button>

            <div class="table-responsive p-3">
                <table class="table table-hover">
                    <thead class="table-light"> 
                        <tr>
                            <th>No</th>
                            <th>Nomor KK</th>
                            <th>Kepala Keluarga</th>
                            <th>Alamat</th>
                            <th class="text-center">RT / RW</th>
                            <th class="text-center">Aksi</th>
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
                            <td class="text-center">
                                <button class="btn btn-warning btn-sm text-white" data-bs-toggle="modal" data-bs-target="#modalEditKK<?php echo $row['no_kk']; ?>">
                                    <i class="bi bi-pencil-square"></i>
                                </button> 
                                
                                <a href="data_klg.php?hapus=<?php echo $row['no_kk']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('PERINGATAN: Menghapus KK ini akan MENGHAPUS SELURUH DATA WARGA yang terdaftar di dalamnya.\n\nYakin ingin menghapus KK Bpk. <?php echo $row['kepala_keluarga']; ?>?')">
                                    <i class="bi bi-trash-fill"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <?php if(count($data_keluarga) == 0): ?>
                    <div class="text-center p-4 text-muted">Belum ada data keluarga. Silakan tambah data.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambahKK" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-dark">
                <h5 class="modal-title">Tambah Kartu Keluarga Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
                        <div class="col-12">
                            <small class="text-muted">* Kelurahan, Kecamatan, Kota otomatis diset default (Batam).</small>
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
<div class="modal fade" id="modalEditKK<?php echo $row['no_kk']; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-dark">Edit Data Keluarga</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="no_kk_lama" value="<?php echo $row['no_kk']; ?>">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Nomor KK</label>
                            <input type="text" name="no_kk" class="form-control" value="<?php echo $row['no_kk']; ?>" required>
                            <small class="text-danger" style="font-size: 0.7rem;">*Mengubah No KK akan otomatis mengubah data warga terkait.</small>
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

<script src="../../bootstrap/js/bootstrap.bundle.min.js"></script>