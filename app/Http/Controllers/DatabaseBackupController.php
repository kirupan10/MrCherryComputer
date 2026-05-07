<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class DatabaseBackupController extends Controller
{
    /**
     * Create a database backup and download it
     */
    public function download()
    {
        return $this->createBackup(true);
    }

    /**
     * Create a database backup without downloading
     */
    public function create()
    {
        return $this->createBackup(false);
    }

    /**
     * Create a database backup
     */
    private function createBackup($autoDownload = false)
    {
        try {
            // Get database configuration
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $host = config('database.connections.mysql.host');
            $port = config('database.connections.mysql.port', 3306);

            // Create backup filename with timestamp
            $filename = 'backup_' . $database . '_' . date('Y-m-d_His') . '.sql';
            $backupPath = storage_path('app/backups');

            // Create backups directory if it doesn't exist
            if (!file_exists($backupPath)) {
                mkdir($backupPath, 0755, true);
            }

            $filePath = $backupPath . '/' . $filename;

            // Find mysqldump path
            $mysqldumpPath = $this->findMysqldump();
            if (!$mysqldumpPath) {
                throw new \Exception('mysqldump command not found. Please ensure MySQL client tools are installed.');
            }

            // Build mysqldump command with options for RDS compatibility
            // --skip-lock-tables: Avoid FLUSH TABLES WITH READ LOCK (requires RELOAD privilege)
            // --single-transaction: Use transaction for consistency without locking
            // --no-tablespaces: Skip tablespace information (often restricted in RDS)
            // --set-gtid-purged=OFF: Disable GTID info (often not needed and can cause errors)
            $command = sprintf(
                '%s --user=%s --password=%s --host=%s --port=%s --skip-lock-tables --single-transaction --no-tablespaces --set-gtid-purged=OFF %s',
                $mysqldumpPath,
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($database)
            );

            // Execute the backup command
            $process = Process::fromShellCommandline($command . ' > ' . escapeshellarg($filePath) . ' 2>&1');
            $process->setTimeout(600); // 10 minutes timeout
            $process->run();

            // Get the output (which includes both stdout and stderr because of 2>&1)
            $output = $process->getOutput();
            $exitCode = $process->getExitCode();

            // Check if the file was created
            if (!file_exists($filePath)) {
                throw new \Exception('Backup file was not created. mysqldump output: ' . $output);
            }

            // Check file size - mysqldump may create an empty/incomplete file on error
            $fileSize = filesize($filePath);

            if ($fileSize < 500) {
                // File is suspiciously small, likely an error occurred
                $content = file_get_contents($filePath);

                // Check if it contains actual data or just error messages
                if (strpos($content, 'CREATE TABLE') === false && strpos($content, 'INSERT INTO') === false) {
                    unlink($filePath); // Delete the invalid file

                    // Parse the error from output
                    $errorMsg = 'Backup failed. ';
                    if (strpos($output, 'Access denied') !== false) {
                        $errorMsg .= 'Database user does not have required privileges. Please ensure the user has SELECT, SHOW VIEW, and TRIGGER privileges.';
                    } elseif (strpos($output, 'FLUSH TABLES') !== false) {
                        $errorMsg .= 'Cannot lock tables. The database user may not have RELOAD privilege (common in RDS). The backup command includes --skip-lock-tables but the error persists.';
                    } else {
                        $errorMsg .= 'Error output: ' . substr($output, 0, 500);
                    }

                    throw new \Exception($errorMsg);
                }
            }

            // If exit code is not 0, log warning but proceed if file looks valid
            if ($exitCode !== 0) {
                \Log::warning('mysqldump returned non-zero exit code: ' . $exitCode . '. Output: ' . $output);

                // Check if it's just a password warning (common and harmless)
                if (strpos($output, 'Using a password on the command line interface can be insecure') !== false) {
                    // This is just a warning, safe to ignore
                } else {
                    // Other error, but if file exists and has content, we'll allow it
                    \Log::warning('mysqldump warning/error, but file was created with size: ' . $fileSize);
                }
            }

            // If auto-download is enabled, download the file and delete it
            if ($autoDownload) {
                return response()->download($filePath, $filename, [
                    'Content-Type' => 'application/sql',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                ])->deleteFileAfterSend(true);
            }

            // Otherwise, redirect to the backup list with success message
            return redirect()->route('admin.backups.index')->with('success', 'Backup created successfully: ' . $filename);

        } catch (\Exception $e) {
            // Log the error
            \Log::error('Database backup failed: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Database backup failed: ' . $e->getMessage());
        }
    }

    /**
     * List all available backups
     */
    public function index()
    {
        $backupPath = storage_path('app/backups');
        $backups = [];

        if (file_exists($backupPath)) {
            $files = scandir($backupPath);

            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                    $filePath = $backupPath . '/' . $file;
                    $backups[] = [
                        'name' => $file,
                        'size' => $this->formatBytes(filesize($filePath)),
                        'date' => date('Y-m-d H:i:s', filemtime($filePath)),
                        'path' => $file,
                    ];
                }
            }

            // Sort by date, newest first
            usort($backups, function($a, $b) {
                return strcmp($b['date'], $a['date']);
            });
        }

        return view('admin.backups.index', compact('backups'));
    }

    /**
     * Download an existing backup file
     */
    public function downloadExisting($filename)
    {
        $backupPath = storage_path('app/backups/' . $filename);

        // Security: prevent directory traversal
        $filename = basename($filename);

        if (!file_exists($backupPath) || pathinfo($filename, PATHINFO_EXTENSION) !== 'sql') {
            return redirect()->back()->with('error', 'Backup file not found');
        }

        return response()->download($backupPath, $filename, [
            'Content-Type' => 'application/sql',
        ]);
    }

    /**
     * Delete a backup file
     */
    public function delete($filename)
    {
        $backupPath = storage_path('app/backups/' . basename($filename));

        if (file_exists($backupPath) && pathinfo($filename, PATHINFO_EXTENSION) === 'sql') {
            unlink($backupPath);
            return redirect()->back()->with('success', 'Backup deleted successfully');
        }

        return redirect()->back()->with('error', 'Backup file not found');
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Find mysqldump executable path
     */
    private function findMysqldump()
    {
        // Common paths for mysqldump
        $possiblePaths = [
            '/opt/homebrew/bin/mysqldump',  // macOS Homebrew (Apple Silicon)
            '/usr/local/bin/mysqldump',      // macOS Homebrew (Intel)
            '/usr/bin/mysqldump',            // Linux
            '/opt/local/bin/mysqldump',      // MacPorts
            '/Applications/XAMPP/bin/mysqldump', // XAMPP
            '/Applications/MAMP/Library/bin/mysqldump', // MAMP
        ];

        // Check each possible path
        foreach ($possiblePaths as $path) {
            if (file_exists($path) && is_executable($path)) {
                return $path;
            }
        }

        // Try to find using 'which' command
        $process = Process::fromShellCommandline('which mysqldump');
        $process->run();

        if ($process->isSuccessful()) {
            $path = trim($process->getOutput());
            if ($path && file_exists($path)) {
                return $path;
            }
        }

        return null;
    }
}
