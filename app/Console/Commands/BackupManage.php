<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

class BackupManage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:manage {action=list} {--days=30 : Number of days for cleanup}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Manage database backups (list, cleanup, restore)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');

        return match ($action) {
            'list' => $this->listBackups(),
            'cleanup' => $this->cleanupOldBackups(),
            'info' => $this->showBackupInfo(),
            'log' => $this->showBackupLog(),
            default => $this->listAvailableActions(),
        };
    }

    /**
     * List all available backups
     */
    private function listBackups()
    {
        $backupDir = database_path('exports');
        $files = glob($backupDir . '/nexoralabs_backup_*.sql');

        if (empty($files)) {
            $this->warn('No backup files found.');
            return 0;
        }

        $this->info("Available Backups:\n");

        $headers = ['#', 'Filename', 'Size', 'Date Created', 'Age'];
        $rows = [];

        usort($files, fn($a, $b) => filemtime($b) - filemtime($a));

        foreach ($files as $index => $file) {
            $filename = basename($file);
            $filesize = filesize($file);
            $filedate = filemtime($file);
            $age = $this->getFileAge($filedate);

            $rows[] = [
                $index + 1,
                $filename,
                $this->formatBytes($filesize),
                date('Y-m-d H:i:s', $filedate),
                $age,
            ];
        }

        $this->table($headers, $rows);

        return 0;
    }

    /**
     * Show backup information and statistics
     */
    private function showBackupInfo()
    {
        $backupDir = database_path('exports');
        $files = glob($backupDir . '/nexoralabs_backup_*.sql');

        if (empty($files)) {
            $this->warn('No backup files found.');
            return 0;
        }

        usort($files, fn($a, $b) => filemtime($b) - filemtime($a));
        $latestFile = reset($files);

        $filename = basename($latestFile);
        $filesize = filesize($latestFile);
        $filedate = filemtime($latestFile);

        $this->info("Latest Backup Information:\n");
        $this->line("  Filename: <info>{$filename}</info>");
        $this->line("  Size: <info>" . $this->formatBytes($filesize) . "</info>");
        $this->line("  Created: <info>" . date('Y-m-d H:i:s', $filedate) . "</info>");
        $this->line("  Path: <info>{$latestFile}</info>");

        // File integrity check
        $isValid = $this->verifyBackupIntegrity($latestFile);
        $status = $isValid ? '<fg=green>✓ Valid</>' : '<fg=red>✗ Invalid</>';
        $this->line("  Status: {$status}");

        $totalBackups = count($files);
        $totalSize = array_sum(array_map('filesize', $files));

        $this->line("\n<info>Total Backups:</info> {$totalBackups}");
        $this->line("<info>Total Size:</info> " . $this->formatBytes($totalSize));

        return 0;
    }

    /**
     * Show backup log
     */
    private function showBackupLog()
    {
        $logFile = database_path('exports/backup_log.txt');

        if (!file_exists($logFile)) {
            $this->warn('No backup log found.');
            return 0;
        }

        $this->info("Backup Log:\n");
        $content = file_get_contents($logFile);
        $this->line($content);

        return 0;
    }

    /**
     * Cleanup old backups
     */
    private function cleanupOldBackups()
    {
        $days = $this->option('days');
        $backupDir = database_path('exports');
        $files = glob($backupDir . '/nexoralabs_backup_*.sql');

        if (empty($files)) {
            $this->warn('No backup files found.');
            return 0;
        }

        $now = time();
        $expiration = $days * 24 * 60 * 60;
        $deletedCount = 0;
        $totalFreed = 0;

        foreach ($files as $file) {
            if ($now - filemtime($file) > $expiration) {
                $filesize = filesize($file);
                unlink($file);
                $deletedCount++;
                $totalFreed += $filesize;
                $this->line("Deleted: " . basename($file) . " (<info>" . $this->formatBytes($filesize) . "</info>)");
            }
        }

        if ($deletedCount > 0) {
            $this->info("\n✓ Cleanup completed!");
            $this->line("Deleted: <info>{$deletedCount}</info> files");
            $this->line("Space freed: <info>" . $this->formatBytes($totalFreed) . "</info>");
        } else {
            $this->info("No old backups to delete.");
        }

        return 0;
    }

    /**
     * Show available actions
     */
    private function listAvailableActions()
    {
        $this->info("Usage: php artisan backup:manage {action}\n");
        $this->line("Available actions:");
        $this->line("  <info>list</info>      - List all available backups");
        $this->line("  <info>info</info>      - Show latest backup information");
        $this->line("  <info>log</info>       - Show backup log");
        $this->line("  <info>cleanup</info>   - Delete old backups (older than --days, default: 30)");

        $this->line("\nExamples:");
        $this->line("  php artisan backup:manage list");
        $this->line("  php artisan backup:manage info");
        $this->line("  php artisan backup:manage cleanup --days=14");

        return 0;
    }

    /**
     * Verify backup integrity
     */
    private function verifyBackupIntegrity($filepath)
    {
        if (!file_exists($filepath)) {
            return false;
        }

        $filesize = filesize($filepath);

        if ($filesize < 1024) {
            return false;
        }

        $content = file_get_contents($filepath, false, null, 0, 1000);

        return (strpos($content, 'CREATE TABLE') !== false ||
                strpos($content, 'INSERT INTO') !== false ||
                strpos($content, '/*!') !== false);
    }

    /**
     * Get file age
     */
    private function getFileAge($timestamp)
    {
        $now = time();
        $diff = $now - $timestamp;

        if ($diff < 60) {
            return 'just now';
        } elseif ($diff < 3600) {
            return round($diff / 60) . ' min ago';
        } elseif ($diff < 86400) {
            return round($diff / 3600) . ' hours ago';
        } elseif ($diff < 604800) {
            return round($diff / 86400) . ' days ago';
        } else {
            return round($diff / 604800) . ' weeks ago';
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
