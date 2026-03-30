<?php
require_once '../../connection/db_connection.php';

// Check if table exists, if not create it
$table_check = "SHOW TABLES LIKE 'school_memorandum'";
$result = $conn->query($table_check);

if ($result->num_rows === 0) {
    // Table doesn't exist, create it
    $create_table = "CREATE TABLE IF NOT EXISTS `school_memorandum` (
      `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
      `title` varchar(255) NOT NULL,
      `date` date NOT NULL,
      `description` text,
      `file` varchar(255),
      `created_at` timestamp DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    
    if ($conn->query($create_table) === TRUE) {
        echo json_encode(['success' => true, 'message' => 'Table created successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error creating table: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => true, 'message' => 'Table already exists']);
}

$conn->close();
?>
