<?php

namespace App\Livewire;

use App\Traits\DatesHelper;
use Illuminate\Support\Carbon;
use Livewire\Component;

class Event extends Component
{
    use DatesHelper;

    public string $name;

    public string $begin;

    public string $end;

    public array $weeks;

    public int $life;

    public int $lifeTotal;

    public function mount(string $birth, string $death, array $event)
    {
        $this->name = $event['name'];
        $this->begin = Carbon::parse($event['begin'])->locale(app()->getLocale())->isoFormat('D MMMM Y');
        $this->end = Carbon::parse($event['end'])->locale(app()->getLocale())->isoFormat('D MMMM Y');
        $this->weeks = $event['weeks'];
        $this->life = $this->getEventLifeDuration($birth, now()->format('d-m-Y'), count($this->weeks));
        $this->lifeTotal = $this->getEventLifeDuration($birth, $death, count($this->weeks));
    }

    public function render()
    {
        return view('livewire.event');
    }
}
