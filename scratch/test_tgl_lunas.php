<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$invoices = App\Models\Invoice::where('status', 'Paid')->get();

foreach ($invoices as $inv) {
    // 1. Check settled_by_jurnal_header_id in BukuPembantu
    $tanggalLunas = null;
    $source = 'updated_at';

    $bp = App\Models\BukuPembantu::whereHas('jurnalHeader', function($q) use ($inv) {
        $q->where('referensi_type', App\Models\Invoice::class)
          ->where('referensi_id', $inv->id);
    })->first();

    if ($bp && $bp->settled_by_jurnal_header_id) {
        $settledJh = App\Models\JurnalHeader::find($bp->settled_by_jurnal_header_id);
        if ($settledJh && $settledJh->tanggal) {
            $tanggalLunas = $settledJh->tanggal;
            $source = 'BukuPembantu settled_by';
        }
    }

    if (!$tanggalLunas) {
        // 2. Check JurnalHeader with "Pembayaran" in deskripsi linked to this invoice
        $payJh = App\Models\JurnalHeader::where('referensi_type', App\Models\Invoice::class)
            ->where('referensi_id', $inv->id)
            ->where('deskripsi', 'like', '%Penerimaan Pembayaran%')
            ->orderBy('tanggal', 'desc')
            ->first();
        if ($payJh && $payJh->tanggal) {
            $tanggalLunas = $payJh->tanggal;
            $source = 'JurnalHeader Penerimaan Pembayaran';
        }
    }

    if (!$tanggalLunas) {
        $tanggalLunas = $inv->updated_at;
    }

    echo "Invoice #{$inv->nomor_invoice} | Tgl Invoice: " . $inv->tanggal_invoice->format('Y-m-d') . " | Tgl Lunas: " . \Carbon\Carbon::parse($tanggalLunas)->format('Y-m-d') . " (Source: {$source})" . PHP_EOL;
}
