<?php
session_start();
function getAllOrders() {
    $file = '../data/datasewa.json';
    if (file_exists($file)) {
        $current_data = json_decode(file_get_contents($file), true);
    } else {
        $current_data = [];
    }
    return $current_data;
}

$orders = getAllOrders();

if (empty($orders)) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histori Pemesanan</title>
    <!-- Link ke file CSS Bootstrap -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="text-center">
            <h1>Histori Pemesanan Car Showroom</h1>
        </div>
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="card mt-2 mx-auto p-4 bg-light">
                    <div class="card-body bg-light">
                        <div class="container">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Nomor HP</th>
                                        <th>Durasi Pemakaian</th>
                                        <th>Mobil</th>
                                        <th>Total Tagihan</th>
                                        <th>Tanggal Pesan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $index => $data) : ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($data['nama']); ?></td>
                                            <td><?php echo htmlspecialchars($data['nomor_hp']); ?></td>
                                            <td><?php echo htmlspecialchars($data['durasi_pemakaian']); ?></td>
                                            <td><?php echo htmlspecialchars($data['pilihan_mobil']); ?></td>
                                            <td>Rp <?php echo isset($data['totalTagihan']) ? number_format((float)$data['totalTagihan'], 0, ',', '.') : '0'; ?></td>
                                            <td><?php echo date('d M Y H:i:s', $data['timestamp']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Link ke file JavaScript Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
