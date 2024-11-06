<?php
// Configuration
$api_url = getenv('API_URL') . "/invoices";
$auth_header = getenv('API_KEY');
$backup_dir = './backups';
$file_age_limit = 14 * 86400; // 14 days in seconds

echo "API URL: " . $api_url . "\n";
echo "API KEY: " . $auth_header . "\n";

// Ensure backup directory exists
if (!is_dir($backup_dir) && !mkdir($backup_dir, 0755, true) && !is_dir($backup_dir)) {
  die('Failed to create backup directory.');
}

// Prepare the backup file path
$file_name = sprintf('%s/%s.json', $backup_dir, date('d-m-Y-H:i'));

// Fetch API data
$ch = curl_init($api_url);
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HTTPHEADER => ['Authorization: ' . $auth_header]
]);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo $response;

// Handle HTTP response
if ($http_code !== 200) {
  $error_messages = [
    401 => 'Unauthorized. Check your authorization header.',
    404 => 'API endpoint not found.',
    500 => 'Internal Server Error. There may be an issue with the API.'
  ];
  echo $error_messages[$http_code] ?? "Error: HTTP status code $http_code" . PHP_EOL;
  exit;
}

// Save response to JSON file
file_put_contents($file_name, $response);
echo "Data saved to $file_name" . PHP_EOL;

// Clean up old backup files
foreach (new DirectoryIterator($backup_dir) as $file) {
  if ($file->isFile() && (time() - $file->getMTime() > $file_age_limit)) {
    unlink($file->getPathname());
    echo 'Deleted old file: ' . $file->getFilename() . PHP_EOL;
  }
}
