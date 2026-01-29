# 🇲🇦 Darija Translator – English to Moroccan Darija REST API

A secure LLM-powered RESTful web service that translates English text into real Moroccan Darija, built with Jakarta EE, JAX-RS, and deployed using Payara Micro.

The system is connected to real client applications, including a PHP web client and a Chrome Extension (Manifest V3).

This project was developed as a Master-level academic project, with a strong focus on REST architecture, security, reusability, and practical usability.

---

## 🎬 Video Demonstration (Start Here)

👉 **Watch the full live demo of the project here:**

[![Darija Translator Demo](https://img.youtube.com/vi/A7HMsDoc5dI/maxresdefault.jpg)](https://youtu.be/A7HMsDoc5dI)

**This video demonstrates:**
- the REST API running on Payara Micro
- authentication using Jakarta Security (Basic Auth)
- live API tests with Postman
- the PHP client in action
- the Chrome Extension (side panel, text selection, voice input, and text-to-speech)

⏱️ **Duration:** ~5 minutes  
🎯 **Purpose:** Live academic demonstration of the complete system

---

## 📑 Table of Contents

- [Features](#-features)
- [Architecture](#️-architecture)
- [Technologies Used](#️-technologies-used)
- [Project Structure](#-project-structure)
- [Quick Start](#-quick-start)
- [API Documentation](#-api-documentation)
- [Client Applications](#-client-applications)
- [Security](#-security)
- [Testing](#-testing)
- [Deployment](#-deployment)
- [Extensions & Future Work](#-extensions--future-work)

---

## ✨ Features

### Core Features
- ✅ RESTful API implemented with JAX-RS
- ✅ LLM-powered translation (English → Moroccan Darija)
- ✅ HTTP Basic Authentication using Jakarta Security
- ✅ Payara Micro (Jakarta EE embedded server)
- ✅ Health check endpoint
- ✅ Environment-based API key configuration

### Client Applications
- ✅ PHP Web Client using cURL
- ✅ Chrome Extension (Manifest V3) with side panel
- ✅ API testing via Postman, SoapUI, and cURL
- ✅ Secure and authenticated REST communication

### Advanced Capabilities
- ✅ Prompt engineering for high-quality Darija output
- ✅ Voice input (Chrome Extension)
- ✅ Text-to-Speech output (Chrome Extension)
- ✅ Automatic text selection from web pages

---

## 🏗️ Architecture
```
┌──────────────────────────────────────────┐
│               Client Layer               │
├──────────────┬──────────────┬───────────┤
│ Chrome Ext.  │  PHP Client  │  Postman  │
└──────────────┴──────────────┴───────────┘
                     │
                     ▼
┌──────────────────────────────────────────┐
│        Jakarta Security (Basic Auth)     │
└──────────────────────────────────────────┘
                     ▼
┌──────────────────────────────────────────┐
│          JAX-RS REST API Layer           │
│      /translator/health /translate       │
└──────────────────────────────────────────┘
                     ▼
┌──────────────────────────────────────────┐
│        Business Logic & Prompting        │
│            TranslatorResource            │
└──────────────────────────────────────────┘
                     ▼
┌──────────────────────────────────────────┐
│       External LLM API (OpenRouter)      │
│        Mistral 7B Instruct Model         │
└──────────────────────────────────────────┘
```

---

## 🛠️ Technologies Used

### Backend
- Java 11
- Jakarta EE 10
- JAX-RS
- Jakarta Security
- Payara Micro
- Maven

### Clients
- PHP (cURL)
- JavaScript (ES6)
- HTML / CSS
- Chrome Extension API (Manifest V3)

### External Services
- OpenRouter API
- Mistral 7B Instruct (LLM)

---

## 📁 Project Structure
```
darija-translator/
│
├── src/main/java/com/darija/translator/
│   ├── TranslatorResource.java
│   ├── ApplicationConfig.java
│   │
│   ├── security/
│   │   ├── SecurityConfig.java
│   │   └── InMemoryIdentityStore.java
│   │
│   └── features/
│       └── ExtendedFeaturesResource.java
│
├── src/main/webapp/WEB-INF/
│   └── web.xml
│
├── clients/
│   ├── php/
│   │   └── translator_client.php
│   │
│   └── chrome-extension/
│       ├── manifest.json
│       ├── background.js
│       ├── sidepanel.html
│       ├── sidepanel.js
│       └── icons/
│
├── tests/
│   ├── postman/
│   ├── soapui/
│   └── curl_tests.sh
│
├── docs/
│   ├── API_DOCUMENTATION.md
│   ├── DEPLOYMENT_GUIDE.md
│   └── TESTING_GUIDE.md
│
├── pom.xml
└── README.md
```

---

## 🚀 Quick Start

### 1. Prerequisites
```bash
java -version     # Java 11+
mvn -version
```

### 2. Configure Environment
```bash
export OPENROUTER_API_KEY="your_openrouter_api_key"
```

### 3. Build the Project
```bash
mvn clean package
```

### 4. Run with Payara Micro
```bash
java -jar ~/.m2/repository/fish/payara/extras/payara-micro/6.2024.2/payara-micro-6.2024.2.jar \
--deploy target/darija-translator-1.0.0.war
```

---

## 📚 API Documentation

### Base URL
```
http://localhost:8080/darija-translator-1.0.0/translator
```

### Authentication
- **Type:** HTTP Basic Authentication
- **Username:** `admin`
- **Password:** `admin123`

---

### 🔹 Health Check

**GET** `/health`

**Response:**
```
Darija Translator API is running
```

---

### 🔹 Translate Text

**POST** `/translate`

**Headers:**
```
Content-Type: text/plain
Authorization: Basic base64(admin:admin123)
```

**Request Body:**
```
Hello how are you?
```

**Response:**
```
كيف داير؟
```

---

### Error Responses
- **401 Unauthorized** – Missing or invalid credentials
- **400 Bad Request** – Empty input
- **500 Internal Server Error** – LLM or server error

---

## 💻 Client Applications

### PHP Client
```bash
cd clients/php
php -S localhost:8000
```

**Open:** 👉 [http://localhost:8000/translator_client.php](http://localhost:8000/translator_client.php)

✔ Simple UI  
✔ Uses cURL  
✔ Authenticated REST calls

---

### Chrome Extension

#### Installation
1. Open `chrome://extensions`
2. Enable **Developer Mode**
3. Click **Load unpacked**
4. Select `clients/chrome-extension`

#### Features
- Side panel UI
- Automatic text selection
- Manual input
- Voice input (Speech Recognition)
- Text-to-Speech output
- Secure REST communication

---

## 🔒 Security

- Implemented using **Jakarta Security**
- **HTTP Basic Authentication**
- In-memory **IdentityStore**
- No external database required
- Fully portable with Payara Micro

**Why this approach?**

To ensure portability and simplicity with Payara Micro, authentication is implemented programmatically using Jakarta Security instead of server-specific file realms.

---

## 🧪 Testing

- ✅ Postman
- ✅ SoapUI
- ✅ cURL

**Example cURL command:**
```bash
curl -u admin:admin123 \
-X POST http://localhost:8080/darija-translator-1.0.0/translator/translate \
-H "Content-Type: text/plain" \
-d "Good morning"
```

---

## 🌐 Deployment

- **Development:** Payara Micro (embedded)
- Portable WAR packaging
- Compatible with any Jakarta EE–compliant server

---

## 🚀 Extensions & Future Work

- 🔊 Improved Text-to-Speech
- 🎤 Enhanced voice recognition
- 🌍 Multi-language support
- ⚡ Caching and rate limiting
- 📱 Mobile client

---

## 👤 Author

**Mohammed Ben Akka Ouayad**  
Master WISD – Web Intelligence & Data Science

---

## 📌 Project Status

- ✅ REST API
- ✅ LLM integration
- ✅ Jakarta Security
- ✅ PHP client
- ✅ Chrome extension
- ✅ Full testing
- ✅ Documentation

---

## 🇲🇦 Darija Translator – Practical, Secure, and Ready for Extension
