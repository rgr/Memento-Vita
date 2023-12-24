<?php

namespace App\Traits;

use Illuminate\Support\Carbon;

trait DatesHelper
{
    public function getDateFromBirth(string $birth, int $week): string
    {
        $birth = Carbon::parse($birth);

        return $birth->addWeeks($week)->toFormattedDateString();
    }

    public function getAge(string $birth, string $date): int
    {
        $birth = Carbon::parse($birth);
        $date = Carbon::parse($date);

        return $birth->diffInYears($date);
    }

    public function getLifeProgress(string $birth, string $death, string $date): int
    {
        $birth = Carbon::parse($birth);
        $death = Carbon::parse($death);
        $date = Carbon::parse($date);

        return $birth->diffInDays($date) / $birth->diffInDays($death) * 100;
    }

    public function getEventLifeDuration(string $from, string $to, int $weeks): int
    {
        $from = Carbon::parse($from);
        $lifeDuration = $from->diffInWeeks($to);

        return $weeks / $lifeDuration * 100;
    }
}
