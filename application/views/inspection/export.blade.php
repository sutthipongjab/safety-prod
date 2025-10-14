@extends('layout/template')
@section('contents')
    <div class="flex flex-col gap-5 mx-5">
        <div class="card w-full  shadow-2xl bg-base-100">
            <div class="card-body search-input">
                <div class="flex flex-col md:flex-row md:gap-5">
                    <div class="w-full md:w-[48%] md:max-w-96">
                        <label class="form-control w-full max-w-sm">
                            <div class="label">
                                <span class="label-text">ช่วงวันที่</span>
                            </div>
                            <div class="join">
                                <input type="text" placeholder="กรอกวันที่" id="rangDate" class="input input-bordered w-full max-w-sm fdate join-item"/>
                                <button class="btn join-item " id="dateClear">Clear</button>
                              </div>
                        </label>
                        <label class="form-control w-full max-w-sm">
                            <div class="label">
                                <span class="label-text">แผนก (Sec)</span>
                            </div>
                            <select class="w-full select select-bordered " id="sec" >
                                <option value=""></option>
                            </select>
                        </label>
                    </div>
                    <div class="w-full md:w-[48%]">
                        <div class="label">
                            <span class="label-text">ประเภท (Class)</span>
                        </div>
                        @foreach ($cla as $key => $c)
                        @php
                            if($c->TYPE_NAME == 'A'){
                                $textTooltip = 'Class A : ผิดกฎระเบียบบริษัทฯ เช่น ไม่ปฏิบัติตามกฏระเบียบ, ไม่มีอุปกรณ์ที่กำหนดตามระเบียบ';
                            }elseif($c->TYPE_NAME == 'B'){
                                $textTooltip = 'Class B : คำแนะนำทั่วไปของคณะกรรมการฯ เช่น อุปกรณชำรุด';
                            }elseif($c->TYPE_NAME == 'C'){
                                $textTooltip = 'Class C : อื่น ๆ เช่น 5ส';
                            }
                        @endphp
                            <div class="form-control md:max-w-24">
                                <label class="label cursor-pointer tooltip md:tooltip-right flex" data-tip="{{$textTooltip}}">
                                    <span class="text-sm " >{{$c->TYPE_NAME}}</span>
                                    <input type="radio" name="class" class="radio  " value="{{$c->TYPE_NAME}}" />
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="flex justify-left mt-4 gap-1">
                    <div for="" class=" btn btn-neutral max-w-xs" id="search">
                        Search 
                    </div>
                </div>
            </div>
        </div>
        <div class="card w-full shadow-2xl bg-base-100 tblData hidden">
            <div class="card-body">
                <div class="overflow-x-auto">
                    <table class="table" id="tbl_patrol"></table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ $GLOBALS['script'] }}patrolExport.bundle.js?ver={{ date('Ymdhis') }}"></script>
@endsection
