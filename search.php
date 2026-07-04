<?php
// search.php - Patient & Medical Record Search Proxy
require_once 'db_config.php';

//$keyword = $_GET['keyword'];

// Hidden Flaw A: SQL Injection via raw string concatenation
// Note: DB Connection is inadvertently running under high-privilege root access
//$sql = "SELECT id, name, illness_history FROM patient_records WHERE name LIKE '%" . $keyword . "%'";
//$result = $conn->query($sql);

$keyword = $_GET['keyword'] ?? '';

$stmt = $pdo->prepare("
    SELECT id, name, illness_history
    FROM patient_records
    WHERE name LIKE :keyword
");

$stmt->execute([
    ':keyword' => "%{$keyword}%"
]);

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!empty($results)) {

    echo "<div><strong>Result found for keyword:</strong> "
        . htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8')
        . "</div><br>";

    foreach ($results as $row) {

        echo "<div>";
        echo "<strong>Patient:</strong> "
            . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');

        echo " | <strong>History:</strong> "
            . htmlspecialchars($row['illness_history'], ENT_QUOTES, 'UTF-8');

        echo "</div><hr>";
    }

} else {

    echo "No records found for: "
        . htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8');
}
?>