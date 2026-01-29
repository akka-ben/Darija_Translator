# Darija Translator - Complete Deployment Guide

## 📋 Table of Contents
1. [Prerequisites](#prerequisites)
2. [Getting Gemini API Key](#getting-gemini-api-key)
3. [Building the Project](#building-the-project)
4. [Deployment Options](#deployment-options)
5. [Chrome Extension Installation](#chrome-extension-installation)
6. [PHP Client Setup](#php-client-setup)
7. [Testing](#testing)
8. [Troubleshooting](#troubleshooting)

---

## 1. Prerequisites

### Required Software
- **Java Development Kit (JDK)** 17 or higher
- **Apache Maven** 3.8+
- **Application Server** (choose one):
  - Apache Tomcat 10.1+
  - WildFly 27+
  - GlassFish 7+
  - Or run embedded server (included)
- **PHP** 7.4+ (for PHP client)
- **Google Chrome** browser (for Chrome extension)

### Installation Commands

#### Ubuntu/Debian
```bash
# Install Java 17
sudo apt update
sudo apt install openjdk-17-jdk

# Install Maven
sudo apt install maven

# Install PHP
sudo apt install php php-curl php-json

# Verify installations
java -version
mvn -version
php -version
```

#### macOS
```bash
# Install using Homebrew
brew install openjdk@17
brew install maven
brew install php

# Set JAVA_HOME
export JAVA_HOME=$(/usr/libexec/java_home -v 17)
```

#### Windows
```powershell
# Install using Chocolatey
choco install openjdk17
choco install maven
choco install php

# Or download from official websites:
# Java: https://adoptium.net/
# Maven: https://maven.apache.org/download.cgi
# PHP: https://windows.php.net/download/
```

---

## 2. Getting Gemini API Key

### Step 1: Visit Google AI Studio
Go to: https://ai.google.dev/

### Step 2: Sign in with Google Account
Click "Get API Key" or "Sign in"

### Step 3: Create New API Key
1. Click "Get API key"
2. Select "Create API key in new project" or use existing project
3. Copy your API key (format: `AIza...`)

### Step 4: Set Environment Variable

#### Linux/macOS
```bash
# Temporary (current session)
export GEMINI_API_KEY="your_api_key_here"

# Permanent (add to ~/.bashrc or ~/.zshrc)
echo 'export GEMINI_API_KEY="your_api_key_here"' >> ~/.bashrc
source ~/.bashrc
```

#### Windows (PowerShell)
```powershell
# Temporary
$env:GEMINI_API_KEY="your_api_key_here"

# Permanent (System Environment Variable)
[System.Environment]::SetEnvironmentVariable('GEMINI_API_KEY', 'your_api_key_here', 'User')
```

#### Windows (Command Prompt)
```cmd
# Temporary
set GEMINI_API_KEY=your_api_key_here

# Permanent
setx GEMINI_API_KEY "your_api_key_here"
```

---

## 3. Building the Project

### Project Structure
```
darija-translator/
├── src/
│   └── main/
│       ├── java/
│       │   └── com/
│       │       └── darija/
│       │           └── translator/
│       │               ├── TranslatorResource.java
│       │               ├── ApplicationConfig.java
│       │               ├── EmbeddedServer.java
│       │               ├── security/
│       │               │   └── SecurityConfiguration.java
│       │               └── features/
│       │                   └── ExtendedFeaturesResource.java
│       └── webapp/
│           └── WEB-INF/
│               └── web.xml (optional)
├── pom.xml
└── README.md
```

### Build Commands

```bash
# Navigate to project directory
cd darija-translator

# Clean and build
mvn clean package

# This creates: target/darija-translator.war
```

### Build Output
- **WAR file**: `target/darija-translator.war` (for deployment to app servers)
- **JAR file**: If configured for embedded server

---

## 4. Deployment Options

### Option A: Embedded Server (Recommended for Development)

#### Run the Embedded Server
```bash
# Set API key
export GEMINI_API_KEY="your_api_key_here"

# Compile and run
mvn clean compile
mvn exec:java -Dexec.mainClass="com.darija.translator.EmbeddedServer"

# Or build JAR and run
mvn clean package
java -jar target/darija-translator.jar
```

#### Custom Configuration
```bash
# Run on custom port
java -jar target/darija-translator.jar --port 9090

# Run with custom host
java -jar target/darija-translator.jar --host localhost --port 8888
```

**Server will be available at:** `http://localhost:8080/darija-translator/`

---

### Option B: Apache Tomcat Deployment

#### Step 1: Download Tomcat
```bash
# Download Tomcat 10.1
wget https://dlcdn.apache.org/tomcat/tomcat-10/v10.1.17/bin/apache-tomcat-10.1.17.tar.gz
tar -xzf apache-tomcat-10.1.17.tar.gz
cd apache-tomcat-10.1.17
```

#### Step 2: Configure Users
Edit `conf/tomcat-users.xml`:
```xml
<tomcat-users>
  <role rolename="manager-gui"/>
  <role rolename="manager-script"/>
  <user username="admin" password="admin123" roles="manager-gui,manager-script"/>
</tomcat-users>
```

#### Step 3: Deploy WAR File
```bash
# Copy WAR to webapps directory
cp /path/to/darija-translator.war $CATALINA_HOME/webapps/

# Or use Tomcat Manager (http://localhost:8080/manager/html)
```

#### Step 4: Set Environment Variables
Edit `bin/setenv.sh` (create if doesn't exist):
```bash
#!/bin/bash
export GEMINI_API_KEY="your_api_key_here"
export JAVA_OPTS="$JAVA_OPTS -Xmx512m"
```

#### Step 5: Start Tomcat
```bash
./bin/startup.sh

# Check logs
tail -f logs/catalina.out
```

**Access at:** `http://localhost:8080/darija-translator/`

---

### Option C: WildFly Deployment

#### Step 1: Download WildFly
```bash
wget https://github.com/wildfly/wildfly/releases/download/27.0.1.Final/wildfly-27.0.1.Final.zip
unzip wildfly-27.0.1.Final.zip
cd wildfly-27.0.1.Final
```

#### Step 2: Add Admin User
```bash
./bin/add-user.sh
# Follow prompts to create management user
```

#### Step 3: Start WildFly
```bash
export GEMINI_API_KEY="your_api_key_here"
./bin/standalone.sh
```

#### Step 4: Deploy WAR
```bash
# Copy to deployments directory
cp /path/to/darija-translator.war standalone/deployments/

# Or use CLI
./bin/jboss-cli.sh --connect
deploy /path/to/darija-translator.war
```

**Access at:** `http://localhost:8080/darija-translator/`

---

### Option D: Docker Deployment

#### Create Dockerfile
```dockerfile
FROM tomcat:10.1-jdk17

# Set environment variable
ENV GEMINI_API_KEY=""

# Copy WAR file
COPY target/darija-translator.war /usr/local/tomcat/webapps/

# Expose port
EXPOSE 8080

# Run Tomcat
CMD ["catalina.sh", "run"]
```

#### Build and Run
```bash
# Build Docker image
docker build -t darija-translator .

# Run container
docker run -d -p 8080:8080 \
  -e GEMINI_API_KEY="your_api_key_here" \
  --name darija-translator \
  darija-translator

# Check logs
docker logs -f darija-translator
```

---

## 5. Chrome Extension Installation

### Prepare Extension Files

Create directory structure:
```
darija-translator-extension/
├── manifest.json
├── background.js
├── sidepanel.html
├── sidepanel.js
└── icons/
    ├── icon16.png
    ├── icon32.png
    ├── icon48.png
    └── icon128.png
```

### Create Icon Files
Use an online tool or image editor to create icons in required sizes.

### Load Extension in Chrome

#### Step 1: Open Extensions Page
1. Open Chrome
2. Go to: `chrome://extensions/`
3. Enable "Developer mode" (toggle in top-right)

#### Step 2: Load Unpacked Extension
1. Click "Load unpacked"
2. Select the `darija-translator-extension` folder
3. Extension will appear in the list

#### Step 3: Pin Extension
1. Click the puzzle icon in Chrome toolbar
2. Find "Darija Translator"
3. Click the pin icon

### Configure Extension

#### Step 4: Set API Credentials
1. Click the extension icon
2. Side panel opens
3. Click "⚙️ Settings"
4. Enter:
   - **API URL**: `http://localhost:8080/darija-translator`
   - **Username**: `testuser`
   - **Password**: `password123`
5. Click "Save Settings"

### Test Extension
1. Select text on any webpage
2. Right-click → "Translate to Darija"
3. Or click extension icon and enter text manually

---

## 6. PHP Client Setup

### Option A: Web Server Setup

#### Apache Configuration
```bash
# Ubuntu/Debian
sudo apt install apache2 php libapache2-mod-php

# Copy PHP file
sudo cp translator_client.php /var/www/html/

# Set permissions
sudo chmod 644 /var/www/html/translator_client.php

# Restart Apache
sudo systemctl restart apache2
```

**Access at:** `http://localhost/translator_client.php`

#### Nginx + PHP-FPM
```bash
# Install
sudo apt install nginx php-fpm

# Configure Nginx
sudo nano /etc/nginx/sites-available/default
```

Add:
```nginx
location ~ \.php$ {
    include snippets/fastcgi-php.conf;
    fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
}
```

```bash
# Restart
sudo systemctl restart nginx php8.1-fpm
```

### Option B: Built-in PHP Server

```bash
# Navigate to PHP client directory
cd /path/to/php-client

# Start PHP development server
php -S localhost:8000 translator_client.php

# Access at: http://localhost:8000
```

### Test PHP Client

#### Command Line Test
```bash
php translator_client.php
```

#### Browser Test
1. Open `http://localhost:8000`
2. Fill in form:
   - API URL: `http://localhost:8080/darija-translator`
   - Username: `testuser`
   - Password: `password123`
   - English Text: `Hello, how are you?`
3. Click "Translate to Darija"

---

## 7. Testing

### Quick Health Check
```bash
curl http://localhost:8080/darija-translator/api/translator/health
```

Expected response:
```json
{
  "status": "OK",
  "message": "Translation service is running"
}
```

### Test Translation
```bash
curl -u testuser:password123 \
  -X POST http://localhost:8080/darija-translator/api/translator/translate \
  -H "Content-Type: application/json" \
  -d '{"text":"Good morning"}'
```

See **Testing Guide** artifact for comprehensive testing instructions.

---

## 8. Troubleshooting

### Common Issues

#### Issue 1: Port Already in Use
```
Error: Address already in use: bind
```

**Solution:**
```bash
# Find process using port 8080
lsof -i :8080  # Linux/Mac
netstat -ano | findstr :8080  # Windows

# Kill process
kill -9 <PID>  # Linux/Mac
taskkill /PID <PID> /F  # Windows

# Or use different port
java -jar app.jar --port 9090
```

#### Issue 2: GEMINI_API_KEY Not Set
```
WARNING: GEMINI_API_KEY environment variable not set!
```

**Solution:**
```bash
export GEMINI_API_KEY="your_key_here"
```

#### Issue 3: 401 Unauthorized
**Solution:** Check credentials:
- Username: `testuser`
- Password: `password123`
- Basic Auth header is correct

#### Issue 4: CORS Errors
**Solution:** Ensure `CorsFilter` is registered in `ApplicationConfig`

#### Issue 5: Chrome Extension Not Loading
**Solution:**
1. Check manifest.json syntax
2. Ensure all file paths are correct
3. Check Chrome Developer Console for errors
4. Reload extension after changes

### Enable Debug Logging

#### Java Application
Add to `logging.properties`:
```properties
.level=INFO
com.darija.translator.level=FINE
```

#### Chrome Extension
```javascript
// In background.js and sidepanel.js
console.log('Debug info:', variable);
```

Check Chrome DevTools Console:
1. Right-click extension icon
2. "Inspect popup" or check background page

---

## 9. Production Deployment Checklist

- [ ] Change default credentials
- [ ] Enable HTTPS/SSL
- [ ] Configure proper logging
- [ ] Set up monitoring
- [ ] Configure firewall rules
- [ ] Set resource limits
- [ ] Configure backup strategy
- [ ] Enable rate limiting
- [ ] Update API URL in Chrome extension
- [ ] Test all endpoints
- [ ] Review security settings
- [ ] Document API for users

---

## 10. Useful Commands Reference

```bash
# Build project
mvn clean package

# Run embedded server
mvn exec:java -Dexec.mainClass="com.darija.translator.EmbeddedServer"

# Run tests
mvn test

# Check API health
curl http://localhost:8080/darija-translator/api/translator/health

# Translate text
curl -u testuser:password123 \
  -X POST http://localhost:8080/darija-translator/api/translator/translate \
  -H "Content-Type: application/json" \
  -d '{"text":"Your text here"}'

# Start PHP server
php -S localhost:8000 translator_client.php

# View logs (Tomcat)
tail -f $CATALINA_HOME/logs/catalina.out

# View logs (Docker)
docker logs -f darija-translator
```

---

## Support

For issues and questions:
- Check application logs
- Review error messages carefully
- Ensure all prerequisites are installed
- Verify API key is valid
- Test with simple examples first