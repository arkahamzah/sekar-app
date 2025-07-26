<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $actionText }} - SEKAR</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .header p {
            margin: 8px 0 0 0;
            opacity: 0.9;
            font-size: 14px;
        }
        .content {
            padding: 30px 20px;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 20px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 25px 0;
            padding: 20px;
            background-color: #f8fafc;
            border-radius: 8px;
            border-left: 4px solid {{ $actionColor }};
        }
        .info-item {
            margin: 0;
        }
        .info-label {
            font-weight: 600;
            color: #374151;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .info-value {
            color: #1f2937;
            font-size: 14px;
            margin: 0;
        }
        .description-box {
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .description-box h3 {
            margin: 0 0 12px 0;
            color: #374151;
            font-size: 16px;
        }
        .description-text {
            color: #6b7280;
            line-height: 1.6;
            white-space: pre-line;
        }
        .action-button {
            display: inline-block;
            background-color: {{ $actionColor }};
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
            margin: 20px 0;
            transition: background-color 0.2s;
        }
        .action-button:hover {
            opacity: 0.9;
        }
        .footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            margin: 0;
            color: #6b7280;
            font-size: 12px;
            line-height: 1.5;
        }
        .footer a {
            color: {{ $actionColor }};
            text-decoration: none;
        }
        @media (max-width: 600px) {
            .info-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            .content {
                padding: 20px 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>SERIKAT KARYAWAN TELKOM</h1>
            <p>Sistem Informasi Keanggotaan</p>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Status Badge -->
            <div class="status-badge" style="background-color: {{ $actionColor }}20; color: {{ $actionColor }};">
                {{ $actionText }} {{ ucfirst($konsultasi->JENIS) }}
            </div>

            <!-- Greeting -->
            <h2 style="margin: 0 0 10px 0; color: #1f2937; font-size: 18px;">
                @if($actionType === 'new')
                    Ada {{ ucfirst($konsultasi->JENIS) }} Baru!
                @elseif($actionType === 'comment')
                    Ada Komentar Baru!
                @elseif($actionType === 'escalate')
                    {{ ucfirst($konsultasi->JENIS) }} Diescalate!
                @elseif($actionType === 'closed')
                    {{ ucfirst($konsultasi->JENIS) }} Telah Selesai!
                @else
                    Ada Update {{ ucfirst($konsultasi->JENIS) }}!
                @endif
            </h2>

            <p style="color: #6b7280; margin: 0 0 25px 0;">
                @if($actionType === 'new')
                    Pengajuan {{ strtolower($konsultasi->JENIS) }} baru telah masuk dan memerlukan perhatian Anda.
                @elseif($actionType === 'comment')
                    Ada komentar baru pada {{ strtolower($konsultasi->JENIS) }} yang perlu ditindaklanjuti.
                @elseif($actionType === 'escalate')
                    {{ ucfirst($konsultasi->JENIS) }} telah diescalate ke level yang lebih tinggi untuk penanganan lebih lanjut.
                @elseif($actionType === 'closed')
                    {{ ucfirst($konsultasi->JENIS) }} telah diselesaikan dan ditutup.
                @else
                    Ada pembaruan pada {{ strtolower($konsultasi->JENIS) }} yang perlu Anda ketahui.
                @endif
            </p>

            <!-- Information Grid -->
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Judul</div>
                    <div class="info-value">{{ $konsultasi->JUDUL }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Jenis</div>
                    <div class="info-value">{{ ucfirst($konsultasi->JENIS) }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Tujuan</div>
                    <div class="info-value">
                        {{ $konsultasi->TUJUAN }}
                        @if($konsultasi->TUJUAN_SPESIFIK)
                            - {{ $konsultasi->TUJUAN_SPESIFIK }}
                        @endif
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Status</div>
                    <div class="info-value">{{ $konsultasi->STATUS }}</div>
                </div>
                @if($karyawan)
                <div class="info-item">
                    <div class="info-label">Pengirim</div>
                    <div class="info-value">{{ $karyawan->V_NAMA_KARYAWAN }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">NIK</div>
                    <div class="info-value">{{ $konsultasi->N_NIK }}</div>
                </div>
                @endif
                <div class="info-item">
                    <div class="info-label">Tanggal</div>
                    <div class="info-value">{{ date('d F Y, H:i', strtotime($konsultasi->CREATED_AT)) }} WIB</div>
                </div>
                @if($konsultasi->KATEGORI_ADVOKASI)
                <div class="info-item">
                    <div class="info-label">Kategori</div>
                    <div class="info-value">{{ $konsultasi->KATEGORI_ADVOKASI }}</div>
                </div>
                @endif
            </div>

            <!-- Description -->
            <div class="description-box">
                <h3>Deskripsi {{ ucfirst($konsultasi->JENIS) }}</h3>
                <div class="description-text">{{ $konsultasi->DESKRIPSI }}</div>
            </div>

            <!-- Action Button -->
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $viewUrl }}" class="action-button">
                    @if($actionType === 'new')
                        Lihat & Tanggapi
                    @elseif($actionType === 'comment')
                        Lihat Komentar
                    @elseif($actionType === 'escalate')
                        Lihat Detail
                    @elseif($actionType === 'closed')
                        Lihat Hasil
                    @else
                        Lihat Detail
                    @endif
                </a>
            </div>

            <!-- Instructions -->
            <div style="background-color: #eff6ff; border-left: 4px solid #3b82f6; padding: 15px; margin: 20px 0; border-radius: 0 6px 6px 0;">
                <p style="margin: 0; color: #1e40af; font-size: 14px; font-weight: 600;">
                    💡 Petunjuk:
                </p>
                <p style="margin: 8px 0 0 0; color: #1e40af; font-size: 13px; line-height: 1.5;">
                    @if($actionType === 'new')
                        Silakan login ke sistem SEKAR untuk memberikan tanggapan atau komentar pada {{ strtolower($konsultasi->JENIS) }} ini.
                    @elseif($actionType === 'comment')
                        Ada komentar baru yang memerlukan respon Anda. Silakan cek dan berikan tanggapan yang diperlukan.
                    @elseif($actionType === 'escalate')
                        {{ ucfirst($konsultasi->JENIS) }} ini telah diescalate. Mohon segera ditindaklanjuti sesuai prosedur yang berlaku.
                    @elseif($actionType === 'closed')
                        {{ ucfirst($konsultasi->JENIS) }} telah diselesaikan. Anda dapat melihat hasil penanganan pada sistem.
                    @else
                        Silakan login ke sistem untuk melihat detail lengkap dan memberikan respon yang diperlukan.
                    @endif
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                <strong>SEKAR - Serikat Karyawan Telkom</strong><br>
                Email ini dikirim secara otomatis oleh sistem. Mohon tidak membalas email ini.<br>
                Untuk bantuan, silakan hubungi <a href="mailto:admin@sekar.telkom.co.id">admin@sekar.telkom.co.id</a>
            </p>
            <p style="margin-top: 15px; font-size: 11px; color: #9ca3af;">
                © {{ date('Y') }} PT Telkom Indonesia. Semua hak dilindungi.<br>
                Dikirim pada {{ date('d F Y, H:i') }} WIB
            </p>
        </div>
    </div>
</body>
</html>