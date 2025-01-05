<?php
require_once 'db_conn.php';

// Set headers for CSV download
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="sensor_data_' . date('Y-m-d_His') . '.csv"');

// Create the output stream
$output = fopen('php://output', 'w');

// Add UTF-8 BOM to ensure special characters are displayed correctly
fwrite($output, "\xEF\xBB\xBF");

// Add CSV header
fputcsv($output, array(
    'Timestamp Stored',
    'Temperature (°C)',
    'pH',
    'Turbidity (NTU)',
    'Dissolved Oxygen(mg/L)',
    'Electrical Conductivity(µS/cm)',
    'ORP (mV)',
    'TDS (PPM)'
));

// Prepare and execute query
$sql = "SELECT 
            DATE_FORMAT(timestamp_stored, '%Y-%m-%d %H:%i:%s') AS timestamp_stored,
            temperature,
            ph,
            turbidity,
            dissolved_oxygen as dissolved_oxigen,
            electrical_conductivity,
            orp,
            tds
        FROM sensor_data 
        ORDER BY id DESC";

$result = $conn->query($sql);

// Write data rows
while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

// Close the output stream
fclose($output);
exit();
?>