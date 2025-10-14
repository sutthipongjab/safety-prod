<div class="hidden preload" id="preload"> 
    <div class="fixed inset-0 flex items-center justify-center bg-gray-200 bg-opacity-50 z-[9999]">
        <div class="relative animate-moveSide  flex items-center">
        {{-- <div class="relative transition-transform duration-500 hover:translate-x-10">  moveSide--}}
        {{-- <div class="relative animate-bounce"> --}}
          {{-- <div class="absolute animate-spin rounded-full h-32 w-32 border-t-4 border-b-4 border-purple-500"></div> --}}
          {{-- <div class="absolute animate-spin rounded-full h-32 w-32 "></div> --}}
            <img
                src="{{ base_url() }}assets/images/preload.png"
                {{-- src="https://www.svgrepo.com/show/509001/avatar-thinking-9.svg" --}}
                class="rounded-full h-28 w-28  animate-bounce"
            />
            <div div class="flex space-x-2 items-center  animate-bounce">
                <div class="w-4 h-4 bg-gray-300 rounded-full animate-smoke"></div>
                <div class="w-5 h-5 bg-gray-400 rounded-full animate-smoke"></div>
                <div class="w-6 h-6 bg-gray-500 rounded-full animate-smoke"></div>
            </div>
        </div>
    </div>
</div>