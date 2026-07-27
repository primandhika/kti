<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set($key, $value)
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    public static function getKepalaBppuPeriods(): array
    {
        $periods = json_decode(self::get('kepala_bppu_periods', '[]'), true);

        if (!is_array($periods)) {
            return [];
        }

        return collect($periods)
            ->map(function ($period) {
                return [
                    'nama' => trim($period['nama'] ?? ''),
                    'nip' => trim($period['nip'] ?? ''),
                    'periode_mulai' => $period['periode_mulai'] ?? '',
                    'periode_selesai' => $period['periode_selesai'] ?? '',
                ];
            })
            ->filter(fn ($period) => $period['nama'] !== '')
            ->values()
            ->all();
    }

    public static function getKepalaBppuForPeriod($startDate, $endDate): array
    {
        if (!$startDate || !$endDate) {
            return ['nama' => '', 'nip' => ''];
        }

        try {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
        } catch (\Throwable $e) {
            return ['nama' => '', 'nip' => ''];
        }

        $period = collect(self::getKepalaBppuPeriods())
            ->filter(function ($period) use ($start, $end) {
                if (empty($period['periode_mulai']) || empty($period['periode_selesai'])) {
                    return false;
                }

                try {
                    $periodStart = Carbon::parse($period['periode_mulai'])->startOfDay();
                    $periodEnd = Carbon::parse($period['periode_selesai'])->endOfDay();
                } catch (\Throwable $e) {
                    return false;
                }

                return $start->greaterThanOrEqualTo($periodStart)
                    && $end->lessThanOrEqualTo($periodEnd);
            })
            ->sortByDesc('periode_mulai')
            ->first();

        return [
            'nama' => $period['nama'] ?? '',
            'nip' => $period['nip'] ?? '',
        ];
    }
}
