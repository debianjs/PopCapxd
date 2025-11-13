// GeminiStack.js - Chat con Gemini AI
(function() {
    'use strict';

    const API_KEY = 'AIzaSyBt77r0sl4YDcBqQBjHIMxu9ZvbjbzVqrk';
    let selectedImage = null;
    let selectedImageBase64 = null;
    let messages = [];

    // Crear los estilos
    const styles = `
        @import url('https://api.fontshare.com/v2/css?f[]=satoshi@400,500,700&display=swap');
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: 'Satoshi', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            }

            .gemini-floating-sphere {
                position: fixed;
                bottom: 2rem;
                right: 2rem;
                width: 70px;
                height: 70px;
                border-radius: 50%;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                box-shadow: 0 8px 32px rgba(102, 126, 234, 0.4);
                z-index: 1000;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .gemini-floating-sphere:hover {
                transform: scale(1.1);
                box-shadow: 0 12px 48px rgba(102, 126, 234, 0.6);
            }

            .gemini-floating-sphere svg {
                width: 36px;
                height: 36px;
                color: white;
            }

            .gemini-chat-container {
                position: fixed;
                inset: 0;
                background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
                z-index: 999;
                display: none;
                clip-path: circle(0% at 95% 95%);
            }

            .gemini-chat-container.gemini-expanding {
                display: block;
                animation: gemini-expand 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            }

            .gemini-chat-container.gemini-active {
                display: flex;
                flex-direction: column;
                clip-path: circle(150% at 95% 95%);
            }

            @keyframes gemini-expand {
                from { clip-path: circle(0% at 95% 95%); }
                to { clip-path: circle(150% at 95% 95%); }
            }

            .gemini-header {
                background: rgba(255, 255, 255, 0.05);
                backdrop-filter: blur(20px);
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
                padding: 1.25rem 1.5rem;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .gemini-menu-btn {
                background: rgba(255, 255, 255, 0.1);
                border: none;
                color: white;
                cursor: pointer;
                padding: 0.75rem;
                border-radius: 12px;
                transition: all 0.3s;
                font-size: 1.5rem;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .gemini-menu-btn:hover {
                background: rgba(255, 255, 255, 0.2);
                transform: translateY(-2px);
            }

            .gemini-header-center {
                display: flex;
                align-items: center;
                gap: 1rem;
            }

            .gemini-logo {
                width: 48px;
                height: 48px;
                border-radius: 12px;
                background: white;
                padding: 6px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            }

            .gemini-logo img {
                width: 100%;
                height: 100%;
                object-fit: contain;
            }

            .gemini-header-center h1 {
                color: white;
                font-size: 1.5rem;
                font-weight: 700;
                letter-spacing: -0.02em;
            }

            .gemini-sidebar {
                position: fixed;
                top: 0;
                left: 0;
                height: 100%;
                width: 320px;
                background: rgba(0, 0, 0, 0.4);
                backdrop-filter: blur(20px);
                border-right: 1px solid rgba(255, 255, 255, 0.1);
                transform: translateX(-100%);
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                z-index: 1001;
                display: flex;
                flex-direction: column;
            }

            .gemini-sidebar.gemini-open {
                transform: translateX(0);
            }

            .gemini-sidebar-header {
                padding: 1.5rem;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .gemini-sidebar-header h2 {
                color: white;
                font-size: 1.25rem;
                font-weight: 700;
            }

            .gemini-close-btn {
                background: rgba(255, 255, 255, 0.1);
                border: none;
                color: white;
                cursor: pointer;
                padding: 0.5rem;
                border-radius: 8px;
                transition: all 0.3s;
                font-size: 1.25rem;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .gemini-close-btn:hover {
                background: rgba(255, 255, 255, 0.2);
            }

            .gemini-history-list {
                flex: 1;
                overflow-y: auto;
                padding: 1rem;
            }

            .gemini-history-list::-webkit-scrollbar {
                width: 6px;
            }

            .gemini-history-list::-webkit-scrollbar-track {
                background: rgba(255, 255, 255, 0.05);
            }

            .gemini-history-list::-webkit-scrollbar-thumb {
                background: rgba(255, 255, 255, 0.2);
                border-radius: 3px;
            }

            .gemini-history-item {
                padding: 1rem;
                background: rgba(255, 255, 255, 0.05);
                border-radius: 12px;
                margin-bottom: 0.75rem;
                cursor: pointer;
                transition: all 0.3s;
                border: 1px solid rgba(255, 255, 255, 0.1);
            }

            .gemini-history-item:hover {
                background: rgba(255, 255, 255, 0.1);
                transform: translateX(4px);
            }

            .gemini-history-item p {
                color: rgba(255, 255, 255, 0.9);
                font-size: 0.95rem;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                font-weight: 500;
            }

            .gemini-history-item span {
                color: rgba(255, 255, 255, 0.5);
                font-size: 0.8rem;
                margin-top: 0.5rem;
                display: block;
            }

            .gemini-messages-area {
                flex: 1;
                overflow-y: auto;
                padding: 2rem;
            }

            .gemini-messages-area::-webkit-scrollbar {
                width: 8px;
            }

            .gemini-messages-area::-webkit-scrollbar-track {
                background: rgba(255, 255, 255, 0.05);
            }

            .gemini-messages-area::-webkit-scrollbar-thumb {
                background: rgba(255, 255, 255, 0.2);
                border-radius: 4px;
            }

            .gemini-empty-state {
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-direction: column;
                gap: 1.5rem;
            }

            .gemini-empty-logo {
                width: 120px;
                height: 120px;
                background: white;
                border-radius: 24px;
                padding: 20px;
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            }

            .gemini-empty-logo img {
                width: 100%;
                height: 100%;
                object-fit: contain;
            }

            .gemini-empty-state p {
                color: rgba(255, 255, 255, 0.7);
                font-size: 1.25rem;
                font-weight: 500;
            }

            .gemini-message {
                display: flex;
                margin-bottom: 1.5rem;
                animation: gemini-slide-up 0.3s ease-out;
            }

            @keyframes gemini-slide-up {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .gemini-message.gemini-user {
                justify-content: flex-end;
            }

            .gemini-message-content {
                max-width: 75%;
                padding: 1.25rem 1.5rem;
                border-radius: 20px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            }

            .gemini-message.gemini-user .gemini-message-content {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                border-bottom-right-radius: 4px;
            }

            .gemini-message.gemini-gemini .gemini-message-content {
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(20px);
                color: white;
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-bottom-left-radius: 4px;
            }

            .gemini-message-image {
                max-width: 100%;
                border-radius: 12px;
                margin-bottom: 0.75rem;
            }

            .gemini-message-text {
                white-space: pre-wrap;
                word-wrap: break-word;
                line-height: 1.6;
                font-size: 0.95rem;
            }

            .gemini-message-time {
                font-size: 0.75rem;
                opacity: 0.6;
                margin-top: 0.75rem;
                font-weight: 500;
            }

            .gemini-loading-dots {
                display: flex;
                gap: 0.5rem;
                padding: 1rem;
            }

            .gemini-loading-dot {
                width: 10px;
                height: 10px;
                border-radius: 50%;
                background: linear-gradient(135deg, #667eea, #764ba2);
                animation: gemini-bounce 1.4s infinite ease-in-out both;
            }

            .gemini-loading-dot:nth-child(1) {
                animation-delay: -0.32s;
            }

            .gemini-loading-dot:nth-child(2) {
                animation-delay: -0.16s;
            }

            @keyframes gemini-bounce {
                0%, 80%, 100% {
                    transform: scale(0);
                }
                40% {
                    transform: scale(1);
                }
            }

            .gemini-input-area {
                background: rgba(255, 255, 255, 0.05);
                backdrop-filter: blur(20px);
                border-top: 1px solid rgba(255, 255, 255, 0.1);
                padding: 1.5rem;
            }

            .gemini-input-container {
                max-width: 1000px;
                margin: 0 auto;
            }

            .gemini-image-preview {
                margin-bottom: 1rem;
                position: relative;
                display: inline-block;
            }

            .gemini-image-preview img {
                height: 100px;
                border-radius: 12px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            }

            .gemini-remove-image {
                position: absolute;
                top: -8px;
                right: -8px;
                width: 28px;
                height: 28px;
                border-radius: 50%;
                background: #ef4444;
                color: white;
                border: 2px solid white;
                cursor: pointer;
                font-size: 1.125rem;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s;
                font-weight: 700;
            }

            .gemini-remove-image:hover {
                background: #dc2626;
                transform: scale(1.1);
            }

            .gemini-messageBox {
                display: flex;
                align-items: center;
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(20px);
                padding: 0.75rem;
                border-radius: 16px;
                border: 1px solid rgba(255, 255, 255, 0.2);
                gap: 0.75rem;
                transition: all 0.3s;
            }

            .gemini-messageBox:focus-within {
                border-color: rgba(102, 126, 234, 0.5);
                box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            }

            .gemini-fileUploadWrapper {
                position: relative;
            }

            .gemini-fileUploadWrapper input {
                display: none;
            }

            .gemini-fileUploadWrapper label {
                cursor: pointer;
                padding: 0.625rem;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 10px;
                transition: all 0.3s;
                background: rgba(255, 255, 255, 0.1);
            }

            .gemini-fileUploadWrapper label:hover {
                background: rgba(255, 255, 255, 0.2);
                transform: translateY(-2px);
            }

            .gemini-fileUploadWrapper label svg {
                height: 22px;
                width: 22px;
            }

            .gemini-tooltip {
                position: absolute;
                bottom: 120%;
                left: 50%;
                transform: translateX(-50%);
                background: rgba(0, 0, 0, 0.9);
                color: white;
                font-size: 0.8rem;
                padding: 0.5rem 0.75rem;
                border-radius: 8px;
                white-space: nowrap;
                opacity: 0;
                pointer-events: none;
                transition: opacity 0.3s;
                font-weight: 500;
            }

            .gemini-fileUploadWrapper label:hover .gemini-tooltip {
                opacity: 1;
            }

            .gemini-messageInput {
                flex: 1;
                background: transparent;
                border: none;
                outline: none;
                color: white;
                padding: 0 0.75rem;
                font-size: 1rem;
                font-weight: 500;
            }

            .gemini-messageInput::placeholder {
                color: rgba(255, 255, 255, 0.5);
            }

            .gemini-sendButton {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border: none;
                cursor: pointer;
                padding: 0.75rem;
                border-radius: 10px;
                transition: all 0.3s;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .gemini-sendButton:hover:not(:disabled) {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
            }

            .gemini-sendButton:disabled {
                opacity: 0.4;
                cursor: not-allowed;
            }

            .gemini-sendButton svg {
                height: 22px;
                width: 22px;
            }

            @media (max-width: 768px) {
                .gemini-sidebar {
                    width: 85vw;
                }

                .gemini-message-content {
                    max-width: 85%;
                }

                .gemini-header-center h1 {
                    font-size: 1.125rem;
                }

                .gemini-input-area {
                    padding: 1rem;
                }

                .gemini-messages-area {
                    padding: 1rem;
                }
            }
        </style>
    `;

    // Crear el HTML
    const html = `
        <div class="gemini-floating-sphere" id="geminiFloatingSphere">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
            </svg>
        </div>

        <div class="gemini-chat-container" id="geminiChatContainer">
            <div class="gemini-sidebar" id="geminiSidebar">
                <div class="gemini-sidebar-header">
                    <h2>Historial</h2>
                    <button class="gemini-close-btn" id="geminiCloseSidebar">✕</button>
                </div>
                <div class="gemini-history-list" id="geminiHistoryList"></div>
            </div>

            <div class="gemini-header">
                <button class="gemini-menu-btn" id="geminiMenuBtn">☰</button>
                <div class="gemini-header-center">
                    <div class="gemini-logo">
                        <img src="https://rogddqelmxyuvhpjvxbf.supabase.co/storage/v1/object/public/files/pegacbhxre.png" alt="Gemini">
                    </div>
                    <h1>Hello, I am Gemini</h1>
                </div>
                <div style="width: 48px;"></div>
            </div>

            <div class="gemini-messages-area" id="geminiMessagesArea">
                <div class="gemini-empty-state" id="geminiEmptyState">
                    <div class="gemini-empty-logo">
                        <img src="https://rogddqelmxyuvhpjvxbf.supabase.co/storage/v1/object/public/files/pegacbhxre.png" alt="Gemini">
                    </div>
                    <p>¿En qué puedo ayudarte hoy?</p>
                </div>
            </div>

            <div class="gemini-input-area">
                <div class="gemini-input-container">
                    <div class="gemini-image-preview" id="geminiImagePreview" style="display: none;">
                        <img id="geminiPreviewImg" src="" alt="Preview">
                        <button class="gemini-remove-image" id="geminiRemoveImage">×</button>
                    </div>
                    <div class="gemini-messageBox">
                        <div class="gemini-fileUploadWrapper">
                            <label for="geminiFile">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 337 337">
                                    <circle stroke-width="20" stroke="#ffffff" fill="none" r="158.5" cy="168.5" cx="168.5" />
                                    <path stroke-linecap="round" stroke-width="25" stroke="#ffffff" d="M167.759 79V259" />
                                    <path stroke-linecap="round" stroke-width="25" stroke="#ffffff" d="M79 167.138H259" />
                                </svg>
                                <span class="gemini-tooltip">Agregar imagen</span>
                            </label>
                            <input type="file" id="geminiFile" accept="image/*">
                        </div>
                        <input type="text" id="geminiMessageInput" class="gemini-messageInput" placeholder="Escribe un mensaje...">
                        <button id="geminiSendButton" class="gemini-sendButton" disabled>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Inicializar cuando el DOM esté listo
    function init() {
        // Insertar estilos
        document.head.insertAdjacentHTML('beforeend', styles);
        
        // Insertar HTML
        document.body.insertAdjacentHTML('beforeend', html);

        // Obtener elementos
        const floatingSphere = document.getElementById('geminiFloatingSphere');
        const chatContainer = document.getElementById('geminiChatContainer');
        const sidebar = document.getElementById('geminiSidebar');
        const menuBtn = document.getElementById('geminiMenuBtn');
        const closeSidebar = document.getElementById('geminiCloseSidebar');
        const messagesArea = document.getElementById('geminiMessagesArea');
        const emptyState = document.getElementById('geminiEmptyState');
        const messageInput = document.getElementById('geminiMessageInput');
        const sendButton = document.getElementById('geminiSendButton');
        const fileInput = document.getElementById('geminiFile');
        const imagePreview = document.getElementById('geminiImagePreview');
        const previewImg = document.getElementById('geminiPreviewImg');
        const removeImageBtn = document.getElementById('geminiRemoveImage');
        const historyList = document.getElementById('geminiHistoryList');

        // Event listeners
        floatingSphere.addEventListener('click', () => {
            chatContainer.classList.add('gemini-expanding');
            setTimeout(() => {
                chatContainer.classList.add('gemini-active');
                chatContainer.classList.remove('gemini-expanding');
            }, 600);
        });

        menuBtn.addEventListener('click', () => {
            sidebar.classList.toggle('gemini-open');
        });

        closeSidebar.addEventListener('click', () => {
            sidebar.classList.remove('gemini-open');
        });

        fileInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                selectedImage = file;
                const reader = new FileReader();
                reader.onload = (e) => {
                    previewImg.src = e.target.result;
                    imagePreview.style.display = 'inline-block';
                    selectedImageBase64 = e.target.result.split(',')[1];
                    sendButton.disabled = false;
                };
                reader.readAsDataURL(file);
            }
        });

        removeImageBtn.addEventListener('click', () => {
            selectedImage = null;
            selectedImageBase64 = null;
            imagePreview.style.display = 'none';
            fileInput.value = '';
            sendButton.disabled = !messageInput.value.trim();
        });

        messageInput.addEventListener('input', () => {
            sendButton.disabled = !messageInput.value.trim() && !selectedImage;
        });

        sendButton.addEventListener('click', () => sendMessage());
        
        messageInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        async function sendMessage() {
            const text = messageInput.value.trim();
            if (!text && !selectedImage) return;

            emptyState.style.display = 'none';

            const userMessage = {
                text: text,
                image: selectedImage ? previewImg.src : null,
                sender: 'user',
                timestamp: new Date().toLocaleTimeString()
            };
            messages.push(userMessage);
            addMessageToUI(userMessage);
            addToHistory(userMessage);

            messageInput.value = '';
            const currentImage = selectedImageBase64;
            const hasImage = !!selectedImage;
            selectedImage = null;
            selectedImageBase64 = null;
            imagePreview.style.display = 'none';
            fileInput.value = '';
            sendButton.disabled = true;

            showLoading();

            try {
                const response = await callGeminiAPI(text, currentImage, hasImage);
                removeLoading();
                
                const geminiMessage = {
                    text: response,
                    sender: 'gemini',
                    timestamp: new Date().toLocaleTimeString()
                };
                messages.push(geminiMessage);
                addMessageToUI(geminiMessage);
            } catch (error) {
                removeLoading();
                const errorMessage = {
                    text: 'Lo siento, hubo un error al procesar tu mensaje. Por favor, intenta nuevamente.',
                    sender: 'gemini',
                    timestamp: new Date().toLocaleTimeString()
                };
                addMessageToUI(errorMessage);
            }
        }

        async function callGeminiAPI(text, imageBase64, hasImage) {
            const url = `https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-8b:generateContent?key=${API_KEY}`;

            const parts = [];
            
            if (text) {
                parts.push({ text: text });
            }
            
            if (hasImage && imageBase64) {
                parts.push({
                    inline_data: {
                        mime_type: "image/jpeg",
                        data: imageBase64
                    }
                });
            }

            const body = {
                contents: [{
                    parts: parts
                }]
            };

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(body)
            });

            if (!response.ok) {
                throw new Error('API request failed');
            }

            const data = await response.json();
            return data.candidates[0].content.parts[0].text;
        }

        function addMessageToUI(message) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `gemini-message gemini-${message.sender}`;
            
            const contentDiv = document.createElement('div');
            contentDiv.className = 'gemini-message-content';
            
            if (message.image) {
                const img = document.createElement('img');
                img.src = message.image;
                img.className = 'gemini-message-image';
                contentDiv.appendChild(img);
            }
            
            if (message.text) {
                const textDiv = document.createElement('div');
                textDiv.className = 'gemini-message-text';
                textDiv.textContent = message.text;
                contentDiv.appendChild(textDiv);
            }
            
            const timeDiv = document.createElement('div');
            timeDiv.className = 'gemini-message-time';
            timeDiv.textContent = message.timestamp;
            contentDiv.appendChild(timeDiv);
            
            messageDiv.appendChild(contentDiv);
            messagesArea.appendChild(messageDiv);
            messagesArea.scrollTop = messagesArea.scrollHeight;
        }

        function showLoading() {
            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'gemini-message gemini-gemini';
            loadingDiv.id = 'geminiLoadingMessage';
            
            const contentDiv = document.createElement('div');
            contentDiv.className = 'gemini-message-content';
            
            const dotsDiv = document.createElement('div');
            dotsDiv.className = 'gemini-loading-dots';
            dotsDiv.innerHTML = '<div class="gemini-loading-dot"></div><div class="gemini-loading-dot"></div><div class="gemini-loading-dot"></div>';
            
            contentDiv.appendChild(dotsDiv);
            loadingDiv.appendChild(contentDiv);
            messagesArea.appendChild(loadingDiv);
            messagesArea.scrollTop = messagesArea.scrollHeight;
        }

        function removeLoading() {
            const loadingMsg = document.getElementById('geminiLoadingMessage');
            if (loadingMsg) {
                loadingMsg.remove();
            }
        }

        function addToHistory(message) {
            if (message.sender === 'user') {
                const historyItem = document.createElement('div');
                historyItem.className = 'gemini-history-item';
                historyItem.innerHTML = `
                    <p>${message.text || 'Imagen enviada'}</p>
                    <span>${message.timestamp}</span>
                `;
                historyList.insertBefore(historyItem, historyList.firstChild);
            }
        }
    }

    // Inicializar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();