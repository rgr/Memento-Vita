<div x-data="{
    dates: '{{ $begin == $end ? $begin : $begin.' • '.$end }}',
    life: '{{ $life.'%' }}',
    lifeTotal: '{{ $lifeTotal.'%' }}',
}">
    <template x-ref="template" >
        <div class="p-5 text-black rounded-lg shadow-lg bg-gray-50">
            <p class="flex flex-col text-xs">
                <span class="text-base font-bold" x-text="dates"></span>
                <div>
                @if($begin != $end)
                    <div class="flex mt-3 justify-evenly">
                        <div>
                            <span class="italic font-semibold">Life</span> : <span x-text="life"></span>
                        </div>
                        <div>
                            <span class="italic font-semibold">Total Life</span> : <span x-text="lifeTotal"></span>
                        </div>
                    </div>
                @endif
            </p>
        </div>
    </template>
    <div id="{{ Str::slug($name) }}" class="cursor-pointer" x-tooltip="{
            content: () => $refs.template.innerHTML,
            allowHTML: true,
            appendTo: $root,
            interactive: true
        }">
        {{ $name }}
    </div>

    <script>
        document.getElementById('{{ Str::slug($name) }}').addEventListener('click', function() {
            var weeks = document.querySelectorAll('.week');
            weeks.forEach(week => {
                if({{ json_encode($weeks) }}.includes(parseInt(week.id))) {
                    if(week.classList.contains('bg-cyan-500')) {
                        week.classList.remove('bg-cyan-500');
                        week.classList.add('bg-gray-700');
                    } else {
                        week.classList.remove('bg-gray-700');
                        week.classList.add('bg-cyan-500');
                    }
                } else {
                    if(parseInt(week.id) < {{ $currentWeek }}) {
                        week.classList.remove('bg-cyan-500');
                        week.classList.add('bg-gray-700');
                    }
                }
            });
        });
    </script>
</div>
