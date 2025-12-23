<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin WAW</title>
    <link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>

<body>
    <header>
        <h1><i class="bi bi-house-door-fill"></i> Admin RT/RW</h1>
        <div class="header-right">
            <span>Halo, <b><?php echo isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Admin'; ?></b></span>
            
            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#logoutModal">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </div>
    </header>

    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm"> <div class="modal-content text-center p-3" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
                <div class="modal-body">
                    <i class="bi bi-exclamation-circle text-warning display-1 mb-3"></i>
                    <h5 class="fw-bold mb-2">Konfirmasi Logout</h5>
                    <p class="text-muted small mb-4">Apakah Anda yakin ingin keluar dari sistem?</p>
                    
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-light fw-bold px-3 w-50" data-bs-dismiss="modal">Batal</button>
                        <button href="../logout.php" class="btn btn-danger fw-bold px-3 w-50">Ya, Keluar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>