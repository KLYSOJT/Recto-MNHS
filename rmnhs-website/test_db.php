<?php
// Test database connection and data
require_once 'connection/db_connection.php';

echo "<h2>Database Connection Test</h2>";

// Test announcements table
echo "<h3>Announcements Table:</h3>";
$result = $conn->query("SELECT COUNT(*) as count FROM announcement");
$row = $result->fetch_assoc();
echo "Total announcements: " . $row['count'] . "<br>";

if ($row['count'] > 0) {
    echo "<pre>";
    $result = $conn->query("SELECT * FROM announcement");
    while ($row = $result->fetch_assoc()) {
        print_r($row);
    }
    echo "</pre>";
}

// Test news table
echo "<h3>News Table:</h3>";
$result = $conn->query("SELECT COUNT(*) as count FROM news");
$row = $result->fetch_assoc();
echo "Total news: " . $row['count'] . "<br>";

if ($row['count'] > 0) {
    echo "<pre>";
    $result = $conn->query("SELECT * FROM news");
    while ($row = $result->fetch_assoc()) {
        print_r($row);
    }
    echo "</pre>";
}

$conn->close();
?>
