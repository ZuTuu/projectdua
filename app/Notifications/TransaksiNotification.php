<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TransaksiNotification extends Notification
{
    use Queueable;

    public $transaksi;
    public $status;

    public function __construct($transaksi, $status)
    {
        $this->transaksi = $transaksi;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['database']; // Masuk ke icon lonceng di header
    }

    public function toArray($notifiable)
    {
        // Pesan beda-beda tergantung di-acc atau ditolak
        if ($this->status == 'Dikonfirmasi') {
            $pesan = 'Hore! Pesanan produk kamu (#TRX-' . $this->transaksi->id . ') berhasil dikonfirmasi Admin dan akan segera diproses.';
        } else {
            $pesan = 'Maaf, pesanan produk kamu (#TRX-' . $this->transaksi->id . ') dibatalkan/ditolak oleh Admin.';
        }

        return [
            'id_transaksi' => $this->transaksi->id,
            'pesan' => $pesan,
            'status' => $this->status,
        ];
    }
}
