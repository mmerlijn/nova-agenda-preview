<?php

use App\Helpers\TimeArray;
use App\Models\pa\Appointment;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. You're free to add
| as many additional routes to this file as your tool may require.
|
*/

// Route::get('/endpoint', function (Request $request) {
//     //
// });
Route::post('/preview/{room}',function(\App\Models\pa\Room $room,Request $request){
    $days = [1 => 'Maandag', 2 => "Dinsdag", 3 => "Woensdag", 4 => "Donderdag", 5 => "Vrijdag", 6 => "Zaterdag", 7 => "Zondag"];
    $dagen_vooruit = min($request->filter['weeks'] * 7, $room->appointment_max_days_ahead_employee);
    $dates = [];
    $activities = [];
    $min_tijd = 1440;
    $max_tijd = 0;

    //alle rules doorlopen met het filter
    foreach ($room->plannings as $rule) {
        if ($rule['active']) {
            //startdatum van de rrule bepalen
            $start = Carbon::parse($rule['start_date']);
            if (Carbon::parse($request->filter['start_date'])->isAfter($rule['start_date'])) {
                $start = Carbon::parse($request->filter['start_date'])->startOfDay();
            }
            //we gaan even kijken hoe ver afspraken in de betreffende ruimte vooruit gepland kunnen worden
            $endDate = CarbonImmutable::parse($request->filter['start_date'])->addDays($dagen_vooruit);


            $RRule = new \Recurr\Rule($rule['rule'], $start->toDateTime());
            //zoek tussen het al eerder bepaalde interval (afhankelijk van week)
            $possibleDates = (new \Recurr\Transformer\ArrayTransformer())->transform($RRule)
                ->startsBetween($start->toDateTime(), $endDate->toDateTime(), true);
            // dd($possibleDates);
            $possibleDates = $possibleDates->toArray();
            //vullen van de agenda array
            foreach ($possibleDates as $date) {
                //er is al iets dus clickTimes toevoegen
                $date_key = $date->getStart()->format('Ymd');
                if (array_key_exists($date_key, $dates)) {
                    if (!isset($dates[$date_key]['activities'][$rule['agroup_id']])) { //bij volgende planning bij locatie kan het zijn dat de activiteit nog niet gezet is
                        $dates[$date_key]['activities'][$rule['agroup_id']] = $rule['agroup'];
                    }
                    $dates[$date_key]['activities'][$rule['agroup_id']]['times'] =
                        TimeArray::create($dates[$date_key]['activities'][$rule['agroup_id']]['times'] ?? [])
                            ->add($rule['times'])->get();

                } else {
                    $rule['agroup']['times'] = TimeArray::create([])
                        ->add($rule['times'])->get();
                    $dates[$date_key] = [
                        'date' => $date->getStart()->format('Y-m-d'),
                        'day' => $date->getStart()->format('N'),
                        'day_text' => $days[$date->getStart()->format('N')],
                        'activities' => [$rule['agroup_id'] => $rule['agroup']],
                        'id' => $date_key,
                    ];
                }
            }
        }
    }
    foreach ($room->closes as $rule) {
        if ($rule['active']) {
            //startdatum van de rrule bepalen
            $start = Carbon::parse($rule['start_date']);
            if (Carbon::parse($request->filter['start_date'])->isAfter($rule['start_date'])) {
                $start = Carbon::parse($request->filter['start_date'])->startOfDay();
            }
            //we gaan even kijken hoe ver afspraken in de betreffende ruimte vooruit gepland kunnen worden
            $endDate = CarbonImmutable::parse($request->filter['start_date'])->addDays($dagen_vooruit);

            $RRule = new \Recurr\Rule($rule['rule'], $start);
            //zoek tussen het al eerder bepaalde interval (afhankelijk van week)
            $possibleDates = (new \Recurr\Transformer\ArrayTransformer())->transform($RRule)
                ->startsBetween($start, $endDate->toDateTime(), true);
            $possibleDates = $possibleDates->toArray();
            foreach ($possibleDates as $date) {
                if (array_key_exists($date->getStart()->format('Ymd'), $dates)) {

                    foreach ($dates[$date->getStart()->format('Ymd')]['activities'] as $id => $a) {
                        $dates[$date->getStart()->format('Ymd')]['activities'][$id]['times'] =
                            TimeArray::create($dates[$date->getStart()->format('Ymd')]['activities'][$id]['times'] ?? [])
                                ->subtract($rule['times'])->get();
                    }
                }
            }


        }
    }

    foreach ($dates as $dag => $date) {
        foreach ($date['activities'] as $id => $a) {
            if (empty($a['times'])) {
                unset($dates[$dag]['activities'][$id]);
            } else {
                foreach ($a['times'] as $clickTime) {
                    $min_tijd = ($clickTime[0] < $min_tijd) ? $clickTime[0] : $min_tijd;
                    $max_tijd = ($clickTime[1] > $max_tijd) ? $clickTime[1] : $max_tijd;
                }
            }

        }
        if (empty($dates[$dag]['activities'])) {
            unset($dates[$dag]);
        }
    }
    if ($request->filter['withAppointments']) {
        $appointments = Appointment::whereRoomId($room->id)
            ->where('start_time', '>=', $request->filter['start_date'])
            ->where('start_time', '<=', Carbon::parse($request->filter['start_date'])->addDays($request->filter['weeks'] * 7)->endOfDay()->format('Y-m-d H:i:s'))
            ->withTrashed()->get();
        foreach ($appointments as $appointment) {
            $dates[$appointment->start_time->format('Ymd')]['appointments'][$appointment->id] = new \App\Http\Resources\pa\Appointment($appointment);
        }
    }
    return ['agenda' => $dates,
        'start' => $min_tijd,
        'eind' => $max_tijd,
    ];

});
