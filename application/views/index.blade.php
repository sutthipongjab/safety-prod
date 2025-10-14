@extends('layout/template')
@section('contents')
    <div class="flex mb-5 mt-16">
        <div class="flex-1">
            {{-- form status --}}
            {{-- @include('home/status') --}}
            {{-- <div class="flex flex-wrap justify-between gap-3 sm:m-5 max-sm:justify-center max-sm:w-lvw mb-5 ">
                <div class="w-[23%] max-md:w-[48%] max-sm:w-[70%] h-40 bg-gray-200 shadow-xl rounded-md "></div>
                <div class="w-[23%] max-md:w-[48%] max-sm:w-[70%] h-40 bg-gray-200 shadow-xl rounded-md "></div>
                <div class="w-[23%] max-md:w-[48%] max-sm:w-[70%] h-40 bg-gray-200 shadow-xl rounded-md "></div>
                <div class="w-[23%] max-md:w-[48%] max-sm:w-[70%] h-40 bg-gray-200 shadow-xl rounded-md "></div>
            </div> --}}
            <div class="flex flex-col justify-center gap-3 mx-20 mb-5 sm:mx-5 sm:flex-wrap sm:flex-row md:justify-between ">
                <div class="card bg-base-100 flex-1 shadow-xl max-md:min-w-[48%] h-40">
                    <div class="card-body">
                        <h2 class="card-title">Card title!</h2>
                        {{-- <p>If a dog chews shoes whose shoes does he choose?</p> --}}
                    </div>
                </div>
                <div class="card bg-base-100 flex-1 shadow-xl max-md:min-w-[48%] h-40">
                    <div class="card-body">
                        <h2 class="card-title">Card title!</h2>
                        {{-- <p>If a dog chews shoes whose shoes does he choose?</p> --}}
                    </div>
                </div>
                <div class="card bg-base-100 flex-1 shadow-xl max-md:min-w-[48%] h-40">
                    <div class="card-body">
                        <h2 class="card-title">Card title!</h2>
                        {{-- <p>If a dog chews shoes whose shoes does he choose?</p> --}}
                    </div>
                </div>
                <div class="card bg-base-100 flex-1 shadow-xl max-md:min-w-[48%] h-40">
                    <div class="card-body">
                        <h2 class="card-title">Card title!</h2>
                        {{-- <p>If a dog chews shoes whose shoes does he choose?</p> --}}
                    </div>
                </div>
                {{-- <div class="bg-gray-200 shadow-xl rounded-md flex-1">d</div>
                <div class="bg-gray-200 shadow-xl rounded-md flex-1">d</div>
                <div class="bg-gray-200 shadow-xl rounded-md flex-1">d</div> --}}
            </div>
            {{-- @include('home/stats') --}}

            <div class="mb-5 ">
                {{-- @include('home/news') --}}
                <h1 class="text-lg font-bold uppercase text-cOrange mb-3 ml-5">News</h1>
                <div class="relative w-1/4 h-1/4 m-5">

                </div>
            </div>
            <div class="mb-5">
                {{-- @include('home/used') --}}
            </div>
        </div>
        {{-- <div class="flex-none w-1/4 bg-primary-content rounded-xl shadow-lg px-10 py-8 m-5">
            <h1 class="text-lg font-bold uppercase text-primary">menu</h1>
        </div> --}}
    </div>
    <div class="flex flex-col lg:flex-row gap-5 mb-12 w-full">
        {{-- @include('home/link-electronic') --}}
        {{-- @include('home/link-utilities') --}}
        {{-- @include('home/link-design') --}}
        {{-- @include('home/link-other') --}}
        {{-- @include('home/link-create') --}}
    </div>
@endsection

@section('scripts')
    {{-- <script src="{{ $GLOBALS['script'] }}home.bundle.js?ver={{ date('Ymdhis') }}"></script> --}}
@endsection
