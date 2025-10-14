@extends('layout/template')
@section('contents')
    <div class="flex mx-5">
        <div class="card w-full  shadow-2xl bg-base-100">
        {{-- <div class="card bg-base-100 flex-1 shadow-xl max-md:min-w-[48%] h-40"> --}}
            <div class="card-body">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">เลือกปีและเดือน</span>
                    </label>
                    <input type="text" class="input input-bordered monthYearPicker max-w-xs" placeholder="Select Month and Year">
                </div>

                <div class="overflow-x-auto">
                    <table class="table" id="tblPatrol"></table>
                </div>
            </div>
        </div>
    </div>

     {{-- modal save  --}}
     @include('layout/modal_save')
    
    {{-- modal delete  --}}
    @include('layout/modal_del')

@endsection

@section('scripts')
    <script src="{{ $GLOBALS['script'] }}patrol.bundle.js?ver={{ date('Ymdhis') }}"></script>
@endsection
