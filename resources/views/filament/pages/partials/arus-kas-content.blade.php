        {{-- Laporan --}}
        <div class="report-container fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden">
            <div class="p-8">
                <x-kop-surat :tenant="$tenant ?? null" />

                <div class="text-center mb-6">
                    <h2 class="text-xl font-bold uppercase tracking-wider dark:text-white">Laporan Arus Kas</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Periode {{ \Carbon\Carbon::parse($dari)->translatedFormat('d F Y') }} s.d. {{ \Carbon\Carbon::parse($sampai)->translatedFormat('d F Y') }}
                    </p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">(Metode Langsung — SAK Entitas Privat)</p>
                </div>

                <table class="report-table text-gray-900 dark:text-gray-100 w-full border-collapse text-sm">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-800 border-b-2 border-gray-400">
                            <th class="py-2 px-4 font-bold text-gray-800 dark:text-white uppercase tracking-wide">Keterangan</th>
                            <th class="py-2 px-4 font-bold text-gray-800 dark:text-white uppercase tracking-wide text-right">Nilai Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Saldo Awal --}}
                        <tr class="bg-blue-50 dark:bg-blue-900/20 border-b border-blue-200 dark:border-blue-800">
                            <td class="py-3 px-4 font-semibold text-blue-800 dark:text-blue-300">Saldo Kas Awal Periode</td>
                            <td class="py-3 px-4 text-right font-mono font-semibold text-blue-800 dark:text-blue-300">Rp {{ number_format($data['saldoAwal'], 0, ',', '.') }}</td>
                        </tr>

                        {{-- Spacer --}}
                        <tr><td colspan="2" class="py-2 border-none"></td></tr>

                        {{-- Aktivitas Operasi --}}
                        <tr class="bg-gray-50 dark:bg-gray-800/50">
                            <td colspan="2" class="py-2 px-4 font-semibold text-gray-700 dark:text-gray-300">ARUS KAS DARI AKTIVITAS OPERASI</td>
                        </tr>
                        @forelse($data['operasi'] as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 border-b border-gray-100 dark:border-gray-800">
                            <td class="py-1.5 pl-12 pr-4 text-gray-600 dark:text-gray-400">{{ $item->kode_akun }} &mdash; {{ $item->nama_akun }}</td>
                            <td class="py-1.5 px-4 text-right font-mono {{ $item->kas_bersih >= 0 ? 'text-green-700 dark:text-green-400' : 'text-red-700 dark:text-red-400' }}">
                                Rp {{ number_format($item->kas_bersih, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="py-1.5 pl-12 italic text-gray-500">Tidak ada transaksi kas</td></tr>
                        @endforelse
                        <tr class="border-t border-gray-300 dark:border-gray-600">
                            <td class="py-2 pl-12 pr-4 font-semibold text-gray-800 dark:text-gray-200">Arus Kas Bersih Aktivitas Operasi</td>
                            <td class="py-2 px-4 text-right font-mono font-semibold {{ $data['totalKasBersih'] >= 0 ? 'text-green-800 dark:text-green-300' : 'text-red-800 dark:text-red-300' }}">
                                Rp {{ number_format($data['totalKasBersih'], 0, ',', '.') }}
                            </td>
                        </tr>

                        {{-- Spacer --}}
                        <tr><td colspan="2" class="py-2 border-none"></td></tr>

                        {{-- Saldo Akhir --}}
                        <tr class="bg-blue-50 dark:bg-blue-900/20 border-t-2 border-b-2 border-blue-300 dark:border-blue-700">
                            <td class="py-3 px-4 font-bold uppercase text-blue-900 dark:text-blue-100">SALDO KAS AKHIR PERIODE</td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-blue-900 dark:text-blue-100">Rp {{ number_format($data['saldoAkhir'], 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>

                {{-- Summary Cards --}}
                <div class="report-summary mt-6 grid grid-cols-1 md:grid-cols-3 gap-4 {{ ($isExport ?? false) ? 'grid-cols-3' : '' }}">
                    <div class="px-6 py-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg text-center shadow-sm">
                        <p class="text-xs font-bold text-green-700 dark:text-green-400 uppercase tracking-wider">Penerimaan Kas (In)</p>
                        <p class="text-xl font-bold font-mono text-green-700 dark:text-green-300 mt-2">Rp {{ number_format($data['totalKasMasuk'], 0, ',', '.') }}</p>
                    </div>
                    <div class="px-6 py-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-center shadow-sm">
                        <p class="text-xs font-bold text-red-700 dark:text-red-400 uppercase tracking-wider">Pengeluaran Kas (Out)</p>
                        <p class="text-xl font-bold font-mono text-red-700 dark:text-red-300 mt-2">Rp {{ number_format($data['totalKasKeluar'], 0, ',', '.') }}</p>
                    </div>
                    <div class="px-6 py-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg text-center shadow-sm">
                        <p class="text-xs font-bold text-blue-700 dark:text-blue-400 uppercase tracking-wider">Perubahan Kas Bersih</p>
                        <p class="text-xl font-bold font-mono text-blue-700 dark:text-blue-300 mt-2">Rp {{ number_format($data['totalKasBersih'], 0, ',', '.') }}</p>
                    </div>
                </div>
@if(($isExport ?? false))
                <div style="clear: both;"></div>
@endif
            </div>
            <x-report-signatures :tenant="$tenant ?? null" />
        </div>
    </div>
