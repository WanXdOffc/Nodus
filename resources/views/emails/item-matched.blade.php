<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kabar Baik dari NODUS</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }
        .header {
            background: #0f172a;
            padding: 30px 40px;
            text-align: center;
        }
        .brand-title {
            color: #ffffff;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: 2px;
            margin: 0;
        }
        .brand-subtitle {
            color: #00C2A8;
            font-size: 12px;
            font-weight: 600;
            margin-top: 4px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .content {
            padding: 36px 40px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 12px;
        }
        .badge-match {
            display: inline-block;
            background-color: #e6fffa;
            color: #00a892;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 800;
            border: 1px solid #b2f5ea;
            margin-bottom: 16px;
        }
        .card-details {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 20px;
            margin: 24px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 13px;
        }
        .detail-row:last-child {
            margin-bottom: 0;
        }
        .detail-label {
            color: #64748b;
            font-weight: 600;
        }
        .detail-value {
            color: #0f172a;
            font-weight: 700;
            text-align: right;
        }
        .btn-wrapper {
            text-align: center;
            margin: 32px 0;
        }
        .btn-cta {
            display: inline-block;
            background-color: #00C2A8;
            color: #ffffff !important;
            padding: 14px 32px;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 800;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(0, 194, 168, 0.3);
            transition: background-color 0.2s;
        }
        .btn-cta:hover {
            background-color: #00ab94;
        }
        .footer {
            background-color: #f1f5f9;
            padding: 24px 40px;
            text-align: center;
            font-size: 11px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1 class="brand-title">NODUS</h1>
            <div class="brand-subtitle">Smart AI Lost &amp; Found Platform</div>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="badge-match">
                ⭐ AI Match Detected ({{ $ticket->ai_match_score }}% Match)
            </div>

            <div class="greeting">
                Halo, {{ $recipient->name }}!
            </div>

            <p style="font-size: 14px; color: #475569; margin-bottom: 16px;">
                @if($userRole === 'seeker')
                    Kabar baik! Sistem AI NODUS baru saja mendeteksi kecocokan antara laporan <strong>barang kehilangan</strong> Anda dengan barang temuan yang baru dilaporkan.
                @else
                    Kabar baik! Barang temuan yang Anda laporkan di NODUS memiliki kecocokan tinggi dengan laporan kehilangan dari pemilik sah.
                @endif
            </p>

            <!-- Card Details -->
            <div class="card-details">
                <div class="detail-row">
                    <span class="detail-label">Barang Kehilangan:</span>
                    <span class="detail-value">{{ $lostItem?->title ?? '-' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Barang Temuan:</span>
                    <span class="detail-value">{{ $foundItem?->title ?? '-' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Kategori:</span>
                    <span class="detail-value">{{ $lostItem?->category ?? $foundItem?->category ?? 'Umum' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Perkiraan Lokasi:</span>
                    <span class="detail-value">{{ $foundItem?->location_name ?? $lostItem?->location_name ?? 'Singaraja' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Tingkat Kecocokan AI:</span>
                    <span class="detail-value" style="color: #00C2A8;">{{ $ticket->ai_match_score }}% Sangat Cocok</span>
                </div>
            </div>

            <p style="font-size: 13px; color: #64748b; margin-bottom: 24px;">
                Tiket komunikasi aman telah dibuka. Anda dapat langsung membuka percakapan terenkripsi untuk memverifikasi detail kepemilikan dan mengatur serah terima barang.
            </p>

            <!-- Action Button -->
            <div class="btn-wrapper">
                <a href="{{ $actionUrl }}" class="btn-cta" target="_blank">
                    Buka Tiket Klaim &amp; Menuju Web NODUS &rarr;
                </a>
            </div>

            <p style="font-size: 12px; color: #94a3b8; text-align: center; margin-top: 24px;">
                Privasi dan nomor telepon Anda tetap aman. Seluruh verifikasi dilakukan melalui platform web NODUS.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            &copy; {{ date('Y') }} NODUS Platform. Interoperabilitas &amp; Rekonsiliasi Cerdas Kampus.<br>
            Email ini dikirim secara otomatis oleh NODUS Notification Engine.
        </div>
    </div>
</body>
</html>
