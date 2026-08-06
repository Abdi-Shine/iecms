<?php

namespace App\Http\Controllers;

use App\Mail\BackupMail;
use App\Models\Backup;
use App\Models\BackupSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class BackupRestoreController extends Controller
{
    private string $disk = 'local';
    private string $folder = 'backups';

    // ── Page ────────────────────────────────────────────────────────────
    public function index()
    {
        $backups  = Backup::orderByDesc('created_at')->get();
        $settings = BackupSetting::allAsArray();

        $settings['auto_backup_enabled'] = (bool) ($settings['auto_backup_enabled'] ?? false);
        $settings['backup_retention']    = $settings['backup_retention'] ?? 30;
        $settings['backup_time']         = $settings['backup_time'] ?? '02:00';
        $settings['server_time']         = now()->format('Y-m-d H:i:s');

        $stats = [
            'total_backups' => $backups->count(),
            'success_rate'  => '100%',
        ];

        $dbSize = $this->getDatabaseSize();

        return view('setting.backup_restore', compact('backups', 'settings', 'stats', 'dbSize'));
    }

    // ── Create backup ────────────────────────────────────────────────────
    public function create(Request $request)
    {
        try {
            [$filePath, $fileName, $size] = $this->runDump();

            Backup::create([
                'filename' => $fileName,
                'size'     => $size,
                'type'     => $request->boolean('automated') ? 'auto' : 'manual',
            ]);

            return response()->json(['success' => true, 'message' => 'Backup created successfully: ' . $fileName]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ── Download ─────────────────────────────────────────────────────────
    public function download(string $id)
    {
        $backup = Backup::findOrFail($id);
        $path   = storage_path('app/' . $this->folder . '/' . $backup->filename);

        if (! file_exists($path)) {
            abort(404, 'Backup file not found on disk.');
        }

        return response()->download($path, $backup->filename);
    }

    // ── Restore ──────────────────────────────────────────────────────────
    public function restore(string $id)
    {
        $backup = Backup::findOrFail($id);
        $path   = storage_path('app/' . $this->folder . '/' . $backup->filename);

        if (! file_exists($path)) {
            $backup->update(['restore_status' => 'failed']);
            return response()->json(['success' => false, 'message' => 'Backup file not found on disk.'], 404);
        }

        try {
            // Preserve all backup ledger records in memory before the restore wipes them out
            $existingBackups = \App\Models\Backup::all()->map->getRawOriginal()->toArray();

            $cfg = config('database.connections.mysql');
            $pass = $cfg['password'] ? '-p' . escapeshellarg($cfg['password']) : '';
            $mysqlPath = 'mysql';
            if (DIRECTORY_SEPARATOR === '\\' && file_exists('C:\\xampp\\mysql\\bin\\mysql.exe')) {
                $mysqlPath = '"C:\\xampp\\mysql\\bin\\mysql.exe"';
            }

            $cmd  = sprintf(
                '%s -h %s -P %s -u %s %s %s < %s 2>&1',
                $mysqlPath,
                escapeshellarg($cfg['host']),
                escapeshellarg($cfg['port']),
                escapeshellarg($cfg['username']),
                $pass,
                escapeshellarg($cfg['database']),
                escapeshellarg($path)
            );

            exec($cmd, $output, $code);

            if ($code !== 0) {
                throw new \RuntimeException(implode("\n", $output));
            }

            // Restore the backup ledger
            \App\Models\Backup::truncate();
            foreach ($existingBackups as $record) {
                \App\Models\Backup::insert($record);
            }

            // Fetch it fresh to update the status
            $backup = \App\Models\Backup::find($id);
            if ($backup) {
                $backup->update(['restore_status' => 'restored', 'restored_at' => now()]);
            }
            
            return response()->json(['success' => true, 'message' => 'Database restored successfully.']);
        } catch (\Throwable $e) {
            $backup->update(['restore_status' => 'failed']);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ── Delete ───────────────────────────────────────────────────────────
    public function delete(string $id)
    {
        $backup = Backup::findOrFail($id);
        $path   = storage_path('app/' . $this->folder . '/' . $backup->filename);

        if (file_exists($path)) {
            unlink($path);
        }

        $backup->delete();
        return response()->json(['success' => true, 'message' => 'Backup deleted.']);
    }

    // ── Settings update ───────────────────────────────────────────────────
    public function updateSettings(Request $request)
    {
        BackupSetting::set('auto_backup_enabled', $request->boolean('automated') ? '1' : '0');
        BackupSetting::set('backup_retention', (string) ($request->input('retention', 30)));
        BackupSetting::set('backup_time', $request->input('backup_time', '02:00'));

        return response()->json(['success' => true]);
    }

    // ── Scheduled trigger (called by client every 60 s) ──────────────────
    public function scheduledTrigger(Request $request)
    {
        $enabled = BackupSetting::get('auto_backup_enabled', '0');
        if (! $enabled) {
            return response()->json(['success' => false, 'message' => 'Automated backups disabled.']);
        }

        $backupTime = BackupSetting::get('backup_time', '02:00');
        $now        = now()->format('H:i');

        if ($now !== $backupTime) {
            return response()->json(['success' => false, 'message' => 'Not scheduled time.']);
        }

        // Prevent duplicate within same minute
        $recent = Backup::where('type', 'auto')
            ->where('created_at', '>=', now()->subMinutes(2))
            ->exists();

        if ($recent) {
            return response()->json(['success' => false, 'message' => 'Already backed up this minute.']);
        }

        try {
            [$filePath, $fileName, $size] = $this->runDump();
            Backup::create(['filename' => $fileName, 'size' => $size, 'type' => 'auto']);
            return response()->json(['success' => true, 'message' => 'Scheduled backup completed.']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ── Gmail backup ──────────────────────────────────────────────────────
    public function gmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        try {
            [$filePath, $fileName, $size] = $this->runDump();

            Backup::create(['filename' => $fileName, 'size' => $size, 'type' => 'gmail']);

            Mail::to($request->email)->send(new BackupMail($filePath, $fileName, $this->getDatabaseSize()));

            return response()->json(['success' => true, 'message' => 'Backup sent to ' . $request->email]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ── Helpers ──────────────────────────────────────────────────────────
    private function runDump(): array
    {
        $cfg      = config('database.connections.mysql');
        $dir      = storage_path('app/' . $this->folder);
        $fileName = 'backup_' . now()->format('Y_m_d_His') . '.sql';
        $filePath = $dir . '/' . $fileName;

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $pass = $cfg['password'] ? '-p' . escapeshellarg($cfg['password']) : '';
        
        $mysqldumpPath = 'mysqldump';
        if (DIRECTORY_SEPARATOR === '\\' && file_exists('C:\\xampp\\mysql\\bin\\mysqldump.exe')) {
            $mysqldumpPath = '"C:\\xampp\\mysql\\bin\\mysqldump.exe"';
        }

        $cmd  = sprintf(
            '%s -h %s -P %s -u %s %s %s > %s 2>&1',
            $mysqldumpPath,
            escapeshellarg($cfg['host']),
            escapeshellarg($cfg['port']),
            escapeshellarg($cfg['username']),
            $pass,
            escapeshellarg($cfg['database']),
            escapeshellarg($filePath)
        );

        exec($cmd, $output, $code);

        if ($code !== 0 || ! file_exists($filePath) || filesize($filePath) === 0) {
            throw new \RuntimeException('mysqldump failed: ' . implode(' ', $output));
        }

        $bytes = filesize($filePath);
        $size  = $bytes < 1024 * 1024
            ? round($bytes / 1024, 1) . ' KB'
            : round($bytes / (1024 * 1024), 2) . ' MB';

        return [$filePath, $fileName, $size];
    }

    private function getDatabaseSize(): string
    {
        $db   = config('database.connections.mysql.database');
        $row  = DB::selectOne(
            "SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size
             FROM information_schema.tables WHERE table_schema = ?",
            [$db]
        );
        return $row->size ?? '0.00';
    }
}
