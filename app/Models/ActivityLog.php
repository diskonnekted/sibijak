<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email',
        'role',
        'action',
        'description',
        'ip_address',
        'user_agent',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper presisi untuk mendapatkan Alamat IP Asli klien (Client Real IP),
     * bahkan jika lalu lintas melewati Cloudflare, Nginx Reverse Proxy, atau Load Balancer.
     */
    public static function getRealClientIp($request = null): string
    {
        $req = $request ?? request();

        if (!$req) {
            return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        }

        // 1. Cloudflare Connecting IP Header (Utama saat menggunakan Cloudflare CDN/WAF)
        if ($req->header('CF-Connecting-IP')) {
            return trim($req->header('CF-Connecting-IP'));
        }

        // 2. X-Real-IP Header (Nginx / Apache Reverse Proxy)
        if ($req->header('X-Real-IP')) {
            return trim($req->header('X-Real-IP'));
        }

        // 3. X-Forwarded-For Header (Proxy Chain — Mengambil IP paling pertama)
        if ($req->header('X-Forwarded-For')) {
            $ips = explode(',', $req->header('X-Forwarded-For'));
            $realIp = trim($ips[0]);
            if (filter_var($realIp, FILTER_VALIDATE_IP)) {
                return $realIp;
            }
        }

        // 4. Fallback ke IP Bawaan Server / Framework
        return $req->ip() ?? $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    /**
     * Limiter Standar Retensi Database Activity Log (Maksimal 1.000 Catatan Log Terbaru).
     */
    public const MAX_RETENTION_LOGS = 1000;

    /**
     * Memangkas log lama secara otomatis agar kapasitas database selalu terukur dan stabil.
     */
    public static function pruneOldLogs(): int
    {
        $totalLogs = self::count();
        if ($totalLogs > self::MAX_RETENTION_LOGS) {
            $keepIds = self::latest('id')->take(self::MAX_RETENTION_LOGS)->pluck('id');
            return self::whereNotIn('id', $keepIds)->delete();
        }
        return 0;
    }

    /**
     * Helper statis untuk mencatat log aktivitas pengguna & sistem secara presisi.
     */
    public static function log(string $action, ?string $description = null, $user = null, $request = null): self
    {
        $req = $request ?? request();
        $currentUser = $user ?? auth()->user();

        $log = self::create([
            'user_id' => $currentUser ? $currentUser->id : null,
            'email' => $currentUser ? $currentUser->email : ($req ? $req->input('email') : null),
            'role' => $currentUser ? $currentUser->role : 'guest',
            'action' => $action,
            'description' => $description,
            'ip_address' => self::getRealClientIp($req),
            'user_agent' => $req ? substr($req->header('User-Agent'), 0, 500) : null,
        ]);

        // Menerapkan Limiter Standar: Pangkas otomatis log lama jika melebihi batas 1.000 record
        self::pruneOldLogs();

        return $log;
    }
}
