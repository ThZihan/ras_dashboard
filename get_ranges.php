<?php
require_once 'db_conn.php';

$sql = "SELECT * FROM parameter_ranges";
$result = $conn->query($sql);

$ranges = array();
while($row = $result->fetch_assoc()) {
    $ranges[$row['parameter_name']] = array(
        'ideal_min' => $row['ideal_min'],
        'ideal_max' => $row['ideal_max'],
        'warning_min' => $row['warning_min'],
        'warning_max' => $row['warning_max']
    );
}

header('Content-Type: application/json');
echo json_encode($ranges);
?>