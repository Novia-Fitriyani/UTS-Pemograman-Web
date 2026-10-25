<?php
// koneksi ke database
require_once __DIR__ . "/../koneksi.php";

// mengambil kata kunci dari pencarian dan predikat
// untuk mengambil data dari pencarian nama
$nama = trim($_GET['nama'] ?? '');

$q = trim($_GET['q'] ?? 'all'); // untuk mengambil memfilter predikat
$urutannilai = $_GET['sort'] ?? 'semua_nilai'; // untuk mengambil pengurutan nilai

// untuk menentukan pengurutan data
$orderBy = ['tertinggi' => 'nilai_akhir DESC', 'terendah' => 'nilai_akhir ASC', 'semua_nilai' => 'created_at DESC'];
$orderSql = $orderBy[$urutannilai] ?? $orderBy['semua_nilai'];

// Menentukan jumlah data perhalaman
$batas = isset($_GET['batas']) ? (int) $_GET['batas'] : 10;
if (!in_array($batas, [5, 10, 15], true)) { $batas = 10; }


$fotoList = []; // untuk menyimpan nama file foto
foreach (['jpg', 'jpeg', 'png', 'webp', 'gif'] as $ext) {
    foreach (glob(__DIR__ . '/assets/img/*.' . $ext) ?: [] as $fileFoto) {
        $fotoList[] = basename($fileFoto);
    }
}
sort($fotoList);

// pagination (menentukan dan mengambil nomor halaman aktif)
$halaman = isset($_GET['halaman']) ? (int) $_GET['halaman'] : 1;
if ($halaman < 1) {
    $halaman = 1;
}
$offset = ($halaman - 1) * $batas;

// membantu bind_param untuk jumlah parameter yang dinamis
function bindParameterDinamis($stmt, $types, &$params) {
    if ($types === '') {
        return;
    }
    $bind = [$types];
    foreach ($params as $key => &$value) {
        $bind[] = &$value;
    }
    call_user_func_array([$stmt, 'bind_param'], $bind);
}

// membuat kondisi umtuk pencarian
$where = [];
$types = '';
$params = [];

// Validasi untuk penulisan Nama dan predikat
if ($nama !== '') {
    $where[] = "nama_lengkap LIKE ?";
    $types .= 's';
    $params[] = '%' . $nama . '%';
}

if ($q !== '' && strtolower($q) !== 'all') {
    $where[] = "LOWER(predikat) = LOWER(?)";
    $types .= 's';
    $params[] = $q;
}

// menggabungkan kondisi where
$where_sql = count($where) > 0 ? ' WHERE ' . implode(' AND ', $where) : '';

// menghitung jumlah data sesuai pencarian dan filter
$sql_count = "SELECT COUNT(*) AS total FROM nilaimhsw" . $where_sql;
$stmt_count = $conn->prepare($sql_count);
bindParameterDinamis($stmt_count, $types, $params);
$stmt_count->execute();
$result_count = $stmt_count->get_result();
$data_count = $result_count->fetch_assoc();
$total_data = (int) $data_count['total'];
$total_halaman = max(1, (int) ceil($total_data / $batas)); // untuk ,menghitung jumlah halaman

// validasi halaman
if ($halaman > $total_halaman) {
    $halaman = $total_halaman;
    $offset = ($halaman - 1) * $batas;
}

// mengambil data sesuai pencarian, filter, dan halaman
$sql = "SELECT * FROM nilaimhsw" . $where_sql . " ORDER BY " . $orderSql . " LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$types_data = $types . 'ii';
$params_data = $params;
$params_data[] = $batas;
$params_data[] = $offset;
bindParameterDinamis($stmt, $types_data, $params_data);
$stmt->execute();
$result = $stmt->get_result();

// data untuk tulisan Showing
// Menentukan nomor data pertama yang ditampilkan.
$showing_awal = $total_data > 0 ? $offset + 1 : 0;
$showing_akhir = min($offset + $batas, $total_data);

// membuat link untuk halaman (pagination)
function urlHalaman($nomor) {
    $parameter = $_GET;
    $parameter['halaman'] = $nomor;
    return '?' . http_build_query($parameter);
}
?>

<!doctype html>
<html lang="en">
<!--begin::Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Data Nilai Mahasiswa</title>

    <!--begin::Theme Init (prevents flash of incorrect theme on load, #6043)-->
    <script>
        (() => {
            'use strict';
            const root = document.documentElement;

            // Applications with their own theming opt out of AdminLTE's color mode
            // entirely, here as well as in the bundle.
            if (root.getAttribute('data-lte-color-mode') === 'off') {
                return;
            }

            const STORAGE_KEY = 'lte-theme';
            let stored = null;
            try {
                stored = localStorage.getItem(STORAGE_KEY);
            } catch {
                // localStorage may be unavailable (private mode, sandboxed iframe).
            }
            // Mirror the precedence in color-mode.ts: the visitor's stored choice
            // wins, then a theme this page declared itself, then the OS preference.
            const authored = root.getAttribute('data-bs-theme');
            let resolved = 'light';
            if (stored === 'dark' || stored === 'light') {
                resolved = stored;
            } else if (authored === 'dark' || authored === 'light') {
                resolved = authored;
            } else if (globalThis.matchMedia('(prefers-color-scheme: dark)').matches) {
                resolved = 'dark';
            }
            root.setAttribute('data-bs-theme', resolved);
            root.style.colorScheme = resolved;
            // Flag values computed here, so the bundle does not mistake them for a
            // theme the page declared and stop following the OS preference.
            if (resolved !== authored) {
                root.setAttribute('data-lte-theme-resolved', '');
            }
        })();
    </script>
    <!--end::Theme Init-->

    <!--begin::Accessibility Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <meta name="color-scheme" content="light dark" />
    <meta name="theme-color" content="#007bff" media="(prefers-color-scheme: light)" />
    <meta name="theme-color" content="#1a1a1a" media="(prefers-color-scheme: dark)" />
    <!--end::Accessibility Meta Tags-->

    <!--begin::Primary Meta Tags-->
    <meta name="title" content="AdminLTE 4 | Users" />
    <meta name="author" content="ColorlibHQ" />
    <meta
        name="description"
        content="AdminLTE is a free Bootstrap 5 admin dashboard template with almost 50 example pages, built with vanilla JS and designed with accessibility in mind." />
    <meta
        name="keywords"
        content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard, bootstrap 5 dashboard, bootstrap 5 charts, bootstrap 5 calendar, bootstrap 5 datepicker, bootstrap 5 tables, bootstrap 5 datatable, vanilla js datatable, colorlibhq, colorlibhq dashboard, colorlibhq admin dashboard, accessible admin panel" />
    <!--end::Primary Meta Tags-->

    <!--begin::Accessibility Features-->
    <!-- Skip links will be dynamically added by accessibility.js -->
    <meta name="supported-color-schemes" content="light dark" />
    <link rel="preload" href="./css/adminlte.css" as="style" />
    <!--end::Accessibility Features-->

    <!--begin::Fonts-->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q="
        crossorigin="anonymous"
        media="print"
        onload="this.media = 'all'" />
    <!--end::Fonts-->

    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
        crossorigin="anonymous" />
    <!--end::Third Party Plugin(OverlayScrollbars)-->

    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
        crossorigin="anonymous" />
    <!--end::Third Party Plugin(Bootstrap Icons)-->

    <!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="./css/adminlte.css" />
    <!--end::Required Plugin(AdminLTE)-->



</head>
<!--end::Head-->
<!--begin::Body-->

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
        <!--begin::Header-->
        <nav class="app-header navbar navbar-expand bg-body">

        </nav>
        <!--end::Header-->

        <!--begin::App Main-->
        <main class="app-main">
            <!--begin::App Content Header-->
            <div class="app-content-header">
                <!--begin::Container-->
                <div class="container-fluid">
                    <!--begin::Row-->
                    <div class="row">
                        <div class="col-sm-6">
                            <h1 class="mb-0 fs-3">Rekap Nilai Mahasiswa</h1>
                        </div>
                        <div class="col-sm-6">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb float-sm-end">
                                    <li class="breadcrumb-item"><a href="#">Beranda</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Nilai</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <!--end::Row-->
                </div>
                <!--end::Container-->
            </div>
            <!--end::App Content Header-->
            <!--begin::App Content-->
            <div class="app-content">
                <!--begin::Container-->
                <div class="container-fluid">
                    <!--begin::Row-->
                    <div class="row">
                        <div class="col-12">
                            <!--begin::Card-->
                            <div class="card mb-4">
                                <?php if (($_GET['error'] ?? '') === 'nama') { ?>
                                    <div class="alert alert-danger m-3 mb-0">Nama tidak valid. Gunakan huruf, spasi, apostrof (') atau tanda hubung (-) saja.</div>
                                <?php } elseif (($_GET['error'] ?? '') === 'input') { ?>
                                    <div class="alert alert-danger m-3 mb-0">Data belum valid. Periksa kembali NIM, nama, dan nilai akhir.</div>
                                <?php } ?>
                                <!--begin::Card Header-->
                                <div class="card-header">
                                    <div class="row g-2 align-items-center">
                                        <div class="col-12 col-md-4">
                                            <h3 class="card-title">Directory Nilai</h3>
                                        </div>
                                        <div class="col-12 col-md-8">
                                            <div class="d-flex flex-wrap justify-content-md-end gap-2">
                                                <!-- Pencarian nama dan filter predikat menggunakan GET -->
                                                <form method="GET" action="" class="d-flex flex-wrap gap-2">
                                                    <div class="input-group input-group-sm w-auto">
                                                        <span class="input-group-text">
                                                            <i class="bi bi-search" aria-hidden="true"></i>
                                                        </span>
                                                        <!-- input untuk mencari data nama mahasiswa -->
                                                        <input
                                                            type="text"
                                                            id="user-search"
                                                            name="nama"
                                                            class="form-control"
                                                            placeholder="Cari berdasarkan nama"
                                                            aria-label="Cari nama"
                                                            style="width: 180px"
                                                            value="<?php echo htmlspecialchars($nama, ENT_QUOTES, 'UTF-8'); ?>" />
                                                    </div>
                                                    
                                                    <!-- untuk memilih predikat, nilai yang dipilih dikirim dari parameter q -->
                                                    <select
                                                        id="predikat"
                                                        name="q"
                                                        class="form-select form-select-sm w-auto"
                                                        aria-label="Filter berdasarkan predikat">
                                                        <option value="all" <?php echo ($q === 'all' || $q === '') ? 'selected' : ''; ?>>Semua Predikat</option>
                                                        <option value="sangat memuaskan" <?php echo (strtolower($q) === 'sangat memuaskan') ? 'selected' : ''; ?>>Sangat Memuaskan</option>
                                                        <option value="memuaskan" <?php echo (strtolower($q) === 'memuaskan') ? 'selected' : ''; ?>>Memuaskan</option>
                                                        <option value="cukup memuaskan" <?php echo (strtolower($q) === 'cukup memuaskan') ? 'selected' : ''; ?>>Cukup Memuaskan</option>
                                                        <option value="kurang memuaskan" <?php echo (strtolower($q) === 'kurang memuaskan') ? 'selected' : ''; ?>>Kurang Memuaskan</option>
                                                        <option value="tidak lulus" <?php echo (strtolower($q) === 'tidak lulus') ? 'selected' : ''; ?>>Tidak Lulus</option>
                                                    </select>

                                                    <!-- untuk mengurutkan nilai dari yang tertinggi atau terendah -->
                                                    <select id="sort" name="sort" class="form-select form-select-sm w-auto" aria-label="Urutkan nilai">
                                                        <option value="semua_nilai" <?php echo $urutannilai === 'semua_nilai' ? 'selected' : ''; ?>>Semua Nilai</option>
                                                        <option value="tertinggi" <?php echo $urutannilai === 'tertinggi' ? 'selected' : ''; ?>>Nilai Tertinggi</option>
                                                        <option value="terendah" <?php echo $urutannilai === 'terendah' ? 'selected' : ''; ?>>Nilai Terendah</option>
                                                    </select>

                                                    <!-- untuk mengurutkan jumlah data yang akan ditampilkan perhalaman -->
                                                    <select id="batas" name="batas" class="form-select form-select-sm w-auto" aria-label="Jumlah data per halaman">
                                                        <option value="5" <?php echo $batas === 5 ? 'selected' : ''; ?>>5 data</option>
                                                        <option value="10" <?php echo $batas === 10 ? 'selected' : ''; ?>>10 data</option>
                                                        <option value="15" <?php echo $batas === 15 ? 'selected' : ''; ?>>15 data</option>
                                                    </select>
                                                    <!-- <button type="submit" class="btn btn-sm btn-outline-primary">Cari / Filter</button> -->
                                                </form>

                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-primary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modal-add-user">
                                                    <i class="bi bi-person-plus-fill me-1" aria-hidden="true"></i>
                                                    Tambah Data Nilai
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Card Header-->

                                    <!--begin::Card Body-->
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle m-0">
                                                <thead>
                                                    <tr>
                                                        <th>NIM</th>
                                                        <th>Nama Lengkap</th>
                                                        <th>Nilai Akhir</th>
                                                        <th>Nilai Huruf</th>
                                                        <th>Predikat</th>
                                                        <th>Tgl dibuat</th>
                                                        <th class="text-end">Actions</th>
                                                    </tr>
                                                </thead>

                                                <!-- menampilkan isi tabel nilai mahasiswa -->
                                                <tbody id="tabel-body">
                                                    <?php if ($total_data === 0) { ?>
                                                        <tr>
                                                            <td colspan="7" class="text-center py-4">Data tidak ditemukan.</td>
                                                        </tr>
                                                    <?php } ?>
                                                    <?php
                                                    while ($row = mysqli_fetch_assoc($result)) {
                                                    ?>
                                                        <tr>
                                                            <td>

                                                                <span class="fw-medium">
                                                                    <?php echo htmlspecialchars((string) $row['nim'], ENT_QUOTES, 'UTF-8'); ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <img
                                                                        src="<?php echo !empty($row['foto']) ? './assets/img/' . rawurlencode(basename($row['foto'])) : './assets/img/user1-128x128.jpg'; ?>"
                                                                        alt="Foto mahasiswa"
                                                                        class="img-size-32 rounded-circle me-2" />
                                                                    <span class="fw-medium"><?php echo htmlspecialchars((string) $row['nama_lengkap'], ENT_QUOTES, 'UTF-8'); ?></span>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <span class="fw-medium"><?php echo htmlspecialchars((string) $row['nilai_akhir'], ENT_QUOTES, 'UTF-8'); ?></span>
                                                            </td>
                                                            <td>
                                                                <span class="fw-medium"><?php echo htmlspecialchars((string) $row['nilai_huruf'], ENT_QUOTES, 'UTF-8'); ?></span>
                                                            </td>
                                                            <td>
                                                                <span class="fw-medium"><?php echo htmlspecialchars((string) $row['predikat'], ENT_QUOTES, 'UTF-8'); ?></span>
                                                            </td>
                                                            <td>
                                                                <span class="fw-medium"><?php echo htmlspecialchars((string) $row['created_at'], ENT_QUOTES, 'UTF-8'); ?></span>
                                                            </td>
                                                            <td class="text-end">
                                                                <div class="btn-group btn-group-sm">
                                                                    <button
                                                                        type="button"
                                                                        class="btn btn-outline-secondary"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#modal-edit-<?php echo $row['nim']; ?>"
                                                                        aria-label="Edit Data">
                                                                        <i class="bi bi-pencil" aria-hidden="true"> </i>
                                                                    </button>
                                                                    <button
                                                                        type="button"
                                                                        class="btn btn-outline-danger"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#modal-delete-<?php echo $row['nim']; ?>"
                                                                        aria-label="Delete Data">
                                                                        <i class="bi bi-trash" aria-hidden="true"> </i>
                                                                    </button>
                                                                </div>

                                                                <!--begin::Edit User Modal-->
                                                                <div
                                                                    class="modal fade text-start"
                                                                    id="modal-edit-<?php echo $row['nim']; ?>"
                                                                    tabindex="-1"
                                                                    aria-labelledby="modal-edit-label-<?php echo $row['nim']; ?>"
                                                                    aria-hidden="true">
                                                                    <div class="modal-dialog">
                                                                        <div class="modal-content">
                                                                            <form action="updatedata_nilai.php" method="POST">
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title" id="modal-edit-label-<?php echo $row['nim']; ?>">Edit Data Nilai</h5>
                                                                                    <button
                                                                                        type="button"
                                                                                        class="btn-close"
                                                                                        data-bs-dismiss="modal"
                                                                                        aria-label="Close"></button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <div class="mb-3">
                                                                                        <label for="edit-nim-<?php echo $row['nim']; ?>" class="form-label"> NIM </label>
                                                                                        <input
                                                                                            type="text"
                                                                                            class="form-control"
                                                                                            id="edit-nim-<?php echo $row['nim']; ?>"
                                                                                            name="nim"
                                                                                            value="<?php echo htmlspecialchars($row['nim']); ?>"
                                                                                            readonly
                                                                                            required />
                                                                                    </div>
                                                                                    <div class="mb-3">
                                                                                        <label for="edit-nama-<?php echo $row['nim']; ?>" class="form-label"> Nama Mahasiswa </label>
                                                                                        <input
                                                                                            type="text"
                                                                                            class="form-control"
                                                                                            id="edit-nama-<?php echo $row['nim']; ?>"
                                                                                            name="nama_lengkap"
                                                                                            pattern="[A-Za-z' -]+"
                                                                                            title="Nama hanya boleh berisi huruf, spasi, apostrof, dan tanda hubung."
                                                                                            value="<?php echo htmlspecialchars($row['nama_lengkap']); ?>"
                                                                                            required />
                                                                                    </div>
                                                                                    <div class="mb-3">
                                                                                        <label for="edit-nilai-<?php echo $row['nim']; ?>" class="form-label"> Nilai Akhir </label>
                                                                                        <input
                                                                                            type="number"
                                                                                            class="form-control"
                                                                                            id="edit-nilai-<?php echo $row['nim']; ?>"
                                                                                            name="nilai_akhir"
                                                                                            value="<?php echo htmlspecialchars($row['nilai_akhir']); ?>"
                                                                                            min="0"
                                                                                            max="100"
                                                                                            step="0.01"
                                                                                            required />
                                                                                    </div>
                                                                                    <div class="mb-3">
                                                                                        <label for="edit-foto-<?php echo $row['nim']; ?>" class="form-label">Foto Mahasiswa</label>
                                                                                        <select class="form-select" id="edit-foto-<?php echo $row['nim']; ?>" name="foto">
                                                                                            <option value="">Gunakan foto default</option>
                                                                                            <?php foreach ($fotoList as $namaFoto) { ?>
                                                                                                <option value="<?php echo htmlspecialchars($namaFoto, ENT_QUOTES, 'UTF-8'); ?>"
                                                                                                                <?php echo (($row['foto'] ?? '') === $namaFoto) ? 'selected' : ''; ?>>
                                                                                                                <?php echo htmlspecialchars($namaFoto, ENT_QUOTES, 'UTF-8'); ?></option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                        <div class="form-text">Pilih foto dari folder assets/img.</div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                                        Cancel
                                                                                    </button>
                                                                                    <button type="submit" class="btn btn-primary">Update Data</button>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!--end::Edit User Modal-->

                                                                <!--begin::Delete User Modal-->
                                                                <div
                                                                    class="modal fade text-start"
                                                                    id="modal-delete-<?php echo $row['nim']; ?>"
                                                                    tabindex="-1"
                                                                    aria-labelledby="modal-delete-label-<?php echo $row['nim']; ?>"
                                                                    aria-hidden="true">
                                                                    <div class="modal-dialog">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title" id="modal-delete-label-<?php echo $row['nim']; ?>">Delete</h5>
                                                                                <button
                                                                                    type="button"
                                                                                    class="btn-close"
                                                                                    data-bs-dismiss="modal"
                                                                                    aria-label="Close"></button>
                                                                            </div>
                                                                            <form action="deletedata_nilai.php" method="POST">
                                                                                <div class="modal-body">
                                                                                    <input
                                                                                        type="hidden"
                                                                                        name="nim"
                                                                                        value="<?php echo htmlspecialchars($row['nim']); ?>">
                                                                                    <p class="mb-0">
                                                                                        Apakah Anda Yakin Ingin Menghapus Data Nilai Ini?
                                                                                    </p>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                                        Cancel
                                                                                    </button>
                                                                                    <button type="Submit" class="btn btn-danger">
                                                                                        Delete Data
                                                                                    </button>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!--end::Delete User Modal-->

                                                            </td>
                                                        </tr>
                                                    <?php
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>

                                        <?php
                                        mysqli_close($conn);
                                        ?>
                                        </div>
                                        <!-- /.table-responsive -->
                                    </div>
                                    <!--end::Card Body-->

                                    <!--begin::Card Footer-->
                                    <div class="card-footer clearfix">
                                        <div class="float-start pt-1 fs-7 text-body-secondary">
                                            Showing <?php echo $showing_awal; ?> to
                                                    <?php echo $showing_akhir; ?> of
                                                    <?php echo $total_data; ?> data
                                        </div>

                                        <!-- Navigasi pindah halaman -->
                                        <ul class="pagination pagination-sm m-0 float-end">
                                            <li class="page-item <?php echo ($halaman <= 1) ? 'disabled' : ''; ?>">
                                                <a class="page-link" href="<?php echo ($halaman > 1) ? htmlspecialchars(urlHalaman($halaman - 1), ENT_QUOTES, 'UTF-8') : '#'; ?>" aria-label="Previous">&laquo;</a>
                                            </li>
                                            <?php for ($i = 1; $i <= $total_halaman; $i++) { ?>
                                                <li class="page-item <?php echo ($i === $halaman) ? 'active' : ''; ?>">
                                                    <a class="page-link" href="<?php echo htmlspecialchars(urlHalaman($i), ENT_QUOTES, 'UTF-8'); ?>"><?php echo $i; ?></a>
                                                </li>
                                            <?php } ?>
                                            <li class="page-item <?php echo ($halaman >= $total_halaman) ? 'disabled' : ''; ?>">
                                                <a class="page-link" href="<?php echo ($halaman < $total_halaman) ? htmlspecialchars(urlHalaman($halaman + 1), ENT_QUOTES, 'UTF-8') : '#'; ?>" aria-label="Next">&raquo;</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <!--end::Card Footer-->
                            </div>
                            <!--end::Card-->
                        </div>
                        <!-- /.col -->
                    </div>
                    <!--end::Row-->

                    <!--begin::Add User Modal-->
                    <div
                        class="modal fade"
                        id="modal-add-user"
                        tabindex="-1"
                        aria-labelledby="modal-add-user-label"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="createdata_nilai.php" method="POST">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modal-add-user-label">Tambah Data Nilai</h5>
                                        <button
                                            type="button"
                                            class="btn-close"
                                            data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="nim" class="form-label"> NIM </label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                id="nim"
                                                name="nim"
                                                placeholder="contoh: 3201216000"
                                                required />
                                        </div>
                                        <div class="mb-3">
                                            <label for="nama_lengkap" class="form-label"> Nama Mahasiswa </label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                id="nama_lengkap"
                                                name="nama_lengkap"
                                                pattern="[A-Za-z' -]+"
                                                title="Nama hanya boleh berisi huruf, spasi, apostrof, dan tanda hubung."
                                                placeholder="contoh: Jane Doe"
                                                required />
                                            <!-- <div class="form-text">The invitation will be sent to this address.</div> -->
                                        </div>
                                        <div class="mb-3">
                                            <label for="nilai_akhir" class="form-label"> Nilai Akhir </label>
                                            <input
                                                type="number"
                                                class="form-control"
                                                id="nilai_akhir"
                                                name="nilai_akhir"
                                                placeholder="75.55"
                                                min="0"
                                                max="100"
                                                step="0.01"
                                                required />
                                        </div>
                                        <div class="mb-3">
                                            <label for="foto" class="form-label">Foto Mahasiswa</label>
                                            <select class="form-select" id="foto" name="foto">
                                                <option value="">Gunakan foto default</option>
                                                <?php foreach ($fotoList as $namaFoto) { ?>
                                                    <option value="<?php echo htmlspecialchars($namaFoto, ENT_QUOTES, 'UTF-8'); ?>">
                                                        <?php echo htmlspecialchars($namaFoto, ENT_QUOTES, 'UTF-8'); ?></option>
                                                <?php } ?>
                                            </select>
                                            <div class="form-text">Foto dipilih dari folder assets/img.</div>
                                        </div>
                                        <div class="form-check">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                id="new-user-welcome"
                                                checked />
                                            <label class="form-check-label" for="new-user-welcome">
                                                Kirim data mahasiswa
                                            </label>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                            Cancel
                                        </button>
                                        <button type="submit" class="btn btn-primary">Create Data</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!--end::Add User Modal-->

                </div>
                <!--end::Container-->
            </div>
            <!--end::App Content-->
        </main>
        <!--end::App Main-->
        <!--begin::Footer-->
        <footer class="app-footer">
            <!--begin::To the end-->
            <div class="float-end d-none d-sm-inline">
                Pemrograman Web Semester 5 - Desain menggunakan
                <a href="https://adminlte.io" class="text-decoration-none">AdminLTE.io</a>.
            </div>
            <!--end::To the end-->
            <!--begin::Copyright-->
            <strong>
                Hak Cipta &copy; Pada Prodi TI 2026&nbsp;
                <a href="https://adminlte.io" class="text-decoration-none">AdminLTE.io</a>.
            </strong>
            Seluruh Hak Cipta Dilindungi.
            <!--end::Copyright-->
        </footer>
        <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <script
        src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"
        crossorigin="anonymous"></script>
    <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script
        src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        crossorigin="anonymous"></script>
    <!--end::Required Plugin(popperjs for Bootstrap 5)--><!--begin::Required Plugin(Bootstrap 5)-->
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"
        crossorigin="anonymous"></script>
    <!--end::Required Plugin(Bootstrap 5)--><!--begin::Required Plugin(AdminLTE)-->
    <script src="./js/adminlte.js"></script>
    <!--end::Required Plugin(AdminLTE)-->
    <!--begin::OverlayScrollbars Configure-->
    <script>
        const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
        const Default = {
            scrollbarTheme: 'os-theme-light',
            scrollbarAutoHide: 'leave',
            scrollbarClickScroll: true,
        };
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);

            // Disable OverlayScrollbars on mobile devices to prevent touch interference
            const isMobile = window.innerWidth <= 992;

            if (
                sidebarWrapper &&
                OverlayScrollbarsGlobal?.OverlayScrollbars !== undefined &&
                !isMobile
            ) {
                OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
                    scrollbars: {
                        theme: Default.scrollbarTheme,
                        autoHide: Default.scrollbarAutoHide,
                        clickScroll: Default.scrollbarClickScroll,
                    },
                });
            }
        });
    </script>
    <!--end::OverlayScrollbars Configure-->

    <!--begin::Color Mode Toggle-->
    <!-- The light/dark/auto switcher ships in adminlte.js as the ColorMode
     module (since 4.1) — no page script needed. Only the no-flash snippet
     in <head> stays inline, because it must run before first paint. -->
    <!--end::Color Mode Toggle-->

    <!-- Pencarian nama dan filter predikat diproses oleh PHP melalui form GET -->
    <!--end::Script-->

    <script>
        // AJAX: untuk pencarian nama, filter predikat, sorting nilai akhir, dan pagination/halaman.
        (function () {
            const searchInput = document.getElementById('user-search');
            const predikatSelect = document.getElementById('predikat');
            const sortSelect = document.getElementById('sort');
            const batasSelect = document.getElementById('batas');
            let timerPencarian;

            function buatUrl(halaman = 1) {
                const params = new URLSearchParams(window.location.search);
                params.set('nama', searchInput ? searchInput.value.trim() : '');
                params.set('q', predikatSelect ? predikatSelect.value : 'all');
                params.set('sort', sortSelect ? sortSelect.value : 'semua_nilai');
                params.set('batas', batasSelect ? batasSelect.value : '10');
                params.set('halaman', halaman);
                return window.location.pathname + '?' + params.toString();
            }
            async function muatData(url, ubahHistory = true) {
                try {
                    const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    if (!response.ok) throw new Error('Gagal mengambil data');
                    const html = await response.text();
                    const doc = new DOMParser().parseFromString(html, 'text/html');
                    const bodyBaru = doc.querySelector('.card-body');
                    const footerBaru = doc.querySelector('.card-footer');
                    const bodyLama = document.querySelector('.card-body');
                    const footerLama = document.querySelector('.card-footer');
                    if (bodyBaru && bodyLama) bodyLama.innerHTML = bodyBaru.innerHTML;
                    if (footerBaru && footerLama) footerLama.innerHTML = footerBaru.innerHTML;
                    // URL browser tetap bersih; parameter hanya dipakai saat fetch data.
                    if (ubahHistory) history.replaceState({}, '', window.location.pathname);
                    pasangLinkPagination();
                } catch (error) {
                    // Jika AJAX gagal, muat ulang halaman menggunakan parameter filter.
                    window.location.href = url;
                }
            }

            function pasangLinkPagination() {
                document.querySelectorAll('.card-footer .pagination a.page-link').forEach(function (link) {
                    link.onclick = function (event) {
                        const href = link.getAttribute('href');
                        if (href && href !== '#') {
                            event.preventDefault();
                            muatData(href);
                        }
                    };
                });
            }

            const formPencarian = searchInput ? searchInput.closest('form') : null;
            if (formPencarian) {
                formPencarian.addEventListener('submit', function (event) {
                    event.preventDefault();
                    muatData(buatUrl(1));
                });
            }

            if (searchInput) searchInput.addEventListener('input', function () {
                clearTimeout(timerPencarian);
                timerPencarian = setTimeout(function () { muatData(buatUrl(1)); }, 300);
            });
            [predikatSelect, sortSelect, batasSelect].forEach(function (el) {
                if (el) el.addEventListener('change', function () { muatData(buatUrl(1)); });
            });
            window.addEventListener('popstate', function () { window.location.reload(); });

            // Bersihkan parameter lama di URL setelah PHP selesai membaca GET.
            if (window.location.search !== '') {
                history.replaceState({}, '', window.location.pathname);
            }
            pasangLinkPagination();
        })();
    </script>

</body>
<!--end::Body-->

</html>