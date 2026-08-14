<?php

namespace App\Services;

use App\Models\AiConversation;
use App\Models\Coa;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiAssistantService
{
    protected AiToolHandler $toolHandler;

    public function __construct(AiToolHandler $toolHandler)
    {
        $this->toolHandler = $toolHandler;
    }

    /**
     * Process a chat message from user.
     * 
     * @param string $message
     * @param string $sessionId
     * @return array ['content' => string, 'metadata' => array]
     */
    public function chat(string $message, string $sessionId): array
    {
        try {
            $apiKey = config('ai-assistant.gemini.api_key');
            if (!$apiKey) {
                return ['content' => 'API Key Gemini belum dikonfigurasi.', 'metadata' => []];
            }

            // Save user message
            $this->saveMessage($sessionId, 'user', $message);

            // Get history
            $contents = $this->getConversationHistory($sessionId);

            // Call Gemini
            $response = $this->callGeminiApi($contents, $apiKey);

            // Handle tool calling loop
            $loopCount = 0;
            $maxLoops = 5;

            while ($this->hasToolCalls($response) && $loopCount < $maxLoops) {
                $response = $this->handleToolCalls($response, $contents, $apiKey);
                $loopCount++;
            }

            $finalText = $this->extractTextResponse($response);
            
            // Save model response
            $this->saveMessage($sessionId, 'assistant', $finalText);

            return [
                'content' => $finalText,
                'metadata' => [
                    'loops' => $loopCount
                ]
            ];

        } catch (\Exception $e) {
            Log::error('AiAssistantService Error: ' . $e->getMessage());
            return [
                'content' => 'Maaf, terjadi kesalahan saat memproses permintaan Anda: ' . $e->getMessage(),
                'metadata' => []
            ];
        }
    }

    private function getConversationHistory(string $sessionId): array
    {
        $maxHistory = config('ai-assistant.max_history', 10);
        
        $messages = AiConversation::where('session_id', $sessionId)
            ->latest()
            ->take($maxHistory)
            ->get()
            ->reverse();

        $contents = [];
        foreach ($messages as $msg) {
            $contents[] = [
                'role' => $msg->role === 'assistant' ? 'model' : 'user',
                'parts' => [['text' => $msg->content]]
            ];
        }

        return $contents;
    }

    private function saveMessage(string $sessionId, string $role, string $content): void
    {
        AiConversation::create([
            'tenant_id'  => auth()->user()->tenant_id,
            'user_id'    => auth()->id(),
            'session_id' => $sessionId,
            'role'       => $role,
            'content'    => $content,
            'created_at' => now(),
        ]);
    }

    private function buildSystemPrompt(): string
    {
        $coas = Coa::get(['kode_akun', 'nama_akun']);
        $coaList = $coas->map(fn($c) => "{$c->kode_akun} - {$c->nama_akun}")->implode("\n");

        return <<<EOT
Kamu adalah Asisten TPST, asisten virtual yang ramah dan helpful untuk aplikasi Sistem Informasi Tata Kelola Tempat Pengelolaan Sampah Terpadu (TPST).
Selalu gunakan Bahasa Indonesia yang profesional namun ramah.

Fitur Aplikasi:
- Dasbor: Ringkasan operasional dan finansial
- Operasional: Ritase/Timbangan, Hasil Pilahan, Pengangkutan Residu
- Keuangan: Jurnal Umum, Jurnal Kas (Penerimaan/Pengeluaran), Transfer Kas, Buku Pembantu Piutang/Utang, Rekonsiliasi Bank, Invoice
- Laporan Keuangan: Laba Rugi, Neraca, Posisi Keuangan, Arus Kas, Buku Besar, Buku Kas
- HRD: Absensi Karyawan, Perhitungan Gaji/Upah
- Master Data: COA (Chart of Account), Klien, Vendor, Pengaturan Perusahaan

Daftar COA Perusahaan Saat Ini:
{$coaList}

Aturan Penting:
1. Jika pengguna meminta untuk mencatat transaksi penerimaan/pengeluaran kas, panggil tool `parse_transaction` untuk memprosesnya. Berikan link aksi kepada pengguna menggunakan format markdown: [Teks Tombol](/admin/jurnal-kas/create?tipe=X&coa_kas=X&coa_lawan=X&nominal=X&deskripsi=X).
2. Jika pengguna menanyakan ringkasan keuangan (saldo kas, pendapatan, beban, utang, piutang), gunakan tool `get_financial_summary`. Format jawaban uang dalam Rupiah (Rp X.XXX.XXX).
3. Jika pengguna ingin diarahkan ke halaman tertentu, gunakan tool `navigate_to_page` untuk mendapatkan URL-nya.
4. Jangan ragu memanggil tool jika merasa membutuhkan konteks tambahan.
5. Gunakan markdown untuk format jawaban (bold, list, tabel jika perlu).
EOT;
    }

    private function buildToolDeclarations(): array
    {
        return [
            [
                'function_declarations' => [
                    [
                        'name' => 'parse_transaction',
                        'description' => 'Menganalisis teks transaksi bahasa natural untuk diekstrak menjadi data jurnal kas.',
                        'parameters' => [
                            'type' => 'object',
                            'properties' => [
                                'text' => [
                                    'type' => 'string',
                                    'description' => 'Teks transaksi dari pengguna'
                                ]
                            ],
                            'required' => ['text']
                        ]
                    ],
                    [
                        'name' => 'lookup_coa',
                        'description' => 'Mencari Chart of Account (COA) berdasarkan nama atau kode.',
                        'parameters' => [
                            'type' => 'object',
                            'properties' => [
                                'query' => [
                                    'type' => 'string',
                                    'description' => 'Kata kunci pencarian akun'
                                ]
                            ],
                            'required' => ['query']
                        ]
                    ],
                    [
                        'name' => 'navigate_to_page',
                        'description' => 'Mencari URL halaman di sistem berdasarkan intensi fitur aplikasi.',
                        'parameters' => [
                            'type' => 'object',
                            'properties' => [
                                'intent' => [
                                    'type' => 'string',
                                    'description' => 'Nama fitur atau halaman yang dituju'
                                ]
                            ],
                            'required' => ['intent']
                        ]
                    ],
                    [
                        'name' => 'get_financial_summary',
                        'description' => 'Mendapatkan ringkasan nilai finansial dari database.',
                        'parameters' => [
                            'type' => 'object',
                            'properties' => [
                                'type' => [
                                    'type' => 'string',
                                    'enum' => ['saldo_kas', 'piutang', 'utang', 'pendapatan', 'beban'],
                                    'description' => 'Jenis metrik keuangan'
                                ]
                            ],
                            'required' => ['type']
                        ]
                    ],
                    [
                        'name' => 'suggest_journal_template',
                        'description' => 'Mencari template jurnal berdasarkan deskripsi transaksi.',
                        'parameters' => [
                            'type' => 'object',
                            'properties' => [
                                'description' => [
                                    'type' => 'string',
                                    'description' => 'Deskripsi kegiatan jurnal'
                                ]
                            ],
                            'required' => ['description']
                        ]
                    ]
                ]
            ]
        ];
    }

    private function callGeminiApi(array $contents, string $apiKey): array
    {
        $model = config('ai-assistant.gemini.model', 'gemini-3.5-flash');

        // Auto-correct any retired/deprecated model to the current stable model.
        // All gemini-1.x and gemini-2.x models have been retired by Google.
        if (preg_match('/^gemini-(1\.|2\.|pro|flash-latest|1\.0)/', $model)) {
            $model = 'gemini-3.5-flash';
        }

        $baseUrl = config('ai-assistant.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta');

        $payload = [
            'system_instruction' => [
                'parts' => [['text' => $this->buildSystemPrompt()]]
            ],
            'contents' => $contents,
            'tools' => $this->buildToolDeclarations(),
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 4096,
            ]
        ];

        // Try primary model with retries, then fallback model
        $modelsToTry = [$model, 'gemini-3.5-flash-lite'];
        $lastException = null;

        foreach (array_unique($modelsToTry) as $tryModel) {
            $url = "{$baseUrl}/models/{$tryModel}:generateContent?key={$apiKey}";

            for ($attempt = 1; $attempt <= 2; $attempt++) {
                try {
                    $response = Http::timeout(60)
                        ->connectTimeout(10)
                        ->post($url, $payload);
                } catch (\Illuminate\Http\Client\ConnectionException $e) {
                    $lastException = new \Exception('Koneksi ke Google Gemini API mengalami timeout. Server tidak dapat terhubung ke generativelanguage.googleapis.com. Harap coba lagi.');
                    continue;
                }

                if ($response->successful()) {
                    return $response->json();
                }

                // Retry on 503 (overloaded) or 429 (rate limit)
                if (in_array($response->status(), [503, 429]) && $attempt < 2) {
                    usleep(1500000); // 1.5 second backoff
                    continue;
                }

                // 404 means model not found — skip to fallback immediately
                if ($response->status() === 404) {
                    break;
                }

                $lastException = new \Exception('Gagal terhubung ke Gemini API (' . $response->status() . '): ' . $response->body());
            }
        }

        throw $lastException ?? new \Exception('Semua model Gemini API sedang tidak tersedia. Silakan coba beberapa saat lagi.');
    }

    private function hasToolCalls(array $response): bool
    {
        if (!isset($response['candidates'][0]['content']['parts'])) {
            return false;
        }

        foreach ($response['candidates'][0]['content']['parts'] as $part) {
            if (isset($part['functionCall'])) {
                return true;
            }
        }
        return false;
    }

    private function extractTextResponse(array $response): string
    {
        if (!isset($response['candidates'][0]['content']['parts'])) {
            return 'Maaf, saya tidak dapat menghasilkan respon.';
        }

        $text = '';
        foreach ($response['candidates'][0]['content']['parts'] as $part) {
            if (isset($part['text'])) {
                $text .= $part['text'];
            }
        }
        
        return empty(trim($text)) ? 'Permintaan telah diproses.' : $text;
    }

    private function handleToolCalls(array $response, array &$contents, string $apiKey): array
    {
        $parts = $response['candidates'][0]['content']['parts'];
        
        // Add the model's function calls to the conversation history
        $contents[] = [
            'role' => 'model',
            'parts' => $parts
        ];

        $functionResponses = [];

        foreach ($parts as $part) {
            if (isset($part['functionCall'])) {
                $call = $part['functionCall'];
                $toolName = $call['name'];
                $args = $call['args'] ?? [];

                $result = $this->toolHandler->dispatch($toolName, $args);

                $functionResponses[] = [
                    'functionResponse' => [
                        'name' => $toolName,
                        'response' => ['result' => $result]
                    ]
                ];
            }
        }

        // Add the function responses to the conversation history (Gemini requires role='user' for functionResponse)
        $contents[] = [
            'role' => 'user',
            'parts' => $functionResponses
        ];

        // Call Gemini again with the updated history
        return $this->callGeminiApi($contents, $apiKey);
    }
}
