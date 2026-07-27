<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Models\MenuItem;
use App\Models\PembagianMitraHistory;
use App\Models\PengajuanPencairan;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Handle the incoming request.
     */
    public function handle(Request $request, \Closure $next)
    {
        // Skip Inertia middleware for JSON API routes
        if ($request->is('member/register') ||
            $request->is('member/survey') ||
            $request->is('member/verify-email') ||
            $request->is('buyer/*')) {
            return $next($request);
        }

        return parent::handle($request, $next);
    }

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'username' => $request->user()->username,
                    'email' => $request->user()->email,
                    'nomor_induk' => $request->user()->nomor_induk ?? null,
                    'must_change_password' => $request->user()->must_change_password,
                    'role_name' => $request->user()->getRoleNames()->first(),
                    'roles' => $request->user()->getRoleNames()->values()->toArray(),
                    'permissions' => $request->user()->getAllPermissions()->pluck('name')->values()->toArray(),
                ] : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'transaction' => fn () => $request->session()->get('transaction'),
                'import_warnings' => fn () => $request->session()->get('import_warnings'),
                'imageUrl' => fn () => $request->session()->get('imageUrl')
                    ?? $request->session()->get('flash.imageUrl'),
            ],
            'pembagianMengajukanCount' => function () use ($request) {
                try {
                    $user = $request->user();
                    if (!$user) return 0;
                    if (!$user->hasAnyRole(['sysadmin', 'officer', 'kantin'])) return 0;
                    return PembagianMitraHistory::where('status_pencairan', 'mengajukan')->count();
                } catch (\Exception $e) {
                    return 0;
                }
            },
            'pengajuanPencairanCount' => function () use ($request) {
                try {
                    $user = $request->user();
                    if (!$user) return 0;
                    if (!$user->hasAnyRole(['sysadmin', 'officer', 'kantin'])) return 0;
                    return PengajuanPencairan::where('status', 'menunggu')->count();
                } catch (\Exception $e) {
                    return 0;
                }
            },
            'navbarMenus' => function () {
                try {
                    return MenuItem::with('activeChildren')
                        ->active()
                        ->parents()
                        ->orderBy('order')
                        ->get()
                        ->map(function ($item) {
                            return [
                                'id' => $item->id,
                                'label' => $item->label,
                                'url' => $item->url,
                                'type' => $item->type,
                                'open_new_tab' => $item->open_new_tab,
                                'children' => $item->activeChildren->map(function ($child) {
                                    return [
                                        'id' => $child->id,
                                        'label' => $child->label,
                                        'url' => $child->url,
                                        'type' => $child->type,
                                        'open_new_tab' => $child->open_new_tab,
                                    ];
                                }),
                            ];
                        });
                } catch (\Exception $e) {
                    return [];
                }
            },
        ];
    }
}
