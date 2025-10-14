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
    <div class="flex flex-col gap-5 m-5">
        <div class="card w-full shadow-xl bg-base-100 selected-form">
            <div class="card-body">
                <form href="#" class="flex gap-5 items-end">
                    <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">พื้นที่</span>
                        </div>
                        <select class="w-full select select-bordered" id="AREA" name="AREA" data-map="AREA">
                            <option value=""></option>
                            @foreach ($area as $key => $a)
                                <option value="{{$a->AREA_ID}}" data-area-id="{{$a->AREA_ID}}" data-area-empno="{{$a->AREA_EMPNO}}" data-area-type="{{$a->AREA_TYPE}}" data-area-category="{{$a->AREA_CATEGORY}}">{{$a->AREA_NAME}} ({{$a->AREA_MANAGER}})</option>
                            @endforeach
                        </select>
                    </label>
                    <button type="button" class="btn btn-primary hidden" id="submit" name="submit">ส่งฟอร์ม</button>
                </form>
            </div>
        </div>
        <div class="card w-full md:w-fit Office shadow-xl bg-base-100 formPreview hidden">
            <div class="card-body">
                <div class="text-2xl">ตัวอย่างฟอร์ม</div>
                <form class="preview" id="formPreview"></form>
                {{-- <table class="table table-pin-rows">
                    <thead>
                        <tr>
                            <th rowspan="2">ITEM</th>
                            <th rowspan="2" colspan="2">รายละเอียด</th>
                            <th colspan="4">ผลการตรวจสอบ</th>
                        </tr>
                        <tr>
                            <th>ปกติ</th>
                            <th>ผิดดปกติ</th>
                            <th>ไม่มี</th>
                            <th>รายละเอียดความผิดปกติ</th>
                        </tr>
                    </thead>
                    tbody>tr*14>(td.flex.flex-col>span+span.text-blue-500)+((td>input:c)*3)+td>input
                    <tbody>
                        <tr>
                            <td rowspan="14"></td>
                            <td rowspan="14"></td>
                            <td class="flex flex-col"><span>1. การตรวจสอบปลั๊กไฟและเต้ารับ  </span><span class="text-blue-500"></span></td>
                            <td><input type="checkbox" name="" id=""></td>
                            <td><input type="checkbox" name="" id=""></td>
                            <td><input type="checkbox" name="" id=""></td>
                            <td><input type="text"></td>
                        </tr>
                        <tr>
                            <td class="flex flex-col"><span></span><span class="text-blue-500"></span></td>
                            <td><input type="checkbox" name="" id=""></td>
                            <td><input type="checkbox" name="" id=""></td>
                            <td><input type="checkbox" name="" id=""></td>
                            <td><input type="text"></td>
                        </tr>
                        <tr>
                            <td class="flex flex-col"><span></span><span class="text-blue-500"></span></td>
                            <td><input type="checkbox" name="" id=""></td>
                            <td><input type="checkbox" name="" id=""></td>
                            <td><input type="checkbox" name="" id=""></td>
                            <td><input type="text"></td>
                        </tr>
                        <tr>
                            <td class="flex flex-col"><span></span><span class="text-blue-500"></span></td>
                            <td><input type="checkbox" name="" id=""></td>
                            <td><input type="checkbox" name="" id=""></td>
                            <td><input type="checkbox" name="" id=""></td>
                            <td><input type="text"></td>
                        </tr>
                        <tr>
                            <td class="flex flex-col"><span></span><span class="text-blue-500"></span></td>
                            <td><input type="checkbox" name="" id=""></td>
                            <td><input type="checkbox" name="" id=""></td>
                            <td><input type="checkbox" name="" id=""></td>
                            <td><input type="text"></td>
                        </tr>
                        <tr>
                            <td class="flex flex-col"><span></span><span class="text-blue-500"></span></td>
                            <td><input type="checkbox" name="" id=""></td>
                            <td><input type="checkbox" name="" id=""></td>
                            <td><input type="checkbox" name="" id=""></td>
                            <td><input type="text"></td>
                        </tr>
                        <tr>
                            <td class="flex flex-col"><span></span><span class="text-blue-500"></span></td>
                            <td><input type="checkbox" name="" id=""></td>
                            <td><input type="checkbox" name="" id=""></td>
                            <td><input type="checkbox" name="" id=""></td>
                            <td><input type="text"></td>
                        </tr>
                        <tr>
                            <td class="flex flex-col"><span></span><span class="text-blue-500"></span></td>
                            <td><input type="checkbox" name="" id=""></td>
                            <td><input type="checkbox" name="" id=""></td>
                            <td><input type="checkbox" name="" id=""></td>
                            <td><input type="text"></td>
                        </tr>
                        <tr>
                            <td class="flex flex-col"><span></span><span class="text-blue-500"></span></td>
                            <td><input type="checkbox" name="" id=""></td>
                            <td><input type="checkbox" name="" id=""></td>
                            <td><input type="checkbox" name="" id=""></td>
                            <td><input type="text"></td>
                        </tr>
                        <tr>
                            <td class="flex flex-col"><span></span><span class="text-blue-500"></span></td>
                            <td><input type="checkbox" name="" id=""></td>
                            <td><input type="checkbox" name="" id=""></td>
                            <td><input type="checkbox" name="" id=""></td>
                            <td><input type="text"></td>
                        </tr>
                        <tr>
                            <td class="flex flex-col"><span></span><span class="text-blue-500"></span></td>
                            <td><input type="checkbox" name="" id=""></td>
                            <td><input type="checkbox" name="" id=""></td>
                            <td><input type="checkbox" name="" id=""></td>
                            <td><input type="text"></td>
                        </tr>
                        <tr>
                            <td class="flex flex-col"><span></span><span class="text-blue-500"></span></td>
                            <td><input type="checkbox" name="" id=""></td>
                            <td><input type="checkbox" name="" id=""></td>
                            <td><input type="checkbox" name="" id=""></td>
                            <td><input type="text"></td>
                        </tr>
                        <tr>
                            <td class="flex flex-col"><span></span><span class="text-blue-500"></span></td>
                            <td><input type="checkbox" name="" id=""></td>
                            <td><input type="checkbox" name="" id=""></td>
                            <td><input type="checkbox" name="" id=""></td>
                            <td><input type="text"></td>
                        </tr>
                        <tr>
                            <td class="flex flex-col"><span></span><span class="text-blue-500"></span></td>
                            <td><input type="checkbox" name="" id=""></td>
                            <td><input type="checkbox" name="" id=""></td>
                            <td><input type="checkbox" name="" id=""></td>
                            <td><input type="text"></td>
                        </tr>
                    </tbody>
                </table> --}}
                {{-- <div class="container grid grid-rows-[repeat(9,_minmax(0,_auto))] grid-cols-[repeat(7,_minmax(0,_auto))] border">
                    <div class="row-span-2 border">ITEM</div>
                    <div class="row-span-2 col-span-2 border">รายละเอียด</div>
                    <div class="col-span-4 border">ผลการตรวจสอบ</div>
                    <div class="border">ปกติ</div>
                    <div class="border">ผิดดปกติ</div>
                    <div class="border">ไม่มี</div>
                    <div class="border">รายละเอียดความผิดปกติ</div>
                    
                    <div class="border row-[span_14_/_span_14]">1</div>
                    <div class="border row-[span_14_/_span_14]">
                        <div class="image-1"></div>
                    </div>
                    <div class="border detail flex flex-col col-span-5">
                        1. การตรวจสอบปลั๊กไฟและเต้ารับ  
                        <span class="text-blue-500">1. Check plug appearance and socket.</span>
                    </div>
                    
                    <div class="border detail flex flex-col">
                        1.1 ต้องอยู่ในสภาพพร้อมใช้งาน, ไม่มีเศษฝุ่นหรือหยากไย่   
                        <span class="text-blue-500">(1.1) Be in good condition. No dust or cobweb.</span>
                    </div>
                    <div class="border"><input type="checkbox" /></div>
                    <div class="border"><input type="checkbox" /></div>
                    <div class="border"><input type="checkbox" /></div>
                    <div class="border"><input type="text" placeholder="กรุณาระบุ" /></div>

                    <div class="border detail flex flex-col">
                        1.2 ไม่มีรอยไหม้, เขม่าควัน หรือเป็นสนิม 
                        <span class="text-blue-500">(1.2)  No burn, soot or rust.</span>
                    </div>
                    <div class="border"><input type="checkbox" /></div>
                    <div class="border"><input type="checkbox" /></div>
                    <div class="border"><input type="checkbox" /></div>
                    <div class="border"><input type="text" placeholder="กรุณาระบุ" /></div>

                    <div class="border detail flex flex-col">
                        1.3 ไม่มีรอยบิดเบี้ยว, ปลอกฉนวนหุ้มฉีกขาด
                        <span class="text-blue-500">(1.3)  No deform or cut on insulator.</span>
                    </div>
                    <div class="border"><input type="checkbox" /></div>
                    <div class="border"><input type="checkbox" /></div>
                    <div class="border"><input type="checkbox" /></div>
                    <div class="border"><input type="text" placeholder="กรุณาระบุ" /></div>

                    <div class="border detail flex flex-col">
                        1.4 ตรวจสอบการต่อระบบสายกราวน์ และต้องมีขาปลั๊ก 3 ขา 
                        <span class="text-blue-500">(1.4) Check Earth wire connection and the plug must be 3-pin plug.</span>
                    </div>
                    <div class="border"><input type="checkbox" /></div>
                    <div class="border"><input type="checkbox" /></div>
                    <div class="border"><input type="checkbox" /></div>
                    <div class="border"><input type="text" placeholder="กรุณาระบุ" /></div>

                    <div class="border detail flex flex-col">
                        1.5 น็อตยึดขั้วปลั๊กทั้งสองข้างต้องแน่น, ไม่หลุด และปลั๊กต้องเสียบแนบสนิท
                        <span class="text-blue-500">(1.5) The screws on the head of plug must fasten tightly and the plug should plug in firmly to the socket.</span>
                    </div>
                    <div class="border"><input type="checkbox" /></div>
                    <div class="border"><input type="checkbox" /></div>
                    <div class="border"><input type="checkbox" /></div>
                    <div class="border"><input type="text" placeholder="กรุณาระบุ" /></div>

                    <div class="border detail flex flex-col">
                        1.6 ขั้วปลั๊กต้องไม่หลุด หรือเห็นเป็นสายเปลือย 
                        <span class="text-blue-500">(1.6) The plug base should be in good condition, not come out or bare wire.</span>
                    </div>
                    <div class="border"><input type="checkbox" /></div>
                    <div class="border"><input type="checkbox" /></div>
                    <div class="border"><input type="checkbox" /></div>
                    <div class="border"><input type="text" placeholder="กรุณาระบุ" /></div>

                    <div class="border detail flex flex-col">
                        1.7 ขั้วปลั๊กต้องไม่หลุดออกจากฉนวนพลาสติก 
                        <span class="text-blue-500">(1.7) Plug base should not come out for the plastic insulation.</span>
                        <span class="text-red-500">** ห้ามใช้ผ้าที่เปียกชื้นเช็ด และใช้อุปกรณ์ใดๆแหย่รูปลั๊กเด็ดขาด</span>
                        <span class="text-blue-500">**Do not clean by wet cloth and Do not plug anything in the socket.</span>
                    </div>
                    <div class="border"><input type="checkbox" /></div>
                    <div class="border"><input type="checkbox" /></div>
                    <div class="border"><input type="checkbox" /></div>
                    <div class="border"><input type="text" placeholder="กรุณาระบุ" /></div>

                    <div class="border detail flex flex-col">
                        1.8 น็อตยึดฝาครอบต้องแน่น, ไม่หลุด หลวม หรือชำรุด 
                        <span class="text-blue-500">(1.8) The screws must fasten on the cover tightly. Not loosen or broken. </span>
                    </div>
                    <div class="border"><input type="checkbox" /></div>
                    <div class="border"><input type="checkbox" /></div>
                    <div class="border"><input type="checkbox" /></div>
                    <div class="border"><input type="text" placeholder="กรุณาระบุ" /></div>

                    <div class="border detail flex flex-col">
                        1.9 ฝาครอบต้องไม่มีรอยบิดเบี้ยว, ไม่แตกร้าว หรือมีรอยบุบ 
                        <span class="text-blue-500">(1.9) No deform, break or dent on the cover.</span>
                        <span class="text-red-500">**กรณีเต้ารับว่างไม่ได้ใช้งานต้องมีการเสียบ Outlet plug </span>
                        <span class="text-blue-500">**If not use, the Outlet Plug must be plugged in.</span>
                    </div>
                    <div class="border"><input type="checkbox" /></div>
                    <div class="border"><input type="checkbox" /></div>
                    <div class="border"><input type="checkbox" /></div>
                    <div class="border"><input type="text" placeholder="กรุณาระบุ" /></div>

                    <div class="border detail">test<span class="text-blue-500">test</span></div>
                    <div class="border detail">test<span class="text-blue-500">test</span></div>
                    
                </div> --}}
            </div>
        </div>
    </div>


@endsection

@section('scripts')
    <script src="{{ $GLOBALS['script'] }}electricInspection.bundle.js?ver={{ date('Ymdhis') }}"></script>
@endsection
