
<?php
session_start();

if (!isset($_SESSION["host"])) {
  header("Location: login.php");
  exit;
}

if (!isset($_SESSION["daftarItem"])) {
  $_SESSION["daftarItem"] = [];
}

// Proses upload bukti struk
$folderTujuan = __DIR__ . "/uploads/";
$namaFileBukti = "";

if (isset($_FILES["bukti"]) &&
    $_FILES["bukti"]["error"] !== UPLOAD_ERR_NO_FILE) {

  if ($_FILES["bukti"]["error"] !== UPLOAD_ERR_OK) {
    die("Upload bukti struk gagal.");
  }

  $ekstensi = strtolower(
    pathinfo($_FILES["bukti"]["name"], PATHINFO_EXTENSION)
  );

  $jenisDiizinkan = [
    "jpg" => "image/jpeg",
    "jpeg" => "image/jpeg",
    "png" => "image/png"
  ];

  $tipeFile = (new finfo(FILEINFO_MIME_TYPE))
    ->file($_FILES["bukti"]["tmp_name"]);

  if (!isset($jenisDiizinkan[$ekstensi]) ||
      $tipeFile !== $jenisDiizinkan[$ekstensi]) {
    die("Bukti struk harus berupa JPG atau PNG.");
  }

  if (!is_dir($folderTujuan) &&
      !mkdir($folderTujuan, 0755, true)) {
    die("Folder upload tidak bisa dibuat.");
  }

  $namaUnik = bin2hex(random_bytes(8)) . "." . $ekstensi;

  if (!move_uploaded_file(
      $_FILES["bukti"]["tmp_name"],
      $folderTujuan . $namaUnik
  )) {
    die("Gagal menyimpan bukti struk.");
  }

  $namaFileBukti = "uploads/" . $namaUnik;
}

// Menyimpan data item ke session
$itemBaru = [
  "nama" => $_POST["nama"],
  "harga" => $_POST["harga"],
  "jumlahOrang" => $_POST["jumlahOrang"],
  "status" => $_POST["status"],
  "bukti" => $namaFileBukti,
];

array_push($_SESSION["daftarItem"], $itemBaru);

header("Location: dashboard.php");
exit;
