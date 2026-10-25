
<?php
// koneksi
require_once __DIR__ . "/../koneksi.php";

// Fungsi sanitasi
function sanitasi($data)
{
    return trim($data);
}

// baca data dari kiriman admin_rekap_nilai.php
$nim = isset($_POST['nim']) ? sanitasi($_POST['nim']) : '';
$namalengkap = isset($_POST['nama_lengkap']) ? sanitasi($_POST['nama_lengkap']) : '';
$nilaiakhir = isset($_POST['nilai_akhir']) ? str_replace(',', '.', sanitasi($_POST['nilai_akhir'])) : '';
$foto = isset($_POST['foto']) ? basename(trim($_POST['foto'])) : '';

$error = [];

// validasi NIM
if (empty($nim)) {
    $error[] = "NIM wajib diisi";
} elseif (!preg_match('/^[0-9]{10}$/', $nim)) {
    $error[] = "NIM harus terdiri dari tepat 10 digit angka";
}

// validasi nama
if (empty($namalengkap)) {
    $error[] = "Nama mahasiswa wajib diisi";
} elseif (!preg_match("/^[a-zA-Z\s]+$/", $namalengkap)) {
    $error[] = "Nama hanya boleh berisi huruf dan spasi";
} elseif (strlen($namalengkap) < 3) {
    $error[] = "Nama minimal 3 karakter";
}

// validasi nilai akhir
if ($nilaiakhir === '') {
    $error[] = "Nilai akhir wajib diisi";
} elseif (!is_numeric($nilaiakhir)) {
    $error[] = "Nilai akhir harus berupa angka";
} elseif ($nilaiakhir < 0 || $nilaiakhir > 100) {
    $error[] = "Nilai akhir harus antara 0 - 100";
}

// jika ada error
if (!empty($error)) {
    echo "<h3>Data gagal diubah</h3>";

    foreach ($error as $pesan) {
        echo htmlspecialchars($pesan, ENT_QUOTES, 'UTF-8') . "<br>";
    }

    echo "<br><a href='admin_rekap_nilai.php'>Kembali</a>";
    exit;
}

// inisialisasi
$nilaihuruf = "";
$predikat = "";

// konversi nilai huruf dan predikat
if ($nilaiakhir <= 34.50) {
    $nilaihuruf = "E";
    $predikat = "Tidak Lulus";
} elseif ($nilaiakhir <= 50.50) {
    $nilaihuruf = "D";
    $predikat = "Kurang Memuaskan";
} elseif ($nilaiakhir <= 65.50) {
    $nilaihuruf = "C";
    $predikat = "Cukup Memuaskan";
} elseif ($nilaiakhir <= 80.50) {
    $nilaihuruf = "B";
    $predikat = "Memuaskan";
} else {
    $nilaihuruf = "A";
    $predikat = "Sangat Memuaskan";
}

// SQL untuk mengubah data
$sql = "UPDATE nilaimhsw
        SET nama_lengkap='$namalengkap',
            nilai_akhir='$nilaiakhir',
            nilai_huruf='$nilaihuruf',
            predikat='$predikat',
            foto='$foto'
        WHERE nim='$nim'";

// menjalankan query
if (mysqli_query($conn, $sql)) {
    header("Location: admin_rekap_nilai.php");
    exit;
} else {
    echo "Error updating record: " . mysqli_error($conn);
}

// tutup koneksi
mysqli_close($conn);
?>
