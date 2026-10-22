<?php

namespace App\Models;

use App\Scopes\TenantScope;
use App\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class JurnalKas extends Model
{
    use TenantTrait, \Spatie\Activitylog\Traits\LogsActivity;

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $table = 'jurnal_kas';

    protected $fillable = [
        'tenant_id',
        'tipe',
        'tanggal',
        'coa_kas_id',
        'coa_lawan_id',
        'nominal',
        'deskripsi',
        'bukti_transaksi',
        'status',
        'contactable_type',
        'contactable_id',
    ];

    /**
     * Get the parent contactable model (Klien or Vendor).
     */
    public function contactable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope());

        static::created(function (JurnalKas $jurnalKas) {
            $jurnalKas->createOrUpdateJurnalAkuntansi();
        });

        static::updated(function (JurnalKas $jurnalKas) {
            // Re-create or update JurnalAkuntansi safely without deleting the JurnalHeader ID
            $jurnalKas->createOrUpdateJurnalAkuntansi();
        });

        static::deleted(function (JurnalKas $jurnalKas) {
            $jurnalKas->deleteJurnalAkuntansi();
        });
    }

    public function createOrUpdateJurnalAkuntansi()
    {
        DB::transaction(function () {
            $jurnalHeader = JurnalHeader::where('referensi_type', self::class)
                ->where('referensi_id', $this->id)
                ->first();

            if (!$jurnalHeader) {
                $jurnalHeader = new JurnalHeader();
                $jurnalHeader->tenant_id = $this->tenant_id;
                $jurnalHeader->referensi_type = self::class;
                $jurnalHeader->referensi_id = $this->id;
            }

            $jurnalHeader->tanggal = $this->tanggal;
            $jurnalHeader->deskripsi = $this->deskripsi ?: "Jurnal Kas: {$this->tipe}";
            $jurnalHeader->status = $this->status ?? 'unposted';
            $jurnalHeader->bukti_transaksi = $this->bukti_transaksi;
            $jurnalHeader->save();

            // Details — hapus dan buat ulang dalam transaction
            $jurnalHeader->jurnalDetails()->get()->each->delete();

            if ($this->tipe === 'Penerimaan') {
                // Kas bertambah (Debit), Lawan bertambah (Kredit - usually Pendapatan)
                $jurnalHeader->jurnalDetails()->create([
                    'coa_id' => $this->coa_kas_id,
                    'debit' => $this->nominal,
                    'kredit' => 0,
                ]);
                $jurnalHeader->jurnalDetails()->create([
                    'coa_id' => $this->coa_lawan_id,
                    'debit' => 0,
                    'kredit' => $this->nominal,
                    'contactable_type' => $this->contactable_type,
                    'contactable_id' => $this->contactable_id,
                ]);
            } else {
                // Pengeluaran
                // Lawan bertambah (Debit - usually Biaya), Kas berkurang (Kredit)
                $jurnalHeader->jurnalDetails()->create([
                    'coa_id' => $this->coa_lawan_id,
                    'debit' => $this->nominal,
                    'kredit' => 0,
                    'contactable_type' => $this->contactable_type,
                    'contactable_id' => $this->contactable_id,
                ]);
                $jurnalHeader->jurnalDetails()->create([
                    'coa_id' => $this->coa_kas_id,
                    'debit' => 0,
                    'kredit' => $this->nominal,
                ]);
            }
        });
    }

    public function deleteJurnalAkuntansi()
    {
        $headers = JurnalHeader::where('referensi_type', self::class)
            ->where('referensi_id', $this->id)
            ->get();

        foreach ($headers as $header) {
            // Hapus file bukti transaksi dari storage
            if ($header->bukti_transaksi) {
                Storage::disk('public')->delete($header->bukti_transaksi);
            }
            $header->delete();
        }
    }

    public function coaKas(): BelongsTo
    {
        return $this->belongsTo(Coa::class, 'coa_kas_id');
    }

    public function coaLawan(): BelongsTo
    {
        return $this->belongsTo(Coa::class, 'coa_lawan_id');
    }

    public function jurnalHeader(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(JurnalHeader::class, 'referensi');
    }

    /**
     * Normalisasi deskripsi untuk perbandingan kesamaan:
     * huruf kecil, hilangkan tanda baca, gabungkan spasi berlebih.
     */
    public static function normalizeDescription(?string $desc): string
    {
        if ($desc === null) {
            return '';
        }
        $str = mb_strtolower(trim($desc), 'UTF-8');
        $str = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $str);
        $str = preg_replace('/\s+/', ' ', $str);
        return trim($str);
    }

    /**
     * Memeriksa apakah dua deskripsi memiliki kemiripan (similarity).
     * Memperhitungkan kesamaan eksak ternormalisasi, kemiripan karakter,
     * substring overlap, dan Jaccard word token similarity.
     */
    public static function isDescriptionSimilar(?string $desc1, ?string $desc2, float $threshold = 0.70): bool
    {
        $norm1 = static::normalizeDescription($desc1);
        $norm2 = static::normalizeDescription($desc2);

        // Keduanya kosong -> dianggap duplikat (misal input tanpa deskripsi berkali-kali)
        if ($norm1 === '' && $norm2 === '') {
            return true;
        }

        // Salah satu kosong, lainnya ada isi -> bukan duplikat
        if ($norm1 === '' || $norm2 === '') {
            return false;
        }

        // Sama persis setelah dinormalisasi
        if ($norm1 === $norm2) {
            return true;
        }

        $len1 = strlen($norm1);
        $len2 = strlen($norm2);
        $minLen = min($len1, $len2);
        $maxLen = max($len1, $len2);

        // Substring overlap signifikan
        if ($minLen >= 4 && $maxLen > 0 && ($minLen / $maxLen) >= 0.65) {
            if (str_contains($norm1, $norm2) || str_contains($norm2, $norm1)) {
                return true;
            }
        }

        // Similar text percentage
        similar_text($norm1, $norm2, $percent);
        if ($percent >= ($threshold * 100)) {
            return true;
        }

        // Word token similarity (Jaccard)
        $words1 = array_filter(explode(' ', $norm1), fn($w) => strlen($w) > 1);
        $words2 = array_filter(explode(' ', $norm2), fn($w) => strlen($w) > 1);
        if (!empty($words1) && !empty($words2)) {
            $intersection = array_intersect($words1, $words2);
            $union = array_unique(array_merge($words1, $words2));
            if (!empty($union)) {
                $jaccard = count($intersection) / count($union);
                if ($jaccard >= 0.65) {
                    return true;
                }
            }
        }

        // Levenshtein distance
        if ($len1 <= 255 && $len2 <= 255 && $maxLen > 0) {
            $lev = levenshtein($norm1, $norm2);
            if ($lev >= 0 && (1 - ($lev / $maxLen)) >= $threshold) {
                return true;
            }
        }

        return false;
    }

    /**
     * Mengelompokkan transaksi jurnal kas yang memenuhi kriteria duplikat.
     * Kriteria default ('kemiripan'):
     * - Tanggal sama
     * - Nominal sama
     * - Ada kemiripan deskripsi
     *
     * Kriteria alternatif:
     * - 'persis': Tanggal, nominal, dan deskripsi ternormalisasi sama persis
     * - 'tanggal_nominal': Tanggal & nominal sama (mengabaikan deskripsi)
     */
    public static function getDuplicateGroups(array $filters = []): \Illuminate\Support\Collection
    {
        $parameter = $filters['parameter'] ?? 'kemiripan';

        // Hanya kandidat yang memiliki tanggal dan nominal yang muncul > 1 kali
        $candidateGroups = static::select('tanggal', 'nominal')
            ->groupBy('tanggal', 'nominal')
            ->havingRaw('COUNT(*) > 1');

        if (!empty($filters['dari'])) {
            $candidateGroups->whereDate('tanggal', '>=', $filters['dari']);
        }
        if (!empty($filters['sampai'])) {
            $candidateGroups->whereDate('tanggal', '<=', $filters['sampai']);
        }
        if (!empty($filters['search'])) {
            $candidateGroups->where('deskripsi', 'like', '%' . $filters['search'] . '%');
        }
        if (!empty($filters['tipe'])) {
            $candidateGroups->where('tipe', $filters['tipe']);
        }

        $candidateQuery = static::with(['jurnalHeader', 'coaKas', 'coaLawan', 'contactable'])
            ->joinSub($candidateGroups, 'cand_grp', function ($join) {
                $join->on('jurnal_kas.tanggal', '=', 'cand_grp.tanggal')
                     ->on('jurnal_kas.nominal', '=', 'cand_grp.nominal');
            });

        if (!empty($filters['dari'])) {
            $candidateQuery->whereDate('jurnal_kas.tanggal', '>=', $filters['dari']);
        }
        if (!empty($filters['sampai'])) {
            $candidateQuery->whereDate('jurnal_kas.tanggal', '<=', $filters['sampai']);
        }
        if (!empty($filters['search'])) {
            $candidateQuery->where('jurnal_kas.deskripsi', 'like', '%' . $filters['search'] . '%');
        }
        if (!empty($filters['tipe'])) {
            $candidateQuery->where('jurnal_kas.tipe', $filters['tipe']);
        }

        $candidates = $candidateQuery->select('jurnal_kas.*')
            ->orderBy('jurnal_kas.tanggal', 'desc')
            ->orderBy('jurnal_kas.nominal', 'desc')
            ->orderBy('jurnal_kas.deskripsi', 'asc')
            ->orderBy('jurnal_kas.id', 'asc')
            ->get();

        if ($candidates->isEmpty()) {
            return collect();
        }

        // Kelompokkan per Tanggal & Nominal
        $byDateAndNominal = $candidates->groupBy(function ($item) {
            $tgl = $item->tanggal ? $item->tanggal->format('Y-m-d') : '';
            $nom = (string) (float) $item->nominal;
            return "{$tgl}|{$nom}";
        });

        $resultGroups = collect();

        foreach ($byDateAndNominal as $bucketKey => $itemsInBucket) {
            if ($parameter === 'tanggal_nominal') {
                // Mode longgar: tanggal sama & nominal sama tanpa cek deskripsi
                if ($itemsInBucket->count() > 1) {
                    $resultGroups->put($bucketKey, $itemsInBucket);
                }
            } elseif (in_array($parameter, ['persis', 'tanggal_nominal_deskripsi'], true)) {
                // Mode persis: tanggal, nominal, dan deskripsi ternormalisasi sama persis
                $byNormalizedDesc = $itemsInBucket->groupBy(function ($item) {
                    return static::normalizeDescription($item->deskripsi);
                });

                foreach ($byNormalizedDesc as $normKey => $subItems) {
                    if ($subItems->count() > 1) {
                        $groupKey = "{$bucketKey}|{$normKey}";
                        $resultGroups->put($groupKey, $subItems);
                    }
                }
            } else {
                // Default mode: 'kemiripan' (tanggal sama, nominal sama, dan ADA KEMIRIPAN DESKRIPSI)
                $clusters = [];
                foreach ($itemsInBucket as $item) {
                    $matchedIndex = null;
                    foreach ($clusters as $idx => $clusterItems) {
                        if (static::isDescriptionSimilar($item->deskripsi, $clusterItems[0]->deskripsi)) {
                            $matchedIndex = $idx;
                            break;
                        }
                    }

                    if ($matchedIndex !== null) {
                        $clusters[$matchedIndex][] = $item;
                    } else {
                        $clusters[] = [$item];
                    }
                }

                foreach ($clusters as $idx => $clusterItems) {
                    // Hanya sub-kelompok yang berisi >= 2 transaksi yang dinyatakan DUPLIKAT
                    if (count($clusterItems) > 1) {
                        $normRep = static::normalizeDescription($clusterItems[0]->deskripsi);
                        $groupKey = "{$bucketKey}|{$normRep}|{$idx}";
                        $resultGroups->put($groupKey, collect($clusterItems));
                    }
                }
            }
        }

        return $resultGroups;
    }

    /**
     * Scope query to find all Jurnal Kas records that belong to duplicate groups.
     */
    public static function getDuplicatesQuery(array $filters = [])
    {
        $stats = static::getDuplicateStats($filters);
        $ids = $stats['duplicate_ids'];

        if (empty($ids)) {
            return static::whereRaw('1 = 0');
        }

        return static::with(['jurnalHeader', 'coaKas', 'coaLawan', 'contactable'])
            ->whereIn('id', $ids)
            ->orderBy('tanggal', 'desc')
            ->orderBy('nominal', 'desc')
            ->orderBy('deskripsi', 'asc')
            ->orderBy('id', 'asc');
    }

    /**
     * Get aggregate statistics about duplicate groups.
     */
    public static function getDuplicateStats(array $filters = []): array
    {
        $groups = static::getDuplicateGroups($filters);

        $groupCount = $groups->count();
        $totalItemsCount = (int) $groups->sum(fn($g) => $g->count());
        $totalNominal = (float) $groups->sum(fn($g) => (float) $g->sum('nominal'));
        $potensiRedundansiNominal = (float) $groups->sum(function ($g) {
            $first = $g->first();
            return $first ? ($g->count() - 1) * (float) $first->nominal : 0;
        });

        $duplicateIds = $groups->flatMap(fn($g) => $g->pluck('id'))->unique()->values()->toArray();

        return [
            'group_count' => $groupCount,
            'total_items_count' => $totalItemsCount,
            'total_nominal' => $totalNominal,
            'potensi_redundansi_nominal' => $potensiRedundansiNominal,
            'duplicate_ids' => $duplicateIds,
        ];
    }
}
