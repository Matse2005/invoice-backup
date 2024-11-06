<?php require_once __DIR__ . '/vendor/autoload.php';

use GO\Scheduler;

// Create a new scheduler
$scheduler = new Scheduler();

// Backup
$scheduler->php("backup.php")->everyMinute();

// Run the scheduler
$scheduler->run();
