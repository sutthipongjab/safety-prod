@extends('layout/template')
@section('contents')
    <div  class="formType" data-formtype="{{$formType->code}}" data-formid="{{$formType->id}}"></div>
    <div class="flex mx-5">
        <div class="card w-full  shadow-2xl bg-base-100">
            <div class="card-body">
                <div class="overflow-x-auto">
                    <table class="table" id="tblMaster"></table>
                </div>
            </div>
        </div>
    </div>

    @include('form/modalEdit')
    @include('layout/modal_del')
    {{-- drawer add and edit area --}}
    @include('form/drawer')
    @include('form/drawer_topic')
    @include('form/drawer_detail')


@endsection

@section('scripts')
    <script src="{{ $GLOBALS['script'] }}form.bundle.js?ver={{ date('Ymdhis') }}"></script>
@endsection
