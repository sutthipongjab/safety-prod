@extends('layout/template')
@section('contents')
    <div class="flex mx-5">
        <div class="card w-full  shadow-2xl bg-base-100">
        {{-- <div class="card bg-base-100 flex-1 shadow-xl max-md:min-w-[48%] h-40"> --}}
            <div class="card-body">
                <form action="#" id="inspection-form" enctype="multipart/form-data">
                    <div class="flex flex-col mb-3">
                        <h2 class="flex items-center gap-1">
                            <p>ระบบการตรวจสอบความปลอดภัยของคณะกรรมการความปลอดภัย ประจำเดือน  
                                <span id="monthYearTH"></span>
                            </p>
                        </h2>
                        <h2 class="flex items-center gap-1 text-blue-500">
                            <p>SAFETY INSPECTION REPORT BY AMSC'S COMMITTEE IN  
                                <span id="monthYearEn"></span>
                            </p>
                        </h2>
                    </div>
                    @php
                        $NFRMNO = '';
                        $VORGNO = '';
                        $CYEAR  = '';
                        if(!empty($formInfo)){
                            $NFRMNO = $formInfo[0]->NNO;
                            $VORGNO = $formInfo[0]->VORGNO;
                            $CYEAR  = $formInfo[0]->CYEAR;
                        }
                    @endphp
                    
                    <div class="hidden form-info" NFRMNO="{{$NFRMNO}}" VORGNO="{{$VORGNO}}" CYEAR="{{$CYEAR}}" userno="{{ $_SESSION['user']->SEMPNO}}"></div>
                    <div class="flex max-sm:flex-col gap-3 mb-5 ">
                        <label class="form-control w-full max-w-xs">
                            <div class="label">
                                <span class="label-text">วัน/เดือน/ปี</span>
                            </div>
                            <input type="text" placeholder="กรอกวันที่" id="checkDate" name="checkDate" class="input input-bordered w-full max-w-xs fdate req" />
                        </label>
                        <label class="form-control w-full max-w-xs own-sec">
                            <div class="label">
                                <span class="label-text">แผนกรับผิดชอบ</span>
                            </div>
                            <select class="w-full select select-bordered req" name="ownerSec"  id="ownerSec" >
                                <option value=""></option>
                                @foreach ($section as $key => $s)
                                    <option value="{{$s->SSECCODE}}" seccode="{{$s->SSECCODE}}">{{$s->SSEC}}</option>
                                @endforeach
                                @if (!empty($st))
                                    @foreach ($st as $s)
                                        <option value="{{$s->SDEPCODE}}" seccode="{{$s->SDEPCODE}}">{{$s->SDEPT}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </label>
                        {{-- <label class="form-control w-full max-w-xs">
                            <div class="label">
                                <span class="label-text">เจ้าของพื้นที่(รหัสพนักงาน)</span>
                            </div>
                            <input type="text" placeholder="Type here" id="empnno" class="input input-bordered w-full max-w-xs req" />
                        </label> --}}
                    </div>

                    <div class="owner-container hidden pl-1 mb-2">
                        <div class="flex gap-3 justify-between text-sm md:max-w-[650px]  max-sm:flex-col flex-wrap">
                            <div class="flex flex-col  min-w-36 max-w-36">
                                {{-- <div class="organization hidden">
                                    <strong class="mb-1">ฝ่าย/ส่วน/แผนก</strong>
                                    <div class="form-control">
                                        <label class="label cursor-pointer">
                                            <span class="text-sm">ฝ่าย(Devision)</span>
                                            <input type="radio" name="org" id="div" class="radio checked:bg-red-500" checked="checked" />
                                        </label>
                                    </div><div class="form-control">
                                        <label class="label cursor-pointer">
                                            <span class="text-sm">ส่วน(Department)</span>
                                            <input type="radio" name="org" id="dept" class="radio checked:bg-red-500" checked="checked" />
                                        </label>
                                    </div><div class="form-control">
                                        <label class="label cursor-pointer">
                                            <span class="text-sm">แผนก(Section)</span>
                                            <input type="radio" name="org" id="sec" class="radio checked:bg-red-500" checked="checked" />
                                        </label>
                                    </div>
                                </div> --}}
                                <strong class="">แผนก(Section)</strong>
                                <p class="text-sm" id="org">แผนก(Section)</p>
                            </div>
                            <div class="flex flex-col ">
                                <strong class="">เจ้าของพื้นที่(Owner Area)</strong>
                                <p class="owner "></p>
                                <p class="ownerEn  text-blue-500"></p>
                            </div>
                            <div class="flex flex-col ">
                                <strong class="">วัน/เดือน/ปี</strong>
                                <div class="dateFull ">-</div>
                            </div>
                        </div>
                    </div>

                    <div class="auditor-container">
                        <div class="flex max-sm:flex-col gap-2 mb-3 md:items-end">
                            <label class="form-control w-full max-w-[41rem]">
                                <div class="label">
                                    <span class="label-text">ผู้ตรวจสอบ(Auditor)</span>
                                </div>
                                <input type="text" placeholder="e.g. Safety Committee" id="auditor" name="auditor" class="input input-bordered w-full req" />
                            </label>
                            {{-- <label for="drawer-inspection" class="drawer-button btn btn-primary max-w-xs" id="add-item">
                                เพิ่มรายการตรวจสอบ
                            </label> --}}
                            {{-- <div for="" class=" btn bg-cOrange text-white hover:bg-active max-w-xs" onclick="modal_save.showModal()">
                                Submit
                            </div> --}}
                        </div>
                    </div>
                </form>
                <div class="overflow-x-auto">
                    <table class="table" id="tblList"></table>
                </div>
                <div class="flex justify-left mt-4 gap-1">
                    <div for="" class=" btn btn-neutral max-w-xs" id="back">
                        Back
                    </div>
                    <div for="" class=" btn btn-primary max-w-xs" onclick="modal_save.showModal()">
                        Request
                    </div>
                </div>
                {{-- drawer auditor --}}
                @include('inspection/drawerAudit')
            </div>
        </div>
    </div>

     {{-- modal save  --}}
     @include('layout/modal_save')
    
    {{-- modal delete  --}}
    @include('layout/modal_del')

@endsection

@section('scripts')
    <script src="{{ $GLOBALS['script'] }}inspection.bundle.js?ver={{ date('Ymdhis') }}"></script>
@endsection
