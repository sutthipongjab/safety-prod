@extends('layout/template')
@section('contents')
    <div class="flex mx-5">
        <div class="card w-full  shadow-2xl bg-base-100">
        {{-- <div class="card bg-base-100 flex-1 shadow-xl max-md:min-w-[48%] h-40"> --}}
            <div class="card-body">
                <div class="overflow-x-auto">
                    <table class="table" id="table_Follow"></table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ $GLOBALS['script'] }}kytfollow.bundle.js?ver={{ date('Ymdhis') }}"></script>
@endsection
