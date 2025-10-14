<div class="drawer drawer-end">
    <input id="drawer-inspection" type="checkbox" class="drawer-toggle" />
    <div class="drawer-content">
    </div>
    <div class="drawer-side z-[100]">    
        <label for="drawer-inspection" aria-label="sidebar" class="drawer-overlay"></label>
        <div class="min-h-full w-full px-5 pt-5 md:w-96 bg-base-100 text-base-content">
            <div class="list-inspection">
                <div class="flex flex-col gap-3 mb-3">
                    {{-- <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">หมวด(ITEMS)</span>
                        </div>
                        <input type="text" placeholder="e.g.สายแลน Lan cable" id="PA_ITEMS" name="PA_ITEMS" class="input input-bordered w-full max-w-sm req" />
                    </label> --}}
                    <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">หมวด (Category)</span>
                        </div>
                        <select class="w-full select select-bordered req" name="PA_ITEMS"  id="PA_ITEMS" >
                            <option value=""></option>
                            @foreach ($items as $key => $i)
                                    @php
                                        $eitem = empty($i->ITEMS_ENAME)? '' : '('.$i->ITEMS_ENAME.')';
                                        $itemName = $i->ITEMS_NAME.$eitem;
                                    @endphp
                                <option value="{{$i->ITEMS_ID}}" d-text="{{$itemName}}">{{$itemName}}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">บริเวณ (Area detected)</span>
                        </div>
                        <textarea class="textarea textarea-bordered req" id="PA_AREA" name="PA_AREA" placeholder="e.g. Booth F1"></textarea>
                        {{-- <select class="w-full select select-bordered req " name="PA_AREA"  id="PA_AREA" >
                            <option value=""></option>
                            @foreach ($area as $key => $a)
                                <option value="{{$a->AREA_ID}}" d-text="{{$a->AREA_NAME}}({{$a->AREA_ENAME}})">{{$a->AREA_NAME}}({{$a->AREA_ENAME}})</option>
                            @endforeach
                        </select> --}}
                    </label>
                    <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">สิ่งที่ควรปรับปรุง (Items detected)</span>
                        </div>
                        <textarea class="textarea textarea-bordered req" id="PA_DETECTED" name="PA_DETECTED" placeholder="e.g. รางครอบแลนชำรุด Lan cover has damaged."></textarea>
                    </label>
                    <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">ภาพก่อนปรับปรุง (Photo before improvement)</span>
                        </div>
                        <input type="file" class="file-input file-input-bordered w-full req" accept="image/*" name="PA_IMAGE" id="PA_IMAGE" />
                    </label>
                    <label class="form-control w-full max-w-sm showimg-drawer hidden tooltip tooltip-warning"   data-tip="คลิกเพื่อดูรูป">
                        <div class="my-5 drop-shadow-lg border-image">
                            {{-- <div class="rounded-lg"> --}}
                            <img id="showimg" class="rounded-lg preview-img" src="" />
                            {{-- </div> --}}
                        </div>
                    </label>
                    <div class="flex flex-col  min-w-36 max-w-36">
                        <div class="organization hidden">
                            {{-- <div class="flex items-center gap-3 mb-1"> --}}
                                <strong class="">ประเภท (Class)</strong>
                                {{-- <button class="tooltip " data-tip="Class A : ผิดกฎระเบียบบริษัทฯ เช่น ไม่ปฏิบัติตามกฏระเบียบ, ไม่มีอุปกรณ์ที่กำหนดตามระเบียบ
                                Class B : คำแนะนำทั่วไปของคณะกรรมการฯ เช่น อุปกรณชำรุด
                                Class C : อื่น ๆ เช่น 5ส">
                                    <i class="icofont-info-circle hover:text-xl"></i>
                                </button> --}}
                            {{-- </div> --}}
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
                                <div class="form-control">
                                    <label class="label cursor-pointer tooltip flex" data-tip="{{$textTooltip}}">
                                        <span class="text-sm " >{{$c->TYPE_NAME}}</span>
                                        <input type="radio" name="PA_CLASS" id="PA_CLASS"  class="radio  req" d-text="{{$c->TYPE_NAME}}" value="{{$c->TYPE_ID}}" />
                                    </label>
                                </div>
                            @endforeach
                            {{-- <div class="form-control">
                                <label class="label cursor-pointer">
                                    <span class="text-sm">B</span>
                                    <input type="radio" name="PA_CLASS"  class="radio checked:bg-red-500"  />
                                </label>
                            </div>
                            <div class="form-control">
                                <label class="label cursor-pointer">
                                    <span class="text-sm">C</span>
                                    <input type="radio" name="PA_CLASS"  class="radio checked:bg-red-500" />
                                </label>
                            </div> --}}
                        </div>
                    </div>
                    <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">ข้อเสนอแนะในการปรับปรุง (Suggestion)</span>
                        </div>
                        <textarea class="textarea textarea-bordered" name="PA_SUGGESTION" id="PA_SUGGESTION" placeholder="e.g. ดำเนินการปรับปรุง Implove it."></textarea>
                    </label>
                    <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">แจ้ง MAT ซ่อมแซม (Inform to MAT Repair)</span>
                        </div>
                        <div class="form-control min-w-36 max-w-36">
                            <label class="label cursor-pointer flex">
                                <span class="text-sm " >Yes</span>
                                <input type="radio" name="PA_MAT" id="PA_MAT"  class="radio  req" d-text="Yes" value="1" />
                            </label>
                            <label class="label cursor-pointer flex">
                                <span class="text-sm " >No</span>
                                <input type="radio" name="PA_MAT" id="PA_MAT"  class="radio  req" d-text="No" value="0" />
                            </label>
                        </div>
                    </label>
                </div>
                <div class="mb-4 flex flex-col gap-3">
                    <div class="btn btn-primary hidden" type="button" id="save-list">
                        <span class="loading loading-spinner hidden"></span>
                        <span class="btn-text">Save</span>
                    </div>
                    <div class="btn btn-primary hidden" type="button" id="edit-list">
                        <span class="loading loading-spinner hidden"></span>
                        <span class="btn-text">Edit</span>
                    </div>
                    <label class="btn btn-error text-white" id="cancle">Cancel</label>
                </div>
            </div>
        </div>
    </div>
</div>