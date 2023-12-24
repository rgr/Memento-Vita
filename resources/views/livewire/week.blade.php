<div x-data="{
    date: '{{ $formatedDate }}',
    week: '{{ $week }}',
    age: '{{ $age.' ans' }}',
    life: '{{ $life.'%' }}',
}">
    <template x-ref="template" >
        <div @class(['rounded-lg shadow-lg p-5 w-48',
        'bg-gray-700 text-white' => !Carbon\Carbon::parse($date)->isCurrentWeek(),
        'bg-green-500 text-white' => Carbon\Carbon::parse($date)->isCurrentWeek(),
        ])>
            <p class="flex flex-col text-xs">
                <span class="font-bold text-base mb-3" x-text="date"></span>
                <div>
                    <span class="font-semibold italic">Week</span> : <span x-text="week"></span>
                </div>
                <div>
                    <span class="font-semibold italic">Age</span> : <span x-text="age"></span>
                </div>
                <div>
                    <span class="font-semibold italic">Life</span> : <span x-text="life"></span>
                </div>
            </p>
        </div>
    </template>
    <div @class(['week w-1 h-1 md:w-2 md:h-2 mt-0.5 mr-0.5 2xl:w-4 2xl:h-4 lg:mt-1 lg:mr-1 cursor-pointer rounded-sm',
        'bg-gray-700' => $status == 'past',
        'border-gray-700 border' => $status == 'future',
        'bg-green-500' => $status == 'current',
        ])
        id='{{ $week }}' x-tooltip="{
            content: () => $refs.template.innerHTML,
            allowHTML: true,
            appendTo: $root,
            interactive: true
        }">
    </div>
</div>
