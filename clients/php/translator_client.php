<?php
// Darija Translator – PHP Client (Final UI)

$result = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $text = trim($_POST["text"] ?? "");

    if ($text === "") {
        $error = "Please enter a text to translate.";
    } else {

        $url = "http://localhost:8080/darija-translator-1.0.0/translator/translate";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: text/plain"
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $text);

        // 🔐 Basic Auth (if enabled)
        curl_setopt($ch, CURLOPT_USERPWD, "admin:admin123");

        $response = curl_exec($ch);

        if ($response === false) {
            $error = "Error calling REST API: " . curl_error($ch);
        } else {
            $result = $response;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Darija Translator – PHP Client</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
/* ===== Reset & Global ===== */
* {
    box-sizing: border-box;
}

html, body {
    margin: 0;
    padding: 0;
}

body {
    min-height: 100vh;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
    overflow-x: hidden;
}

/* ===== Animated Background ===== */
body::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle at 20% 50%, rgba(102, 126, 234, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(118, 75, 162, 0.1) 0%, transparent 50%);
    pointer-events: none;
    z-index: -1;
}

/* ===== Card Container ===== */
.container {
    width: 100%;
    max-width: 600px;
    animation: slideIn 0.6s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.card {
    background: #ffffff;
    width: 100%;
    padding: 36px;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

@media (max-width: 480px) {
    .card {
        padding: 24px;
        border-radius: 16px;
    }
}

/* ===== Header ===== */
.header {
    text-align: center;
    margin-bottom: 32px;
    position: relative;
}

.header h1 {
    margin: 0;
    font-size: 32px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: 700;
    letter-spacing: -0.5px;
}

.header .flag {
    font-size: 40px;
    display: inline-block;
    margin-right: 8px;
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-8px); }
}

.header p {
    margin: 8px 0 0 0;
    font-size: 14px;
    color: #888;
    font-weight: 500;
}

/* ===== Form Section ===== */
.form-group {
    margin-bottom: 20px;
}

label {
    display: block;
    font-weight: 600;
    font-size: 14px;
    color: #333;
    margin-bottom: 10px;
    transition: color 0.3s ease;
}

.textarea-wrapper {
    position: relative;
}

textarea {
    width: 100%;
    height: 140px;
    padding: 14px;
    border-radius: 12px;
    border: 2px solid #e0e0e0;
    resize: vertical;
    font-size: 14px;
    font-family: inherit;
    transition: all 0.3s ease;
    background: #f9f9f9;
}

textarea:focus {
    outline: none;
    border-color: #667eea;
    background: #ffffff;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

textarea::placeholder {
    color: #bbb;
}

/* ===== Character Counter ===== */
.char-counter {
    font-size: 12px;
    color: #999;
    margin-top: 6px;
    transition: color 0.3s ease;
}

/* ===== Button ===== */
.button-group {
    display: flex;
    gap: 12px;
    margin-top: 24px;
}

button {
    flex: 1;
    padding: 14px;
    font-size: 15px;
    font-weight: 600;
    color: #fff;
    background: linear-gradient(135deg, #667eea, #764ba2);
    border: none;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

button::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.2);
    transition: left 0.3s ease;
}

button:hover::before {
    left: 100%;
}

button:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
}

button:active {
    transform: translateY(-1px);
}

button.secondary {
    background: #f0f0f0;
    color: #667eea;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

button.secondary:hover {
    background: #e8e8e8;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* ===== Loading State ===== */
.spinner {
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 3px solid rgba(255, 255, 255, 0.3);
    border-top: 3px solid #fff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-right: 8px;
    vertical-align: middle;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

button:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}

/* ===== Result Box ===== */
.result-box {
    margin-top: 28px;
    padding: 20px;
    border-radius: 12px;
    background: linear-gradient(135deg, #f5f7ff 0%, #f0f3ff 100%);
    border: 2px solid #e8ecff;
    border-left: 5px solid #667eea;
    animation: slideUp 0.5s ease-out;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.result-box h3 {
    margin: 0 0 12px 0;
    font-size: 15px;
    color: #667eea;
    font-weight: 600;
    display: flex;
    align-items: center;
}

.result-box h3::before {
    content: '✓';
    display: inline-block;
    width: 22px;
    height: 22px;
    background: #667eea;
    color: #fff;
    border-radius: 50%;
    text-align: center;
    line-height: 22px;
    margin-right: 10px;
    font-size: 12px;
    font-weight: bold;
}

.result-box p {
    margin: 0;
    font-size: 15px;
    color: #333;
    line-height: 1.6;
    word-break: break-word;
}

.copy-btn {
    width: auto;
    padding: 8px 16px;
    font-size: 12px;
    margin-top: 12px;
    background: #667eea;
    color: #fff;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.copy-btn:hover {
    background: #764ba2;
}

/* ===== Error Box ===== */
.error {
    margin-top: 20px;
    padding: 16px;
    background: linear-gradient(135deg, #ffe3e3 0%, #ffcccb 100%);
    border: 2px solid #ff6b6b;
    border-left: 5px solid #e74c3c;
    color: #c0392b;
    border-radius: 12px;
    font-size: 14px;
    animation: slideUp 0.5s ease-out;
    display: flex;
    align-items: center;
    gap: 12px;
}

.error::before {
    content: '⚠';
    font-size: 20px;
    flex-shrink: 0;
}

.error p {
    margin: 0;
    line-height: 1.5;
}

/* ===== Footer ===== */
.footer {
    margin-top: 28px;
    text-align: center;
    font-size: 12px;
    color: #999;
    border-top: 1px solid #eee;
    padding-top: 16px;
}

.footer a {
    color: #667eea;
    text-decoration: none;
    transition: color 0.3s ease;
}

.footer a:hover {
    color: #764ba2;
    text-decoration: underline;
}

/* ===== Info Box ===== */
.info-box {
    background: #e3f2fd;
    border: 2px solid #90caf9;
    border-radius: 12px;
    padding: 14px;
    margin-bottom: 20px;
    font-size: 13px;
    color: #1976d2;
    display: flex;
    align-items: center;
    gap: 10px;
}

.info-box::before {
    content: 'ℹ';
    display: inline-block;
    width: 24px;
    height: 24px;
    background: #1976d2;
    color: #fff;
    border-radius: 50%;
    text-align: center;
    line-height: 24px;
    font-weight: bold;
    flex-shrink: 0;
}

.hidden {
    display: none !important;
}
</style>

</head>

<body>

<div class="container">
    <div class="card">

        <div class="header">
            <h1>Darija Translator</h1>
            <p>English → Moroccan Arabic Dialect</p>
        </div>

        <div class="info-box">
            Translate English text to Moroccan Darija instantly
        </div>

        <form method="post" id="translatorForm">
            <div class="form-group">
                <label for="text">English text</label>
                <div class="textarea-wrapper">
                    <textarea name="text" id="text" placeholder="Enter English text to translate..." autocomplete="off"><?= htmlspecialchars($_POST["text"] ?? "") ?></textarea>
                    <div class="char-counter">
                        <span id="charCount">0</span> characters
                    </div>
                </div>
            </div>

            <div class="button-group">
                <button type="submit" id="translateBtn">
                    <span>Translate</span>
                </button>
                <button type="reset" class="secondary" id="clearBtn" onclick="clearForm()">
                    Clear
                </button>
            </div>
        </form>

        <?php if ($error): ?>
            <div class="error" id="errorBox">
                <p><?= htmlspecialchars($error) ?></p>
            </div>
        <?php endif; ?>

        <?php if ($result): ?>
            <div class="result-box" id="resultBox">
                <h3>Translation Result</h3>
                <p id="resultText"><?= htmlspecialchars($result) ?></p>
                <button class="copy-btn" onclick="copyToClipboard()">
                    Copy
                </button>
            </div>
        <?php endif; ?>

        <div class="footer">
            REST Client – PHP • Darija Translator Project
        </div>

    </div>
</div>

<script>
    // ===== Character Counter =====
    const textarea = document.getElementById('text');
    const charCount = document.getElementById('charCount');

    textarea.addEventListener('input', function() {
        charCount.textContent = this.value.length;
    });

    // Initialize counter
    charCount.textContent = textarea.value.length;

    // ===== Form Validation & Loading State =====
    const form = document.getElementById('translatorForm');
    const translateBtn = document.getElementById('translateBtn');

    form.addEventListener('submit', function(e) {
        const text = textarea.value.trim();
        
        if (text === '') {
            e.preventDefault();
            // Error will be shown by PHP
            return false;
        }

        // Show loading state
        translateBtn.disabled = true;
        translateBtn.innerHTML = '<span class="spinner"></span>Translating...';
    });

    // ===== Clear Form =====
    function clearForm() {
        textarea.value = '';
        charCount.textContent = '0';
        const resultBox = document.getElementById('resultBox');
        const errorBox = document.getElementById('errorBox');
        
        if (resultBox) resultBox.classList.add('hidden');
        if (errorBox) errorBox.classList.add('hidden');
        
        textarea.focus();
    }

    // ===== Copy to Clipboard =====
    function copyToClipboard() {
        const resultText = document.getElementById('resultText');
        const copyBtn = event.target.closest('.copy-btn');
        
        if (!resultText) return;

        const text = resultText.textContent;
        
        navigator.clipboard.writeText(text).then(() => {
            const originalText = copyBtn.textContent;
            copyBtn.textContent = '✓ Copied!';
            copyBtn.style.background = '#4caf50';
            
            setTimeout(() => {
                copyBtn.textContent = originalText;
                copyBtn.style.background = '#667eea';
            }, 2000);
        }).catch(err => {
            console.error('Failed to copy:', err);
        });
    }

    // ===== Auto-focus on page load with text =====
    window.addEventListener('load', function() {
        if (textarea.value.length > 0) {
            textarea.focus();
            textarea.setSelectionRange(textarea.value.length, textarea.value.length);
        }
    });

    // ===== Reset button state after page load =====
    window.addEventListener('load', function() {
        translateBtn.disabled = false;
        translateBtn.innerHTML = 'Translate';
    });

    // ===== Auto-dismiss error after 5 seconds (optional) =====
    const errorBox = document.getElementById('errorBox');
    if (errorBox) {
        setTimeout(() => {
            errorBox.style.animation = 'slideUp 0.5s ease-out reverse';
            setTimeout(() => {
                errorBox.classList.add('hidden');
            }, 500);
        }, 5000);
    }
</script>

</body>
</html>