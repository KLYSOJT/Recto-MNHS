<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Functional Dropdown</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            padding-top: 50px;
        }

        .dropdown-container {
            width: 350px;
            filter: drop-shadow(0px 4px 6px rgba(0,0,0,0.1));
            border-radius: 15px;
            overflow: hidden;
        }

        /* The Header (The Toggle Button) */
        .dropdown-header {
            background: linear-gradient(180deg, #d30000 0%, #8b0000 100%);
            color: white;
            padding: 25px;
            text-align: center;
            cursor: pointer;
            position: relative;
            z-index: 2;
        }

        .dropdown-header h2 {
            margin: 0;
            font-size: 32px;
            font-weight: 600;
        }

        .arrow-icon {
            position: absolute;
            bottom: 10px;
            right: 20px;
            font-size: 14px;
            transition: transform 0.3s ease;
        }

        /* The Reports List */
        .reports-list {
            background-color: white;
            list-style: none;
            margin: 0;
            padding: 0;
            /* Hidden State */
            max-height: 0;
            opacity: 0;
            transition: all 0.3s ease-in-out;
        }

        /* Active State (When Opened) */
        .dropdown-container.active .reports-list {
            max-height: 500px;
            opacity: 1;
        }

        .dropdown-container.active .arrow-icon {
            transform: rotate(180deg);
        }

        .reports-list li {
            padding: 18px 25px;
            border-bottom: 1px solid #eee;
            color: #444;
            font-size: 18px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .reports-list li:hover {
            background-color: #fcfcfc;
            color: #d30000;
            padding-left: 30px; /* Subtle slide effect on hover */
        }

        .reports-list li:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>

<div class="dropdown-container" id="myDropdown">
    <div class="dropdown-header" onclick="toggleMenu()">
        <h2>Maka-Diyos</h2>
        <span class="arrow-icon">▼</span>
    </div>

    <ul class="reports-list">
        <li onclick="handleReportClick(1)">Accomplishment Report 1</li>
        <li onclick="handleReportClick(2)">Accomplishment Report 2</li>
        <li onclick="handleReportClick(3)">Accomplishment Report 3</li>
    </ul>
</div>

<script>
    // Toggles the visibility of the list
    function toggleMenu() {
        const container = document.getElementById('myDropdown');
        container.classList.toggle('active');
    }

    // Handles the clicking of actual reports
    function handleReportClick(reportNumber) {
        alert("Opening Accomplishment Report " + reportNumber);
        // You can replace the alert with: window.location.href = 'report' + reportNumber + '.html';
    }
</script>

</body>
</html>