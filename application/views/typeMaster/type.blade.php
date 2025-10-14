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

    {{-- drawer add and edit --}}
    @include('typeMaster/drawerType')
    
    {{-- modal delete  --}}
    @include('layout/modal_del')
@endsection

@section('scripts')
    <script src="{{ $GLOBALS['script'] }}type.bundle.js?ver={{ date('Ymdhis') }}"></script>
@endsection
