<?php 
session_start();
// Mundur 2 langkah cari koneksi
include '../koneksi.php'; 

// 1. CEK KEAMANAN (SATPAM)
// Kalau belum login atau bukan admin, tendang ke login
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
    
    // Bersihkan input biar aman
    $safe_keyword = mysqli_real_escape_string($koneksi, $keyword);

    // Cari di database: Nama MIRIP keyword ATAU NIK MIRIP keyword
    $query = "SELECT * FROM warga 
              WHERE nama LIKE '%$safe_keyword%' 
              OR nik LIKE '%$safe_keyword%' 
              ORDER BY nama ASC";
              
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
                       aria-label="Recipient's username" aria-describedby="button-addon2" required>
                
                <button class="btn btn-primary" type="submit" id="button-addon2">
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
                        <table class="table table-hover table-bordered mb-0">
                            <thead class="table-primary">
                                <tr>
                                    <th>No</th>
                                    <th>NIK</th>
                                    <th>Nama Lengkap</th>
                                    <th>Jenis Kelamin</th>
                                    <th>No. KK</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($data_warga as $warga): ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo $warga['nik']; ?></td>
                                    <td><b><?php echo $warga['nama'] ? $warga['nama'] : '<span class="text-muted small">(Belum diisi)</span>'; ?></b></td>
                                    <td><?php echo $warga['jenis_kelamin'] == 'L' ? 'Laki-laki' : ($warga['jenis_kelamin'] == 'P' ? 'Perempuan' : '-'); ?></td>
                                    <td><?php echo $warga['no_kk'] ? $warga['no_kk'] : '-'; ?></td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-info text-white"><i class="bi bi-eye"></i> Detail</a>
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

<script src="../../bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>