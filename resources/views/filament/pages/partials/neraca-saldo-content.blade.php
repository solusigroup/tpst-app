        {{-- Laporan --}}
        <div class="report-container fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden">
            <div class="p-8 overflow-x-auto">
                <x-kop-surat :tenant="$tenant ?? null" />

                <div class="text-center mb-6">
                    <h2 class="text-xl font-bold uppercase tracking-wider dark:text-white">Neraca Saldo</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Periode {{ \Carbon\Carbon::parse($dari)->translatedFormat('d F Y') }} s.d. {{ \Carbon\Carbon::parse($sampai)->translatedFormat('d F Y') }}
                    </p>
                </div>

                <table class="report-table text-gray-900 dark:text-gray-100 w-full border-collapse text-sm min-w-full">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-800 border-b-2 border-gray-400">
                            <th class="py-2 px-4 font-bold text-gray-800 dark:text-white uppercase tracking-wide w-1-6">Kode Akun</th>
                            <th class="py-2 px-4 font-bold text-gray-800 dark:text-white uppercase tracking-wide w-1-2">Nama Akun</th>
                            <th class="py-2 px-4 font-bold text-gray-800 dark:text-white uppercase tracking-wide text-right w-1-6">Debit</th>
                            <th class="py-2 px-4 font-bold text-gray-800 dark:text-white uppercase tracking-wide text-right w-1-6">Kredit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['rows'] as $row)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 border-b border-gray-100 dark:border-gray-800">
                            <td class="py-2 px-4 font-mono text-gray-600 dark:text-gray-400">{{ $row->kode_akun }}</td>
                            <td class="py-2 px-4 text-gray-700 dark:text-gray-300">{{ $row->nama_akun }}</td>
                            <td class="py-2 px-4 text-right font-mono {{ $row->total_debit > 0 ? '' : 'text-gray-400 dark:text-gray-600' }}">
                                {{ $row->total_debit > 0 ? "Rp " . number_format($row->total_debit, 0, ',', '.') : '-' }}
                            </td>
                            <td class="py-2 px-4 text-right font-mono {{ $row->total_kredit > 0 ? '' : 'text-gray-400 dark:text-gray-600' }}">
                                {{ $row->total_kredit > 0 ? "Rp " . number_format($row->total_kredit, 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="py-2 px-4 italic text-gray-500 text-center border-b border-gray-100 dark:border-gray-800">Tidak ada data untuk periode ini</td></tr>
                        @endforelse

                        {{-- Spacer --}}
                        <tr><td colspan="4" class="py-2 border-none"></td></tr>

                        {{-- Total --}}
                        <tr class="bg-amber-50 dark:bg-amber-900/20 border-t-2 border-b-2 border-gray-500 dark:border-gray-400">
                            <td colspan="2" class="py-3 px-4 text-right font-bold uppercase text-gray-900 dark:text-white">TOTAL</td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-gray-900 dark:text-white {{ $data['totalDebit'] != $data['totalKredit'] ? 'text-red-600 dark:text-red-400' : '' }}">Rp {{ number_format($data['totalDebit'], 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-gray-900 dark:text-white {{ $data['totalDebit'] != $data['totalKredit'] ? 'text-red-600 dark:text-red-400' : '' }}">Rp {{ number_format($data['totalKredit'], 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
                <x-report-signatures :tenant="$tenant ?? null" />
            </div>
        </div>
