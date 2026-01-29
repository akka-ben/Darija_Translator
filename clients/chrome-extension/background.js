/**
 * Background Script - SIMPLE VERSION
 */

console.log('🔧 BACKGROUND LOADED');

let textBuffer = '';

chrome.runtime.onMessage.addListener((message, sender, sendResponse) => {
    console.log('🔧 Message:', message.type);
    
    // Depuis content script
    if (message.type === 'SELECTION') {
        console.log('🔧 Selection:', message.text);
        textBuffer = message.text;
        
        // Ouvrir le side panel
        chrome.sidePanel.open({ windowId: sender.tab.windowId });
        
        // Envoyer au side panel
        chrome.tabs.query({active: true}, (tabs) => {
            chrome.runtime.sendMessage({
                type: 'SET_TEXT',
                text: message.text
            });
        });
    }
    
    // Depuis side panel (demande du texte)
    if (message.type === 'GET_TEXT') {
        sendResponse({ text: textBuffer });
    }
});

// Menu contextuel
chrome.runtime.onInstalled.addListener(() => {
    chrome.contextMenus.create({
        id: 'translateToDarija',
        title: 'Traduire en Darija',
        contexts: ['selection']
    });
});

chrome.contextMenus.onClicked.addListener((info, tab) => {
    textBuffer = info.selectionText;
    chrome.sidePanel.open({ windowId: tab.windowId });
});