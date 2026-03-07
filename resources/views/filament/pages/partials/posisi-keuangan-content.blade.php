        {{-- Laporan --}}
        <div class="report-container fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden">
            <div class="p-8">
                <x-kop-surat :tenant="$tenant ?? null" />

                <div class="text-center mb-6">
                    <h2 class="text-xl font-bold uppercase tracking-wider dark:text-white">Laporan Posisi Keuangan</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Per {{ \Carbon\Carbon::parse($sampai)->translatedFormat('d F Y') }}
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
                        {{-- ASET --}}
                        <tr class="bg-gray-50 dark:bg-gray-800/50">
                            <td colspan="2" class="py-2 px-4 font-semibold text-gray-700 dark:text-gray-300">ASET</td>
                        </tr>
                        
                        {{-- Aset Lancar --}}
                        <tr>
                            <td colspan="2" class="py-2 pl-8 pr-4 font-semibold text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-800/20">Aset Lancar</td>
                        </tr>
                        @forelse($data['asetLancar'] as $akun)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 border-b border-gray-100 dark:border-gray-800">
                            <td class="py-1.5 pl-12 pr-4 text-gray-600 dark:text-gray-400">{{ $akun->kode_akun }} &mdash; {{ $akun->nama_akun }}</td>
                            <td class="py-1.5 px-4 text-right font-mono text-gray-700 dark:text-gray-300">Rp {{ number_format($akun->saldo, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="py-1.5 pl-12 italic text-gray-500">Tidak ada data</td></tr>
                        @endforelse
                        <tr class="border-t border-gray-300 dark:border-gray-600">
                            <td class="py-2 pl-12 pr-4 font-semibold text-gray-800 dark:text-gray-200">Total Aset Lancar</td>
                            <td class="py-2 px-4 text-right font-mono font-semibold text-gray-800 dark:text-gray-200 whitespace-nowrap">Rp {{ number_format($data['totalAsetLancar'], 0, ',', '.') }}</td>
                        </tr>

                        {{-- Aset Tidak Lancar --}}
                        <tr>
                            <td colspan="2" class="py-2 pl-8 pr-4 font-semibold text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-800/20">Aset Tidak Lancar</td>
                        </tr>
                        @forelse($data['asetTidakLancar'] as $akun)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 border-b border-gray-100 dark:border-gray-800">
                            <td class="py-1.5 pl-12 pr-4 text-gray-600 dark:text-gray-400">{{ $akun->kode_akun }} &mdash; {{ $akun->nama_akun }}</td>
                            <td class="py-1.5 px-4 text-right font-mono text-gray-700 dark:text-gray-300">Rp {{ number_format($akun->saldo, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="py-1.5 pl-12 italic text-gray-500">Tidak ada data</td></tr>
                        @endforelse
                        <tr class="border-t border-gray-300 dark:border-gray-600">
                            <td class="py-2 pl-12 pr-4 font-semibold text-gray-800 dark:text-gray-200">Total Aset Tidak Lancar</td>
                            <td class="py-2 px-4 text-right font-mono font-semibold text-gray-800 dark:text-gray-200 whitespace-nowrap">Rp {{ number_format($data['totalAsetTidakLancar'], 0, ',', '.') }}</td>
                        </tr>

                        <tr class="bg-amber-50 dark:bg-amber-900/20 border-t-2 border-b-2 border-gray-500 dark:border-gray-400">
                            <td class="py-3 px-4 font-bold uppercase text-gray-900 dark:text-white">TOTAL ASET</td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-gray-900 dark:text-white">Rp {{ number_format($data['totalAset'], 0, ',', '.') }}</td>
                        </tr>

                        {{-- Spacer --}}
                        <tr><td colspan="2" class="py-4 border-none"></td></tr>

                        {{-- LIABILITAS DAN EKUITAS --}}
                        <tr class="bg-gray-50 dark:bg-gray-800/50">
                            <td colspan="2" class="py-2 px-4 font-semibold text-gray-700 dark:text-gray-300">LIABILITAS DAN EKUITAS</td>
                        </tr>

                        {{-- Liabilitas Jangka Pendek --}}
                        <tr>
                            <td colspan="2" class="py-2 pl-8 pr-4 font-semibold text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-800/20">Liabilitas Jangka Pendek</td>
                        </tr>
                        @forelse($data['liabilitasJangkaPendek'] as $akun)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 border-b border-gray-100 dark:border-gray-800">
                            <td class="py-1.5 pl-12 pr-4 text-gray-600 dark:text-gray-400">{{ $akun->kode_akun }} &mdash; {{ $akun->nama_akun }}</td>
                            <td class="py-1.5 px-4 text-right font-mono text-gray-700 dark:text-gray-300">Rp {{ number_format($akun->saldo, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="py-1.5 pl-12 italic text-gray-500">Tidak ada data</td></tr>
                        @endforelse
                        <tr class="border-t border-gray-300 dark:border-gray-600">
                            <td class="py-2 pl-12 pr-4 font-semibold text-gray-800 dark:text-gray-200">Total Liabilitas Jangka Pendek</td>
                            <td class="py-2 px-4 text-right font-mono font-semibold text-gray-800 dark:text-gray-200 whitespace-nowrap">Rp {{ number_format($data['totalLiabilitasJP'], 0, ',', '.') }}</td>
                        </tr>

                        {{-- Liabilitas Jangka Panjang --}}
                        @if($data['liabilitasJangkaPanjang']->count() > 0)
                        <tr>
                            <td colspan="2" class="py-2 pl-8 pr-4 font-semibold text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-800/20">Liabilitas Jangka Panjang</td>
                        </tr>
                        @foreach($data['liabilitasJangkaPanjang'] as $akun)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 border-b border-gray-100 dark:border-gray-800">
                            <td class="py-1.5 pl-12 pr-4 text-gray-600 dark:text-gray-400">{{ $akun->kode_akun }} &mdash; {{ $akun->nama_akun }}</td>
                            <td class="py-1.5 px-4 text-right font-mono text-gray-700 dark:text-gray-300">Rp {{ number_format($akun->saldo, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                        <tr class="border-t border-gray-300 dark:border-gray-600">
                            <td class="py-2 pl-12 pr-4 font-semibold text-gray-800 dark:text-gray-200">Total Liabilitas Jangka Panjang</td>
                            <td class="py-2 px-4 text-right font-mono font-semibold text-gray-800 dark:text-gray-200 whitespace-nowrap">Rp {{ number_format($data['totalLiabilitasJPj'], 0, ',', '.') }}</td>
                        </tr>
                        @endif

                        <tr class="border-t border-gray-300 dark:border-gray-600">
                            <td class="py-2 pl-8 pr-4 font-semibold text-gray-800 dark:text-gray-200">Total Liabilitas</td>
                            <td class="py-2 px-4 text-right font-mono font-semibold text-gray-800 dark:text-gray-200 whitespace-nowrap">Rp {{ number_format($data['totalLiabilitas'], 0, ',', '.') }}</td>
                        </tr>

                        {{-- Ekuitas --}}
                        <tr>
                            <td colspan="2" class="py-2 pl-8 pr-4 font-semibold text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-800/20">Ekuitas</td>
                        </tr>
                        @forelse($data['ekuitas'] as $akun)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 border-b border-gray-100 dark:border-gray-800">
                            <td class="py-1.5 pl-12 pr-4 text-gray-600 dark:text-gray-400">{{ $akun->kode_akun }} &mdash; {{ $akun->nama_akun }}</td>
                            <td class="py-1.5 px-4 text-right font-mono text-gray-700 dark:text-gray-300">Rp {{ number_format($akun->saldo, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="py-1.5 pl-12 italic text-gray-500">Tidak ada data</td></tr>
                        @endforelse
                        <tr class="border-t border-gray-300 dark:border-gray-600">
                            <td class="py-2 pl-12 pr-4 font-semibold text-gray-800 dark:text-gray-200">Total Ekuitas</td>
                            <td class="py-2 px-4 text-right font-mono font-semibold text-gray-800 dark:text-gray-200 whitespace-nowrap">Rp {{ number_format($data['totalEkuitas'], 0, ',', '.') }}</td>
                        </tr>

                        <tr class="bg-amber-50 dark:bg-amber-900/20 border-t-2 border-b-2 border-gray-500 dark:border-gray-400">
                            <td class="py-3 px-4 font-bold uppercase text-gray-900 dark:text-white">TOTAL LIABILITAS DAN EKUITAS</td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-gray-900 dark:text-white">Rp {{ number_format($data['totalLiabilitasEkuitas'], 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>

                {{-- Balance Check --}}
                <div class="no-print balance-check mt-6 p-4 rounded-lg flex items-center gap-3 border {{ abs($data['totalAset'] - $data['totalLiabilitasEkuitas']) > 0.01 ? 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800' : 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800' }}">
                    @if(abs($data['totalAset'] - $data['totalLiabilitasEkuitas']) > 0.01)
                        <span class="text-red-500 text-xl font-bold">⚠️</span>
                        <div>
                            <p class="font-bold text-red-700 dark:text-red-300">Neraca Tidak Balance</p>
                            <p class="text-sm font-mono mt-1 text-red-600 dark:text-red-400">Selisih: Rp {{ number_format(abs($data['totalAset'] - $data['totalLiabilitasEkuitas']), 0, ',', '.') }}</p>
                        </div>
                    @else
                        <span class="text-green-500 text-xl font-bold">✅</span>
                        <p class="font-bold text-green-700 dark:text-green-300">Neraca Balance — Total Aset = Total Liabilitas + Ekuitas</p>
                    @endif
                </div>
                <x-report-signatures :tenant="$tenant ?? null" />
            </div>
        </div>
