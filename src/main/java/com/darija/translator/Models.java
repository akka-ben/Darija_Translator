package com.darija.translator;

import java.util.ArrayList;
import java.util.List;

/**
 * Request and Response model classes for the Translator API
 */

// Translation Request
class TranslationRequest {
    private String text;
    
    public TranslationRequest() {}
    
    public TranslationRequest(String text) {
        this.text = text;
    }
    
    public String getText() { return text; }
    public void setText(String text) { this.text = text; }
}

// Translation Response
class TranslationResponse {
    private String sourceText;
    private String translatedText;
    private String sourceLang;
    private String targetLang;
    private long timestamp;
    
    public TranslationResponse() {}
    
    public TranslationResponse(String sourceText, String translatedText, 
                              String sourceLang, String targetLang) {
        this.sourceText = sourceText;
        this.translatedText = translatedText;
        this.sourceLang = sourceLang;
        this.targetLang = targetLang;
        this.timestamp = System.currentTimeMillis();
    }
    
    // Getters and Setters
    public String getSourceText() { return sourceText; }
    public void setSourceText(String sourceText) { this.sourceText = sourceText; }
    
    public String getTranslatedText() { return translatedText; }
    public void setTranslatedText(String translatedText) { this.translatedText = translatedText; }
    
    public String getSourceLang() { return sourceLang; }
    public void setSourceLang(String sourceLang) { this.sourceLang = sourceLang; }
    
    public String getTargetLang() { return targetLang; }
    public void setTargetLang(String targetLang) { this.targetLang = targetLang; }
    
    public long getTimestamp() { return timestamp; }
    public void setTimestamp(long timestamp) { this.timestamp = timestamp; }
}

// Batch Translation Request
class BatchTranslationRequest {
    private List<String> texts;
    
    public BatchTranslationRequest() {}
    
    public BatchTranslationRequest(List<String> texts) {
        this.texts = texts;
    }
    
    public List<String> getTexts() { return texts; }
    public void setTexts(List<String> texts) { this.texts = texts; }
}

// Batch Translation Response
class BatchTranslationResponse {
    private List<TranslationPair> translations = new ArrayList<>();
    
    public BatchTranslationResponse() {}
    
    public void addTranslation(String source, String translated) {
        translations.add(new TranslationPair(source, translated));
    }
    
    public List<TranslationPair> getTranslations() { return translations; }
    public void setTranslations(List<TranslationPair> translations) { 
        this.translations = translations; 
    }
    
    public static class TranslationPair {
        private String sourceText;
        private String translatedText;
        
        public TranslationPair() {}
        
        public TranslationPair(String source, String translated) {
            this.sourceText = source;
            this.translatedText = translated;
        }
        
        public String getSourceText() { return sourceText; }
        public void setSourceText(String sourceText) { this.sourceText = sourceText; }
        
        public String getTranslatedText() { return translatedText; }
        public void setTranslatedText(String translatedText) { 
            this.translatedText = translatedText; 
        }
    }
}

// Error Response
class ErrorResponse {
    private String error;
    private long timestamp;
    
    public ErrorResponse() {}
    
    public ErrorResponse(String error) {
        this.error = error;
        this.timestamp = System.currentTimeMillis();
    }
    
    public String getError() { return error; }
    public void setError(String error) { this.error = error; }
    
    public long getTimestamp() { return timestamp; }
    public void setTimestamp(long timestamp) { this.timestamp = timestamp; }
}

// Health Response
class HealthResponse {
    private String status;
    private String message;
    
    public HealthResponse() {}
    
    public HealthResponse(String status, String message) {
        this.status = status;
        this.message = message;
    }
    
    public String getStatus() { return status; }
    public void setStatus(String status) { this.status = status; }
    
    public String getMessage() { return message; }
    public void setMessage(String message) { this.message = message; }
}