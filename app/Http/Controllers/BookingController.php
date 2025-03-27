<?php

namespace App\Http\Controllers;

use App\Models\ProviderProfile;
use App\Models\WorkingHour;

class BookingController extends Controller
{
    public function index($id)
    {
        $provider = ProviderProfile::with('user')->findOrFail($id);

        return view('includes.booking', compact('provider'));
    }

    public function getBusinessHours($providerId)
    {

        $workingHours = WorkingHour::where('provider_id', $providerId)
            ->where('is_working_day', 1)
            ->get(['day', 'open_time', 'close_time']);

        $dayMap = [
            'Sunday' => 0,
            'Monday' => 1,
            'Tuesday' => 2,
            'Wednesday' => 3,
            'Thursday' => 4,
            'Friday' => 5,
            'Saturday' => 6,
        ];

        $businessHours = [];
        foreach ($workingHours as $hour) {
            $businessHours[] = [
                'daysOfWeek' => $dayMap[$hour->day],
                'startTime' => substr($hour->open_time, 0, 5),
                'endTime' => substr($hour->close_time, 0, 5),
            ];
        }

        return response()->json($businessHours, 200, [], JSON_PRETTY_PRINT);

    }
}
