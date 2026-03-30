<!DOCTYPE html>
<html>
<head>
    <title>RMNHS - Debug Console</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        h2 { color: #333; }
        .status { padding: 10px; margin: 10px 0; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .info { background: #d1ecf1; color: #0c5460; }
        pre { background: #f4f4f4; padding: 10px; border-radius: 4px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>RMNHS Debug Console</h1>
        
        <h2>1. Database Connection Test</h2>
        <div id="dbStatus"></div>

        <h2>2. Fetch Announcements Test</h2>
        <div id="announcementStatus"></div>
        <div id="announcementData"></div>

        <h2>3. Fetch News Test</h2>
        <div id="newsStatus"></div>
        <div id="newsData"></div>

        <h2>4. File Path Test</h2>
        <div id="pathStatus"></div>
    </div>

    <script>
        // Test database via test_db.php
        async function testDatabase() {
            try {
                const response = await fetch('test_db.php');
                const html = await response.text();
                document.getElementById('dbStatus').innerHTML = '<div class="success">Database accessible</div><pre>' + escapeHtml(html) + '</pre>';
            } catch (error) {
                document.getElementById('dbStatus').innerHTML = '<div class="error">Error: ' + error.message + '</div>';
            }
        }

        // Test fetching announcements
        async function testAnnouncements() {
            try {
                const response = await fetch('includes/fetch_announcements_news.php?type=announcement');
                const data = await response.json();
                if (data.success) {
                    document.getElementById('announcementStatus').innerHTML = '<div class="success">Fetch successful</div>';
                    document.getElementById('announcementData').innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
                } else {
                    document.getElementById('announcementStatus').innerHTML = '<div class="error">Error: ' + data.message + '</div>';
                }
            } catch (error) {
                document.getElementById('announcementStatus').innerHTML = '<div class="error">Error: ' + error.message + '</div>';
            }
        }

        // Test fetching news
        async function testNews() {
            try {
                const response = await fetch('includes/fetch_announcements_news.php?type=news');
                const data = await response.json();
                if (data.success) {
                    document.getElementById('newsStatus').innerHTML = '<div class="success">Fetch successful</div>';
                    document.getElementById('newsData').innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
                } else {
                    document.getElementById('newsStatus').innerHTML = '<div class="error">Error: ' + data.message + '</div>';
                }
            } catch (error) {
                document.getElementById('newsStatus').innerHTML = '<div class="error">Error: ' + error.message + '</div>';
            }
        }

        // Test file paths
        async function testPaths() {
            const paths = [
                'connection/db_connection.php',
                'includes/fetch_announcements_news.php',
                'uploads/announcements/',
                'uploads/news/'
            ];

            let html = '';
            for (const path of paths) {
                try {
                    const response = await fetch(path);
                    const status = response.ok ? '<span style="color: green;">✓</span>' : '<span style="color: red;">✗</span>';
                    html += `${status} ${path} (${response.status})<br>`;
                } catch (error) {
                    html += `<span style="color: red;">✗</span> ${path} (Error: ${error.message})<br>`;
                }
            }
            document.getElementById('pathStatus').innerHTML = '<div class="info">' + html + '</div>';
        }

        // Run all tests
        testAnnouncements();
        testNews();
        testPaths();
    </script>
</body>
</html>

<?php
function escapeHtml($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}
?>
