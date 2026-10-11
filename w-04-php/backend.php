<?php
// echo "ini halaman backend.php";
$data = $_POST;
$nama = $data['nama'];
$nilai = $data['nilai'];
$keputusan = 'Tidak Lulus';
if ($nilai >= 60) {
    $keputusan = "Lulus";
} else {
    $keputusan = "Tidak Lulus";
}
print_r('siswa atas nama ' . $nama . ' nilai kamu adalah ' . $nilai . ' dengan keputusan ' . $keputusan);
