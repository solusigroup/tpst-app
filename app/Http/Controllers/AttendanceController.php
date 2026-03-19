<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Handle check-in request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkIn()
    {
        $user = Auth::user();

        // Logic for check-in (e.g., save to database)
        // Save attendance record
        $tenantId = $user->tenant_id ?? null;
        $today = now()->toDateString();

        \App\Models\Attendance::firstOrCreate(
            [
                'tenant_id' => $tenantId,
                'user_id' => $user->id,
                'attendance_date' => $today,
            ],
            [
                'status' => 'present',
            ]
        )->update(['check_in' => now()->format('H:i:s')]);

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Check-in successful', 'user' => $user->name, 'time' => now()->toDateTimeString()]);
        }

        return back()->with('success', 'Check-in berhasil');
    }

    /**
     * Handle check-out request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkOut()
    {
        $user = Auth::user();

        // Logic for check-out (e.g., save to database)
        $tenantId = $user->tenant_id ?? null;
        $today = now()->toDateString();

        $attendance = \App\Models\Attendance::where('tenant_id', $tenantId)
            ->where('user_id', $user->id)
            ->where('attendance_date', $today)
            ->first();

        if ($attendance) {
            $attendance->update(['check_out' => now()->format('H:i:s')]);
        }

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Check-out successful', 'user' => $user->name, 'time' => now()->toDateTimeString()]);
        }

        return back()->with('success', 'Check-out berhasil');
    }
}