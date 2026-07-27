<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MemberSurvey;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SurveyResultController extends Controller
{
    public function index(Request $request)
    {
        $query = MemberSurvey::with(['user:id,name,username,email']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('menu_favorit', 'like', "%{$search}%")
              ->orWhere('menu_rekomendasi', 'like', "%{$search}%")
              ->orWhere('kritik_saran', 'like', "%{$search}%");
        }

        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        if (!in_array($sortField, ['created_at', 'rating_pelayanan', 'rating_kualitas', 'rating_variasi', 'rating_harga', 'rating_suasana'])) {
            $sortField = 'created_at';
        }

        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }

        $query->orderBy($sortField, $sortDirection);

        $surveys = $query->paginate(15)->withQueryString();

        $stats = $this->getStats();

        return Inertia::render('Admin/Survey/Index', [
            'surveys' => $surveys,
            'stats' => $stats,
            'filters' => $request->only(['search', 'sort', 'direction']),
        ]);
    }

    private function getStats(): array
    {
        $total = MemberSurvey::count();

        if ($total === 0) {
            return [
                'total' => 0,
                'avg_pelayanan' => 0,
                'avg_kualitas' => 0,
                'avg_variasi' => 0,
                'avg_harga' => 0,
                'avg_suasana' => 0,
                'avg_keseluruhan' => 0,
            ];
        }

        $avgs = MemberSurvey::selectRaw('
            AVG(rating_pelayanan) as avg_pelayanan,
            AVG(rating_kualitas) as avg_kualitas,
            AVG(rating_variasi) as avg_variasi,
            AVG(rating_harga) as avg_harga,
            AVG(rating_suasana) as avg_suasana
        ')->first();

        $avgKeseluruhan = ($avgs->avg_pelayanan + $avgs->avg_kualitas + $avgs->avg_variasi + $avgs->avg_harga + $avgs->avg_suasana) / 5;

        return [
            'total' => $total,
            'avg_pelayanan' => round($avgs->avg_pelayanan, 2),
            'avg_kualitas' => round($avgs->avg_kualitas, 2),
            'avg_variasi' => round($avgs->avg_variasi, 2),
            'avg_harga' => round($avgs->avg_harga, 2),
            'avg_suasana' => round($avgs->avg_suasana, 2),
            'avg_keseluruhan' => round($avgKeseluruhan, 2),
        ];
    }
}
