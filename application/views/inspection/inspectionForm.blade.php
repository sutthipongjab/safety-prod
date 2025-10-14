{{-- @extends('layout/webflowTemplate') --}}
{{-- @extends('layout/template') --}}
@php
    $template = $website == 'safety' ? 'layout/template' : 'layout/webflowTemplate';
@endphp
@extends($template)
@section('contents')
    <div class="flex mx-5">
        <div class="card w-full  shadow-2xl bg-base-100">
            <div class="card-body">
                <div class="flex flex-col mb-3 gap-1">
                    <h2 class="flex items-center" id="monthYearTH">
                        <div class="skeleton h-4 w-11/12 "></div>
                    </h2>
                    <h2 class="flex items-center text-blue-500" id="monthYearEn">
                        <div class="skeleton h-4 w-full "></div>
                    </h2>
                </div>
                <div class="owner-container pl-1 mb-2">
                    <div class="flex gap-3 justify-between text-sm md:max-w-[650px]  max-sm:flex-col flex-wrap">
                        <div class="flex flex-col gap-1 min-w-36 max-w-36">
                            <strong><div class="skeleton h-4 w-full "></div></strong>
                            <span class="text-sm" id="org">
                                <div class="skeleton h-4 w-2/3 "></div>
                            </span>
                        </div>
                        <div class="flex flex-col gap-1 min-w-36">
                            <strong><div class="skeleton h-4 w-full "></div></strong>
                            <span class="owner"><div class="skeleton h-4 w-2/3 "></div></span>
                            <span class="ownerEn text-blue-500"><div class="skeleton h-4 w-full "></div></span>
                        </div>
                        <div class="flex flex-col gap-1 min-w-36 ">
                            <strong><div class="skeleton h-4 w-2/3 "></div></strong>
                            <div class="dateFull "><div class="skeleton h-4 w-full "></div></div>
                        </div>
                    </div>
                </div>
                <div class="auditor-container">
                    <div class="flex max-sm:flex-col gap-2 mb-3 md:items-end">
                        <strong><div class="skeleton h-4 w-2/3 "></div></strong>
                        <div class="auditor "><div class="skeleton h-4 w-full "></div></div>
                    </div>
                </div>
                <div class="overflow-scroll">
                    <table class="table" id="tblPatrol"></table>
                </div>
                <div class="remark-text hidden">
                    <div class="flex gap-3">
                        <div><strong>**Remark : </strong></div>
                        <div class="flex flex-col">
                            <strong>Class A : ผิดกฎระเบียบบริษัทฯ เช่น ไม่ปฏิบัติตามกฏระเบียบ, ไม่มีอุปกรณ์ที่กำหนดตามระเบียบ</strong>
                            <strong>Class B : คำแนะนำทั่วไปของคณะกรรมการฯ เช่น อุปกรณชำรุด</strong>
                            <strong>Class C : อื่น ๆ เช่น 5ส</strong>
                        </div>
                    </div>
                </div>
            </div>
            @if ($website != 'safety')
                <div class="user-data hidden" empno="{{$empno}}" cextData="{{$cextData}}"></div>
                @if ($approve)
                    @switch($cextData)
                        @case('01')
                            <div class="corrective-person hidden my-5 mx-8">
                                <label class="form-control w-full max-w-xs own-sec">
                                    <div class="label">
                                        <span class="label-text">ผู้ดำเนินการ</span>
                                    </div>
                                    <select class="w-full select select-bordered req" name="employee"  id="employee" >
                                        <option value=""></option>
                                        {{-- @foreach ($section as $key => $s)
                                            <option value="{{$s->SSECCODE}}" seccode="{{$s->SSECCODE}}">{{$s->SSEC}}</option>
                                        @endforeach --}}
                                    </select>
                                </label>
                            </div>
                            @break
                        @case('02')
                            @include('inspection/drawerCorrective')
                            @break
                        @case('03')
                            @include('inspection/modal_evaluate')
                            @break
                        @default
                            
                    @endswitch
                        
                    <div class="actions-Form hidden my-5 mx-8">
                        <label class="form-control mb-5 w-80">
                            <div class="label">
                                <span class="label-text">Remark</span>
                            </div>
                            <textarea class="textarea textarea-bordered h-24" id="remark" ></textarea>
                        </label>
                        <div class="flex gap-3 ">
                            <button type="button" class="btn btn-primary" name="btnAction" value="approve">Approve</button>
                            <button type="button" class="btn btn-neutral mg-l-12" name="btnAction" value="reject">Reject</button>
                        </div>
                    </div>
                @endif
                
            @endif
               
            <div class="card-footer m-8">
                <div class="formno" NFRMNO="{{$form['NFRMNO']}}" VORGNO="{{$form['VORGNO']}}" CYEAR="{{$form['CYEAR']}}" CYEAR2="{{$form['CYEAR2']}}" NRUNNO="{{$form['NRUNNO']}}"></div>
                {{-- <div class="formno" NFRMNO="{{$patrol[0]->NFRMNO}}" VORGNO="{{$patrol[0]->VORGNO}}" CYEAR="{{$patrol[0]->CYEAR}}" CYEAR2="{{$patrol[0]->CYEAR2}}" NRUNNO="{{$patrol[0]->NRUNNO}}"></div> --}}
                <div id="flow"><div class="skeleton h-32 w-full"></div></div>
                @if ($website == 'safety')
                    <div class="flex justify-left mt-4 gap-1">
                        <div for="" class=" btn btn-neutral max-w-xs  hidden" id="back">
                            Back
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
{{-- @if ($website == 'safety')
    <script src="{{ $GLOBALS['script'] }}patrolForm.bundle.js?ver={{ date('Ymdhis') }}"></script>
@else --}}
    <script src="{{ $GLOBALS['script'] }}patrolFormWebflow.bundle.js?ver={{ date('Ymdhis') }}"></script>
{{-- @endif --}}
@endsection
