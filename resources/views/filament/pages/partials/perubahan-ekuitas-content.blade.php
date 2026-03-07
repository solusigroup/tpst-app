        {{-- Laporan --}}
        <div class="report-container fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden">
            <div class="p-8 overflow-x-auto">
                <x-kop-surat :tenant="$tenant ?? null" />

                <div class="text-center mb-6">
                    <h2 class="text-xl font-bold uppercase tracking-wider dark:text-white">Laporan Perubahan Ekuitas</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Periode {{ \Carbon\Carbon::parse($dari)->translatedFormat('d F Y') }} s.d. {{ \Carbon\Carbon::parse($sampai)->translatedFormat('d F Y') }}
                    </p>
                </div>

                <table class="report-table text-gray-900 dark:text-gray-100 w-full border-collapse text-sm min-w-full">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-800 border-b-2 border-gray-400">
                            <th class="py-2 px-4 font-bold text-gray-800 dark:text-white uppercase tracking-wide w-2-5">Keterangan</th>
                            <th class="py-2 px-4 font-bold text-gray-800 dark:text-white uppercase tracking-wide text-right w-1-5">Saldo Awal</th>
                            <th class="py-2 px-4 font-bold text-gray-800 dark:text-white uppercase tracking-wide text-right w-1-5">Penambahan</th>
                            <th class="py-2 px-4 font-bold text-gray-800 dark:text-white uppercase tracking-wide text-right w-1-5">Pengurangan</th>
                            <th class="py-2 px-4 font-bold text-gray-800 dark:text-white uppercase tracking-wide text-right w-1-5">Saldo Akhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['rows'] as $row)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 border-b border-gray-100 dark:border-gray-800">
                            <td class="py-2 px-4 text-gray-700 dark:text-gray-300">{{ $row['kode_akun'] }} &mdash; {{ $row['nama_akun'] }}</td>
                            <td class="py-2 px-4 text-right font-mono text-gray-700 dark:text-gray-300">Rp {{ number_format($row['saldo_awal'], 0, ',', '.') }}</td>
                            <td class="py-2 px-4 text-right font-mono text-green-700 dark:text-green-400 font-normal">Rp {{ number_format($row['penambahan'], 0, ',', '.') }}</td>
                            <td class="py-2 px-4 text-right font-mono text-red-700 dark:text-red-400 font-normal">(Rp {{ number_format($row['pengurangan'], 0, ',', '.') }})</td>
                            <td class="py-2 px-4 text-right font-mono font-bold text-gray-900 dark:text-white">Rp {{ number_format($row['saldo_akhir'], 0, ',', '.') }}</td>
                        </tr>
                        @endforeach

                        {{-- Spacer --}}
                        <tr><td colspan="5" class="py-2 border-none"></td></tr>

                        {{-- Laba Rugi Bersih --}}
                        <tr class="bg-gray-50/50 dark:bg-gray-800/20 border-b border-gray-200 dark:border-gray-700">
                            <td class="py-2 px-4 font-bold {{ $data['labaRugi'] >= 0 ? 'text-green-800 dark:text-green-300' : 'text-red-800 dark:text-red-300' }}">
                                Laba (Rugi) Bersih Periode Berjalan
                            </td>
                            <td class="py-2 px-4 text-right font-mono text-gray-400 dark:text-gray-600">&mdash;</td>
                            <td class="py-2 px-4 text-right font-mono font-normal {{ $data['labaRugi'] >= 0 ? 'text-green-700 dark:text-green-400' : 'text-gray-400 dark:text-gray-600' }}">
                                {{ $data['labaRugi'] >= 0 ? 'Rp ' . number_format($data['labaRugi'], 0, ',', '.') : '—' }}
                            </td>
                            <td class="py-2 px-4 text-right font-mono font-normal {{ $data['labaRugi'] < 0 ? 'text-red-700 dark:text-red-400' : 'text-gray-400 dark:text-gray-600' }}">
                                {{ $data['labaRugi'] < 0 ? '(Rp ' . number_format(abs($data['labaRugi']), 0, ',', '.') . ')' : '—' }}
                            </td>
                            <td class="py-2 px-4 text-right font-mono font-bold {{ $data['labaRugi'] >= 0 ? 'text-green-800 dark:text-green-300' : 'text-red-800 dark:text-red-300' }}">
                                Rp {{ number_format($data['labaRugi'], 0, ',', '.') }}
                            </td>
                        </tr>

                        {{-- Spacer --}}
                        <tr><td colspan="5" class="py-2 border-none"></td></tr>

                        {{-- Total --}}
                        <tr class="bg-amber-50 dark:bg-amber-900/20 border-t-2 border-b-2 border-gray-500 dark:border-gray-400">
                            <td class="py-3 px-4 font-bold uppercase text-gray-900 dark:text-white">TOTAL EKUITAS</td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-gray-900 dark:text-white">Rp {{ number_format($data['totalSaldoAwal'], 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-green-700 dark:text-green-400">Rp {{ number_format($data['totalPenambahan'], 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-red-700 dark:text-red-400">(Rp {{ number_format($data['totalPengurangan'], 0, ',', '.') }})</td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-gray-900 dark:text-white">Rp {{ number_format($data['totalSaldoAkhir'], 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
                <x-report-signatures :tenant="$tenant ?? null" />
            </div>
        </div>
