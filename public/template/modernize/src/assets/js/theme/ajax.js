setTimeout(() => {
    loadPartial('left-sidebar', 'partials/sidebar.html');
    loadPartial('header', 'partials/header.html');
    loadPartial('horizontal-header', 'partials/horizontal-header.html');
    loadPartial('horizontal-sidebar', 'partials/horizontal-sidebar.html');
    loadPartial('customizer', 'partials/customizer.html');
    loadPartial('header-components-searchbar', 'partials/header-components/dd-searchbar.html');
    loadPartial('header-components-shopping-cart', 'partials/header-components/dd-shopping-cart.html');

    function loadScriptsSequentially(scripts, callback) {
        function loadScript(index) {
            if (index >= scripts.length) {
                callback();
                return;
            }

            const script = document.createElement('script');
            script.type = 'text/javascript';
            script.src = scripts[index];
            script.onload = () => loadScript(index + 1);
            document.head.appendChild(script);
        }
        setTimeout(() => {
            loadScript(0);
        }, 250);
    }

    // Usage
    setTimeout(() => {
        loadScriptsSequentially([
            'assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js',
            'assets/libs/simplebar/dist/simplebar.min.js',
            'assets/js/theme/app.init.js',
            'assets/js/theme/app.min.js',
            // 'assets/js/theme/theme.js',
            'assets/js/theme/sidebarmenu.js',
            'https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js'
        ], () => {
            setTimeout(() => {
                loadPage('dashboard.html');

                document.querySelector('#sidebarnav').addEventListener('click', (e) => {
                    e.preventDefault();
                    updatePageContent(e);
                });
                document.querySelector('#sidebarnavh').addEventListener('click', (e) => {
                    console.log("loaded")
                    e.preventDefault();
                    updatePageContent(e);
                });
            }, 0);
        });

        function updatePageContent(e) {
            let page = '';
            if (e.target.tagName === 'A') {
                page = e.target.getAttribute('href');
                if (e.target.getAttribute("target")) {
                    loadNewPage(page);
                    return false;
                }
            } else if (e.target.tagName === "SPAN") {
                page = e.target.closest("a").getAttribute('href');
                if (e.target.closest("a").getAttribute("target")) {
                    loadNewPage(page);
                    return false;
                }
            }
            if (page.includes(".html"))
                loadPage(`${page}`);
            return false;
        }
    }, 0);
},0);



function loadNewPage(url) {
    const currentUrl = window.location.href;
    const newUrl = currentUrl.replace("index.html", url);
    window.open(newUrl, "_blank");
}
function loadPartial(elementId, url) {
    $.ajax({
        url: `${url}`,
        method: 'GET',
        success: function (response) {
            $(`#${elementId}`).html(response);
        },
        error: function (xhr, status, error) {
            console.log('Error:', error);
        }
    });
}

function loadPage(url) {
    $.ajax({
        url: `pages/${url}`,
        method: 'GET',
        success: function (response) {
            var content = document.getElementById('content');
            content.innerHTML = '';
            $("#content").html(response);
        },
        error: function (xhr, status, error) {
            console.log('Error:', error);
        }
    });
}
