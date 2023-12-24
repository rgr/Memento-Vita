<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MementoVitaController extends Controller
{
    public function index(Request $request)
    {
        ini_set('memory_limit', '2048M');

        $name = $request->route('name');
        if (! $name) {
            return redirect()->route('index', ['name' => 'john']);
        }

        $data = $this->loadData($name);

        return view('welcome', compact('data'));
    }

    private function loadData(string $name): array
    {
        if (! Storage::disk('local')->json($name.'.json')) {
            return [];
        }

        $data = Storage::disk('local')->json($name.'.json');

        // User
        $birth = Carbon::parse($data['user']['birth']);
        $death = Carbon::parse($data['user']['death']);
        $user = [
            'name' => $data['user']['name'],
            'birth' => $birth,
            'death' => $death,
            'weeks' => $birth->diffInWeeks($death),
        ];

        // Events & categories
        $categories = [];
        foreach ($data['events'] as $category => $events) {
            $name = $category;
            $category = Str::slug($category);
            $categories[$category]['name'] = $name;
            $categories[$category]['begin'] = null;
            $categories[$category]['end'] = null;
            $categories[$category]['weeks'] = [];
            foreach ($events as $event) {
                $begin = null;
                $end = null;
                $weeks = [];
                foreach ($event['period'] as $period) {
                    $period['end'] == 'now' ? $period['end'] = now()->format('d-m-Y') : $period['end'];
                    if ($categories[$category]['begin'] == null || Carbon::parse($categories[$category]['begin'])->isAfter(Carbon::parse($period['begin']))) {
                        $categories[$category]['begin'] = $period['begin'];
                    }
                    if ($categories[$category]['end'] == null || Carbon::parse($categories[$category]['end'])->isBefore(Carbon::parse($period['end']))) {
                        $categories[$category]['end'] = $period['end'];
                    }
                    if ($begin == null || Carbon::parse($begin)->isAfter(Carbon::parse($period['begin']))) {
                        $begin = $period['begin'];
                    }
                    if ($end == null || Carbon::parse($end)->isBefore(Carbon::parse($period['end']))) {
                        $end = $period['end'];
                    }
                    // Get the weeks number between the begin and the end of the period
                    $weeks = array_merge($weeks, range($birth->diffInWeeks(Carbon::parse($period['begin'])), $birth->diffInWeeks(Carbon::parse($period['end']))));
                }
                $categories[$category]['weeks'] = array_unique(array_merge($categories[$category]['weeks'], $weeks));
                $categories[$category]['events'][] = [
                    'name' => $event['name'],
                    'begin' => $begin,
                    'end' => $end,
                    'weeks' => $weeks,
                    'minWeek' => min($weeks)];
            }
        }

        return [
            'user' => $user,
            'categories' => $categories,
        ];
    }
}
