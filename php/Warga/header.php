<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Warga</title>
    <link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/style_warga.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <header>
        <h1><i class="bi bi-house-door-fill"></i> Portal Warga RT 03</h1>
        <div class="header-right">
            <span>Halo, <b><?php echo isset($_SESSION['nama']) ? $_SESSION['nama'] : $_SESSION['nik']; ?></b></span>
            
            <a href="../logout.php" class="btn btn-hapus" onclick="return confirm('Yakin ingin keluar?')">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </header>