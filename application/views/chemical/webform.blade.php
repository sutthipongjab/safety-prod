@extends('layout/webflowTemplate')
@php
    function checkSelect($value, $check, $return){
        if (!$return){
            return '';
        }
        if($value == $check || ((is_array($value) && in_array($check, $value)))){
            return 'selected';
        }
        return ''; 
    }

    function checkHidden($value, $return){
        if(!$return){
            return 'hidden';
        }
        if ($value) {
            return '';
        }
        return 'hidden';
    }

    function checkWidth($value, $return){
        if (!$return) {
            return 'w-full';
        }
        if ($value) {
            return 'w-1/2';
        }
        return 'w-full';
    }
@endphp
@section('contents')
    <div class="flex mx-5">
        <div class="card w-full  shadow-2xl bg-base-100">
            <div class="card-body">

                <div class="ownerLoading mt-5 flex flex-col gap-5">
                    <div class="flex gap-5 w-full justify-center ">
                        <div class="skeleton h-10 w-80"></div>
                    </div>

                    <div class="flex gap-5">
                        <div class="skeleton min-h-40 h-full w-full"></div>
                        @if ($approve && $detail->FORM_TYPE == 1)
                            @switch($cextData)
                            @case('00')
                                <div class="safety-form1-Load flex flex-col gap-5 w-full">
                                    <div class="skeleton h-28 w-full"></div>
                                    <div class="flex gap-2">
                                        <div class="flex flex-col w-full gap-1">
                                            <div class="skeleton h-10 w-full"></div>
                                            <div class="skeleton h-10 w-full"></div>
                                            <div class="skeleton h-10 w-full"></div>
                                            <div class="skeleton h-10 w-full"></div>
                                        </div>
                                        <div class="flex flex-col w-full gap-1">
                                            <div class="skeleton h-10 w-full"></div>
                                            <div class="skeleton h-10 w-full"></div>
                                            <div class="skeleton h-10 w-full"></div>
                                            <div class="skeleton h-10 w-full"></div>
                                        </div>
                                    </div>
                                </div>
                                @break
                            @case('02')
                                <div class="efc-form-Load flex flex-col gap-5 w-full">
                                    <div class="flex flex-col gap-2">
                                        <div class="flex gap-5 w-full">
                                            <div class="skeleton h-10 w-full"></div>
                                            <div class="skeleton h-10 w-full"></div>
                                        </div>
                                        <div class="skeleton min-h-40 h-full w-full"></div>
                                    </div>
                                </div>
                                @break
                            @case('04')
                                <div class="bp-form-Load flex flex-col gap-5 w-full">
                                    <div class="flex flex-col gap-2">
                                        <div class="flex gap-5 w-full">
                                            <div class="skeleton h-10 w-full"></div>
                                            <div class="skeleton h-10 w-full"></div>
                                        </div>
                                        <div class="skeleton min-h-10 h-full w-full"></div>
                                        <div class="flex gap-5 w-full">
                                            <div class="skeleton h-10 w-full"></div>
                                            <div class="skeleton h-10 w-full"></div>
                                        </div>
                                        <div class="skeleton min-h-40 h-full w-full"></div>
                                    </div>
                                </div>
                                @break
                            @case('06')
                                <div class="safety-form2-Load flex flex-col gap-5 w-full">
                                    <div class="flex flex-col gap-2">
                                        <div class="skeleton min-h-10 h-full w-full"></div>
                                        <div class="flex gap-5 w-full">
                                            <div class="flex flex-col gap-2 w-full">
                                                <div class="skeleton h-10 w-full"></div>
                                                <div class="skeleton h-10 w-full"></div>
                                                <div class="skeleton h-10 w-full"></div>
                                                <div class="skeleton h-10 w-full"></div>
                                                <div class="skeleton h-10 w-full"></div>
                                            </div>
                                            <div class="flex flex-col gap-2 w-full">
                                                <div class="skeleton h-10 w-full"></div>
                                                <div class="skeleton h-10 w-full"></div>
                                                <div class="skeleton h-10 w-full"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @break
                            @default
                            @endswitch
                        @endif

                    </div>
                    {{-- flow --}}
                    <div class="skeleton h-40 w-80"></div>
                    {{-- button --}}
                    <div class="flex gap-1">
                        <div class="skeleton h-10 w-24"></div>
                        <div class="skeleton h-10 w-24"></div>
                    </div>
                </div>
                {{-- ขอใช้สารเคมี --}}
                @if ($detail->FORM_TYPE == 1)

                    <div class="form-Name text-3xl font-bold text-center hidden mb-5"></div> 
                    <div class="flex flex-col lg:flex-row">
                        <div class="join join-vertical h-full border-base-300 border w-full lg:w-1/3 detail hidden">
                            <div class="collapse collapse-arrow join-item information">
                                <input type="radio" name="my-collapse"/>
                                <div class="collapse-title text-xl font-bold">ข้อมูลสารเคมี</div>
                                <div class="collapse-content ">
                                    <div class="grid grid-flow-col">
                                        <div class="grid grid-rows-[repeat(8,_minmax(0,_auto))] grid-flow-col grid-cols-[max-content]  gap-3">
                                            <span class="font-bold">ผู้ขอใช้สารเคมี</span>
                                            <span class="font-bold">Requested</span>
                                            <span class="font-bold">ชื่อสารเคมี</span>
                                            <span class="font-bold">ปริมาณที่ใช้</span>
                                            <span class="font-bold">ใช้สำหรับ</span>
                                            <span class="font-bold">ไฟล์แนบ</span>
                                            <span class="font-bold">จุดใช้งาน</span>
                                            <span class="font-bold">จุดจัดเก็บ</span>
                                        
                                            <span class="text-gray-500">{{$detail->OWNER}}</span>
                                            <span class="text-gray-500">({{$request->SEMPNO}}) {{$request->STNAME}}</span>
                                            <span class="text-gray-500">{{ $detail->CHEMICAL_NAME}}</span>
                                            <span class="text-gray-500">{{ $detail->QUANTITY_KG}} {{ $detail->QUANTITY_TYPE}}</span>
                                            {{-- @if ($detail->QUANTITY_TYPE == '1')
                                                <span class="text-gray-500">{{ $detail->QUANTITY_KG }} kg/วัน</span>
                                            @elseif ($detail->QUANTITY_TYPE == '2')
                                                <span class="text-gray-500">{{ $detail->QUANTITY_KG }} kg/เดือน</span>
                                            @endif --}}
                                            <span class="text-gray-500">{{ $detail->USED_FOR}}</span>
                                            <div>
                                                <a href="{{ base_url() }}chemical/webflow/downloadFile/{{ $layoutFile->FILE_ONAME }}/{{$layoutFile->FILE_FNAME }}" target="_blank">
                                                    <i class="icofont-download text-blue-500"></i>
                                                    <span class="text-gray-500">{{ $layoutFile->FILE_ONAME}}</span>
                                                </a>
                                            </div>
                                            <span class="text-gray-500">{{str_replace('|',', ', $detail->USED_AREA)}} </span> 
                                            <span class="text-gray-500">{{str_replace('|',', ', $detail->KEEPING_POINT)}}</span> 
                                           
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="collapse collapse-arrow join-item border-t safety1-detail hidden ">
                                <input type="radio" name="my-collapse" />
                                <div class="collapse-title text-xl font-bold">ส่วนความปลอดภัย</div>
                                <div class="collapse-content">
                                    <div class="w-full">
                                        <div class="grid grid-flow-col ">
                                            <div class="grid grid-rows-[repeat(6,_minmax(0,_auto))] grid-cols-[max-content] grid-flow-col   gap-3">
                                                <span class="font-bold">กฎหมายที่เกี่ยวข้อง</span>
                                                <span class="font-bold">ตรวจสุขภาพเพิ่มเติม</span>
                                                <span class="font-bold">ตรวจสอบสิ่งแวดล้อมเพิ่มเติม</span>
                                                <span class="font-bold">อุปกรณ์ PPE ที่ต้องใช้</span>
                                                <span class="font-bold">ผู้ควบคุมพิเศษ</span>
                                                <span class="font-bold">ระบบป้องกันและระงับอัคคีภัย</span>
                                            
                                                <span class="text-gray-500 ">{{$detail->LAW}}</span> 
                                                <span class="text-gray-500">{!!$detail->BEI!!}</span> 
                                                <span class="text-gray-500">{!!$detail->EVM_PARAMETER!!}</span>
                                                <span class="text-gray-500">{{$detail->PPE_EQUIPMENT}}</span>
                                                <span class="text-gray-500">{!!$detail->SPECIAL_CONTROLLER!!}</span>
                                                <span class="text-gray-500"> {{$detail->FIRE_EQUIPMENT}}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="collapse collapse-arrow join-item border-t efc-detail hidden">
                                <input type="radio" name="my-collapse"/>
                                <div class="collapse-title text-xl font-bold">ส่วนสิ่งแวดล้อม</div>
                                <div class="collapse-content">
                                    <div class="w-full">
                                        <div class="grid grid-flow-col ">
                                            <div class="grid grid-rows-[repeat(3,_minmax(0,_auto))] grid-cols-[max-content] grid-flow-col   gap-3">
                                                <span class="font-bold">การควบคุมน้ำเสีย , ของเสีย</span>
                                                <span class="font-bold">ผลการตรวจสอบ</span>
                                                <span class="font-bold">เหตุผลที่ไม่ผ่าน</span>

                                                <span class="text-gray-500">{!!$detail->EFC_WASTE!!}</span>
                                                <span class="text-gray-500">{!!$detail->EFC_RESULT!!}</span>
                                                <span class="text-gray-500">{{ $detail->EFC_REASON }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="collapse collapse-arrow join-item border-t bp-detail hidden">
                                <input type="radio" name="my-collapse"/>
                                <div class="collapse-title text-xl font-bold">ส่วนจัดซื้อ</div>
                                <div class="collapse-content">
                                    <div class="w-full">
                                        <div class="grid grid-flow-col ">
                                            <div class="grid grid-rows-[repeat(6,_minmax(0,_auto))] grid-cols-[max-content] grid-flow-col   gap-3">
                                                <span class="font-bold">เจ้าหน้าที่จัดซื้อสารเคมี</span>
                                                <span class="font-bold">Product Code หรือ Item No.</span>
                                                <span class="font-bold">SDS ต้นฉบับ </span>
                                                <span class="font-bold">บริษัทผู้ขาย  </span>
                                                <span class="font-bold">เลขประจำตัวผู้เสียภาษี </span>
                                                <span class="font-bold">ที่อยู่ผู้ขาย  </span>
                                                <span class="text-gray-500">{{$detail->PUR_CODE}}{{ $detail->PUR_INCHARGE }}</span>
                                                <span class="text-gray-500">{{ $detail->PRODUCT_CODE }}</span>
                                                @if (!empty($SDSfile))
                                                    
                                                    <div>
                                                        <a href="{{ base_url() }}chemical/webflow/downloadFile/{{ $SDSfile->FILE_ONAME }}/{{ $SDSfile->FILE_FNAME }}" target="_blank">
                                                            <i class="icofont-download text-blue-500"></i>
                                                            <span class="text-gray-500">{{ $SDSfile->FILE_ONAME}}</span>
                                                        </a>
                                                    </div>
                                                @else
                                                    <span>-</span>
                                                @endif
                                                <span class="text-gray-500">{{ $detail->VENDOR_NAME }}</span>
                                                <span class="text-gray-500">{{ $detail->VENDOR_TAX_NO }}</span>
                                                <span class="text-gray-500">{{ $detail->VENDOR_ADDRESS }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="collapse collapse-arrow join-item border-t safety2-detail hidden">
                                <input type="radio" name="my-collapse"/>
                                <div class="collapse-title text-xl font-bold">ส่วนความปลอดภัย (วัตถุอันตราย)</div>
                                <div class="collapse-content">
                                    <div class="w-full">
                                        <div class="grid grid-flow-col ">
                                            <div class="grid grid-rows-[repeat(9,_minmax(0,_auto))] grid-cols-[max-content] grid-flow-col   gap-3">
                                                <span class="font-bold">รายการวัตถุอันตราย</span>
                                                <span class="font-bold">CAS No.</span>
                                                <span class="font-bold">ชื่อสาร </span>
                                                <span class="font-bold">น้ำหนัก (%)  </span>
                                                <span class="font-bold">ประเภทวัตถุอันตราย </span>
                                                <span class="font-bold">สารที่สามารถก่อมะเร็ง  </span>
                                                <span class="font-bold">ข้อมูลเพิ่มเติม</span>
                                                <span class="font-bold">กลุ่มการเกิดมะเร็ง  </span>
                                                <span class="font-bold">CLASS  </span>
                                                <span class="text-gray-500">{{$detail->HAZARDOUS}}</span>
                                                <span class="text-gray-500">{{ $detail->CAS_NO }}</span>
                                                <span class="text-gray-500">{{ $detail->SUBSTANCE_NAME }}</span>
                                                <span class="text-gray-500">{{ $detail->SUBSTANCE_WEIGHT }}</span>
                                                <span class="text-gray-500">{{ $detail->SUBSTANCE_TYPE }}</span>
                                                <span class="text-gray-500">{{$detail->CARCINOGENS}}</span>
                                                <span class="text-gray-500">{{$detail->CARCINOGENS_DETAIL}}</span>
                                                <span class="text-gray-500">{{$detail->CARCINOGENS_TYPE}}</span>
                                                <span class="text-gray-500">{{$detail->CLASS}}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="divider lg:divider-horizontal detail-divider hidden"></div>

                        <div class="w-full lg:w-2/3 fill-in">
                           
                            {{-- safety form 1 --}}
                            @switch($cextData)
                                @case('00')
                                    @php
                                        $LAW = '';
                                        $BEI = '';
                                        $EVM_PARAMETER = '';
                                        $PPE_EQUIPMENT = '';
                                        $EFFECTIVE_DATE = '';
                                        $FIRE_EQUIPMENT = '';
                                        if($return){
                                            $LAW = $defaultDetail->LAW;
                                            $BEI = $defaultDetail->BEI;
                                            $EVM_PARAMETER  = $defaultDetail->EVM_PARAMETER; 
                                            $PPE_EQUIPMENT  = !empty($defaultDetail->PPE_EQUIPMENT) ? explode('|', $defaultDetail->PPE_EQUIPMENT) : '';
                                            $FIRE_EQUIPMENT = !empty($defaultDetail->FIRE_EQUIPMENT) ? explode('|', $defaultDetail->FIRE_EQUIPMENT) : ''; 
                                            $EFFECTIVE_DATE = $detail->EFFECTIVE_DATE; 
                                        } 
                                    @endphp
                                    <form class="safety-form1 flex-col hidden">
                                        {{-- <div class="divider"></div> --}}
                                        <div class="text-2xl font-bold">ส่วนความปลอดภัย</div> 

                                        <label class="form-control w-full">
                                            <div class="label">
                                                <span class="label-text">กฎหมายที่เกี่ยวข้อง</span>
                                            </div>
                                            <textarea type="text" placeholder="กรอกชื่อกฎหมายที่เกี่ยวข้อง" id="LAW" name="LAW" class="textarea textarea-bordered w-full h-20 py-3 req"
                                            >{{$LAW}}</textarea>
                                        </label>  

                                        <div class="flex mt-2">
                                            <div class="flex-1">
                                                <div class="flex flex-col xl:flex-row xl:gap-5">
                                                    <label class="form-control {{checkWidth($BEI, $return)}} min-w-40">
                                                        <div class="label">
                                                            <span class="label-text">ตรวจสุขภาพเพิ่มเติม</span>
                                                        </div>
                                                        <select class="select select-bordered w-full req" id="HEALTH" name="HEALTH">
                                                            <option value="">กรุณาเลือก</option>
                                                            <option value="1"{{ checkSelect($defaultDetail->HEALTH, 1, $return)}}>จำเป็น</option>
                                                            <option value="0"{{ checkSelect($defaultDetail->HEALTH, 0, $return)}}>ไม่จำเป็น</option>
                                                        </select>
                                                    </label>
                                                    <label class="form-control w-full  {{ checkHidden($BEI, $return)}}">
                                                        <div class="label">
                                                            <span class="label-text">BEI ที่ต้องตรวจ</span>
                                                        </div>
                                                        <input type="text" placeholder="กรอก BEI ที่ต้องตรวจ" id="BEI" name="BEI" class="input input-bordered w-full  req" value="{{ $BEI }}"/>
                                                    </label>
                                                </div>
                    
                                                <div class="flex flex-col xl:flex-row xl:gap-5">
                                                    <label class="form-control {{checkWidth($EVM_PARAMETER, $return)}} min-w-40">
                                                        <div class="label">
                                                            <span class="label-text">ตรวจสิ่งแวดล้อมเพิ่มเติม</span>
                                                        </div>
                                                        <select class="select select-bordered w-full req" id="ENVIRONMENT" name="ENVIRONMENT">
                                                            <option value="">กรุณาเลือก</option>
                                                            {{-- <option value="1">จำเป็น</option>
                                                            <option value="0">ไม่จำเป็น</option> --}}
                                                            <option value="1" {{ checkSelect($defaultDetail->ENVIRONMENT, 1, $return)}}>จำเป็น</option>
                                                            <option value="0" {{ checkSelect($defaultDetail->ENVIRONMENT, 0, $return)}}>ไม่จำเป็น</option>
                                                        </select>
                                                    </label>
                                                    <label class="form-control w-full  {{ checkHidden($EVM_PARAMETER, $return)}}">
                                                        <div class="label">
                                                            <span class="label-text">Parameter ที่ต้องตรวจ</span>
                                                        </div>
                                                        <input type="text" placeholder="กรอก Parameter" id="EVM_PARAMETER" name="EVM_PARAMETER" class="input input-bordered w-full  req" value="{{ $EVM_PARAMETER }}"/>
                                                    </label>
                                                </div>
                    
                                                <div class="flex flex-col xl:flex-row xl:gap-5">
                                                    <label class="form-control {{checkWidth($PPE_EQUIPMENT, $return)}} min-w-40">
                                                        <div class="label">
                                                            <span class="label-text">อุปกรณ์ PPE ที่ต้องใช้</span>
                                                        </div>
                                                        <select class="select select-bordered w-full req" id="PPE" name="PPE">
                                                            <option value="">กรุณาเลือก</option>
                                                            {{-- <option value="1">จำเป็น</option>
                                                            <option value="0">ไม่จำเป็น</option> --}}
                                                            <option value="1" {{ checkSelect($defaultDetail->PPE, 1, $return)}}>จำเป็น</option>
                                                            <option value="0" {{ checkSelect($defaultDetail->PPE, 0, $return)}}>ไม่จำเป็น</option>
                                                        </select>
                                                    </label>
                                                    <label class="form-control w-full  {{ checkHidden($PPE_EQUIPMENT, $return)}}">
                                                        <div class="label">
                                                            <span class="label-text">เลือกอุปกรณ์</span>
                                                        </div>
                                                        <select class="select select-bordered w-xs req" id="PPE_EQUIPMENT" name="PPE_EQUIPMENT" multiple="multiple">
                                                            {{-- <option value="">กรุณาเลือกอุปกรณ์</option> --}}
                                                            <option value="หน้ากาก" {{ checkSelect($PPE_EQUIPMENT, 'หน้ากาก', $return) }}>หน้ากาก</option>
                                                            <option value="แว่นตา" {{ checkSelect($PPE_EQUIPMENT, 'แว่นตา', $return) }}>แว่นตา</option>
                                                            <option value="ถุงมือ" {{ checkSelect($PPE_EQUIPMENT, 'ถุงมือ', $return) }}>ถุงมือ</option>
                                                        </select>
                                                    </label>
                                                </div>
                    
                                                <label class="form-control w-full">
                                                    <div class="label">
                                                        <span class="label-text">ผู้ควบคุมพิเศษ</span>
                                                    </div>
                                                    <select class="select select-bordered w-full req" id="SPECIAL_CONTROLLER" name="SPECIAL_CONTROLLER">
                                                        <option value="">กรุณาเลือก</option>
                                                        <option value="1"{{ checkSelect($defaultDetail->SPECIAL_CONTROLLER, 1, $return)}}>จำเป็น</option>
                                                        <option value="0"{{ checkSelect($defaultDetail->SPECIAL_CONTROLLER, 0, $return)}}>ไม่จำเป็น</option>
                                                    </select>
                                                </label>
                                            </div>
                                            
                                            <div class="divider divider-horizontal"></div>

                                            <div class="flex-1">
                                                <label class="form-control w-full ">
                                                    <div class="label">
                                                        <span class="label-text">ระบบป้องกันและระงับอัคคีภัย</span>
                                                    </div>
                                                    <select class="select select-bordered w-xs req" id="FIRE_EQUIPMENT" name="FIRE_EQUIPMENT" multiple="multiple" >
                                                        {{-- <option value="" disabled>กรุณาเลือกอุปกรณ์</option> --}}
                                                        <option value="โฟม" {{ checkSelect( $FIRE_EQUIPMENT, 'โฟม', $return) }}>โฟม</option>
                                                        <option value="น้ำ" {{ checkSelect( $FIRE_EQUIPMENT, 'น้ำ', $return) }}>น้ำ</option>
                                                        <option value="ผงเคมีแห้ง" {{ checkSelect( $FIRE_EQUIPMENT, 'ผงเคมีแห้ง', $return) }}>ผงเคมีแห้ง</option>
                                                        <option value="คาร์บอนไดออกไซด์" {{ checkSelect( $FIRE_EQUIPMENT, 'คาร์บอนไดออกไซด์', $return) }}>คาร์บอนไดออกไซด์</option>
                                                    </select>
                                                </label>
                                                <label class="form-control w-full ">
                                                    <div class="label">
                                                        <span class="label-text">EFFECTIVE DATE</span>
                                                    </div>
                                                    <input type="text" placeholder="กรอกวันที่" id="EFFECTIVE_DATE" name="EFFECTIVE_DATE" class="input input-bordered w-full  fdate req" 
                                                    @if ($detail->NEW_CHEMICAL == '0')
                                                        value="{{$detail->EFFECTIVE_DATE}}"
                                                        disabled
                                                    @else
                                                        value="{{$EFFECTIVE_DATE}}"
                                                    @endif />
                                                </label>
                                                <label class="form-control w-full ">
                                                    <div class="label">
                                                        <span class="label-text">เลือก EFC</span>
                                                    </div>
                                                    <select class="select select-bordered w-xs req" id="EFC" name="EFC">
                                                        <option value="">กรุณาเลือก</option>
                                                        @foreach ($EFC as $e)
                                                            <option value="{{ $e->SEMPNO }}" {{ checkSelect( $EFCAPV->VAPVNO, $e->SEMPNO, $return) }}>({{ $e->SEMPNO }}){{ $e->STNAME }}</option>
                                                        @endforeach
                                                    </select>
                                                </label>
                                                @if ($detail->NEW_CHEMICAL == '1')
                                                    <label class="form-control w-full ">
                                                        <div class="label">
                                                            <span class="label-text">เลือก B/P</span>
                                                        </div>
                                                        <select class="select select-bordered w-xs req" id="BP" name="BP">
                                                            <option value="">กรุณาเลือก</option>
                                                            @foreach ($BP as $b)
                                                                <option value="{{ $b->SEMPNO }}" {{ checkSelect( $BPAPV->VAPVNO, $b->SEMPNO, $return) }}>({{ $b->SEMPNO }}){{ $b->STNAME }}</option>
                                                            @endforeach
                                                        </select>
                                                    </label>
                                                @endif
                                            </div>
                                        </div>
                                    </form>
                                    @break
                                @case('02')
                                    @php
                                        $EFC_REASON = '';
                                        if($return){
                                            $EFC_REASON = $defaultDetail->EFC_REASON;
                                        } 
                                    @endphp
                                    <form class="efc-form flex-col h-full hidden">
                                        {{-- <div class="divider"></div> --}}
                                        <div class="flex gap-5">
                                            <label class="form-control w-full">
                                                <div class="label">
                                                    <span class="label-text">การควบคุมน้ำเสีย , ของเสีย</span>
                                                </div>
                                                <select class="select select-bordered w-full req" id="EFC_WASTE" name="EFC_WASTE">
                                                    <option value="">กรุณาเลือก</option>
                                                    <option value="1" {{ checkSelect($defaultDetail->EFC_WASTE, 1, $return)}}>จำเป็น</option>
                                                    <option value="0" {{ checkSelect($defaultDetail->EFC_WASTE, 0, $return)}}>ไม่จำเป็น</option>
                                                </select>
                                            </label>
                                            <label class="form-control w-full">
                                                <div class="label">
                                                    <span class="label-text">ผลการตรวจสอบ</span>
                                                </div>
                                                <select class="select select-bordered w-full req" id="EFC_RESULT" name="EFC_RESULT">
                                                    <option value="">กรุณาเลือก</option>
                                                    <option value="1" {{ checkSelect($defaultDetail->EFC_RESULT, 1, $return)}}>ผ่าน</option>
                                                    <option value="0" {{ checkSelect($defaultDetail->EFC_RESULT, 0, $return)}}>ไม่ผ่าน</option>
                                                </select>
                                            </label>
                                        </div>

                                        <label class="form-control w-full h-full {{ checkHidden($EFC_REASON, $return)}}">
                                            <div class="label">
                                                <span class="label-text">เหตุผล</span>
                                            </div>
                                            <textarea name="EFC_REASON" id="EFC_REASON" placeholder="กรอกเหตุผล" class="input input-bordered w-full min-h-40 h-full req">{{ $EFC_REASON }}</textarea>
                                        </label>
                                    </form>
                                    @break
                                @case('04')
                                    @php
                                        $PUR_CODE     = '';
                                        $PUR_INCHARGE = '';
                                        $VENDOR_NAME  = '';
                                        $PRODUCT_CODE = '';
                                        $VENDOR_TAX_NO  = '';
                                        $VENDOR_ADDRESS = '-';
                                        if($return){
                                            $PUR_CODE     = $defaultDetail->PUR_CODE;
                                            $PUR_INCHARGE = $defaultDetail->PUR_INCHARGE;
                                            $VENDOR_NAME  = $defaultDetail->VENDOR_NAME;
                                            $PRODUCT_CODE = $defaultDetail->PRODUCT_CODE;
                                            $VENDOR_TAX_NO  = $defaultDetail->VENDOR_TAX_NO;
                                            $VENDOR_ADDRESS = $defaultDetail->VENDOR_ADDRESS;
                                        }
                                    @endphp
                                    <form class="bp-form flex-col h-full hidden">
                                        {{-- <div class="divider"></div> --}}
                                        <input type="text" id="PUR_INCHARGE" name="PUR_INCHARGE"  class="hidden" value="{{ $PUR_INCHARGE }}">
                                        <input type="text" id="VENDOR_NAME" name="VENDOR_NAME" class="hidden" value="{{ $VENDOR_NAME }}">
                                        <div class="flex gap-5">
                                            <label class="form-control w-full">
                                                <div class="label">
                                                    <span class="label-text">รหัสพนักงาน ของเจ้าหน้าที่จัดซื้อที่ดูแลการสั่งสารเคมีนี้</span>
                                                </div>
                                                <input type="text" class="input input-bordered w-full req" id="PUR_CODE" name="PUR_CODE" placeholder="e.g. 24008" value="{{ $PUR_CODE }}">
                                            </label>
                                            <label class="form-control w-full">
                                                <div class="label">
                                                    <span class="label-text">Product Code หรือ Item No. </span>
                                                </div>
                                                <input type="text" class="input input-bordered w-full" id="PRODUCT_CODE" name="PRODUCT_CODE" placeholder="(โปรดระบุถ้ามี)" value="{{ $PRODUCT_CODE }}">
                                            </label>
                                        </div>
                                        <label class="form-control w-full">
                                            <div class="label justify-start gap-3">
                                                <span class="label-text">SDS ต้นฉบับ</span>
                                                @if (!empty($SDSfile))
                                                    <div>
                                                        <a href="{{ base_url() }}chemical/webflow/downloadFile/{{ $SDSfile->FILE_ONAME }}/{{ $SDSfile->FILE_FNAME }}" target="_blank">
                                                            <i class="icofont-download text-blue-500"></i>
                                                            <span class="text-gray-500">{{ $SDSfile->FILE_ONAME}}</span>
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                            <input type="file" id="SDS_FILE" name="SDS_FILE" accept="application/pdf" class="file-input file-input-bordered w-full req" />
                                        </label>
                                        <div class="flex gap-5">
                                            <label class="form-control w-full">
                                                <div class="label">
                                                    <span class="label-text">บริษัทผู้ขาย</span>
                                                </div>
                                                <select class="select select-bordered w-full req" id="VENDOR" name="VENDOR">
                                                    {{-- <option value="">กรุณาเลือก</option> --}}
                                                    <option value="">กรุณาเลือกผู้ขาย</option>
                                                    @foreach ($VENDORS as $v)
                                                        <option value="{{ $v->VENCODE }}" {{ checkSelect( $defaultDetail->VENDOR, $v->VENCODE, $return) }}>({{ $v->VENCODE }}){{ $v->VENNAME }}</option>
                                                    @endforeach
                                                </select>
                                                {{-- <input type="text" class="input input-bordered w-full req" id="VENDOR" name="VENDOR" placeholder="(ชื่อบริษัท ผู้ขาย)" > --}}
                                            </label>
                                            <label class="form-control w-full">
                                                <div class="label">
                                                    <span class="label-text">เลขประจำตัวผู้เสียภาษี</span>
                                                </div>
                                                <input type="text" class="input input-bordered w-full req" id="VENDOR_TAX_NO" name="VENDOR_TAX_NO" value="{{ $VENDOR_TAX_NO }}">
                                            </label>
                                        </div>
                                        <label class="form-control w-full h-full">
                                            <div class="label">
                                                <span class="label-text">ที่อยู่</span>
                                            </div>
                                            <span id="VENDOR_ADDRESS" name="VENDOR_ADDRESS">{{ $VENDOR_ADDRESS }}</span>
                                            {{-- <textarea class="textarea textarea-bordered w-full min-h-40 h-full req" id="VENDOR_ADDRESS" name="VENDOR_ADDRESS" placeholder="(ที่อยู่, เบอร์โทรศัพท์บริษัท ผู้ขาย)" disabled></textarea> --}}
                                        </label>

                                        
                                    </form>
                                    @break
                                @case('06')
                                    @php
                                        $CLASS     = '';
                                        $HAZARDOUS = '';
                                        $CAS_NO    = '';
                                        $SUBSTANCE_NAME     = '';
                                        $SUBSTANCE_WEIGHT   = '';
                                        $SUBSTANCE_TYPE     = '';
                                        $CARCINOGENS        = '';
                                        $CARCINOGENS_DETAIL = '';
                                        $CARCINOGENS_TYPE   = '';
                                        if($return){
                                            $CLASS     = $defaultDetail->CLASS;
                                            $HAZARDOUS = $defaultDetail->HAZARDOUS;
                                            $CAS_NO    = $defaultDetail->CAS_NO;
                                            $SUBSTANCE_NAME     = $defaultDetail->SUBSTANCE_NAME;
                                            $SUBSTANCE_WEIGHT   = $defaultDetail->SUBSTANCE_WEIGHT;
                                            $SUBSTANCE_TYPE     = $defaultDetail->SUBSTANCE_TYPE;
                                            $CARCINOGENS        = $defaultDetail->CARCINOGENS;
                                            $CARCINOGENS_DETAIL = $defaultDetail->CARCINOGENS_DETAIL;
                                            $CARCINOGENS_TYPE   = $defaultDetail->CARCINOGENS_TYPE;
                                        }
                                    @endphp
                                    <form class="safety-form2 flex-col h-full hidden">
                                        <label class="form-control w-full">
                                            <div class="label">
                                                <span class="label-text">Class</span>
                                            </div>
                                            <select class="select select-bordered w-full req" id="CLASS" name="CLASS">
                                                <option value="">กรุณาเลือกผู้ขาย</option>
                                                    @foreach ($HAZARDOUSCLASS as $h)
                                                        <option value="{{ $h->TYPE_ID }}" {{ checkSelect( $CLASS, $h->TYPE_ID, $return) }}>{{ $h->TYPE_NAME }} : {{ $h->TYPE_DETAIL }}</option>
                                                    @endforeach
                                            </select>
                                        </label>
                                        <div class="flex">
                                            <div class="flex flex-col w-full">
                                                <label class="form-control w-full">
                                                    <div class="label">
                                                        <span class="label-text">รายการวัตถุอันตราย</span>
                                                    </div>
                                                    <select class="select select-bordered w-full req" id="HAZARDOUS" name="HAZARDOUS">
                                                        <option value="">กรุณาเลือก</option>
                                                        <option value="1" {{ checkSelect( $HAZARDOUS, 1, $return) }}>มี</option>
                                                        <option value="0" {{ checkSelect( $HAZARDOUS, 0, $return) }}>ไม่มี</option>
                                                    </select>
                                                </label>
                                                <label class="form-control {{ checkHidden($HAZARDOUS, $return) }}">
                                                    <div class="label">
                                                        <span class="label-text">CAS No.</span>
                                                    </div>
                                                    <input type="text" class="input input-bordered w-full req" id="CAS_NO" name="CAS_NO" value="{{ $CAS_NO }}" placeholder="กรุณาระบุ Cas No.">
                                                </label>
                                                <label class="form-control {{ checkHidden($HAZARDOUS, $return) }}">
                                                    <div class="label">
                                                        <span class="label-text">ชื่อสาร</span>
                                                    </div>
                                                    <input type="text" class="input input-bordered w-full req" id="SUBSTANCE_NAME" name="SUBSTANCE_NAME" value="{{ $SUBSTANCE_NAME }}" placeholder="กรุณาระบุชื่อสาร">
                                                </label>
                                                <label class="form-control {{ checkHidden($HAZARDOUS, $return) }}">
                                                    <div class="label">
                                                        <span class="label-text">น้ำหนัก (%)</span>
                                                    </div>
                                                    <input type="number" class="input input-bordered w-full req" id="SUBSTANCE_WEIGHT" name="SUBSTANCE_WEIGHT" min="0" step="0.01" value="{{ $SUBSTANCE_WEIGHT }}" placeholder="กรุณาระบุน้ำหนัก เป็นตัวเลข">
                                                </label>
                                                <label class="form-control {{ checkHidden($HAZARDOUS, $return) }} w-full">
                                                    <div class="label">
                                                        <span class="label-text">ประเภทวัตถุอันตราย</span>
                                                    </div>
                                                    <select class="select select-bordered w-full req" id="SUBSTANCE_TYPE" name="SUBSTANCE_TYPE">
                                                        <option value="">กรุณาเลือก</option>
                                                        <option value="1" {{ checkSelect( $SUBSTANCE_TYPE, 1, $return) }}>1</option>
                                                        <option value="2" {{ checkSelect( $SUBSTANCE_TYPE, 2, $return) }}>2</option>
                                                        <option value="3" {{ checkSelect( $SUBSTANCE_TYPE, 3, $return) }}>3</option>
                                                    </select>
                                                </label>
                                            </div>
                                            <div class="divider divider-horizontal safety2-divider "></div>
                                            <div class="flex flex-col w-full">
                                                <label class="form-control w-full">
                                                    <div class="label">
                                                        <span class="label-text">สารที่สามารถก่อมะเร็ง</span>
                                                    </div>
                                                    <select class="select select-bordered w-full req" id="CARCINOGENS" name="CARCINOGENS">
                                                        <option value="">กรุณาเลือก</option>
                                                        <option value="1" {{ checkSelect( $CARCINOGENS, 1, $return) }}>มี</option>
                                                        <option value="0" {{ checkSelect( $CARCINOGENS, 0, $return) }}>ไม่มี</option>
                                                    </select>
                                                </label>
                                                <label class="form-control {{ checkHidden($CARCINOGENS, $return) }}">
                                                    <div class="label">
                                                        <span class="label-text">ระบุ</span>
                                                    </div>
                                                    <input type="text" class="input input-bordered w-full req" id="CARCINOGENS_DETAIL" name="CARCINOGENS_DETAIL" value="{{ $CARCINOGENS_DETAIL }}" placeholder="กรุณาระบุรายละเอียด">
                                                </label>
                                                <label class="form-control {{ checkHidden($CARCINOGENS, $return) }} w-full">
                                                    <div class="label">
                                                        <span class="label-text">กลุ่มการเกิดมะเร็ง</span>
                                                    </div>
                                                    <select class="select select-bordered w-full req" id="CARCINOGENS_TYPE" name="CARCINOGENS_TYPE">
                                                        <option value="">กรุณาเลือก</option>
                                                        @foreach ($CARCINOGENSCLASS as $c)
                                                            <option value="{{ $c->TYPE_ID }}" {{ checkSelect( $c->TYPE_ID, $CARCINOGENS_TYPE,  $return) }}>{{ $c->TYPE_NAME }} : {{ $c->TYPE_DETAIL }}</option>
                                                        @endforeach
                                                        {{-- <option value="1">กลุ่ม 1 ยืนยันว่าเป็นสารก่อมะเร็งในมนุษย์</option>
                                                        <option value="2">กลุ่ม 2A น่าจะเป็นสารก่อมะเร็งในมนุษย์</option>
                                                        <option value="3">กลุ่ม 2B อาจจะเป็นสารก่อมะเร็งในมนุษย์</option>
                                                        <option value="4">กลุ่ม 3 ไม่สารมารถจัดกลุ่มได้ว่าเป็นสารก่อมะเร็งในมนุษย์หรือไม่</option>
                                                        <option value="5">กลุ่ม 4 น่าจะไม่เป็นสารก่อมะเร็งในมนุษย์</option> --}}
                                                    </select>
                                                </label>
                                            </div>
                                        </div>
                                        
                                        
                                    </form>
                                    @break
                                @default
                            @endswitch
                        </div>
                    </div>   

                    
                {{-- ขอเปลี่ยนสารเคมี --}}
                @elseif ($detail->FORM_TYPE == 2)
                    <div class="form-Name text-3xl font-bold text-center hidden">แบบฟอร์มการขอเปลี่ยนสารเคมี</div>
                    <div class="detail flex-col gap-5 border rounded-lg p-5 hidden">
                        <div class="w-full">
                            <div class="grid grid-flow-col ">
                                <div class="grid grid-rows-[repeat(7,_minmax(0,_auto))] grid-cols-[max-content] grid-flow-col   gap-3">
                                    <span class="font-bold">ผู้ขอเปลี่ยนสารเคมี</span>
                                    <span class="font-bold">Requested</span>
                                    <span class="font-bold">ชื่อสารเคมี </span>
                                    <span class="font-bold">การใช้ประโยชน์</span>
                                    <span class="font-bold">จุดใช้งาน </span>
                                    <span class="font-bold">จุดจัดเก็บ </span>
                                    <span class="font-bold">ชื่อผู้ควบคุมดูแลสารเคมีในแผนก</span>
                                    <span class="text-gray-500">{{$detail->OWNER}}</span>
                                    <span class="text-gray-500">({{$request->SEMPNO}}) {{$request->STNAME}}</span>
                                    <span class="text-gray-500">{{ $detail->CHEMICAL_NAME}}</span>
                                    <span class="text-gray-500">{{ $detail->USED_FOR}}</span>
                                    <span class="text-gray-500">{{str_replace('|',', ', $detail->USED_AREA)}} </span> 
                                    <span class="text-gray-500">{{str_replace('|',', ', $detail->KEEPING_POINT)}}</span> 
                                    <span class="text-gray-500">{{$detail->USER_CONTROL}}</span> 
                                </div>
                            </div>
                        </div>
                    </div>
                {{-- ยกเลิกใช้สารเคมี --}}
                @elseif ($detail->FORM_TYPE == 3)
                    
                    {{-- Add your logic here for "ยกเลิกใช้สารเคมี" form type --}}
                    <div class="form-Name text-3xl font-bold text-center hidden">แบบฟอร์มยกเลิกใช้สารเคมี</div>
                    <div class="detail flex-col gap-5 border rounded-lg p-5 hidden">
                        <div class="w-full">
                            <div class="grid grid-flow-col ">
                                <div class="grid grid-rows-[repeat(6,_minmax(0,_auto))] grid-cols-[max-content] grid-flow-col   gap-3">
                                    <span class="font-bold">ผู้ขอเปลี่ยนสารเคมี</span>
                                    <span class="font-bold">Requested</span>
                                    <span class="font-bold">ชื่อสารเคมี </span>
                                    <span class="font-bold">การใช้ประโยชน์</span>
                                    <span class="font-bold">จุดใช้งาน </span>
                                    <span class="font-bold">เหตุผลการยกเลิกใช้สารเคมี</span>
                                    <span class="text-gray-500">{{$detail->OWNER}}</span>
                                    <span class="text-gray-500">({{$request->SEMPNO}}) {{$request->STNAME}}</span>
                                    <span class="text-gray-500">{{ $detail->CHEMICAL_NAME}}</span>
                                    <span class="text-gray-500">{{ $detail->USED_FOR}}</span>
                                    <span class="text-gray-500">{{str_replace('|',', ', $detail->USED_AREA)}} </span> 
                                    <span class="text-gray-500">{{$detail->REASON_CANCEL}}</span> 
                                </div>
                            </div>
                        </div>
                    </div> 
                @endif
                
                @if ($approve)
                    
                    <div class="actions-Form my-5 hidden">
                        <label class="form-control mb-5 w-80">
                            <div class="label">
                                <span class="label-text">Remark</span>
                            </div>
                            <textarea class="textarea textarea-bordered h-24" id="remark" ></textarea>
                        </label>
                        <div class="flex gap-3 ">
                            <button type="button" class="btn btn-primary" name="btnAction" value="approve">Approve</button>
                            <button type="button" class="btn btn-neutral mg-l-12" name="btnAction" value="reject">Reject</button>
                            @if ($detail->FORM_TYPE == 1)
                                {{-- {{$cextData}} {{$request->SEMPNO}} {{$empno}} --}}
                                @switch($cextData)
                                    @case('01')
                                    @case('03')
                                    @case('05')
                                    @case('07')
                                        {{-- @if($detail->NEW_CHEMICAL == 1) --}}
                                            <button type="button" class="btn btn-secondary" name="btnAction" value="returnb">Return</button>
                                        {{-- @endif --}}
                                        @break
                                    {{-- @default
                                        @if(is_null($cextData) && $request->SEMPNO != $empno)
                                            <button type="button" class="btn btn-secondary" name="btnAction" value="return">Return</button>
                                        @endif --}}
                                @endswitch
                            @endif
                        </div>
                    </div>
                @endif

            </div>

            <div class="card-footer m-8">
                <div class="user-data hidden" empno="{{$empno}}" cextData="{{$cextData}}"></div>
                <div class="form-detail hidden" 
                    newChm="{{ $detail->NEW_CHEMICAL }}"
                    formType="{{ $detail->FORM_TYPE }}"
                    formReturn="{{$return}}">
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
    <script src="{{ $GLOBALS['script'] }}chemicalWebform.bundle.js?ver={{ date('Ymdhis') }}"></script>
@endsection
