<?php
session_start();
function saveToJSON($data) {
    $file = '../data/datasewa.json';
    if (file_exists($file)) {
        $current_data = json_decode(file_get_contents($file), true);
    } else {
        $current_data = [];
    }
    $current_data[] = $data;
    file_put_contents($file, json_encode($current_data, JSON_PRETTY_PRINT));
}

function convertDurationToDays($duration) {
    $parts = explode(' ', $duration);
    $number = (int)$parts[0];
    $unit = strtolower($parts[1]);

    switch ($unit) {
        case 'jam':
            return $number / 24; // Konversi jam ke hari
        case 'hari':
            return $number;
        case 'minggu':
            return $number * 7;
        case 'bulan':
            return $number * 30;
        case 'tahun':
            return $number * 365;
        default:
            return 0;
    }
}

// Memeriksa apakah data input sudah ada di session
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['hitung'])) {
        $_SESSION['name'] = $_POST['name'];
        $_SESSION['nowa'] = $_POST['nowa'];
        $_SESSION['durasi'] = $_POST['durasi'];
        $_SESSION['need'] = $_POST['need'];

        // Menghitung total tagihan
        $durasiPemakaian = convertDurationToDays($_POST['durasi']);
        $pilihanMobil = $_POST['need'];

        // Harga sewa mobil per jam dan per hari
        $hargaSewaMobil = [
            'Toyota' => [
                'jam' => 100000,
                'hari' => 1000000
            ],
            'BMW' => [
                'jam' => 500000,
                'hari' => 5000000
            ],
            'Suzuki' => [
                'jam' => 120000,
                'hari' => 1200000
            ],
            'Honda' => [
                'jam' => 300000,
                'hari' => 3000000
            ]
        ];

        // Hitung total tagihan berdasarkan durasi pemakaian dan pilihan mobil
        $hargaPerJam = $hargaSewaMobil[$pilihanMobil]['jam'];
        $hargaPerHari = $hargaSewaMobil[$pilihanMobil]['hari'];

        // Jika durasi kurang dari 1 hari, hitung berdasarkan harga per jam
        if ($durasiPemakaian < 1) {
            $totalTagihan = $durasiPemakaian * $hargaPerJam;
        } else {
            $totalTagihan = $durasiPemakaian * $hargaPerHari;
        }

        $_SESSION['totalTagihan'] = $totalTagihan;
    }

    if (isset($_POST['kirim'])) {
        // Menyimpan data pemesanan ke file JSON
        $data = [
            'nama' => $_SESSION['name'],
            'nomor_hp' => $_SESSION['nowa'],
            'durasi_pemakaian' => $_SESSION['durasi'],
            'pilihan_mobil' => $_SESSION['need'],
            'totalTagihan' => $_SESSION['totalTagihan'],
            'timestamp' => time()
        ];

        saveToJSON($data);

        // Hapus sesi setelah data disimpan
        unset($_SESSION['name']);
        unset($_SESSION['nowa']);
        unset($_SESSION['durasi']);
        unset($_SESSION['need']);
        unset($_SESSION['totalTagihan']);

        // Redirect ke halaman detail atau sukses
        header("Location: detail.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PEMESANAN</title>
    <!-- Link ke file CSS Bootstrap -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <div class="text-center mt-5">
        <h1>CAR SHOWROOM</h1>
    </div>
    
    <div class="row">
        <div class="col-lg-7 mx-auto">
            <div class="card mt-2 mx-auto p-4 bg-light">
                <div class="card-body bg-light">
                    <div class="container">
                        <!-- Form pemesanan -->
                        <form id="contact-form" method="post" role="form">
                            <div class="controls">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="form_name">Nama</label>
                                            <input id="form_name" type="text" name="name" class="form-control" placeholder="Masukan Nama" required="required" data-error="Nama tidak tersedia." value="<?php echo isset($_SESSION['name']) ? $_SESSION['name'] : ''; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="form_nowa">No HP</label>
                                            <input id="form_nowa" type="text" name="nowa" class="form-control" placeholder="0817...." required="required" data-error="No tidak tersedia." value="<?php echo isset($_SESSION['nowa']) ? $_SESSION['nowa'] : ''; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="form_durasi">Durasi Pemakaian</label>
                                            <input id="form_durasi" type="text" name="durasi" class="form-control" placeholder="Masukan Durasi (contoh: 3 hari atau 2 minggu)" required="required" pattern="^[0-9]+(\s?(hari|minggu|bulan|tahun|jam))$" data-error="Durasi harus diisi dan valid." value="<?php echo isset($_SESSION['durasi']) ? $_SESSION['durasi'] : ''; ?>">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="form_need">Pilih Mobil yang Anda inginkan</label>
                                            <select id="form_need" name="need" class="form-control" required="required" data-error="Please specify your need.">
                                                <option value="" selected disabled>Sewa Mobil</option>
                                                <option <?php echo (isset($_SESSION['need']) && $_SESSION['need'] == 'Toyota') ? 'selected' : ''; ?>>Toyota</option>
                                                <option <?php echo (isset($_SESSION['need']) && $_SESSION['need'] == 'BMW') ? 'selected' : ''; ?>>BMW</option>
                                                <option <?php echo (isset($_SESSION['need']) && $_SESSION['need'] == 'Suzuki') ? 'selected' : ''; ?>>Suzuki</option>
                                                <option <?php echo (isset($_SESSION['need']) && $_SESSION['need'] == 'Honda') ? 'selected' : ''; ?>>Honda</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 d-flex justify-content-end">
                                        <!-- Tombol HITUNG dan KIRIM -->
                                        <button type="submit" name="hitung" class="btn btn-success btn-send pt-2 mr-2">HITUNG</button>
                                        <button type="submit" name="kirim" class="btn btn-primary btn-send pt-2">KIRIM</button>
                                    </div>
                                </div>
                                <!-- Menampilkan total tagihan jika sudah dihitung -->
                                <?php
                                if (isset($_SESSION['totalTagihan'])) {
                                    echo "<div class='mt-4'><h4>Total Tagihan: Rp " . number_format($_SESSION['totalTagihan'], 0, ',', '.') . "</h4></div>";
                                }
                                ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- /.8 -->
        </div>
        <!-- /.row-->
    </div>
</div>

<!-- Link ke file JavaScript Bootstrap -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
