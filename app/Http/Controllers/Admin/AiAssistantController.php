<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiConversation;
use App\Services\AiAssistantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AiAssistantController extends Controller
{
    public function __construct(
        private AiAssistantService $aiService,
    ) {}

    /**
     * Send a chat message and get AI response.
     */
    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'message'    => 'required|string|max:2000',
            'session_id' => 'required|string|max:36',
        ]);

        if (!config('ai-assistant.enabled')) {
            return response()->json([
                'error' => 'AI Assistant is currently disabled.',
            ], 503);
        }

        if (!config('ai-assistant.gemini.api_key')) {
            return response()->json([
                'error' => 'AI Assistant is not configured. Please set GEMINI_API_KEY.',
            ], 503);
        }

        try {
            $result = $this->aiService->chat(
                message:   $request->input('message'),
                sessionId: $request->input('session_id'),
            );

            return response()->json([
                'success' => true,
                'content' => $result['content'],
                'metadata' => $result['metadata'] ?? null,
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'error'   => 'Maaf, terjadi kesalahan saat memproses pesan Anda. Silakan coba lagi.',
            ], 500);
        }
    }

    /**
     * Get conversation history for a session.
     */
    public function history(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|string|max:36',
        ]);

        $messages = AiConversation::forSession($request->input('session_id'))
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn ($msg) => [
                'role'      => $msg->role,
                'content'   => $msg->content,
                'metadata'  => $msg->metadata,
                'timestamp' => $msg->created_at->toIso8601String(),
            ]);

        return response()->json([
            'success'  => true,
            'messages' => $messages,
        ]);
    }

    /**
     * Clear conversation history for a session.
     */
    public function clearHistory(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|string|max:36',
        ]);

        AiConversation::forSession($request->input('session_id'))
            ->where('user_id', auth()->id())
            ->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Start a new chat session.
     */
    public function newSession(): JsonResponse
    {
        return response()->json([
            'success'    => true,
            'session_id' => (string) Str::uuid(),
        ]);
    }
}
