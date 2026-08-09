/**
 * Premium AI Chat Widget Logic
 * Uses native Fetch API (zero external dependencies)
 */

document.addEventListener('DOMContentLoaded', () => {
    // Check if the chat widget exists in the DOM
    const fab = document.getElementById('ai-chat-fab');
    if (!fab) return;

    class AiChatWidget {
        constructor() {
            this.fab = document.getElementById('ai-chat-fab');
            this.panel = document.getElementById('ai-chat-panel');
            this.closeBtn = document.getElementById('ai-chat-close');
            this.newSessionBtn = document.getElementById('ai-chat-new-session');
            this.messagesContainer = document.getElementById('ai-chat-messages');
            this.inputArea = document.getElementById('ai-chat-input');
            this.sendBtn = document.getElementById('ai-chat-send');
            this.actionChips = document.querySelectorAll('.ai-chat-action-chip');
            
            this.sessionId = this.getOrCreateSessionId();
            this.isOpen = localStorage.getItem('ai_chat_open') === 'true';
            this.isTyping = false;

            this.bindEvents();
            this.init();
        }

        getCsrfToken() {
            const tokenMeta = document.head.querySelector('meta[name="csrf-token"]');
            return tokenMeta ? tokenMeta.content : '';
        }

        getOrCreateSessionId() {
            let sid = localStorage.getItem('ai_chat_session');
            if (!sid) {
                sid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                    const r = Math.random() * 16 | 0, v = c === 'x' ? r : (r & 0x3 | 0x8);
                    return v.toString(16);
                });
                localStorage.setItem('ai_chat_session', sid);
            }
            return sid;
        }

        bindEvents() {
            this.fab.addEventListener('click', () => this.togglePanel());
            this.closeBtn.addEventListener('click', () => this.togglePanel(false));
            this.newSessionBtn.addEventListener('click', () => this.newSession());
            
            this.sendBtn.addEventListener('click', () => this.handleSend());
            this.inputArea.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    this.handleSend();
                }
            });

            // Auto-resize textarea
            this.inputArea.addEventListener('input', () => {
                this.inputArea.style.height = 'auto';
                this.inputArea.style.height = Math.min(this.inputArea.scrollHeight, 100) + 'px';
            });

            this.actionChips.forEach(chip => {
                chip.addEventListener('click', () => {
                    const msg = chip.getAttribute('data-message');
                    if (msg) this.sendMessage(msg);
                });
            });
        }

        init() {
            if (this.isOpen) {
                this.openPanel();
            }
            this.loadHistory();
        }

        togglePanel(forceOpen) {
            this.isOpen = forceOpen !== undefined ? forceOpen : !this.isOpen;
            localStorage.setItem('ai_chat_open', this.isOpen);
            
            if (this.isOpen) {
                this.openPanel();
            } else {
                this.closePanel();
            }
        }

        openPanel() {
            this.panel.classList.add('is-open');
            this.fab.classList.add('is-open');
            setTimeout(() => this.inputArea.focus(), 300);
            this.scrollToBottom();
        }

        closePanel() {
            this.panel.classList.remove('is-open');
            this.fab.classList.remove('is-open');
        }

        async loadHistory() {
            try {
                const response = await fetch(`/admin/ai-assistant/history?session_id=${this.sessionId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': this.getCsrfToken()
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.success && data.messages && data.messages.length > 0) {
                        this.messagesContainer.innerHTML = '';
                        data.messages.forEach(msg => {
                            this.renderMessage(msg.role, msg.content);
                        });
                        this.scrollToBottom();
                        return;
                    }
                }
                
                if (this.messagesContainer.children.length === 0) {
                    this.showWelcomeMessage();
                }
            } catch (error) {
                console.error("Failed to load chat history", error);
                if (this.messagesContainer.children.length === 0) {
                    this.showWelcomeMessage();
                }
            }
        }

        showWelcomeMessage() {
            this.renderMessage('assistant', 'Halo! Saya Asisten TPST. Ada yang bisa saya bantu hari ini? Anda bisa menanyakan saldo, membuat jurnal, atau bantuan lainnya.');
        }

        async newSession() {
            if (!confirm('Mulai sesi obrolan baru?')) return;
            
            this.messagesContainer.innerHTML = '';
            localStorage.removeItem('ai_chat_session');
            this.sessionId = this.getOrCreateSessionId();
            
            try {
                await fetch('/admin/ai-assistant/new-session', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': this.getCsrfToken()
                    },
                    body: JSON.stringify({ session_id: this.sessionId })
                });
            } catch (error) {
                console.error("New session error", error);
            }
            
            this.showWelcomeMessage();
        }

        handleSend() {
            const text = this.inputArea.value.trim();
            if (!text || this.isTyping) return;
            
            this.inputArea.value = '';
            this.inputArea.style.height = 'auto';
            this.sendMessage(text);
        }

        async sendMessage(text) {
            this.renderMessage('user', text);
            this.showTypingIndicator();
            this.scrollToBottom();

            try {
                const response = await fetch('/admin/ai-assistant/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': this.getCsrfToken()
                    },
                    body: JSON.stringify({
                        message: text,
                        session_id: this.sessionId
                    })
                });

                const data = await response.json();
                this.removeTypingIndicator();

                if (response.ok && data.success) {
                    const replyText = data.content || data.reply || data.message;
                    if (replyText) {
                        this.renderMessage('assistant', replyText);
                    } else {
                        this.renderMessage('assistant', 'Maaf, tidak ada tanggapan yang dihasilkan.');
                    }
                } else {
                    const errorMsg = data.error || data.message || `Terjadi kesalahan (HTTP ${response.status})`;
                    this.renderMessage('assistant', `⚠️ ${errorMsg}`);
                }
            } catch (error) {
                this.removeTypingIndicator();
                this.renderMessage('assistant', `⚠️ Terjadi kesalahan koneksi: ${error.message}`);
                console.error("Chat error:", error);
            }
            
            this.scrollToBottom();
        }

        renderMessage(role, content) {
            // Map roles if needed
            const roleClass = role === 'user' ? 'user' : 'assistant';
            
            const wrapper = document.createElement('div');
            wrapper.className = `ai-chat-bubble-wrapper ai-chat-bubble-wrapper--${roleClass}`;
            
            const bubble = document.createElement('div');
            bubble.className = `ai-chat-bubble ai-chat-bubble--${roleClass}`;
            bubble.innerHTML = this.parseMarkdown(content);
            
            const time = document.createElement('div');
            time.className = 'ai-chat-timestamp';
            const now = new Date();
            time.innerText = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');

            wrapper.appendChild(bubble);
            wrapper.appendChild(time);
            
            this.messagesContainer.appendChild(wrapper);
            this.scrollToBottom();
        }

        showTypingIndicator() {
            this.isTyping = true;
            this.sendBtn.disabled = true;
            const wrapper = document.createElement('div');
            wrapper.className = `ai-chat-bubble-wrapper ai-chat-bubble-wrapper--assistant`;
            wrapper.id = 'ai-typing-indicator';
            
            const bubble = document.createElement('div');
            bubble.className = `ai-chat-bubble ai-chat-bubble--assistant`;
            bubble.innerHTML = `
                <div class="ai-chat-typing">
                    <span></span><span></span><span></span>
                </div>
            `;
            
            wrapper.appendChild(bubble);
            this.messagesContainer.appendChild(wrapper);
        }

        removeTypingIndicator() {
            this.isTyping = false;
            this.sendBtn.disabled = false;
            const el = document.getElementById('ai-typing-indicator');
            if (el) el.remove();
        }

        scrollToBottom() {
            setTimeout(() => {
                this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight;
            }, 50);
        }

        parseMarkdown(text) {
            if (!text) return '';
            
            let html = text
                // Escape HTML first to prevent XSS (basic)
                .replace(/</g, '&lt;').replace(/>/g, '&gt;')
                
                // Code blocks
                .replace(/```([\s\S]*?)```/g, '<pre><code>$1</code></pre>')
                // Inline code
                .replace(/`([^`]+)`/g, '<code>$1</code>')
                // Bold
                .replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>')
                // Italic
                .replace(/\*([^*]+)\*/g, '<em>$1</em>')
                // Links / Buttons
                .replace(/\[([^\]]+)\]\(([^)]+)\)/g, (match, label, url) => {
                    // If URL starts with /admin/ or /app/ treat it as an action button
                    if (url.startsWith('/') || url.includes(window.location.host)) {
                        return `<a href="${url}" class="ai-action-btn">${label}</a>`;
                    }
                    return `<a href="${url}" target="_blank" rel="noopener">${label}</a>`;
                })
                // Lists (simple)
                .replace(/^\s*-\s+(.+)/gm, '<ul><li>$1</li></ul>')
                .replace(/^\s*\*\s+(.+)/gm, '<ul><li>$1</li></ul>')
                // Fix adjacent uls
                .replace(/<\/ul>\s*<ul>/g, '')
                // Line breaks
                .replace(/\n/g, '<br>');

            return html;
        }
    }

    // Initialize widget
    window.aiChatWidget = new AiChatWidget();
});
