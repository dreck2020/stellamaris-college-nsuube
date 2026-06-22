<?php
// test_user_doc.php - Debug user documents with path testing
require_once 'config/database.php';
$db = new Database();
$conn = $db->getConnection();

echo "<h2>User Documents Debug</h2>";
echo "<p><strong>Current directory (__DIR__):</strong> " . __DIR__ . "</p>";
echo "<p><strong>Document root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<hr>";

// Get all user documents
$docs = $conn->query("SELECT * FROM user_documents ORDER BY id DESC LIMIT 5");

if($docs->rowCount() > 0) {
    echo "<table border='1' cellpadding='10' style='border-collapse:collapse;'>";
    echo "<tr style='background:#333;color:#fff;'>
            <th>ID</th>
            <th>Title</th>
            <th>DB Path</th>
            <th>File Exists?</th>
            <th>Full Path</th>
            <th>Action</th>
          </tr>";
    
    while($doc = $docs->fetch(PDO::FETCH_ASSOC)) {
        // Try multiple path combinations
        $paths = [
            'using __DIR__' => __DIR__ . '/' . $doc['file_path'],
            'using DOCUMENT_ROOT' => $_SERVER['DOCUMENT_ROOT'] . '/' . $doc['file_path'],
            'without assets/' => __DIR__ . '/' . str_replace('assets/', '', $doc['file_path']),
            'just filename' => __DIR__ . '/assets/uploads/documents/' . basename($doc['file_path'])
        ];
        
        $found_path = null;
        foreach($paths as $method => $path) {
            if(file_exists($path)) {
                $found_path = $path;
                break;
            }
        }
        
        echo "<tr>";
        echo "<td>" . $doc['id'] . "</td>";
        echo "<td>" . htmlspecialchars($doc['title']) . "</td>";
        echo "<td><code>" . htmlspecialchars($doc['file_path']) . "</code></td>";
        echo "<td><strong>" . ($found_path ? '✅ YES' : '❌ NO') . "</strong></td>";
        echo "<td><code>" . ($found_path ? htmlspecialchars($found_path) : 'Not found') . "</code></td>";
        echo "<td>";
        if($found_path) {
            echo "<a href='download-user-file.php?id=" . $doc['id'] . "' class='btn btn-primary' target='_blank'>Download via New</a><br>";
            echo "<a href='download.php?userfile=" . $doc['id'] . "' class='btn btn-warning' target='_blank'>Download via Old</a><br>";
            echo "<a href='" . $doc['file_path'] . "' class='btn btn-success' target='_blank'>Direct Link</a>";
        } else {
            echo "File missing!";
        }
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No user documents found.</p>";
}

echo "<h3>Test Link:</h3>";
$first = $conn->query("SELECT id FROM user_documents LIMIT 1")->fetch(PDO::FETCH_ASSOC);
if($first) {
    echo "<p><a href='download-user-file.php?id=" . $first['id'] . "' class='btn btn-primary' target='_blank'>Test New Download: id=" . $first['id'] . "</a></p>";
    echo "<p><a href='download.php?userfile=" . $first['id'] . "' class='btn btn-warning' target='_blank'>Test Old Download: ?userfile=" . $first['id'] . "</a></p>";
}
?>