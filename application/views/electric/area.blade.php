@extends('layout/template')
@section('contents')
    <div class="flex mx-5">
        <div class="card w-full  shadow-2xl bg-base-100">
            <div class="card-body">
                <div class="overflow-x-auto">
                    <table class="table" id="tblMaster"></table>
                </div>
            </div>
        </div>
    </div>

    {{-- drawer add and edit area --}}
    @include('electric/drawer')


@endsection

@section('scripts')
    <script src="{{ $GLOBALS['script'] }}electricArea.bundle.js?ver={{ date('Ymdhis') }}"></script>
@endsection
