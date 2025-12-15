<?php include "header.php"; ?>
<?php include "sidebar.php"; ?>

    <div class="content">

        <div id="surat" class="page">
            <h2>Data Surat</h2>
            <button class="btn btn-tambah"><i class="bi bi-envelope-plus-fill"></i> Tambah Surat</button>
            
            <div class="card">
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th>No</th>
                            <th>Jenis Surat</th>
                            <th>Nomor Surat</th>
                            <th>Tanggal</th>
                            <th>Penerima/Pengirim</th>
                            <th>Aksi</th>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td>Surat Masuk</td>
                            <td>001/RT/2025</td>
                            <td>12 Jan 2025</td>
                            <td>Lurah</td>
                            <td><button class="btn btn-edit"><i class="bi bi-pencil-square"></i> Edit</button> <button
                                    class="btn btn-hapus"><i class="bi bi-trash-fill"></i> Hapus</button></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>