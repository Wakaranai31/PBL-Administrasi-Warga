<?php 
session_start();
include '../koneksi.php'; 

// 1. CEK KEAMANAN
if (!isset($_SESSION['nik']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// 2. LOGIKA PENCARIAN
$data_warga = [];
$keyword = "";
$pencarian_dilakukan = false;

if (isset($_GET['keyword'])) {
    $pencarian_dilakukan = true;
    $keyword = $_GET['keyword'];
    
    $safe_keyword = mysqli_real_escape_string($koneksi, $keyword);

    // Gunakan LEFT JOIN agar bisa mengambil data ALAMAT dari tabel keluarga
    $query = "SELECT warga.*, keluarga.alamat 
              FROM warga 
              LEFT JOIN keluarga ON warga.no_kk = keluarga.no_kk
              WHERE warga.nama LIKE '%$safe_keyword%' 
              OR warga.nik LIKE '%$safe_keyword%' 
              ORDER BY warga.nama ASC";
              
    $result = mysqli_query($koneksi, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $data_warga[] = $row;
    }
}
?>

<?php include "header.php" ?>
<?php include "sidebar.php" ?>

<div class="content">
    <div id="pencarian" class="page">
        <h2>Pencarian Data Warga</h2>
        
        <form action="" method="GET">
            <div class="input-group mb-3">
                <input type="text" name="keyword" class="form-control" 
                    placeholder="Masukkan NIK atau Nama Warga..." 
                    value="<?php echo htmlspecialchars($keyword); ?>" 
                    required>
                
                <button class="btn btn-primary" type="submit">
                    <i class="bi bi-search"></i> Cari
                </button>
            </div>
        </form>

        <?php if ($pencarian_dilakukan): ?>
            
            <?php if (count($data_warga) > 0): ?>
                <div class="alert alert-success">
                    Ditemukan <b><?php echo count($data_warga); ?></b> data.
                </div>

                <div class="card p-3 shadow-sm">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered mb-0 align-middle">
                            <thead class="table-primary">
                                <tr>
                                    <th>No</th>
                                    <th>NIK</th>
                                    <th>Nama Lengkap</th>
                                    <th>L/P</th>
                                    <th>No. KK</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // LOOPING 1: HANYA MENAMPILKAN TABEL DAN TOMBOL
                                $no = 1; 
                                foreach ($data_warga as $warga): 
                                ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo $warga['nik']; ?></td>
                                    <td><b><?php echo $warga['nama']; ?></b></td>
                                    <td><?php echo $warga['jenis_kelamin'] == 'L' ? 'L' : 'P'; ?></td>
                                    <td><?php echo $warga['no_kk'] ? $warga['no_kk'] : '-'; ?></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#modalDetailCari<?php echo $warga['nik']; ?>">
                                            <i class="bi bi-eye-fill"></i> Detail
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php else: ?>
                <div class="alert alert-danger text-center">
                    <i class="bi bi-emoji-frown" style="font-size: 2rem;"></i><br>
                    Data tidak ditemukan untuk kata kunci "<b><?php echo htmlspecialchars($keyword); ?></b>".
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="card p-4 text-center text-muted">
                <i class="bi bi-search" style="font-size: 3rem; opacity: 0.3;"></i>
                <p class="mt-2">Silakan ketik Nama atau NIK pada kolom di atas untuk mencari data.</p>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php if ($pencarian_dilakukan && count($data_warga) > 0): ?>
    <?php foreach ($data_warga as $warga): ?>
    <div class="modal fade" id="modalDetailCari<?php echo $warga['nik']; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-dark">
                    <h5 class="modal-title fw-bold">Detail Data Warga</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-start"> 
                    <div class="row">
                        <div class="col-md-6 mb-2"><label class="fw-bold">NIK:</label> <p><?php echo $warga['nik']; ?></p></div>
                        <div class="col-md-6 mb-2"><label class="fw-bold">Nama:</label> <p><?php echo $warga['nama']; ?></p></div>
                        
                        <div class="col-md-6 mb-2"><label class="fw-bold">No KK:</label> <p><?php echo $warga['no_kk']; ?></p></div>
                        <div class="col-md-6 mb-2"><label class="fw-bold">Alamat:</label> <p><?php echo $warga['alamat'] ? $warga['alamat'] : '-'; ?></p></div>
                        
                        <div class="col-md-6 mb-2"><label class="fw-bold">TTL:</label> <p><?php echo $warga['tempat_lahir'] . ", " . $warga['tanggal_lahir']; ?></p></div>
                        <div class="col-md-6 mb-2"><label class="fw-bold">Gender:</label> <p><?php echo $warga['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?></p></div>
                        
                        <div class="col-md-6 mb-2"><label class="fw-bold">Agama:</label> <p><?php echo $warga['agama']; ?></p></div>
                        <div class="col-md-6 mb-2"><label class="fw-bold">Status Hub:</label> <p><?php echo $warga['status_hub_keluarga']; ?></p></div>
                        
                        <div class="col-md-6 mb-2"><label class="fw-bold">Status Kawin:</label> <p><?php echo $warga['status_perkawinan']; ?></p></div>
                        <div class="col-md-6 mb-2"><label class="fw-bold">Pendidikan:</label> <p><?php echo $warga['pendidikan']; ?></p></div>
                        
                        <div class="col-md-6 mb-2"><label class="fw-bold">Pekerjaan:</label> <p><?php echo $warga['pekerjaan']; ?></p></div>
                        <div class="col-md-6 mb-2"><label class="fw-bold">No HP:</label> <p><?php echo $warga['no_hp']; ?></p></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php include "footer.php" ?>