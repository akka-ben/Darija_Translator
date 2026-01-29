# Testing Guide for Darija Translator REST API

## Prerequisites
- API running at: `http://localhost:8080/darija-translator`
- Username: `testuser`
- Password: `password123`

## 1. Testing with cURL

### Health Check
```bash
curl -X GET http://localhost:8080/darija-translator/api/translator/health
```

### Single Translation
```bash
curl -X POST http://localhost:8080/darija-translator/api/translator/translate \
  -H "Content-Type: application/json" \
  -H "Authorization: Basic dGVzdHVzZXI6cGFzc3dvcmQxMjM=" \
  -d '{"text":"Hello, how are you?"}'
```

### Batch Translation
```bash
curl -X POST http://localhost:8080/darija-translator/api/translator/translate/batch \
  -H "Content-Type: application/json" \
  -H "Authorization: Basic dGVzdHVzZXI6cGFzc3dvcmQxMjM=" \
  -d '{"texts":["Good morning","Thank you","See you later"]}'
```

### Generate Basic Auth Header
```bash
# For username "testuser" and password "password123"
echo -n "testuser:password123" | base64
# Result: dGVzdHVzZXI6cGFzc3dvcmQxMjM=
```

---

## 2. Testing with Postman

### Step 1: Create New Request

1. Open Postman
2. Click "New" → "HTTP Request"
3. Name it "Darija Translator - Translate"

### Step 2: Configure Request

**Method:** POST
**URL:** `http://localhost:8080/darija-translator/api/translator/translate`

### Step 3: Set Authorization

1. Go to "Authorization" tab
2. Type: Select "Basic Auth"
3. Username: `testuser`
4. Password: `password123`

### Step 4: Set Headers

1. Go to "Headers" tab
2. Add header:
   - Key: `Content-Type`
   - Value: `application/json`

### Step 5: Set Body

1. Go to "Body" tab
2. Select "raw"
3. Select "JSON" from dropdown
4. Enter:
```json
{
  "text": "Hello, how are you?"
}
```

### Step 6: Send Request

Click "Send" button

### Expected Response (200 OK)
```json
{
  "sourceText": "Hello, how are you?",
  "translatedText": "السلام، كيف داير؟",
  "sourceLang": "en",
  "targetLang": "ary",
  "timestamp": 1234567890
}
```

### Postman Collection

Save these requests as a collection:

1. **Health Check**
   - GET `http://localhost:8080/darija-translator/api/translator/health`
   - No auth required

2. **Single Translation**
   - POST `http://localhost:8080/darija-translator/api/translator/translate`
   - Basic Auth
   - Body: `{"text": "Your text here"}`

3. **Batch Translation**
   - POST `http://localhost:8080/darija-translator/api/translator/translate/batch`
   - Basic Auth
   - Body: `{"texts": ["Text 1", "Text 2"]}`

---

## 3. Testing with SoapUI

### Step 1: Create New REST Project

1. Open SoapUI
2. File → New REST Project
3. Project Name: "Darija Translator"
4. Initial URI: `http://localhost:8080/darija-translator/api/translator`

### Step 2: Add Translation Endpoint

1. Right-click on project → New Resource
2. Resource Path: `/translate`
3. Method: POST

### Step 3: Configure Request

1. Select the POST method
2. In the request editor:

**Headers:**
```
Content-Type: application/json
```

**Auth:**
1. Click on "Auth" button (bottom left)
2. Select "Basic"
3. Username: `testuser`
4. Password: `password123`

**Body:**
```json
{
  "text": "Hello, how are you?"
}
```

### Step 4: Execute Request

Click the green "Play" button

### Step 5: Add Test Assertions

1. In response window, click "Assertions" tab
2. Add assertions:
   - **Valid HTTP Status Codes:** 200
   - **JsonPath Match:** `$.translatedText` exists
   - **Response Time:** Less than 5000ms

### SoapUI Test Suite

Create a test suite with these cases:

#### Test Case 1: Valid Translation
```
Endpoint: POST /translate
Auth: Basic (testuser:password123)
Body: {"text":"Good morning"}
Assert: Status 200, translatedText exists
```

#### Test Case 2: Empty Text
```
Endpoint: POST /translate
Auth: Basic (testuser:password123)
Body: {"text":""}
Assert: Status 400, error message exists
```

#### Test Case 3: No Authentication
```
Endpoint: POST /translate
Auth: None
Body: {"text":"Hello"}
Assert: Status 401
```

#### Test Case 4: Invalid Credentials
```
Endpoint: POST /translate
Auth: Basic (wrong:credentials)
Body: {"text":"Hello"}
Assert: Status 401
```

#### Test Case 5: Batch Translation
```
Endpoint: POST /translate/batch
Auth: Basic (testuser:password123)
Body: {"texts":["Hello","Goodbye"]}
Assert: Status 200, translations array length = 2
```

---

## 4. Common Test Scenarios

### 4.1 Authentication Tests

#### Valid Authentication
```bash
curl -u testuser:password123 \
  -X POST http://localhost:8080/darija-translator/api/translator/translate \
  -H "Content-Type: application/json" \
  -d '{"text":"test"}'
```
Expected: 200 OK

#### No Authentication
```bash
curl -X POST http://localhost:8080/darija-translator/api/translator/translate \
  -H "Content-Type: application/json" \
  -d '{"text":"test"}'
```
Expected: 401 Unauthorized

#### Invalid Credentials
```bash
curl -u wrong:credentials \
  -X POST http://localhost:8080/darija-translator/api/translator/translate \
  -H "Content-Type: application/json" \
  -d '{"text":"test"}'
```
Expected: 401 Unauthorized

### 4.2 Validation Tests

#### Empty Text
```bash
curl -u testuser:password123 \
  -X POST http://localhost:8080/darija-translator/api/translator/translate \
  -H "Content-Type: application/json" \
  -d '{"text":""}'
```
Expected: 400 Bad Request

#### Missing Text Field
```bash
curl -u testuser:password123 \
  -X POST http://localhost:8080/darija-translator/api/translator/translate \
  -H "Content-Type: application/json" \
  -d '{}'
```
Expected: 400 Bad Request

#### Large Text (Stress Test)
```bash
curl -u testuser:password123 \
  -X POST http://localhost:8080/darija-translator/api/translator/translate \
  -H "Content-Type: application/json" \
  -d '{"text":"'$(python3 -c 'print("word " * 1000)')'"}'
```

### 4.3 Performance Tests

#### Response Time Test
```bash
time curl -u testuser:password123 \
  -X POST http://localhost:8080/darija-translator/api/translator/translate \
  -H "Content-Type: application/json" \
  -d '{"text":"Hello world"}'
```

#### Concurrent Requests (using GNU parallel)
```bash
seq 10 | parallel -j 10 \
  'curl -u testuser:password123 \
   -X POST http://localhost:8080/darija-translator/api/translator/translate \
   -H "Content-Type: application/json" \
   -d "{\"text\":\"Request {}\"}"'
```

---

## 5. Automated Testing Script

### Bash Script for Automated Testing

```bash
#!/bin/bash

API_URL="http://localhost:8080/darija-translator/api/translator"
AUTH="testuser:password123"

echo "Starting API Tests..."

# Test 1: Health Check
echo "Test 1: Health Check"
curl -s -X GET "$API_URL/health" | jq .
echo ""

# Test 2: Valid Translation
echo "Test 2: Valid Translation"
curl -s -u $AUTH -X POST "$API_URL/translate" \
  -H "Content-Type: application/json" \
  -d '{"text":"Hello"}' | jq .
echo ""

# Test 3: Empty Text (Should fail)
echo "Test 3: Empty Text Validation"
curl -s -u $AUTH -X POST "$API_URL/translate" \
  -H "Content-Type: application/json" \
  -d '{"text":""}' | jq .
echo ""

# Test 4: Batch Translation
echo "Test 4: Batch Translation"
curl -s -u $AUTH -X POST "$API_URL/translate/batch" \
  -H "Content-Type: application/json" \
  -d '{"texts":["Good morning","Thank you"]}' | jq .
echo ""

# Test 5: No Auth (Should fail)
echo "Test 5: No Authentication"
curl -s -X POST "$API_URL/translate" \
  -H "Content-Type: application/json" \
  -d '{"text":"Hello"}' | jq .
echo ""

echo "Tests completed!"
```

---

## 6. Response Status Codes

| Code | Meaning | Scenario |
|------|---------|----------|
| 200 | OK | Translation successful |
| 400 | Bad Request | Empty/invalid input |
| 401 | Unauthorized | Missing/invalid credentials |
| 403 | Forbidden | Valid auth but insufficient permissions |
| 500 | Internal Server Error | Translation service error |

---

## 7. Troubleshooting

### Issue: 401 Unauthorized
**Solution:** Check credentials and ensure Basic Auth header is correct

### Issue: 500 Internal Server Error
**Solution:** 
1. Check GEMINI_API_KEY environment variable is set
2. Verify Gemini API is accessible
3. Check server logs

### Issue: Connection Refused
**Solution:** Ensure the server is running on port 8080

### Issue: CORS Error (from browser)
**Solution:** CORS filter should handle this, check CorsFilter configuration