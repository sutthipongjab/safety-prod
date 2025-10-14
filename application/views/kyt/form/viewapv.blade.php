{{-- @extends('layout/webflowTemplate') --}}

@php
    $template = $webSafety == 1 ? 'layout/template' : 'layout/webflowTemplate';
   
@endphp
@extends($template)
@section('contents')
<div class="flex mx-5">
    <div class="card w-full  shadow-2xl bg-base-100">
        <div class="card-body">
            <form action="#" id="kyt-form" enctype="multipart/form-data">
                <div class="overflow-x-auto">
                    <div class="text-center font-bold text-xl">แบบฟอร์ม บันทึกการทำกิจกรรม KYT ประจำวัน (KYT Daily
                        Record)</div>
                    <div class="overflow-x-auto">
                    <table class="table dataTable" id="table"></table>
                    </div>
                   
                    @if ($webSafety != 1)
                        <div id="actions-Form" class="hidden max-sm:flex-col gap-3">
                            <button type="button" class="btn btn-primary mg-s-12" name="btnAction"
                                value="approve">Save</button>
                        </div>
                    @endif
                </div>
            </form>
        </div>
        <div class="card-footer m-8">
            <div class="formno hidden" NFRMNO="{{ $kytfrm[0]->NFRMNO }}" VORGNO="{{ $kytfrm[0]->VORGNO }}"
                CYEAR="{{ $kytfrm[0]->CYEAR }}" CYEAR2="{{ $kytfrm[0]->CYEAR2 }}"
                NRUNNO="{{ $kytfrm[0]->NRUNNO }}"></div>
            <div class="user-data hidden" empno="{{ $empno }}" mode="{{ $mode }}" cextData="{{ $cextData }}"></div>
            <div id="flow" >
                <div class="h-32 w-full"></div>
            </div>

        </div>
    </div>
</div>
{{-- modal save --}}
@include('layout/modal_save')
@endsection

@section('scripts')
<script
    src="{{ $GLOBALS['script'] }}kytviewapv.bundle.js?ver={{ date('Ymdhis') }}">
</script>
@endsection


<style>
#flow div{
    overflow: auto !important;
}
</style>
