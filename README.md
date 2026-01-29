# 🇲🇦 Darija Translator - English to Moroccan Arabic REST API

A comprehensive LLM-powered RESTful web service for translating English text to Moroccan Arabic Dialect (Darija) using Google Gemini API, built with Jakarta EE and JAX-RS.

## 📑 Table of Contents
- [Features](#features)
- [Architecture](#architecture)
- [Technologies Used](#technologies-used)
- [Project Structure](#project-structure)
- [Quick Start](#quick-start)
- [API Documentation](#api-documentation)
- [Client Applications](#client-applications)
- [Security](#security)
- [Testing](#testing)
- [Deployment](#deployment)
- [Extensions](#extensions)
- [Contributing](#contributing)

---

## ✨ Features

### Core Features
- ✅ **RESTful API** with JAX-RS implementation
- ✅ **LLM-Powered Translation** using Google Gemini 1.5 Flash
- ✅ **Basic Authentication** with Jakarta Security
- ✅ **Batch Translation** for multiple texts
- ✅ **CORS Support** for cross-origin requests
- ✅ **Health Check** endpoint

### Client Applications
- ✅ **PHP Web Client** with beautiful UI
- ✅ **Chrome Extension** (Manifest V3) with side panel
- ✅ **Postman/SoapUI** test collections
- ✅ **cURL Examples** for CLI testing

### Extended Features
- ✅ **Embedded Server** using Grizzly HTTP
- ✅ **Voice Translation** support (extensible)
- ✅ **Text-to-Speech** capability (extensible)
- ✅ **Language Detection**
- ✅ **Stream Translation** for real-time use

---

## 🏗️ Architecture

```
┌─────────────────────────────────────────────────────────┐
│                     Client Layer                        │
├──────────────┬──────────────┬──────────────┬───────────┤
│ Chrome Ext.  │  PHP Client  │   Postman    │   cURL    │
└──────────────┴──────────────┴──────────────┴───────────┘
                            ▼
┌─────────────────────────────────────────────────────────┐
│                  API Gateway / CORS                     │
│              (Basic Authentication)                     │
└─────────────────────────────────────────────────────────┘
                            ▼
┌─────────────────────────────────────────────────────────┐
│              JAX-RS REST Endpoints                      │
│  ┌──────────────┬──────────────┬──────────────┐        │
│  │  /translate  │  /batch      │  /health     │        │
│  └──────────────┴──────────────┴──────────────┘        │
└─────────────────────────────────────────────────────────┘
                            ▼
┌─────────────────────────────────────────────────────────┐
│              Business Logic Layer                       │
│         TranslatorResource + Extensions                 │
└─────────────────────────────────────────────────────────┘
                            ▼
┌─────────────────────────────────────────────────────────┐
│              External API Integration                   │
│               Google Gemini 1.5 Flash                   │
└─────────────────────────────────────────────────────────┘
```

---

## 🛠️ Technologies Used

### Backend
- **Java 17** - Programming language
- **Jakarta EE 10** - Enterprise Java platform
- **JAX-RS 3.1** - RESTful web services
- **Jakarta Security** - Authentication & authorization
- **Jersey 3.1** - JAX-RS implementation
- **Grizzly HTTP Server** - Embedded server
- **Maven** - Build tool

### Frontend
- **PHP 7.4+** - Web client
- **JavaScript (ES6+)** - Chrome extension
- **HTML5/CSS3** - User interfaces
- **Chrome Extension API** - Browser integration

### External Services
- **Google Gemini API** - LLM for translation
- **Google Cloud TTS** (optional) - Text-to-speech
- **Whisper API** (optional) - Speech-to-text

---

## 📁 Project Structure

```
darija-translator/
│
├── src/main/java/com/darija/translator/
│   ├── TranslatorResource.java          # Main REST endpoint
│   ├── ApplicationConfig.java           # JAX-RS configuration
│   ├── EmbeddedServer.java             # Standalone server
│   │
│   ├── security/
│   │   └── SecurityConfiguration.java   # Auth configuration
│   │
│   └── features/
│       └── ExtendedFeaturesResource.java # Voice, TTS, etc.
│
├── clients/
│   ├── php/
│   │   └── translator_client.php        # PHP web client
│   │
│   └── chrome-extension/
│       ├── manifest.json                # Extension manifest
│       ├── background.js                # Service worker
│       ├── sidepanel.html              # UI
│       ├── sidepanel.js                # Logic
│       └── icons/                      # Extension icons
│
├── tests/
│   ├── postman/                        # Postman collection
│   ├── soapui/                         # SoapUI project
│   └── curl_tests.sh                   # cURL test scripts
│
├── docs/
│   ├── API_DOCUMENTATION.md
│   ├── DEPLOYMENT_GUIDE.md
│   └── TESTING_GUIDE.md
│
├── pom.xml                             # Maven configuration
└── README.md                           # This file
```

---

## 🚀 Quick Start

### 1. Prerequisites
```bash
# Install Java 17
java -version  # Should show version 17+

# Install Maven
mvn -version

# Get Gemini API Key from https://ai.google.dev/
```

### 2. Clone & Configure
```bash
# Clone repository (or create project structure)
git clone <repository-url>
cd darija-translator

# Set API key
export GEMINI_API_KEY="your_gemini_api_key_here"
```

### 3. Build & Run
```bash
# Build project
mvn clean package

# Run embedded server
mvn exec:java -Dexec.mainClass="com.darija.translator.EmbeddedServer"

# Or run standalone
java -jar target/darija-translator.jar
```

### 4. Test
```bash
# Health check
curl http://localhost:8080/darija-translator/api/translator/health

# Translate
curl -u testuser:password123 \
  -X POST http://localhost:8080/darija-translator/api/translator/translate \
  -H "Content-Type: application/json" \
  -d '{"text":"Hello, how are you?"}'
```

---

## 📚 API Documentation

### Base URL
```
http://localhost:8080/darija-translator/api/translator
```

### Authentication
All endpoints (except `/health`) require Basic Authentication:
- **Username:** `testuser`
- **Password:** `password123`

### Endpoints

#### 1. Health Check
```http
GET /health
```

**Response:**
```json
{
  "status": "OK",
  "message": "Translation service is running"
}
```

---

#### 2. Single Translation
```http
POST /translate
Content-Type: application/json
Authorization: Basic dGVzdHVzZXI6cGFzc3dvcmQxMjM=
```

**Request Body:**
```json
{
  "text": "Hello, how are you?"
}
```

**Response:**
```json
{
  "sourceText": "Hello, how are you?",
  "translatedText": "السلام، كيف داير؟",
  "sourceLang": "en",
  "targetLang": "ary",
  "timestamp": 1234567890123
}
```

---

#### 3. Batch Translation
```http
POST /translate/batch
Content-Type: application/json
Authorization: Basic dGVzdHVzZXI6cGFzc3dvcmQxMjM=
```

**Request Body:**
```json
{
  "texts": [
    "Good morning",
    "Thank you",
    "See you later"
  ]
}
```

**Response:**
```json
{
  "translations": [
    {
      "sourceText": "Good morning",
      "translatedText": "صباح الخير"
    },
    {
      "sourceText": "Thank you",
      "translatedText": "شكرا"
    },
    {
      "sourceText": "See you later",
      "translatedText": "نشوفك من بعد"
    }
  ]
}
```

---

### Error Responses

#### 400 Bad Request
```json
{
  "error": "Text cannot be empty",
  "timestamp": 1234567890123
}
```

#### 401 Unauthorized
```json
{
  "error": "Authentication required",
  "timestamp": 1234567890123
}
```

#### 500 Internal Server Error
```json
{
  "error": "Translation failed: API error",
  "timestamp": 1234567890123
}
```

---

## 💻 Client Applications

### 1. PHP Web Client

**Setup:**
```bash
# Start PHP server
php -S localhost:8000 clients/php/translator_client.php
```

**Access:** `http://localhost:8000`

**Features:**
- Beautiful web interface
- Form-based translation
- Batch processing
- Error handling

---

### 2. Chrome Extension

**Installation:**
1. Open `chrome://extensions/`
2. Enable "Developer mode"
3. Click "Load unpacked"
4. Select `clients/chrome-extension/` folder

**Features:**
- Side panel interface
- Context menu integration
- Text selection translation
- Keyboard shortcut (Ctrl+Shift+D)

**Usage:**
- Click extension icon
- Right-click selected text → "Translate to Darija"
- Use keyboard shortcut

---

### 3. cURL Examples

See `tests/curl_tests.sh` for comprehensive examples:

```bash
# Basic translation
curl -u testuser:password123 \
  -X POST http://localhost:8080/darija-translator/api/translator/translate \
  -H "Content-Type: application/json" \
  -d '{"text":"Good morning"}'

# Batch translation
curl -u testuser:password123 \
  -X POST http://localhost:8080/darija-translator/api/translator/translate/batch \
  -H "Content-Type: application/json" \
  -d '{"texts":["Hello","Goodbye","Thank you"]}'
```

---

## 🔒 Security

### Authentication Methods

#### Basic Authentication (Implemented)
```java
@BasicAuthenticationMechanismDefinition(realmName = "DarijaTranslator")
```

**Usage:**
```bash
# With username:password
curl -u testuser:password123 <url>

# With header
curl -H "Authorization: Basic dGVzdHVzZXI6cGFzc3dvcmQxMjM=" <url>
```

### Security Best Practices

1. **Change Default Credentials** in production
2. **Use HTTPS** for encrypted communication
3. **Store API Keys** securely (environment variables)
4. **Implement Rate Limiting** to prevent abuse
5. **Enable CORS** only for trusted domains
6. **Log Security Events** for auditing

### Production Configuration

```java
// Recommended: Use database identity store
@DatabaseIdentityStoreDefinition(
    dataSourceLookup = "java:comp/DefaultDataSource",
    callerQuery = "SELECT password FROM users WHERE username = ?",
    groupsQuery = "SELECT role FROM user_roles WHERE username = ?",
    hashAlgorithm = Pbkdf2PasswordHash.class
)
```

---

## 🧪 Testing

### Testing Tools
- ✅ **Postman** - API testing & automation
- ✅ **SoapUI** - REST/SOAP testing
- ✅ **cURL** - Command-line testing
- ✅ **JUnit** - Unit tests (add as needed)

### Test Collections

Import test collections from:
- `tests/postman/DarijaTranslator.postman_collection.json`
- `tests/soapui/DarijaTranslator-soapui-project.xml`

### Running Tests

```bash
# Run cURL test suite
bash tests/curl_tests.sh

# Run with Maven
mvn test

# Manual testing with Postman
# Import collection and run
```

See **TESTING_GUIDE.md** for detailed testing instructions.

---

## 🌐 Deployment

### Deployment Options

1. **Embedded Server** (Development)
   ```bash
   java -jar target/darija-translator.jar
   ```

2. **Apache Tomcat** (Production)
   ```bash
   cp target/darija-translator.war $CATALINA_HOME/webapps/
   ```

3. **WildFly** (Enterprise)
   ```bash
   ./bin/jboss-cli.sh --connect
   deploy /path/to/darija-translator.war
   ```

4. **Docker** (Containerized)
   ```bash
   docker build -t darija-translator .
   docker run -p 8080:8080 -e GEMINI_API_KEY="key" darija-translator
   ```

See **DEPLOYMENT_GUIDE.md** for detailed deployment instructions.

---

## 🎯 Extensions

### Implemented Extensions

1. **Voice Translation** (`/features/voice-translate`)
   - Speech-to-text transcription
   - Translation
   - Text-to-speech output

2. **Text-to-Speech** (`/features/text-to-speech`)
   - Convert Darija text to audio
   - Multiple voice options

3. **Stream Translation** (`/features/stream-translate`)
   - Real-time translation
   - Chunk processing

4. **Language Detection** (`/features/detect-language`)
   - Auto-detect English vs Darija

### Future Enhancements

- [ ] Local LLM deployment (Ollama, LLaMA)
- [ ] Caching layer (Redis)
- [ ] Rate limiting
- [ ] API analytics
- [ ] Mobile app clients
- [ ] WebSocket support for real-time
- [ ] Multi-language support

---

## 📝 Configuration

### Environment Variables

```bash
# Required
GEMINI_API_KEY=your_api_key_here

# Optional
SERVER_PORT=8080
SERVER_HOST=0.0.0.0
CONTEXT_PATH=/darija-translator
```

### Application Properties

Create `application.properties`:
```properties
# Server Configuration
server.port=8080
server.host=0.0.0.0

# API Configuration
gemini.api.url=https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent
gemini.api.key=${GEMINI_API_KEY}

# Security
security.basic.enabled=true
security.user.name=testuser
security.user.password=password123
```

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Development Guidelines

- Follow Java naming conventions
- Add unit tests for new features
- Update documentation
- Test all endpoints before submitting
- Keep commits atomic and descriptive

---

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

---

## 👥 Authors

- **Your Name** - Initial work

---

## 🙏 Acknowledgments

- Google Gemini API for LLM capabilities
- Jakarta EE community
- Jersey framework team
- All contributors

---

## 📞 Support

For issues, questions, or contributions:
- Open an issue on GitHub
- Contact: your.email@example.com
- Documentation: See `/docs` folder

---

## 📊 Project Status

- ✅ Core API implementation
- ✅ Basic authentication
- ✅ PHP client
- ✅ Chrome extension
- ✅ Testing suite
- ✅ Documentation
- 🚧 Production deployment
- 🚧 Advanced features

---

**Happy Translating! 🇲🇦**