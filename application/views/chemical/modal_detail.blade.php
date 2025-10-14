
<input type="checkbox" id="modal_detail" class="modal-toggle" />
<div class="modal" role="dialog">
  <div class="modal-box w-full  lg:min-w-max">
    <div class="detail-Loading mt-5 flex flex-col gap-5">
        <div class="flex gap-5 w-full justify-center ">
            <div class="skeleton h-10 w-80"></div>
        </div>

        <div class="flex gap-5">
            <div class="skeleton min-h-40 h-full w-full"></div>
        </div>
    </div>
    <div role="tablist" class="tabs tabs-bordered  detail hidden max-h-[calc(100vh-15em)] overflow-auto">
        <div class="bg-white w-11/12 h-10 absolute top-4"></div>
        <input type="radio" id="detail-chemical" name="my_tabs_1" role="tab" class="tab text-nowrap sticky top-0" aria-label="ข้อมูลสารเคมี" checked="checked"/>
        <div role="tabpanel" class="tab-content pt-10">
            <div class="join join-vertical h-full border-base-300 border w-full">
                <div class="collapse collapse-arrow join-item information">
                    <input type="radio" name="my-collapse"/>
                    <div class="collapse-title text-xl font-bold">ข้อมูลสารเคมี</div>
                    <div class="collapse-content ">
                        <div class="grid grid-flow-col">
                            <div class="grid grid-rows-[repeat(6,_minmax(0,_auto))] grid-flow-col grid-cols-[max-content] general-detail  gap-3">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="collapse collapse-arrow join-item border-t   ">
                    <input type="radio" name="my-collapse" />
                    <div class="collapse-title text-xl font-bold">ส่วนความปลอดภัย</div>
                    <div class="collapse-content">
                        <div class="w-full">
                            <div class="grid grid-flow-col ">
                                <div class="grid grid-rows-[repeat(6,_minmax(0,_auto))] grid-cols-[max-content] grid-flow-col safety1-detail  gap-3">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="collapse collapse-arrow join-item border-t  ">
                    <input type="radio" name="my-collapse"/>
                    <div class="collapse-title text-xl font-bold">ส่วนสิ่งแวดล้อม</div>
                    <div class="collapse-content">
                        <div class="w-full">
                            <div class="grid grid-flow-col ">
                                <div class="grid grid-rows-[repeat(3,_minmax(0,_auto))] grid-cols-[max-content] grid-flow-col  efc-detail gap-3">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="collapse collapse-arrow join-item border-t  ">
                    <input type="radio" name="my-collapse"/>
                    <div class="collapse-title text-xl font-bold">ส่วนจัดซื้อ</div>
                    <div class="collapse-content">
                        <div class="w-full">
                            <div class="grid grid-flow-col ">
                                <div class="grid grid-rows-[repeat(6,_minmax(0,_auto))] grid-cols-[max-content] grid-flow-col bp-detail gap-3">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="collapse collapse-arrow join-item border-t  ">
                    <input type="radio" name="my-collapse"/>
                    <div class="collapse-title text-xl font-bold">ส่วนความปลอดภัย (วัตถุอันตราย)</div>
                    <div class="collapse-content">
                        <div class="w-full">
                            <div class="grid grid-flow-col ">
                                <div class="grid grid-rows-[repeat(9,_minmax(0,_auto))] grid-cols-[max-content] grid-flow-col safety2-detail  gap-3">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      
        <input type="radio" name="my_tabs_1" role="tab" class="tab text-nowrap sticky top-0" aria-label="PDF Preview"/>
        <div role="tabpanel" class="tab-content p-10">
            <label for="" class="btn btn-error flex items-center w-12 absolute top-2 left-64 tooltip tooltip-right" data-tip="ดาว์นโหลดไฟล์ PDF" id="exportPDFDetail" >
                <i class="icofont-file-pdf text-xl" ></i>
            </label>
            <div id="print-preview">
                <h2 class="text-2xl mb-5 preview-chmName">ข้อมูลสารเคมี</h2>
                <div class="preview-detail grid grid-rows-[repeat(28,_minmax(0,_auto))] grid-flow-col grid-cols-[max-content]"></div>
            </div>
        </div>
    </div>
    
    
    <div class="modal-action">
        <label for="modal_detail" class="btn btn-neutral">Close</label>
    </div>
  </div>
  
</div>