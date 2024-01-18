<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <title>Memento Vita • {{ $data['user']['name'] }}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="p-10 antialiased text-white bg-fixed bg-gray-900 bg-no-repeat bg-fit" style="background-image: url('img/skull.svg');">
        <div class='flex justify-center mb-10 text-6xl font-extralight'>
            <h1>Memento Vita, {{ $data['user']['name'] }}</h1>
        </div>

        @if($data != null)
            <div class='flex my-5'>
                <div class='text-2xl italic font-light'><h5>{{ Carbon\Carbon::parse($data['user']['birth'])->locale(app()->getLocale())->isoFormat('D MMMM Y') }}</h5></div>
            </div>

            <div class='flex flex-wrap'>
                @for($i = 1; $i <= $data['user']['weeks']; $i++)
                    @livewire('week', ['week' => $i, 'data' => $data])
                @endfor
            </div>

            <div class='flex justify-end my-5'>
                <div class='text-2xl italic font-light'><h5>{{ Carbon\Carbon::parse($data['user']['death'])->locale(app()->getLocale())->isoFormat('D MMMM Y') }}</h5></div>
            </div>

            <div class="flex flex-col space-y-1">
                @foreach($data['categories'] as $category)
                    <div class="flex items-start justify-start">
                        <div class='w-32 italic font-semibold'>
                            <h5>@livewire('event', ['birth' => $data['user']['birth'], 'death' => $data['user']['death'], 'event' => $category])</h5>
                        </div>

                        <div class='flex flex-wrap w-4/5'>
                            @foreach($category['events'] as $event)
                                @livewire('event', ['birth' => $data['user']['birth'], 'death' => $data['user']['death'], 'event' => $event])
                                @if(!$loop->last)
                                    <span class='mx-1'>•</span>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class='flex justify-center text-2xl italic font-light'>
                <h5>No data for this user.</h5>
            </div>
        @endif
    </body>
</html>
