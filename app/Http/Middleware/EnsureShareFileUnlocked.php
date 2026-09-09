<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class EnsureShareFileUnlocked
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $enabled = Setting::get('sharefile_password_enabled', '1') === '1';

        // If password protection is disabled, allow access
        if (!$enabled) {
            return $next($request);
        }

        // If user is logged in as admin, allow access
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request);
        }

        // If session has unlocked ShareFile, verify inactivity timeout (5 minutes = 300 seconds)
        if ($request->session()->get('sharefile_unlocked')) {
            $lastActivity = $request->session()->get('sharefile_last_activity');
            $timeoutSeconds = 300; // 5 menit

            if ($lastActivity && (time() - $lastActivity > $timeoutSeconds)) {
                $request->session()->forget(['sharefile_unlocked', 'sharefile_last_activity']);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'locked' => true,
                        'message' => 'Sesi Anda telah terkunci otomatis karena tidak ada aktivitas selama 5 menit. Silakan masukkan kata sandi kembali.'
                    ], 403);
                }

                return redirect('/data-File')->with('lock_error', 'Sesi Anda telah terkunci otomatis karena tidak ada aktivitas selama 5 menit. Silakan masukkan kata sandi kembali.');
            }

            // Still active, update last activity timestamp
            $request->session()->put('sharefile_last_activity', time());
            return $next($request);
        }

        // Block unauthorized actions
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses berkas terkunci. Silakan masukkan kata sandi terlebih dahulu.'
            ], 403);
        }

        return redirect('/data-File')->with('lock_error', 'Akses berkas terkunci. Silakan masukkan kata sandi terlebih dahulu.');
    }
}
