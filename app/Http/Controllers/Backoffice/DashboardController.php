<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\Client;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\RentalContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $agency = $user->agency;
        
        // Get date range from request or set defaults
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : now()->subDays(7);
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date')) : now();
        $period = $request->get('period', 'custom');
        
        // VEHICLE STATS (not filtered by date - these are totals)
        $vehicleQuery = Vehicle::query();
        if ($user->hasRole('manager') && $agency) {
            $vehicleQuery->where('agency_id', $agency->id);
        }
        
        $totalVehicles = $vehicleQuery->count();
        $availableVehicles = (clone $vehicleQuery)->where('status', 'available')->count();
        $rentedVehicles = (clone $vehicleQuery)->where('status', 'rented')->count();
        
        // VEHICLE CHANGE (compare with previous period)
        $previousStartDate = clone $startDate;
        $previousEndDate = clone $endDate;
        $daysDiff = $startDate->diffInDays($endDate);
        $previousStartDate->subDays($daysDiff + 1);
        $previousEndDate->subDays($daysDiff + 1);
        
        $previousPeriodVehicles = (clone $vehicleQuery)
            ->whereBetween('created_at', [$previousStartDate, $previousEndDate])
            ->count();
        $vehicleChange = $previousPeriodVehicles > 0 ? round((($totalVehicles - $previousPeriodVehicles) / $previousPeriodVehicles) * 100) : 0;
        
        // BOOKING STATS - filtered by date
        $bookingQuery = Booking::query();
        if ($user->hasRole('manager') && $agency) {
            $bookingQuery->where('agency_id', $agency->id);
        }
        
        // Apply date filter to bookings created in this period
        $filteredBookingQuery = (clone $bookingQuery)
            ->whereBetween('created_at', [$startDate, $endDate]);
        
        $totalBookings = $filteredBookingQuery->count();
        $activeBookings = (clone $filteredBookingQuery)->whereIn('status', ['active', 'confirmed'])->count();
        
        // BOOKINGS GROWTH (compare with previous period)
        $previousPeriodBookings = (clone $bookingQuery)
            ->whereBetween('created_at', [$previousStartDate, $previousEndDate])
            ->count();
        $bookingsGrowth = $previousPeriodBookings > 0 ? round((($totalBookings - $previousPeriodBookings) / $previousPeriodBookings) * 100) : 0;
        
        // REVENUE STATS - filtered by date
        $paymentQuery = Payment::query();
        if ($user->hasRole('manager') && $agency) {
            $paymentQuery->where('agency_id', $agency->id);
        }
        
        $totalRevenue = (clone $paymentQuery)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');
        
        $monthRevenue = (clone $paymentQuery)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');
        
        // REVENUE GROWTH
        $previousPeriodRevenue = (clone $paymentQuery)
            ->whereBetween('created_at', [$previousStartDate, $previousEndDate])
            ->sum('amount');
        $revenueGrowth = $previousPeriodRevenue > 0 ? round((($totalRevenue - $previousPeriodRevenue) / $previousPeriodRevenue) * 100) : 0;
        
        // EXPENSES (if you have an expenses table)
        $monthExpenses = 0;
        $expenseGrowth = 0;
        
        // NEWLY ADDED CARS (last 4 vehicles - not filtered by date)
        $newVehicles = (clone $vehicleQuery)
            ->with('model.brand')
            ->latest()
            ->limit(4)
            ->get();
        
        // RECENT BOOKINGS (filtered by date)
        $recentBookings = (clone $bookingQuery)
            ->with(['client', 'vehicle.model.brand'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($booking) {
                if ($booking->start_date && $booking->end_date) {
                    $start = Carbon::parse($booking->start_date);
                    $end = Carbon::parse($booking->end_date);
                    $booking->duration = $start->diffInDays($end) . ' Days';
                } else {
                    $booking->duration = 'N/A';
                }
                return $booking;
            });
        
        // TOP CUSTOMERS (filtered by date)
        $topCustomers = Client::select('clients.*')
            ->selectSub(function ($query) use ($startDate, $endDate) {
                $query->from('bookings')
                    ->whereColumn('bookings.client_id', 'clients.id')
                    ->whereBetween('bookings.created_at', [$startDate, $endDate])
                    ->selectRaw('count(*)');
            }, 'bookings_count')
            ->when($user->hasRole('manager') && $agency, function ($q) use ($agency) {
                $q->where('clients.agency_id', $agency->id);
            })
            ->orderBy('bookings_count', 'desc')
            ->limit(3)
            ->get();
        
        return view('backoffice.dashboard.index', compact(
            'totalVehicles',
            'availableVehicles',
            'rentedVehicles',
            'vehicleChange',
            'totalBookings',
            'activeBookings',
            'bookingsGrowth',
            'totalRevenue',
            'monthRevenue',
            'revenueGrowth',
            'monthExpenses',
            'expenseGrowth',
            'newVehicles',
            'recentBookings',
            'topCustomers',
            'startDate',
            'endDate',
            'period'
        ));
    }
    
    /**
     * Get filtered statistics for AJAX requests
     */
    public function getFilteredStats(Request $request)
    {
        $user = auth()->user();
        $agency = $user->agency;
        
        $startDate = Carbon::parse($request->get('start_date'));
        $endDate = Carbon::parse($request->get('end_date'));
        
        // Bookings count in date range
        $bookingQuery = Booking::query();
        if ($user->hasRole('manager') && $agency) {
            $bookingQuery->where('agency_id', $agency->id);
        }
        
        $bookings = (clone $bookingQuery)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
        
        // Revenue in date range
        $paymentQuery = Payment::query();
        if ($user->hasRole('manager') && $agency) {
            $paymentQuery->where('agency_id', $agency->id);
        }
        
        $revenue = (clone $paymentQuery)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');
        
        return response()->json([
            'success' => true,
            'bookings' => $bookings,
            'revenue' => $revenue,
            'formatted_revenue' => '$' . number_format($revenue, 0)
        ]);
    }
}