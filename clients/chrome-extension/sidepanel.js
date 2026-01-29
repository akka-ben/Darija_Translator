/**
 * Darija Translator - Side Panel (Enhanced Version)
 * UI Enhancements with same core logic
 */

// Elements
const inputText = document.getElementById('inputText');
const translateBtn = document.getElementById('translateBtn');
const voiceBtn = document.getElementById('voiceBtn');
const pasteBtn = document.getElementById('pasteBtn');
const speakBtn = document.getElementById('speakBtn');
const stopBtn = document.getElementById('stopBtn');
const resultDiv = document.getElementById('result');
const ttsControls = document.getElementById('ttsControls');
const charCount = document.getElementById('charCount');

// API Configuration
const API_URL = 'http://localhost:8080/darija-translator-1.0.0/translator/translate';
const USERNAME = 'admin';
const PASSWORD = 'admin123';

// Speech Recognition
let recognition = null;
let isRecording = false;

// Text-to-Speech
let currentUtterance = null;

// Dernière traduction
let lastTranslation = '';

// ===== Character Counter =====
inputText.addEventListener('input', function() {
    charCount.textContent = this.value.length;
});

// Initialize Speech Recognition
if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    recognition = new SpeechRecognition();
    recognition.lang = 'en-US';
    recognition.continuous = false;
    recognition.interimResults = false;

    recognition.onresult = (event) => {
        const transcript = event.results[0][0].transcript;
        inputText.value = transcript;
        charCount.textContent = transcript.length;
        stopRecording();
        translateBtn.click();
    };

    recognition.onerror = (event) => {
        console.error('Speech recognition error:', event.error);
        stopRecording();
        
        let errorMsg = 'Voice recognition error';
        if (event.error === 'not-allowed') {
            errorMsg = 'Microphone access denied. Please allow microphone in Chrome settings.';
        }
        showResult(errorMsg, true);
    };

    recognition.onend = () => {
        stopRecording();
    };
}

// Event Listeners
translateBtn.addEventListener('click', translateText);
voiceBtn.addEventListener('click', toggleVoiceInput);
pasteBtn.addEventListener('click', pasteFromClipboard);
speakBtn.addEventListener('click', speakTranslation);
stopBtn.addEventListener('click', stopSpeaking);

// Ctrl+Enter to translate
inputText.addEventListener('keydown', (e) => {
    if (e.ctrlKey && e.key === 'Enter') {
        translateBtn.click();
    }
});

console.log('🎨 SIDE PANEL LOADED');

// Écouter les messages du background
chrome.runtime.onMessage.addListener((message, sender, sendResponse) => {
    console.log('🎨 Message:', message.type);
    
    if (message.type === 'SET_TEXT') {
        console.log('🎨 Setting text:', message.text);
        inputText.value = message.text;
        charCount.textContent = message.text.length;
        inputText.focus();
    }
});

// Au démarrage, demander le texte
chrome.runtime.sendMessage({ type: 'GET_TEXT' }, (response) => {
    if (response && response.text) {
        console.log('🎨 Received text:', response.text);
        inputText.value = response.text;
        charCount.textContent = response.text.length;
        inputText.focus();
    }
});

/**
 * Translate text using REST API
 */
async function translateText() {
    const text = inputText.value.trim();
    
    if (!text) {
        showResult('Please enter some text to translate', true);
        return;
    }
    
    showLoading();
    translateBtn.disabled = true;
    ttsControls.classList.remove('show');
    
    try {
        const auth = btoa(`${USERNAME}:${PASSWORD}`);
        
        const response = await fetch(API_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'text/plain',
                'Authorization': `Basic ${auth}`
            },
            body: text
        });
        
        if (response.status === 401) {
            throw new Error('Authentication failed');
        }
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        
        const translation = await response.text();
        lastTranslation = translation;
        
        showResult(`
            <h3>Translation Complete</h3>
            <p><strong>English:</strong> ${escapeHtml(text)}</p>
            <p><strong>Darija:</strong> ${escapeHtml(translation)}</p>
            <button class="copy-btn" onclick="copyToClipboard()">
                Copy Translation
            </button>
        `, false);
        
        // Show TTS controls
        ttsControls.classList.add('show');
        
    } catch (error) {
        console.error('Translation error:', error);
        showResult(`
            <h3>Error</h3>
            <p>${escapeHtml(error.message)}</p>
        `, true);
        
    } finally {
        translateBtn.disabled = false;
    }
}

/**
 * Toggle voice input (Speech-to-Text)
 */
function toggleVoiceInput() {
    if (!recognition) {
        showResult('Speech recognition not supported in this browser', true);
        return;
    }
    
    if (isRecording) {
        stopRecording();
    } else {
        startRecording();
    }
}

function startRecording() {
    try {
        isRecording = true;
        voiceBtn.classList.add('recording');
        voiceBtn.innerHTML = '<span class="icon">⏺</span> Recording...';
        recognition.start();
        showResult('🎤 Listening... Speak now', false);
    } catch (error) {
        console.error('Start recording error:', error);
        stopRecording();
        showResult('Could not start voice recognition. Try again.', true);
    }
}

function stopRecording() {
    isRecording = false;
    voiceBtn.classList.remove('recording');
    voiceBtn.innerHTML = '<span class="icon">🎤</span> Voice';
    if (recognition) {
        try {
            recognition.stop();
        } catch (e) {
            // Ignore stop errors
        }
    }
}

/**
 * Paste from clipboard - VERSION SIMPLIFIÉE
 */
async function pasteFromClipboard() {
    try {
        // Méthode 1: Essayer l'API Clipboard moderne
        if (navigator.clipboard && navigator.clipboard.readText) {
            const text = await navigator.clipboard.readText();
            if (text) {
                inputText.value = text;
                charCount.textContent = text.length;
                showResult('✅ Text pasted from clipboard', false);
                return;
            }
        }
    } catch (error) {
        console.log('Clipboard API failed, using fallback');
    }
    
    // Méthode 2: Fallback - demander à l'utilisateur
    showResult('Please use Ctrl+V (or Cmd+V on Mac) to paste text manually', false);
    inputText.focus();
}

/**
 * Copy to Clipboard
 */
function copyToClipboard() {
    if (!lastTranslation) return;

    navigator.clipboard.writeText(lastTranslation).then(() => {
        const copyBtn = event.target;
        const originalText = copyBtn.textContent;
        copyBtn.innerHTML = '<span class="icon">✓</span> Copied!';
        copyBtn.style.background = 'linear-gradient(135deg, #4caf50, #45a049)';
        
        setTimeout(() => {
            copyBtn.innerHTML = originalText;
            copyBtn.style.background = '';
        }, 2000);
    }).catch(err => {
        console.error('Failed to copy:', err);
        showResult('Could not copy to clipboard', true);
    });
}

/**
 * Speak translation (Text-to-Speech)
 */
function speakTranslation() {
    if (!lastTranslation) {
        showResult('No translation to read', true);
        return;
    }
    
    if (!window.speechSynthesis) {
        showResult('Text-to-speech not supported in this browser', true);
        return;
    }
    
    stopSpeaking(); // Stop any ongoing speech
    
    currentUtterance = new SpeechSynthesisUtterance(lastTranslation);
    currentUtterance.lang = 'ar-SA'; // Arabic voice
    currentUtterance.rate = 0.9; // Slightly slower for clarity
    
    currentUtterance.onend = () => {
        speakBtn.disabled = false;
    };
    
    currentUtterance.onerror = (event) => {
        console.error('TTS error:', event);
        showResult('Text-to-speech error. Try again.', true);
        speakBtn.disabled = false;
    };
    
    speakBtn.disabled = true;
    
    try {
        window.speechSynthesis.speak(currentUtterance);
    } catch (error) {
        console.error('Speak error:', error);
        showResult('Could not speak translation', true);
        speakBtn.disabled = false;
    }
}

/**
 * Stop speaking
 */
function stopSpeaking() {
    try {
        window.speechSynthesis.cancel();
    } catch (e) {
        // Ignore errors
    }
    speakBtn.disabled = false;
}

/**
 * Show result
 */
function showResult(content, isError) {
    resultDiv.innerHTML = content;
    resultDiv.className = 'result show' + (isError ? ' error' : '');
}

/**
 * Show loading
 */
function showLoading() {
    resultDiv.innerHTML = `
        <div class="loading">
            <div class="spinner"></div>
            <p>Translating...</p>
        </div>
    `;
    resultDiv.className = 'result show';
}

/**
 * Escape HTML
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}