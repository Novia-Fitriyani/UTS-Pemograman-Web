<?php
// koneksi ke database
require_once __DIR__ . "/../koneksi.php";

// baca data dari kiriman admin_rekap_nilai.php
$nim = $_POST['nim'];

// var_dump($nim);
// exit;

// SQL to delete a record
$sql = "DELETE FROM nilaimhsw WHERE nim= '$nim'";

if (mysqli_query($conn, $sql)) {
    // jika penghapusan data berhasil
  echo "<script>alert('Record deleted successfully');</script>";
} else {
    // jika pengapusan data gagal
  echo "Error deleting record: " . mysqli_error($conn);
}

// kembali ke tampilan data nilai (file: adminlte.php)
header("location: admin_rekap_nilai.php");
exit;

// tutup / akhiri koneksi ke db
//mysqli_close($conn);

?>