<?php

namespace App\Http\Controllers\Web\Manager;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Stat cards
        $stats = [
            'total_companies'    => Company::count(),
            'active_companies'   => Company::active()->count(),
            'pending_companies'  => Company::pending()->count(),
            'inactive_companies' => Company::inactive()->count(),
            'total_users'        => User::count(),
        ];

        // Monthly company growth — last 12 months
        $companiesGrowth = Company::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('YEAR(created_at) as year'),
            DB::raw('COUNT(*) as total')
        )
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Monthly user growth — last 12 months
        $usersGrowth = User::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('YEAR(created_at) as year'),
            DB::raw('COUNT(*) as total')
        )
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Build 12-month label array
        $months = collect(range(11, 0))->map(fn ($i) => now()->subMonths($i));

        $companiesChartData = $months->map(function ($month) use ($companiesGrowth) {
            $found = $companiesGrowth->first(
                fn ($r) => (int) $r->month === (int) $month->month && (int) $r->year === (int) $month->year
            );

            return $found ? (int) $found->total : 0;
        });

        $usersChartData = $months->map(function ($month) use ($usersGrowth) {
            $found = $usersGrowth->first(
                fn ($r) => (int) $r->month === (int) $month->month && (int) $r->year === (int) $month->year
            );

            return $found ? (int) $found->total : 0;
        });

        $chartLabels = $months->map(fn ($m) => $m->locale('ar')->isoFormat('MMM YYYY'));

        // Recent activity
        $latestCompanies = Company::with('user')->latest()->limit(5)->get();
        $latestUsers     = User::latest()->limit(5)->get();

        return view('admin.dashboard', compact(
            'stats',
            'latestCompanies',
            'latestUsers',
            'companiesChartData',
            'usersChartData',
            'chartLabels'
        ));
    }
}
