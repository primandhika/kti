<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

class ActivityLogger
{
    public static function log(
        string $action,
        string $module,
        string $description,
        ?Model $subject = null,
        ?array $metadata = null
    ): void {
        $request = request();
        $user = $request->user();

        ActivityLog::create([
            'user_id'      => $user?->id,
            'user_name'    => $user?->name,
            'action'       => $action,
            'module'       => $module,
            'description'  => $description,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id'   => $subject?->id,
            'ip_address'   => $request->ip(),
            'user_agent'   => substr($request->userAgent() ?? '', 0, 255),
            'metadata'     => $metadata,
        ]);
    }

    public static function login($user): void
    {
        $request = request();
        ActivityLog::create([
            'user_id'     => $user->id,
            'user_name'   => $user->name,
            'action'      => 'login',
            'module'      => 'auth',
            'description' => "Login berhasil: {$user->name}",
            'ip_address'  => $request->ip(),
            'user_agent'  => substr($request->userAgent() ?? '', 0, 255),
        ]);
    }

    public static function logout($user): void
    {
        $request = request();
        ActivityLog::create([
            'user_id'     => $user->id,
            'user_name'   => $user->name,
            'action'      => 'logout',
            'module'      => 'auth',
            'description' => "Logout: {$user->name}",
            'ip_address'  => $request->ip(),
            'user_agent'  => substr($request->userAgent() ?? '', 0, 255),
        ]);
    }
}
