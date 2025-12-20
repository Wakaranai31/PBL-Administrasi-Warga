<?php
include "header.php";
include "sidebar.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$data = [];
if ($id > 0) {
    $query = mysqli_query($koneksi, "SELECT * FROM keluarga WHERE id=$id");
    $data = mysqli_fetch_assoc($query);
}
if (isset($_POST['update'])) {
    $nama = $_POST['nama'];
    $hubungan = $_POST['hubungan'];
    $usia = $_POST['usia'];
    $pekerjaan = $_POST['pekerjaan'];

    $update = mysqli_query($koneksi, "UPDATE keluarga SET 
        nama='$nama',
        hubungan='$hubungan',
        usia='$usia',
        pekerjaan='$pekerjaan'
        WHERE id=$id
    ");

    if ($update) {
        echo "<script>alert('Data berhasil diupdate!'); window.location='data_keluarga.php';</script>";
    } else {
        echo "<script>alert('Data gagal diupdate!');</script>";
    }
}
?>

<link rel="stylesheet" href="../../css/style_edit_data.css">

<div class="content">
    <div class="page">
        <h2>Edit Data Keluarga</h2>

        <form method="POST">
            <div class="form-grid">

                <div class="form-group">
                    <label>NIK</label>
                    <input type="text" name="nik"
                           value="<?= $data['nik'] ?? '' ?>" required>
                </div>

                <div class="form-group">
                    <label>Nama Anggota</label>
                    <input type="text" name="nama"
                           value="<?= $data['nama'] ?? '' ?>" required>
                </div>

                <div class="form-group">
                    <label>Tempat Lahir</label>
                    <input type="text" name="tempat lahir"
                           value="<?= $data['tempat lahir'] ?? '' ?>" required>
                </div>

                <div class="form-group">
                    <label>Tanggal Lahir</label>
                    <input type="text" name="tanggal lahir"
                           value="<?= $data['tanggal lahir'] ?? '' ?>" required>
                </div>

                <div class="form-group">
                    <label>Umur</label>
                    <input type="number" name="umur"
                           value="<?= $data['umur'] ?? '' ?>" required>
                </div>

                <div class="form-group">
                    <label>Jenis Kelamin</label>
                    <select name="hubungan" required>
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <?php
                        $opsi = ["Perempuan","Laki Laki"];
                        foreach ($opsi as $o) {
                            $selected = (($data['hubungan'] ?? '') == $o) ? 'selected' : '';
                            echo "<option value='$o' $selected>$o</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="hubungan" required>
                        <option value="">-- Pilih Status --</option>
                        <?php
                        $opsi = ["Suami","Istri","Anak","Orang Tua"];
                        foreach ($opsi as $o) {
                            $selected = (($data['hubungan'] ?? '') == $o) ? 'selected' : '';
                            echo "<option value='$o' $selected>$o</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Alamat</label>
                    <input type="text" name="alamat"
                           value="<?= $data['alamat'] ?? '' ?>" required>
                </div>

                <div class="form-group">
                    <label>Pendidikan</label>
                    <input type="text" name="pendidikan"
                           value="<?= $data['pendidikan'] ?? '' ?>" required>
                </div>

                <div class="form-group">
                    <label>Pekerjaan</label>
                    <input type="text" name="pekerjaan"
                           value="<?= $data['pekerjaan'] ?? '' ?>" required>
                </div>

                <div class="form-group">
                    <label>No. HP</label>
                    <input type="text" name="no hp"
                           value="<?= $data['no hp'] ?? '' ?>" required>
                </div>

            </div>

            <div class="btn-group">
                <button type="submit" name="update" class="btn btn-primary">
                    Update
                </button>
                <a href="data_keluarga.php" class="btn btn-secondary">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

