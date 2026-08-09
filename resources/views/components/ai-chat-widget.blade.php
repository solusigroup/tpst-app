{{-- Only render if AI assistant is enabled --}}
@if(config('ai-assistant.enabled', true) && auth()->check())

{{-- Load CSS --}}
<link rel="stylesheet" href="{{ asset('css/ai-chat-widget.css') }}">

{{-- Chat FAB Button --}}
<button class="ai-chat-fab" id="ai-chat-fab" title="Asisten TPST">
    <span class="ai-chat-fab__icon">✨</span>
    <span class="ai-chat-fab__close">✕</span>
    <span class="ai-chat-fab__pulse"></span>
</button>

{{-- Chat Panel --}}
<div class="ai-chat-panel" id="ai-chat-panel">
    {{-- Header --}}
    <div class="ai-chat-header">
        <div class="ai-chat-header__info">
            <span class="ai-chat-header__icon">🤖</span>
            <div>
                <h6 class="ai-chat-header__title">Asisten TPST</h6>
                <small class="ai-chat-header__subtitle">Siap membantu Anda</small>
            </div>
        </div>
        <div class="ai-chat-header__actions">
            <button class="ai-chat-header__btn" id="ai-chat-new-session" title="Sesi Baru">
                <svg viewBox="0 0 24 24" width="16" height="16">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" fill="currentColor"/>
                </svg>
            </button>
            <button class="ai-chat-header__btn" id="ai-chat-close" title="Tutup">
                <svg viewBox="0 0 24 24" width="16" height="16">
                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z" fill="currentColor"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Messages Area --}}
    <div class="ai-chat-messages" id="ai-chat-messages">
        {{-- Messages rendered by JS --}}
    </div>

    {{-- Quick Actions --}}
    <div class="ai-chat-actions" id="ai-chat-actions">
        <button class="ai-chat-action-chip" data-message="Saya ingin membuat jurnal kas">💰 Buat Jurnal</button>
        <button class="ai-chat-action-chip" data-message="Berapa saldo kas saat ini?">💵 Cek Saldo</button>
        <button class="ai-chat-action-chip" data-message="Bagaimana cara menggunakan aplikasi ini?">❓ Bantuan</button>
        <button class="ai-chat-action-chip" data-message="Tampilkan ringkasan piutang">📊 Piutang</button>
    </div>

    {{-- Input Area --}}
    <div class="ai-chat-input-area">
        <div class="ai-chat-input-wrapper">
            <textarea class="ai-chat-input" id="ai-chat-input" placeholder="Ketik pesan..." rows="1"></textarea>
            <button class="ai-chat-send" id="ai-chat-send" title="Kirim">
                <svg viewBox="0 0 24 24" width="14" height="14">
                    <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z" fill="currentColor"/>
                </svg>
            </button>
        </div>
    </div>
</div>

{{-- Load JS --}}
<script src="{{ asset('js/ai-chat-widget.js') }}"></script>

@endif
