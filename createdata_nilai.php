
<?php
// koneksi ke database
require_once __DIR__ . "/../koneksi.php";

// baca data dari admin_data_nilai.php
$nim = isset($_POST['nim']) ? trim($_POST['nim']) : '';
$namalengkap = isset($_POST['nama_lengkap']) ? trim($_POST['nama_lengkap']) : '';
$nilaiakhir = isset($_POST['nilai_akhir']) ? str_replace(',', '.', trim($_POST['nilai_akhir'])) : '';
$foto = isset($_POST['foto']) ? basename(trim($_POST['foto'])) : '';

// validasi NIM
if (empty($nim)) {
    echo "NIM wajib diisi";
    exit;
} elseif (!preg_match('/^[0-9]{10}$/', $nim)) {
    echo "NIM harus terdiri dari tepat 10 digit angka";
    exit;
}

// validasi nama
if (empty($namalengkap)) {
    echo "Nama mahasiswa wajib diisi";
    exit;
} elseif (!preg_match("/^[a-zA-Z\s]+$/", $namalengkap)) {
    echo "Nama hanya boleh berisi huruf dan spasi";
    exit;
} elseif (strlen($namalengkap) < 3) {
    echo "Nama minimal 3 karakter";
    exit;
}

// validasi nilai akhir
if ($nilaiakhir === '') {
    echo "Nilai akhir wajib diisi";
    exit;
} elseif (!is_numeric($nilaiakhir)) {
    echo "Nilai akhir harus berupa angka";
    exit;
} elseif ($nilaiakhir < 0 || $nilaiakhir > 100) {
    echo "Nilai akhir harus antara 0 - 100";
    exit;
}

// inisialisasi
$nilaihuruf = "";
$predikat = "";

// menentukan nilai huruf dan predikat
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

//untuk mengecek nim apakah sudah ada dalam database
$cek = mysqli_query($conn, "SELECT nim FROM nilaimhsw WHERE nim = '$nim'");

if (mysqli_num_rows($cek) > 0) {
    echo "<script>
        alert('NIM sudah terdaftar!');
        window.history.back();
    </script>";
    exit;
}

// SQL untuk menambahkan data
$sql = "INSERT INTO nilaimhsw
        (nim, nama_lengkap, nilai_akhir, nilai_huruf, predikat, foto, created_at)
        VALUES ('$nim', '$namalengkap', '$nilaiakhir', '$nilaihuruf', '$predikat', '$foto', NOW())";

// menjalankan query
if (mysqli_query($conn, $sql)) {
    header("Location: admin_rekap_nilai.php");
    exit;
} else {
    echo "Gagal menambahkan data: " . mysqli_error($conn);
}

// tutup koneksi
mysqli_close($conn);
?>
