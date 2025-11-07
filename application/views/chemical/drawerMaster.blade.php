<div class="drawer drawer-end">
    <input id="drawer-master" type="checkbox" class="drawer-toggle" />
    <div class="drawer-content">
    </div>
    <div class="drawer-side z-[100]">    
        <label for="drawer-master" aria-label="sidebar" class="drawer-overlay"></label>
        <div class="min-h-full w-full px-5 pt-5 md:w-96 bg-base-100 text-base-content">
            <h2 class="mb-4 text-2xl font-bold text-gray-500" id="headeritem">เพิ่ม/แก้ไข</h2>
            <form id="chemical-master" action="#">
                <div class="mb-4 flex flex-col gap-3">
                    <input type="text" name="AMEC_SDS_ID" data-map="AMEC_SDS_ID" id="AMEC_SDS_ID" class="hidden"/>
                    <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">RECEIVED SDS DATE</span>
                        </div>
                        <input type="text" placeholder="กรอกวันที่" id="RECEIVED_SDS_DATE" name="RECEIVED_SDS_DATE" data-map="RECEIVED_SDS_DATE" class="input input-bordered w-full max-w-sm fdate req">
                    </label>
                    <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">EFFECTIVE DATE</span>
                        </div>
                        <input type="text" placeholder="กรอกวันที่" id="EFFECTIVE_DATE" name="EFFECTIVE_DATE" data-map="EFFECTIVE_DATE" class="input input-bordered w-full max-w-sm fdate req">
                    </label>
                    <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">PRODUCT CODE / ITEM NO.</span>
                        </div>
                        <input class="input input-bordered w-full max-w-sm req" id="PRODUCT_CODE" name="PRODUCT_CODE" data-map="PRODUCT_CODE" placeholder="e.g. A0301">
                    </label>
                    <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">CHEMICAL NAME/TRADE NAME</span>
                        </div>
                        <input class="input input-bordered w-full max-w-sm req" id="CHEMICAL_NAME" name="CHEMICAL_NAME" data-map="CHEMICAL_NAME" placeholder="e.g. Acetylene gas">
                    </label>
                    <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">MANUFACTURER / VENDOR</span>
                        </div>
                        <input class="input input-bordered w-full max-w-sm req" id="VENDOR" name="VENDOR" data-map="VENDOR" placeholder="e.g. Sangsub Oxygen Co.,Ltd.">
                    </label>
                    <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">PUR. INCHARGE</span>
                        </div>
                        <input class="input input-bordered w-full max-w-sm req" id="PUR_INCHARGE" name="PUR_INCHARGE" data-map="PUR_INCHARGE" placeholder="e.g. Noppamat">
                    </label>
                    <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">UN CLASS</span>
                        </div>
                        <select class="w-full select select-bordered req" id="UN_CLASS" name="UN_CLASS" data-map="UN_CLASS">
                            <option value=""></option>
                            @foreach ($class as $key => $c)
                                <option value="{{$c->TYPE_NO}}" >{{$c->TYPE_NAME}} : {{$c->TYPE_DETAIL}}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">REV</span>
                        </div>
                        <input class="input input-bordered w-full max-w-sm req" id="REV" name="REV" data-map="REV" placeholder="e.g. A">
                    </label>

                    <div class="btn btn-primary" type="button" id="save">
                        <span class="loading loading-spinner hidden"></span>
                        <span class="btn-text">Save</span>
                    </div>
                    <label class="btn btn-error text-white" id="cancle">Cancel</label>
                </div>
            </form>
        </div>
    </div>
</div>