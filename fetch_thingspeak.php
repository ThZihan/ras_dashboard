<?php
require_once 'db_conn.php';

// Function to fetch data from ThingSpeak
function fetchThingSpeakData() {
    $url = "https://api.thingspeak.com/channels/2801541/feeds.json?api_key=0P2BT04JJI94ZSE2&results=3";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    
    if(curl_errno($ch)) {
        error_log('Curl error: ' . curl_error($ch));
        return false;
    }
    
    curl_close($ch);
    return json_decode($response, true);
}

// Function to store data in database
function storeData($feed) {
    global $conn;
    
    // Check if entry_id already exists
    $stmt = $conn->prepare("SELECT id FROM sensor_data WHERE entry_id = ?");
    $stmt->bind_param("i", $feed['entry_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        // Entry already exists
        return false;
    }
    
    // Prepare INSERT statement
    $sql = "INSERT INTO sensor_data (
        entry_id, 
        ph, 
        turbidity, 
        dissolved_oxygen, 
        electrical_conductivity, 
        temperature, 
        orp, 
        tds, 
        created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    
    // Convert created_at to proper datetime format
    $created_at = date('Y-m-d H:i:s', strtotime($feed['created_at']));
    
    // Clean and convert TDS value (remove \r\n)
    $tds = floatval(trim($feed['field7']));
    
    $stmt->bind_param("iddddddds", 
        $feed['entry_id'],
        $feed['field1'],
        $feed['field2'],
        $feed['field3'],
        $feed['field4'],
        $feed['field5'],
        $feed['field6'],
        $tds,
        $created_at
    );
    
    $result = $stmt->execute();
    
    if(!$result) {
        error_log("MySQL Error: " . $stmt->error);
        return false;
    }
    
    return true;
}

// Main execution
$data = fetchThingSpeakData();

if($data && isset($data['feeds'])) {
    foreach($data['feeds'] as $feed) {
        $stored = storeData($feed);
        if($stored) {
            echo "Stored entry_id: " . $feed['entry_id'] . "\n";
        }
    }
} else {
    echo "Failed to fetch data from ThingSpeak\n";
}
?>