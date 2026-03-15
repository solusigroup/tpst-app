<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with('causer');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', '%' . $search . '%')
                  ->orWhere('log_name', 'like', '%' . $search . '%')
                  ->orWhere('event', 'like', '%' . $search . '%')
                  ->orWhereHasMorph('causer', '*', function ($causerQuery) use ($search) {
                      $causerQuery->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        $activities = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('admin.activities.index', compact('activities'));
    }
}
