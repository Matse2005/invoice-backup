<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.10.5/dist/cdn.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
</head>

<body class="bg-slate-50 min-h-screen">
    <div x-data="{
        selectedFile: null,
        isLoading: false,
        showDeleteModal: false,
        fileToDelete: null,
        formatDate(date) {
            return moment(date).format('MMM DD, YYYY HH:mm:ss');
        }
    }" class="min-h-screen">
        <nav class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        <h1 class="ml-2 text-2xl font-bold text-gray-900">Backup Manager</h1>
                    </div>
                </div>
            </div>
        </nav>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-1">
                    <div class="bg-white shadow rounded-lg overflow-hidden">
                        <div class="p-4 border-b border-gray-200">
                            <h2 class="text-lg font-medium text-gray-900">Backup Files</h2>
                        </div>
                        <div class="divide-y divide-gray-200 max-h-[600px] overflow-y-auto">
                            <?php
                            // Define the backup directory
                            $backup_dir = './backups';

                            // Ensure the backup directory exists
                            if (!is_dir($backup_dir)) {
                                echo '<p class="p-4 text-sm text-gray-500">Backup directory does not exist.</p>';
                            } else {
                                // Get all JSON files in the backup directory
                                $files = array_filter(scandir($backup_dir), function ($file) use ($backup_dir) {
                                    return is_file("$backup_dir/$file") && pathinfo($file, PATHINFO_EXTENSION) === 'json';
                                });

                                if (empty($files)) {
                                    echo '<p class="p-4 text-sm text-gray-500">No backup files found.</p>';
                                } else {
                                    foreach ($files as $file) {
                                        echo '<div class="p-4 flex justify-between items-center hover:bg-gray-50 cursor-pointer" @click="selectedFile = \'' . htmlspecialchars($file) . '\'">
                                                <span class="text-sm text-gray-900">' . htmlspecialchars($file) . '</span>
                                                <span class="text-sm text-gray-500">' . date("M d, Y H:i:s", filemtime("$backup_dir/$file")) . '</span>
                                              </div>';
                                    }
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div x-show="selectedFile" x-cloak class="bg-white shadow rounded-lg overflow-hidden">
                        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                            <h2 class="text-lg font-medium text-gray-900" x-text="'Preview: ' + selectedFile"></h2>
                            <div class="flex space-x-2">
                                <a :href="'viewer.php?file=' + selectedFile" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">View</a>
                                <button @click="showDeleteModal = true; fileToDelete = selectedFile" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">Delete</button>
                            </div>
                        </div>
                        <div class="p-4">
                            <div x-show="isLoading" class="flex justify-center items-center py-12">
                                <svg class="animate-spin h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div x-show="!selectedFile" x-cloak class="bg-white shadow rounded-lg overflow-hidden">
                        <div class="p-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No file selected</h3>
                            <p class="mt-1 text-sm text-gray-500">Select a backup file from the list to view its contents</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <div x-show="showDeleteModal" class="fixed z-10 inset-0 overflow-y-auto" x-cloak>
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showDeleteModal" class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <div x-show="showDeleteModal" class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Backup File</h3>
                            <p class="text-sm text-gray-500">Are you sure you want to delete this backup file? This action cannot be undone.</p>
                            <p class="mt-1 text-sm font-medium text-gray-900" x-text="fileToDelete"></p>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <button @click="
                            isLoading = true;
                            fetch('delete.php?file=' + fileToDelete)
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        window.location.reload();
                                    } else {
                                        alert('Error deleting file: ' + data.message);
                                    }
                                })
                                .catch(error => {
                                    alert('Error deleting file');
                                })
                                .finally(() => {
                                    isLoading = false;
                                    showDeleteModal = false;
                                })
                            " type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Delete</button>
                        <button @click="showDeleteModal = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>