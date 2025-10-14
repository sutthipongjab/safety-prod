<div class="drawer drawer-end">
    <input id="drawer-corrective" type="checkbox" class="drawer-toggle" />
    <div class="drawer-content">
    </div>
    <div class="drawer-side z-[100]">    
        <label for="drawer-corrective" aria-label="sidebar" class="drawer-overlay"></label>
        <div class="min-h-full w-full px-5 pt-5 md:w-96 bg-base-100 text-base-content">
            <div class="list-corrective">
                <div class="flex flex-col gap-3 mb-3">
                    <input type="text" name="PA_ID" data-map="PA_ID" id="PA_ID" class="hidden"/>
                   
                    <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">การดำเนินการในการปรับปรุง (Corrective action)</span>
                        </div>
                        <textarea class="textarea textarea-bordered req" id="PA_CORRECTIVE" name="PA_CORRECTIVE" data-map="PA_CORRECTIVE" placeholder="e.g. ดำเนินการแก้ไขเรียบร้อย"></textarea>
                    </label>
                    <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">ภาพหลังการปรับปรุง (Photo after improvement)</span>
                        </div>
                        <input type="file" class="file-input file-input-bordered w-full req" accept="image/*" name="PA_IMAGE_AFTER" id="PA_IMAGE_AFTER" />
                    </label>
                    <label class="form-control w-full max-w-sm showimg-drawer hidden tooltip tooltip-warning"   data-tip="คลิกเพื่อดูรูป">
                        <div class="my-5 drop-shadow-lg border-image">
                            {{-- <div class="rounded-lg"> --}}
                            <img id="showimg" class="rounded-lg preview-img" src="" />
                            {{-- </div> --}}
                        </div>
                    </label>
                    <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">วันแล้วเสร็จ (Finish date)</span>
                        </div>
                        <input type="text" placeholder="กรอกวันที่" id="PA_FINISH_DATE" name="PA_FINISH_DATE" data-map="PA_FINISH_DATE" class="input input-bordered w-full max-w-sm fdate req" />
                    </label>
                    <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">วันที่ชี้แจง (Morning talk)</span>
                        </div>
                        <input type="text" placeholder="กรอกวันที่" id="PA_MORNING_TALK" name="PA_MORNING_TALK" data-map="PA_MORNING_TALK" class="input input-bordered w-full max-w-sm fdate req" />
                    </label>
                </div>
                <div class="mb-4 flex flex-col gap-3">
                    <div class="btn btn-primary" type="button" id="save-corrective">
                        <span class="loading loading-spinner hidden"></span>
                        <span class="btn-text">Save</span>
                    </div>
                    <label class="btn btn-error text-white" id="cancle">Cancel</label>
                </div>
            </div>
        </div>
    </div>
</div>