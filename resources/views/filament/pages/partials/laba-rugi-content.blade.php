        {{-- Laporan --}}
        <div class="report-container fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden">
            <div class="p-8">
                
                <x-kop-surat :tenant="$tenant ?? null" />

                <div class="text-center mb-6">
                    <h2 class="text-xl font-bold uppercase tracking-wider dark:text-white">Laporan Laba Rugi</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Periode {{ \Carbon\Carbon::parse($dari)->translatedFormat('d F Y') }} s.d. {{ \Carbon\Carbon::parse($sampai)->translatedFormat('d F Y') }}
                    </p>
                </div>

                <table class="report-table text-gray-900 dark:text-gray-100 w-full border-collapse text-sm">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-800 border-b-2 border-gray-400">
                            <th class="py-2 px-4 font-bold text-gray-800 dark:text-white uppercase tracking-wide">Keterangan</th>
                            <th class="py-2 px-4 font-bold text-gray-800 dark:text-white uppercase tracking-wide text-right">Nilai Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- PENDAPATAN --}}
                        <tr class="bg-gray-50 dark:bg-gray-800/50">
                            <td colspan="2" class="py-2 px-4 font-semibold text-gray-700 dark:text-gray-300">PENDAPATAN</td>
                        </tr>
                        @forelse($data['pendapatan'] as $akun)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 border-b border-gray-100 dark:border-gray-800">
                            <td class="py-1.5 pl-8 pr-4 text-gray-600 dark:text-gray-400">{{ $akun->kode_akun }} &mdash; {{ $akun->nama_akun }}</td>
                            <td class="py-1.5 px-4 text-right font-mono text-gray-700 dark:text-gray-300">Rp {{ number_format($akun->saldo, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="py-1.5 pl-8 italic text-gray-500">Tidak ada transaksi</td></tr>
                        @endforelse
                        <tr class="border-t border-gray-300 dark:border-gray-600">
                            <td class="py-2 pl-8 pr-4 font-semibold text-green-700 dark:text-green-400">Total Pendapatan</td>
                            <td class="py-2 px-4 text-right font-mono font-semibold text-green-700 dark:text-green-400 whitespace-nowrap">Rp {{ number_format($data['totalPendapatan'], 0, ',', '.') }}</td>
                        </tr>

                        {{-- Spacer --}}
                        <tr><td colspan="2" class="py-3"></td></tr>

                        {{-- BEBAN --}}
                        <tr class="bg-gray-50 dark:bg-gray-800/50">
                            <td colspan="2" class="py-2 px-4 font-semibold text-gray-700 dark:text-gray-300">BEBAN</td>
                        </tr>
                        @forelse($data['beban'] as $akun)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 border-b border-gray-100 dark:border-gray-800">
                            <td class="py-1.5 pl-8 pr-4 text-gray-600 dark:text-gray-400">{{ $akun->kode_akun }} &mdash; {{ $akun->nama_akun }}</td>
                            <td class="py-1.5 px-4 text-right font-mono text-red-600 dark:text-red-400">(Rp {{ number_format($akun->saldo, 0, ',', '.') }})</td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="py-1.5 pl-8 italic text-gray-500">Tidak ada transaksi</td></tr>
                        @endforelse
                        <tr class="border-t border-gray-300 dark:border-gray-600">
                            <td class="py-2 pl-8 pr-4 font-semibold text-red-700 dark:text-red-400">Total Beban</td>
                            <td class="py-2 px-4 text-right font-mono font-semibold text-red-700 dark:text-red-400 whitespace-nowrap">(Rp {{ number_format($data['totalBeban'], 0, ',', '.') }})</td>
                        </tr>

                        {{-- Spacer --}}
                        <tr><td colspan="2" class="py-3 border-none"></td></tr>

                        {{-- LABA/RUGI BERSIH --}}
                        <tr class="bg-amber-50 dark:bg-amber-900/20">
                            <td class="py-3 px-4 font-bold uppercase {{ $data['labaRugiBersih'] >= 0 ? 'text-green-800 dark:text-green-300' : 'text-red-800 dark:text-red-300' }} border-t-2 border-b-2 border-gray-500 dark:border-gray-400">
                                {{ $data['labaRugiBersih'] >= 0 ? 'Laba Bersih' : 'Rugi Bersih' }}
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-bold whitespace-nowrap {{ $data['labaRugiBersih'] >= 0 ? 'text-green-800 dark:text-green-300' : 'text-red-800 dark:text-red-300' }} border-t-2 border-b-2 border-gray-500 dark:border-gray-400">
                                Rp {{ number_format(abs($data['labaRugiBersih']), 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <x-report-signatures :tenant="$tenant ?? null" />
            </div>
        </div>
