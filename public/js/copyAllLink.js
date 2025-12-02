/**
 * Copy all href links with class .link_file to clipboard
 * Each link will be on a separate line
 */
function copyAllLink() {
    // Find all elements with class .link_file
    const allLinkElements = document.querySelectorAll('.link_file');
    
    if (allLinkElements.length === 0) {
        alert('Không tìm thấy link nào để copy!');
        return;
    }
    
    // Extract all href links and join with newlines
    const allLinks = [];
    
    allLinkElements.forEach(function(element) {
        let linkText = '';
        
        // If element is a link (a tag), get href
        if (element.tagName.toLowerCase() === 'a' && element.href) {
            linkText = element.href;
        }
        // If element has href attribute
        else if (element.href) {
            linkText = element.href;
        }
        
        // Clean up the link text
        linkText = linkText.trim();
        
        if (linkText) {
            allLinks.push(linkText);
        }
    });
    
    if (allLinks.length === 0) {
        alert('Không có link hợp lệ để copy!');
        return;
    }
    
    // Join all links with newlines
    const allLinksText = allLinks.join('\n');
    
    // Copy to clipboard using modern API
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(allLinksText).then(function() {
            showToastSuccess(`Đã copy ${allLinks.length} link vào clipboard!`);
        }).catch(function(err) {
            console.error('Failed to copy: ', err);
            fallbackCopyTextToClipboard(allLinksText, allLinks.length);
        });
    } else {
        // Fallback for older browsers
        fallbackCopyTextToClipboard(allLinksText, allLinks.length);
    }
}

/**
 * Fallback method for copying text to clipboard
 */
function fallbackCopyTextToClipboard(text, count) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    
    // Avoid scrolling to bottom
    textArea.style.top = "0";
    textArea.style.left = "0";
    textArea.style.position = "fixed";
    textArea.style.opacity = "0";
    
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        const successful = document.execCommand('copy');
        if (successful) {
            showToastSuccess(`Đã copy ${count} link vào clipboard!`);
        } else {
            showToastError('Không thể copy link!');
        }
    } catch (err) {
        console.error('Fallback: Oops, unable to copy', err);
        showToastError('Lỗi khi copy link: ' + err.message);
    }
    
    document.body.removeChild(textArea);
}

/**
 * Show success toast message
 */
function showToastSuccess(message) {
    if (typeof showToastWarningTop === 'function') {
        showToastWarningTop(message);
    } else if (typeof alert === 'function') {
        alert(message);
    } else {
        console.log(message);
    }
}

/**
 * Show error toast message
 */
function showToastError(message) {
    if (typeof showToastWarningTop === 'function') {
        showToastWarningTop(message);
    } else if (typeof alert === 'function') {
        alert(message);
    } else {
        console.error(message);
    }
}

// Make function available globally
window.copyAllLink = copyAllLink; 