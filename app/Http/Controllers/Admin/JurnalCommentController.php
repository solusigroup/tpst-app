<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JurnalComment;
use App\Models\JurnalHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class JurnalCommentController extends Controller
{
    /**
     * Get all comments for a journal header.
     */
    public function index(JurnalHeader $jurnal)
    {
        $comments = $jurnal->comments()->with('user')->get()->map(function ($c) {
            $user = $c->user;
            $roleLabel = 'Staf Entry';
            $roleBadge = 'secondary';
            if ($user) {
                if ($user->is_super_admin || $user->role === 'super_admin') {
                    $roleLabel = 'Super Admin';
                    $roleBadge = 'danger';
                } elseif (in_array(strtolower($user->role ?? ''), ['manajemen', 'management', 'manager', 'direktur'])) {
                    $roleLabel = 'Manajemen';
                    $roleBadge = 'primary';
                } elseif (in_array(strtolower($user->role ?? ''), ['admin', 'administrator', 'akuntan'])) {
                    $roleLabel = 'Admin';
                    $roleBadge = 'info';
                }
            }

            return [
                'id' => $c->id,
                'user_id' => $c->user_id,
                'user_name' => $user->name ?? 'Pengguna',
                'user_role' => $roleLabel,
                'user_badge' => $roleBadge,
                'comment' => $c->comment,
                'created_at_human' => $c->created_at->diffForHumans(),
                'created_at_formatted' => $c->created_at->format('d/m/Y H:i'),
                'can_delete' => auth()->id() === $c->user_id || auth()->user()->is_super_admin || auth()->user()->role === 'super_admin',
            ];
        });

        return response()->json([
            'status' => 'success',
            'jurnal_id' => $jurnal->id,
            'nomor_referensi' => $jurnal->nomor_referensi,
            'deskripsi' => $jurnal->deskripsi,
            'nominal' => $jurnal->nominal,
            'comments' => $comments,
            'count' => $comments->count(),
        ]);
    }

    /**
     * Store a new comment.
     */
    public function store(Request $request, JurnalHeader $jurnal)
    {
        $validated = $request->validate([
            'comment' => 'required|string|min:1|max:2000',
        ]);

        $comment = $jurnal->comments()->create([
            'tenant_id' => auth()->user()->getEffectiveTenantId(),
            'user_id' => auth()->id(),
            'comment' => trim($validated['comment']),
        ]);

        $comment->load('user');
        $user = $comment->user;
        $roleLabel = 'Staf Entry';
        $roleBadge = 'secondary';
        if ($user) {
            if ($user->is_super_admin || $user->role === 'super_admin') {
                $roleLabel = 'Super Admin';
                $roleBadge = 'danger';
            } elseif (in_array(strtolower($user->role ?? ''), ['manajemen', 'management', 'manager', 'direktur'])) {
                $roleLabel = 'Manajemen';
                $roleBadge = 'primary';
            } elseif (in_array(strtolower($user->role ?? ''), ['admin', 'administrator', 'akuntan'])) {
                $roleLabel = 'Admin';
                $roleBadge = 'info';
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Komentar berhasil dikirim.',
            'comment' => [
                'id' => $comment->id,
                'user_id' => $comment->user_id,
                'user_name' => $user->name ?? 'Pengguna',
                'user_role' => $roleLabel,
                'user_badge' => $roleBadge,
                'comment' => $comment->comment,
                'created_at_human' => $comment->created_at->diffForHumans(),
                'created_at_formatted' => $comment->created_at->format('d/m/Y H:i'),
                'can_delete' => true,
            ],
            'total_count' => $jurnal->comments()->count(),
        ]);
    }

    /**
     * Delete a comment.
     */
    public function destroy(JurnalComment $comment)
    {
        if (auth()->id() !== $comment->user_id && !auth()->user()->is_super_admin && auth()->user()->role !== 'super_admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki izin untuk menghapus komentar ini.',
            ], 403);
        }

        $jurnalHeaderId = $comment->jurnal_header_id;
        $comment->delete();

        $remainingCount = JurnalComment::where('jurnal_header_id', $jurnalHeaderId)->count();

        return response()->json([
            'status' => 'success',
            'message' => 'Komentar berhasil dihapus.',
            'remaining_count' => $remainingCount,
        ]);
    }
}
