<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Backup Viewer</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.10.5/dist/cdn.min.js"></script>
</head>

<body class="bg-gray-100">
  <div class="container mx-auto py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Backup Viewer</h1>

    <?php
    $backup_dir = './backups';
    $selectedFile = isset($_GET['file']) ? $_GET['file'] : null;
    $fileContent = '';
    $fileExtension = '';

    if ($selectedFile) {
      $filePath = $backup_dir . '/' . $selectedFile;
      if (file_exists($filePath)) {
        $fileContent = file_get_contents($filePath);
        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
      } else {
        echo "<p class='text-red-600'>File not found.</p>";
      }
    }
    ?>

    <?php if ($selectedFile && $fileExtension) : ?>
      <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
          <h2 class="text-xl font-semibold text-gray-800">Preview: <?php echo htmlspecialchars($selectedFile); ?></h2>
        </div>

        <div class="p-4 flex space-x-2">
          <a href="<?php echo htmlspecialchars($filePath); ?>" class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg">
            Download
          </a>
          <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg">
            Back to Backup List
          </a>
        </div>

        <div class="p-4">
          <?php if ($fileExtension === 'json') : ?>
            <pre class="whitespace-pre-wrap font-mono text-gray-800 bg-gray-100 p-4 rounded-md overflow-x-auto text-sm"><?php echo htmlspecialchars(json_encode(json_decode($fileContent), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)); ?></pre>
          <?php else : ?>
            <div class="whitespace-pre-wrap font-mono text-gray-800 bg-gray-100 p-4 rounded-md overflow-x-auto text-sm">
              <?php echo htmlspecialchars($fileContent); ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    <?php else : ?>
      <p class="text-gray-600">Please select a valid backup file to view its contents.</p>
      <a href="index.php" class="mt-4 inline-block bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg">
        Back to Backup List
      </a>
    <?php endif; ?>
  </div>
</body>

</html>