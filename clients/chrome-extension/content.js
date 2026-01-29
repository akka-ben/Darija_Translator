/**
 * Content Script - SIMPLE VERSION
 */

console.log('✅ CONTENT SCRIPT LOADED');

document.addEventListener('mouseup', () => {
    const text = window.getSelection().toString().trim();
    
    if (text && text.length > 2) {
        console.log('📝 Text selected:', text);
        
        chrome.runtime.sendMessage({
            type: 'SELECTION',
            text: text
        });
    }
});