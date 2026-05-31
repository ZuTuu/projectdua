<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReservasiNotification extends Notification
{
    use Queueable;

    // Tambahkan dua baris ini agar variabel bisa diakses di seluruh class
    public $reservasi;
    public $status;

    // Update bagian __construct untuk menangkap kiriman dari Controller
    public function __construct($reservasi, $status)
    {
        $this->reservasi = $reservasi;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['database']; // Menyimpan notifikasi ke tabel notifications
    }

    public function toArray($notifiable)
        {
            // Sesuaikan pesan berdasarkan status
            if ($this->status == 'Dikonfirmasi') {
                $pesan = 'Hore! Pembayaran DP untuk reservasi tanggal ' . $this->reservasi->tanggal . ' berhasil dikonfirmasi Admin. Layanan akan segera diproses.';
            } else {
                $pesan = 'Maaf, pembayaran DP untuk reservasi tanggal ' . $this->reservasi->tanggal . ' gagal/ditolak. Silakan cek kembali.';
            }

            return [
                'id_reservasi' => $this->reservasi->id,
                'pesan' => $pesan,
                'status' => $this->status,
            ];
        }
}
