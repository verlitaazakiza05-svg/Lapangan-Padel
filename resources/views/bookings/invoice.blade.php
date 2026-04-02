<!DOCTYPE html>
<html>
<head>
    <title>Invoice Booking - {{ $booking->kode_booking }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #800020;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #800020;
            margin: 0;
        }
        .info {
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .total {
            margin-top: 20px;
            text-align: right;
            font-size: 18px;
            font-weight: bold;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="header">
            <h1>PADEL ARENA</h1>
            <p>Jl. Raya Padel No. 123, Jakarta</p>
            <p>Telp: (021) 1234567</p>
        </div>

        <div class="info">
            <h3>INVOICE BOOKING</h3>
            <p><strong>Kode Booking:</strong> {{ $booking->kode_booking }}</p>
            <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
        </div>

        <table>
            <tr>
                <th colspan="2">Detail Booking</th>
            </tr>
            <tr>
                <td width="30%">Lapangan</td>
                <td>{{ $booking->lapangan->nama_lapangan ?? '-' }}</td>
            </tr>
            <tr>
                <td>Pelanggan</td>
                <td>{{ $booking->pelanggan->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td>Tanggal Booking</td>
                <td>{{ \Carbon\Carbon::parse($booking->tanggal_booking)->format('d F Y') }}</td>
            </tr>
            <tr>
                <td>Waktu</td>
                <td>{{ substr($booking->waktu_mulai, 0, 5) }} - {{ substr($booking->waktu_selesai, 0, 5) }}</td>
            </tr>
            <tr>
                <td>Durasi</td>
                <td>{{ $booking->total_jam }} Jam</td>
            </tr>
            <tr>
                <td>Harga/Jam</td>
                <td>Rp {{ number_format($booking->lapangan->harga_per_jam ?? 0, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="total">
            Total: Rp {{ number_format($booking->total_harga, 0, ',', '.') }}
        </div>

        <div class="footer">
            <p>Terima kasih telah menggunakan layanan kami</p>
            <p>Booking dapat dibatalkan maksimal H-1 sebelum jadwal</p>
        </div>
    </div>

    <script>
        window.print();
    </script>
</body>
</html>
