<?php

namespace App\Livewire;

use App\Traits\DatesHelper;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Component;

class Week extends Component
{
    use DatesHelper;

    public int $week;

    public string $date;

    public string $status;

    public string $formatedDate;

    public int $age;

    public int $life;

    public function mount(int $week, array $data)
    {
        $this->week = $week;
        $diff = Carbon::parse($data['user']['birth'])->diffInWeeks(now());
        if ($diff == $this->week) {
            $this->status = 'current';
        } elseif ($diff < $this->week) {
            $this->status = 'future';
        } elseif ($diff > $this->week) {
            $this->status = 'past';
        }
        $this->date = $this->getDateFromBirth($data['user']['birth'], $this->week);
        $this->formatedDate = Str::title(Carbon::parse($this->date)->locale(app()->getLocale())->isoFormat('MMMM Y').' ('.Carbon::parse($this->date)->weekOfMonth.')');
        $this->age = $this->getAge($data['user']['birth'], $this->date);
        $this->life = $this->getLifeProgress($data['user']['birth'], $data['user']['death'], $this->date);
    }

    public function render()
    {
        return view('livewire.week');
    }
}
