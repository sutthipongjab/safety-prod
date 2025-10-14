@extends('layout/webflowTemplate')
@section('contents')
    <div class="flex mx-5">
        <div class="card min-w-full w-full  md:w-fit Office shadow-xl bg-base-100">
            <div class="card-body">
                <div class="flex">
                    <div class="text-2xl flex-1 formHeader"></div>
                    <button class="btn btn-accent hidden"  id="matForm">MAT Web flow</button>
                </div>
                <form class="" id="form"></form>
            </div>

            <div class="formLoading m-8 flex flex-col gap-5">
                {{-- table --}}
                <div class="skeleton h-10 w-80"></div>
                <div class="skeleton h-80 w-full"></div>
                {{-- remark --}}
                <div class="skeleton h-40 w-80"></div>
                {{-- button --}}
                <div class="flex gap-1">
                    <div class="skeleton h-10 w-24"></div>
                    <div class="skeleton h-10 w-24"></div>
                </div>
            </div>

            <div class="actions-Form m-8 hidden">
                <label class="form-control mb-5 w-80">
                    <div class="label">
                        <span class="label-text">Remark</span>
                    </div>
                    <textarea class="textarea textarea-bordered h-24" id="remark" ></textarea>
                </label>
                <div class="flex gap-3 ">
                    <button type="button" class="btn btn-primary" name="btnAction" value="approve">Approve</button>
                    <button type="button" class="btn btn-neutral mg-l-12" name="btnAction" value="reject">Reject</button>
                    <button type="button" class="btn btn-secondary mg-l-12 hidden btnReturn" name="btnAction" value="return">Return</button>
                </div>
            </div>

            <div class="card-footer m-8">
                <div class="user-data hidden" empno="{{$empno}}" mode="{{$mode}}" cstep="{{$cstep}}" cextData="{{ $cextData }}"></div>
                <div class="form-detail hidden" 
                    formCatagory="{{ $formData->FORM_CATEGORY }}"
                    formNo="{{ $formData->FORM_NO }}"
                    formName="{{ $formMaster->FORMNAME}}"
                    formArea="{{ $formData->AREA_NAME}}"
                    formCreate="{{ $formData->FORM_CREATE}}">
                </div>
                <div class="formno hidden" 
                    NFRMNO="{{$form['NFRMNO']}}" 
                    VORGNO="{{$form['VORGNO']}}" 
                    CYEAR="{{$form['CYEAR']}}" 
                    CYEAR2="{{$form['CYEAR2']}}" 
                    NRUNNO="{{$form['NRUNNO']}}">
                </div>
                <div id="flow">
                    <div class="flex justify-center">
                        <div class="skeleton h-32 w-[36rem]"></div>
                    </div>
                </div> 
                
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ $GLOBALS['script'] }}electricWebForm.bundle.js?ver={{ date('Ymdhis') }}"></script>
@endsection
