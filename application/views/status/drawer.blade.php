<div class="drawer drawer-end">
    <input id="drawer" type="checkbox" class="drawer-toggle" />
    <div class="drawer-content">
    </div>
    <div class="drawer-side z-[100]">    
        <label for="drawer" aria-label="sidebar" class="drawer-overlay"></label>
        <div class="min-h-full w-full px-5 pt-5 md:w-96 bg-base-100 text-base-content">
            <form id="form" action="#">
                <h2 class="mb-4 text-2xl font-bold text-gray-500" id="headeritem">เพิ่ม/แก้ไข</h2>
                <input type="text" name="ST_ID" data-map="ST_ID" id="ST_ID" class="hidden"/>
                <div class="flex flex-col gap-3 mb-3">
                    <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">CODE</span>
                        </div>
                        <input type="text" class="input input-bordered w-full txt-upper req" name="ST_CODE" id="ST_CODE" data-map="ST_CODE" placeholder="e.g. FRM" />
                    </label>
                    <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">NO</span>
                        </div>
                        <input type="number" class="input input-bordered w-full req" name="ST_NO" id="ST_NO" data-map="ST_NO" placeholder="e.g. 1"/>
                    </label>
                    <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">STATUS</span>
                        </div>
                        <input type="text" class="input input-bordered w-full req" name="ST_STATUS" id="ST_STATUS" data-map="ST_STATUS" placeholder="e.g. New"/>
                    </label>
                    <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">TABLE</span>
                        </div>
                        <select class="w-full select select-bordered req"  name="ST_TABLE" id="ST_TABLE" data-map="ST_TABLE">
                            <option></option>
                        </select>
                    </label>
                    <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">COLUMN</span>
                        </div>
                        <select class="w-full select select-bordered req" name="ST_COLUMN" id="ST_COLUMN" data-map="ST_COLUMN" disabled>
                            <option></option>
                        </select>
                    </label>
                    <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">REMARK</span>
                        </div>
                        <textarea class="textarea textarea-bordered" id="ST_REMARK" name="ST_REMARK" data-map="ST_REMARK" placeholder="e.g. Table: STY_CHEMICAL_FORM, Col: NEW_CHEMICAL"></textarea>
                    </label>
                </div>
                <div class="mb-4 flex flex-col gap-3">
                    <button class="btn btn-primary" type="button" id="save">
                        <span class="loading loading-spinner hidden"></span>
                        <span class="btn-text">Save</span>
                    </button>
                    <label class="btn btn-error text-white" id="cancle">Cancel</label>
                </div>
            </form>
        </div>
    </div>
</div>