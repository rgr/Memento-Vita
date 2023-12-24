<div x-data="{
    dates: '{{ $begin == $end ? $begin : $begin.' • '.$end }}',
    life: '{{ $life.'%' }}',
    lifeTotal: '{{ $lifeTotal.'%' }}',
}">
    <template x-ref="template" >
        <div class="rounded-lg shadow-lg p-5 bg-gray-50 text-black">
            <p class="flex flex-col text-xs">
                <span class="font-bold text-base" x-text="dates"></span>
                <div>
                @if($begin != $end)
                    <div class="flex justify-evenly mt-3">
                        <div>
                            <span class="font-semibold italic">Life</span> : <span x-text="life"></span>
                        </div>
                        <div>
                            <span class="font-semibold italic">Total Life</span> : <span x-text="lifeTotal"></span>
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
                week.classList.remove('bg-cyan-500');
                if({{ json_encode($weeks) }}.includes(parseInt(week.id))) {
                    week.classList.add('bg-cyan-500')
                }
            });
        });
    </script>
</div>
