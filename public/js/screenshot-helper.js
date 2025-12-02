/**
 * Screenshot Helper - Server-side automation
 * 
 * Gọi API để Puppeteer visit page thật và click nút download
 * Code gốc (domtoimage) KHÔNG thay đổi!
 */

function downloadTreeServerSide() {
    // Show loading
    jQuery('.loader1').show();
    showToastInfoBottom("Đang xử lý ảnh ở server...", "Vui lòng đợi", 10000);
    
    // Get current URL
    const currentUrl = window.location.href;
    
    // Call Laravel API
    fetch('/api/screenshot/url', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        },
        body: JSON.stringify({
            url: currentUrl,
            selector: '.btn_ctrl_svg1',  // Nút download hiện tại
            width: 1920,
            height: 1080,
            timeout: 60
        })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => {
                throw new Error(err.message || 'Screenshot failed');
            });
        }
        return response.blob();
    })
    .then(blob => {
        console.log("Server screenshot completed, size:", blob.size);
        
        // Download
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        a.download = 'tree-' + Date.now() + '.png';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
        
        jQuery('.loader1').hide();
        showToastInfoBottom("✅ Tải ảnh thành công!", "", 3000);
    })
    .catch(error => {
        console.error("Server screenshot error:", error);
        jQuery('.loader1').hide();
        alert("Lỗi: " + error.message + "\n\nHãy thử phương pháp download thông thường.");
    });
}

// Export
window.downloadTreeServerSide = downloadTreeServerSide;
