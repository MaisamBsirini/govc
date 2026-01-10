<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BackupProject extends Command
{
    protected $signature = 'project:backup';
    protected $description = 'Create a backup of database and important files';

    public function handle()
    {
        // تأكد وجود مجلد backup
        $backupDir = storage_path('app/backups');
        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0777, true);
        }

        // ========================
        // Backup Database
        // ========================
        $dbName = env('DB_DATABASE');
        $dbUser = env('DB_USERNAME');
        $dbPass = env('DB_PASSWORD');
        $backupFile = $backupDir . '/db_backup_' . date('Y-m-d_H-i-s') . '.sql';

        $mysqldumpPath = "C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe";

        // تنفيذ النسخة الاحتياطية
        exec("\"$mysqldumpPath\" -u $dbUser -p$dbPass $dbName > \"$backupFile\"");

        // ========================
        // Backup .env
        // ========================
        copy(base_path('.env'), $backupDir . '/.env_backup_' . date('Y-m-d_H-i-s'));

        // ========================
        // Backup Files Count
        // ========================
        $backupFiles = glob($backupDir . '/*'); // كل الملفات داخل المجلد
        $backupCount = count($backupFiles);

        $this->info('Backup completed successfully!');
        $this->info("Total backup files so far: $backupCount");
    }
}
