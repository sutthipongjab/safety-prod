@extends('layout/template')
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
           
@section('contents')
    <div class="hidden form-info" NFRMNO="{{$NFRMNO}}" VORGNO="{{$VORGNO}}" CYEAR="{{$CYEAR}}"></div>
    <div class="hidden site-info" page-tize="{{$page}}" ></div>
    <div class="flex mx-5">
        <div class="card w-full  shadow-2xl bg-base-100">
            <div class="card-body">
                
                <div id="chemical_detail" class=" " >
                    <div class="ownerLoading flex flex-col gap-2 max-w-xs">
                        <div class="skeleton h-4 w-24"></div>
                        <div class="skeleton h-5 w-24"></div>
                        <div class="skeleton h-10 "></div>
                    </div>
                    <label class="form-control w-full max-w-xs hidden">
                        <div class="label">
                            <span class="label-text h-topic">ผู้ขอเปลี่ยนแปลงสารเคมี</span>
                        </div>
                        <div class="owner flex gap-5"></div>
                    </label>

                    <div class="flex-col lg:flex-row searching gap-5 hidden">
                        <label class="form-control w-full max-w-xs">
                            <div class="label">
                                <span class="label-text">เลือกสารเคมี</span>
                            </div>
                            <select class="select select-bordered w-full req" id="chemicalList" name="chemicalList" ></select>
                        </label>
                    </div>

                    
                    <form class="detail hidden" enctype="multipart/form-data">
                        <div class="font-bold text-2xl header my-5">ขอใช้สารเคมี</div>

                        
                        <div class="body border rounded-lg p-5">
                            <input type="text" id="AMEC_SDS_ID" name="AMEC_SDS_ID" class="hidden" />

                                <label class="form-control w-full max-w-xs">
                                    <div class="label">
                                        <span class="label-text">ชื่อสารเคมี</span>
                                    </div>
                                    <input type="text" placeholder="Acetylene gas" id="CHEMICAL_NAME" name="CHEMICAL_NAME" class="input input-bordered w-full max-w-xs req" />
                                </label>
                            
                            <label class="form-control w-full">
                                <div class="label">
                                    <span class="label-text">ใช้สำหรับ</span>
                                </div>
                                <textarea type="text" placeholder="ถังก๊าซรถเข็นใช้เชื่อม,ตัด ชิ้นงาน" id="USED_FOR" name="USED_FOR" class="textarea  textarea-bordered w-full h-28 py-3 req" ></textarea>
                            </label>

                            <div class="flex flex-col lg:flex-row  justify-between gap-5">
                                <label class="form-control w-full">
                                    <div class="label">
                                        <span class="label-text">จุดใช้งาน</span>
                                    </div>
                                    <div class="flex gap-1">
                                        <div class="flex flex-col flex-1 inputGroup gap-1">
                                            <input type="text" placeholder="Pit Ass'y" name="USED_AREA[]" class="input input-bordered w-full req" />
                                        </div>
                                        <div class="flex flex-col justify-end">
                                            <i class="icofont-minus-square text-5xl hover:text-gray-400 hover:scale-105 remove-area hidden"></i>
                                            <i class="icofont-plus-square text-5xl hover:text-gray-400 hover:scale-105 add-area"></i>
                                        </div>
                                    </div>
                                </label>
                            @if ($page == 'modify')
                                <label class="form-control w-full">
                                    <div class="label">
                                        <span class="label-text">จุดจัดเก็บ</span>
                                    </div>
                                    <div class="flex gap-1">
                                        <div class="flex flex-col flex-1 inputGroup gap-1">
                                            <input type="text" placeholder="H203R(N6), 4C" name="KEEPING_POINT[]" class="input input-bordered w-full req" />
                                        </div>
                                        <div class="flex flex-col justify-end">
                                            <i class="icofont-minus-square text-5xl hover:text-gray-400 hover:scale-105 remove-keep hidden"></i>
                                            <i class="icofont-plus-square text-5xl  hover:text-gray-400 hover:scale-105 add-keep"></i>
                                        </div>
                                    </div>
                                </label>
                            @endif
                            </div>
                            @if ($page == 'modify')
                                <label class="form-control w-full">
                                    <div class="label">
                                        <span class="label-text">ผู้ควบคุมดูแลสารเคมีในแผนก </span>
                                    </div>
                                    <div class="flex gap-1">
                                        <select class="select select-bordered w-full req" id="USER_CONTROL" name="USER_CONTROL" multiple></select>
                                    </div>
                                </label>
                            @endif
                            @if ($page == 'cancel')
                                <label class="form-control w-full">
                                    <div class="label">
                                        <span class="label-text">เหตุผลในการยกเลิก</span>
                                    </div>
                                    <textarea type="text" placeholder="กรอกเหตุผลสั้น" id="REASON_CANCEL" name="REASON_CANCEL" class="textarea  textarea-bordered w-full h-28 py-3 req" ></textarea>
                                </label>
                            @endif
                            <div class="flex justify-left mt-4 gap-1">
                                <div for="" class=" btn btn-primary max-w-xs" onclick="modal_save.showModal()">
                                    Request
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="detailLoading mt-5  flex-col gap-5 hidden">
                        <div class="flex gap-5 w-full ">
                            <div class="skeleton h-10 w-80"></div>
                            <div class="skeleton h-10 w-80"></div>
                        </div>
                        <div class="skeleton h-40 w-full"></div>
                        <div class="flex justify-between gap-5">
                            <div class="skeleton h-10 w-full"></div>
                            <div class="skeleton h-10 w-full"></div>
                        </div>
                        <div class="skeleton h-10 w-80"></div>
                        <div class="flex gap-1">
                            <div class="skeleton h-10 w-24"></div>
                            <div class="skeleton h-10 w-24"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- modal save  --}}
    @include('layout/modal_save')
@endsection

@section('scripts')
    <script src="{{ $GLOBALS['script'] }}chemicalModify.bundle.js?ver={{ date('Ymdhis') }}"></script>
@endsection
