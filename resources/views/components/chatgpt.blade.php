
   <div class="container m-auto pt-20">
    <div class="w-1/2 m-auto">
        @if (Route::has('login'))
        <div class="sm:fixed sm:top-0 sm:right-0 p-6 text-right">
            @auth
            <a href="{{ url('/home') }}"
                class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Home</a>
            @else
            <a href="{{ route('login') }}"
                class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Log
                in</a>

            @if (Route::has('register'))
            <a href="{{ route('register') }}"
                class="ml-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Register</a>
            @endif
            @endauth
        </div>
        @endif

        <form class="p-4 flex space-x-4 justify-center items-center" action="/" method="post">
            @csrf
            <label for="message">Question:</label>
            <input id="message" type="text" name="message" autocomplete="off" class="border rounded-md  p-2 flex-1" />
            <a class="bg-gray-800 text-white p-2 rounded-md" href="/reset">Reset Conversation</a>
        </form>
        @foreach($messages as $message)
        <div
            class="flex rounded-lg p-4 m-4 @if ($message['role'] === 'assistant') bg-green-200 flex-reverse @else bg-blue-200 @endif ">
            <div class="ml-4">
                <div class="text-lg">
                    @if ($message['role'] === 'assistant')
                    <a href="#" class="font-medium text-gray-900">GPT</a>
                    @else
                    <a href="#" class="font-medium text-gray-900">You</a>
                    @endif
                </div>
                <div class="mt-1">
                    <p class="text-gray-600">


                        {!! \Illuminate\Mail\Markdown::parse($message['content']) !!}

                    </p>
                </div>
            </div>
        </div>
        @endforeach
    </div>

</div>
