@extends('layout/template')
@section('contents')
    <div class="flex mx-5">
        <div class="card w-full  shadow-2xl bg-base-100">
            <div class="card-body">
                {{-- <div class="font-bold">
                    <span class="revision-master">Rev. No. *</span>
                    <span class="rev-edit"></span>
                </div> --}}
            </div>
        </div>
    </div>
    @include('chemical/drawerMaster')
    @include('chemical/modal_rebuild')
    @include('chemical/modal_rev')
    @include('layout/modal_del')
@endsection

@section('scripts')
    <script src="{{ $GLOBALS['script'] }}chemicalList.bundle.js?ver={{ date('Ymdhis') }}"></script>
@endsection
