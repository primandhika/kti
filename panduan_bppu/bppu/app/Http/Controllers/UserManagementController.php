<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WorkUnit;
use App\Models\MembershipTier;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    /**
     * Display list of users
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        $isSysadmin = $authUser->hasRole('sysadmin');
        $isDataClerk = $authUser->hasRole('data_clerk');

        $search = $request->input('search');
        $roleFilter = $request->input('role');
        $tierFilter = $request->input('tier');
        // Non-sysadmin hanya boleh akses tab buyers
        $activeTab = $isSysadmin ? $request->input('tab', 'staff') : 'buyers';
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = strtolower($request->input('sort_order', 'desc')) === 'asc' ? 'asc' : 'desc';

        $mitraRoles = ['supplier', 'tenant', 'vendor', 'distributor', 'produsen', 'reseller', 'konsinyasi'];

        // Staff, Mitra & Mesin hanya untuk sysadmin
        if (!$isSysadmin) {
            $empty = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
            $users = $empty;
            $mitras = $empty;
            $mesinUsers = $empty;
        } else {
            // Get staff users (non-buyer, non-mitra, non-mesin) with pagination
            $nonStaffRoles = array_merge(['buyer', 'mesin'], $mitraRoles);
            $usersQuery = User::with(['roles', 'workUnits'])
                ->whereHas('roles', function ($query) use ($nonStaffRoles) {
                    $query->whereNotIn('name', $nonStaffRoles);
                });

        if ($search) {
            $usersQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($roleFilter) {
            $usersQuery->whereHas('roles', function ($query) use ($roleFilter) {
                $query->where('name', $roleFilter);
            });
        }

        switch ($sortBy) {
            case 'name':
                $usersQuery->orderBy('name', $sortOrder);
                break;
            case 'username':
                $usersQuery->orderBy('username', $sortOrder);
                break;
            case 'email':
                $usersQuery->orderBy('email', $sortOrder);
                break;
            case 'role':
                $usersQuery->orderBy(
                    DB::table('roles')
                        ->select('roles.name')
                        ->join('model_has_roles', 'roles.id', '=', 'model_has_roles.role_id')
                        ->whereColumn('model_has_roles.model_id', 'users.id')
                        ->where('model_has_roles.model_type', User::class)
                        ->limit(1),
                    $sortOrder
                );
                break;
            case 'work_unit':
                $usersQuery->orderBy(
                    DB::table('work_units')
                        ->select('work_units.name')
                        ->join('user_work_unit', 'work_units.id', '=', 'user_work_unit.work_unit_id')
                        ->whereColumn('user_work_unit.user_id', 'users.id')
                        ->orderBy('work_units.name')
                        ->limit(1),
                    $sortOrder
                );
                break;
            case 'created_at':
            default:
                $usersQuery->orderBy('created_at', $sortOrder);
                break;
        }

        $users = $usersQuery
            ->paginate(15)
            ->through(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'roles' => $user->getRoleNames(),
                    'work_units' => $user->workUnits->map(function ($unit) {
                        return [
                            'id' => $unit->id,
                            'name' => $unit->name,
                        ];
                    }),
                    'created_at' => $user->created_at->format('d M Y'),
                ];
            });
        } // end else sysadmin (staff)

        // Get buyer users (members) with pagination - semua role bisa akses
        $buyersQuery = User::with(['roles', 'membershipTier'])
            ->whereHas('roles', function ($query) {
                $query->where('name', 'buyer');
            });

        if ($search) {
            $buyersQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('member_code', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($tierFilter) {
            $buyersQuery->where('membership_tier_id', $tierFilter);
        }

        switch ($sortBy) {
            case 'member_code':
                $buyersQuery->orderBy('member_code', $sortOrder);
                break;
            case 'name':
                $buyersQuery->orderBy('name', $sortOrder);
                break;
            case 'contact':
                $buyersQuery->orderBy('email', $sortOrder)->orderBy('phone', $sortOrder);
                break;
            case 'tier':
                $buyersQuery->orderBy(
                    DB::table('membership_tiers')
                        ->select('name')
                        ->whereColumn('membership_tiers.id', 'users.membership_tier_id')
                        ->limit(1),
                    $sortOrder
                );
                break;
            case 'total_points':
                $buyersQuery->orderBy('total_points', $sortOrder);
                break;
            case 'member_since':
                $buyersQuery->orderBy('member_since', $sortOrder);
                break;
            case 'created_at':
            default:
                $buyersQuery->orderBy('created_at', $sortOrder);
                break;
        }

        $buyers = $buyersQuery
            ->paginate(15)
            ->through(function ($user) {
                return [
                    'id' => $user->id,
                    'member_code' => $user->member_code ?? '-',
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'phone' => $user->phone ?? '-',
                    'total_points' => $user->total_points ?? 0,
                    'membership_tier' => $user->membershipTier ? [
                        'id' => $user->membershipTier->id,
                        'name' => $user->membershipTier->name,
                        'color' => $user->membershipTier->color,
                        'discount_percentage' => $user->membershipTier->discount_percentage,
                    ] : null,
                    'member_since' => $user->member_since ? $user->member_since->format('d M Y') : '-',
                    'created_at' => $user->created_at->format('d M Y'),
                ];
            });

        // Get mitra & mesin - sysadmin only (non-sysadmin sudah dapat $empty di atas)
        if ($isSysadmin) {
        // Get mitra users with pagination
        $mitrasQuery = User::with(['roles'])
            ->whereHas('roles', function ($query) use ($mitraRoles) {
                $query->whereIn('name', $mitraRoles);
            });

        if ($search) {
            $mitrasQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        switch ($sortBy) {
            case 'username':
                $mitrasQuery->orderBy('username', $sortOrder);
                break;
            case 'name':
                $mitrasQuery->orderBy('name', $sortOrder);
                break;
            case 'contact':
                $mitrasQuery->orderBy('email', $sortOrder)->orderBy('phone', $sortOrder);
                break;
            case 'role':
                $mitrasQuery->orderBy(
                    DB::table('roles')
                        ->select('roles.name')
                        ->join('model_has_roles', 'roles.id', '=', 'model_has_roles.role_id')
                        ->whereColumn('model_has_roles.model_id', 'users.id')
                        ->where('model_has_roles.model_type', User::class)
                        ->limit(1),
                    $sortOrder
                );
                break;
            case 'created_at':
            default:
                $mitrasQuery->orderBy('created_at', $sortOrder);
                break;
        }

        $mitras = $mitrasQuery
            ->paginate(15)
            ->through(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'phone' => $user->phone ?? '-',
                    'roles' => $user->getRoleNames(),
                    'created_at' => $user->created_at->format('d M Y'),
                ];
            });

        // Get mesin users
        $mesinQuery = User::with(['roles'])
            ->whereHas('roles', function ($query) {
                $query->where('name', 'mesin');
            });

        if ($search) {
            $mesinQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        switch ($sortBy) {
            case 'username':
                $mesinQuery->orderBy('username', $sortOrder);
                break;
            case 'name':
                $mesinQuery->orderBy('name', $sortOrder);
                break;
            case 'email':
                $mesinQuery->orderBy('email', $sortOrder);
                break;
            case 'created_at':
            default:
                $mesinQuery->orderBy('created_at', $sortOrder);
                break;
        }

        $mesinUsers = $mesinQuery
            ->paginate(15)
            ->through(function ($user) {
                return [
                    'id'         => $user->id,
                    'name'       => $user->name,
                    'username'   => $user->username,
                    'email'      => $user->email,
                    'roles'      => $user->getRoleNames(),
                    'created_at' => $user->created_at->format('d M Y'),
                ];
            });
        } // end if sysadmin (mitra & mesin)

        // Roles available: data_clerk hanya bisa assign buyer, officer tidak bisa assign sysadmin
        $allRoles = Role::all();
        $roles = $allRoles
            ->when($isDataClerk, fn($col) => $col->filter(fn($r) => $r->name === 'buyer'))
            ->when(!$isSysadmin && !$isDataClerk, fn($col) => $col->reject(fn($r) => $r->name === 'sysadmin'))
            ->map(function ($role) {
                return [
                    'name' => $role->name,
                    'description' => $role->description ?? '',
                ];
            })->values();

        $workUnits = WorkUnit::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function ($unit) {
                return [
                    'id' => $unit->id,
                    'name' => $unit->name,
                    'type' => $unit->type,
                ];
            });

        $membershipTiers = MembershipTier::where('is_active', true)
            ->orderBy('order')
            ->get()
            ->map(function ($tier) {
                return [
                    'id' => $tier->id,
                    'name' => $tier->name,
                    'color' => $tier->color,
                ];
            });

        return Inertia::render('Admin/UserManagement/Index', [
            'users' => $users,
            'buyers' => $buyers,
            'mitras' => $mitras,
            'mesinUsers' => $mesinUsers,
            'roles' => $roles,
            'workUnits' => $workUnits,
            'membershipTiers' => $membershipTiers,
            'isSysadmin' => $isSysadmin,
            'isDataClerk' => $isDataClerk,
            'filters' => [
                'search' => $search,
                'role' => $roleFilter,
                'tier' => $tierFilter,
                'tab' => $activeTab,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
            ],
        ]);
    }

    /**
     * Store a new user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
            'role' => 'required|exists:roles,name',
            'work_unit_ids' => 'nullable|array',
            'work_unit_ids.*' => 'exists:work_units,id',
            'membership_tier_id' => 'nullable|exists:membership_tiers,id',
        ]);

        $authUser = auth()->user();
        if (!$authUser->hasRole('sysadmin') && $validated['role'] === 'sysadmin') {
            return redirect()->back()->with('error', 'Tidak memiliki izin untuk membuat akun dengan role System Admin.');
        }
        if ($authUser->hasRole('data_clerk') && $validated['role'] !== 'buyer') {
            return redirect()->back()->with('error', 'Akun data clerk hanya dapat membuat akun Member Buyer.');
        }

        $userData = [
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'must_change_password' => $request->boolean('must_change_password', true),
        ];

        // For buyer role, member_code MUST always equal username
        if ($validated['role'] === 'buyer') {
            $userData['member_code'] = $validated['username'];
            $userData['member_since'] = now();
            $userData['total_points'] = 0;

            // Set membership tier (default or provided)
            if (!empty($validated['membership_tier_id'])) {
                $userData['membership_tier_id'] = $validated['membership_tier_id'];
            }
        }

        $user = User::create($userData);

        $user->assignRole($validated['role']);

        // Attach work units (not for buyer)
        if ($validated['role'] !== 'buyer' && !empty($validated['work_unit_ids'])) {
            $user->workUnits()->attach($validated['work_unit_ids']);
        }

        ActivityLogger::log('create', 'user', "Tambah pengguna: {$user->name} ({$validated['role']})", $user);

        return redirect()->back()->with('success', 'User berhasil ditambahkan!');
    }

    /**
     * Update existing user
     */
    public function update(Request $request, User $user)
    {
        $authUser = auth()->user();
        if (!$authUser->hasRole('sysadmin') && $user->hasRole('sysadmin')) {
            return redirect()->back()->with('error', 'Tidak memiliki izin untuk mengubah akun System Admin.');
        }
        if ($authUser->hasRole('data_clerk') && !$user->hasRole('buyer')) {
            return redirect()->back()->with('error', 'Akun data clerk hanya dapat mengubah akun Member Buyer.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'role' => 'required|exists:roles,name',
            'password' => 'nullable|string|min:8',
            'work_unit_ids' => 'nullable|array',
            'work_unit_ids.*' => 'exists:work_units,id',
            'membership_tier_id' => 'nullable|exists:membership_tiers,id',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ];

        // For buyer role, member_code MUST always equal username
        if ($validated['role'] === 'buyer') {
            $updateData['member_code'] = $validated['username'];

            if (isset($validated['membership_tier_id'])) {
                $updateData['membership_tier_id'] = $validated['membership_tier_id'];
            }
        }

        $user->update($updateData);

        if (!empty($validated['password'])) {
            $user->update([
                'password' => Hash::make($validated['password']),
                'must_change_password' => $request->boolean('must_change_password', false),
            ]);
        }

        // Sync role
        $user->syncRoles([$validated['role']]);

        // Sync work units (not for buyer)
        if ($validated['role'] !== 'buyer' && isset($validated['work_unit_ids'])) {
            $user->workUnits()->sync($validated['work_unit_ids']);
        } else if ($validated['role'] === 'buyer') {
            $user->workUnits()->sync([]);
        }

        ActivityLogger::log('update', 'user', "Update pengguna: {$user->name} (role: {$validated['role']})", $user);

        return redirect()->back()->with('success', 'User berhasil diupdate!');
    }

    /**
     * Delete user
     */
    public function destroy(User $user)
    {
        // Prevent deleting own account
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus akun sendiri!');
        }

        $authUser = auth()->user();
        if (!$authUser->hasRole('sysadmin') && $user->hasRole('sysadmin')) {
            return redirect()->back()->with('error', 'Tidak memiliki izin untuk menghapus akun System Admin.');
        }
        if ($authUser->hasRole('data_clerk') && !$user->hasRole('buyer')) {
            return redirect()->back()->with('error', 'Akun data clerk hanya dapat menghapus akun Member Buyer.');
        }

        ActivityLogger::log('delete', 'user', "Hapus pengguna: {$user->name} ({$user->roles->pluck('name')->join(', ')})");

        $user->delete();

        return redirect()->back()->with('success', 'User berhasil dihapus!');
    }

    public function searchMember(Request $request)
    {
        $q = $request->get('q', '');
        $limit = min((int) $request->get('limit', 10), 50);

        $members = User::where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('member_code', 'like', "%{$q}%")
                    ->orWhere('nim', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            })
            ->select('id', 'name', 'nim', 'member_code', 'email')
            ->limit($limit)
            ->get();

        return response()->json($members);
    }
}
