<div class="drawer drawer-end">
    <input id="drawer-type" type="checkbox" class="drawer-toggle" />
    <div class="drawer-content">
    </div>
    <div class="drawer-side z-[100]">    
        <label for="drawer-type" aria-label="sidebar" class="drawer-overlay"></label>
        <div class="min-h-full w-full px-5 pt-5 md:w-96 bg-base-100 text-base-content">
            <form id="type-form" action="#" {{--enctype="multipart/form-data"--}}>
                <h2 class="mb-4 text-2xl font-bold text-gray-500" id="headeritem">เพิ่ม/แก้ไข</h2>
                <input type="text" name="TYPE_ID" data-map="TYPE_ID" id="TYPE_ID" class="hidden"/>
                <div class="flex flex-col gap-3 mb-3">
                    <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">ประเภท</span>
                        </div>
                        <select class="w-full select select-bordered" id="type">
                            <option value="newType">New</option>
                            @foreach ($master as $key => $m)
                                <option value="{{$m->TYPE_MASTER}}" >{{$m->TYPE_MASTER}}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">HEADER</span>
                        </div>
                        <input type="text" class="input input-bordered w-full req" name="TYPE_MASTER" id="TYPE_MASTER" data-map="TYPE_MASTER" placeholder="e.g. AREA" />
                    </label>
                    <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">TYPE CODE</span>
                        </div>
                        <input type="text" class="input input-bordered w-full req" name="TYPE_CODE" id="TYPE_CODE" data-map="TYPE_CODE" placeholder="e.g. AM"/>
                    </label>
                    <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">No 
                                <div class="tooltip" data-tip="0 = Master, 99 = Category">
                                    <i class="icofont-info-square "></i>
                                </div>
                            </span>
                        </div>
                        <input type="number" class="input input-bordered w-full req" name="TYPE_NO" id="TYPE_NO" data-map="TYPE_NO" placeholder="e.g. 0"/>
                    </label>
                    <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">NAME</span>
                        </div>
                        <input type="text" class="input input-bordered w-full req" name="TYPE_NAME" id="TYPE_NAME" data-map="TYPE_NAME" placeholder="e.g. AREA"/>
                    </label>
                    <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">DETAIL</span>
                        </div>
                        <textarea class="textarea textarea-bordered req" id="TYPE_DETAIL" name="TYPE_DETAIL" data-map="TYPE_DETAIL" placeholder="e.g. AREA MASTER"></textarea>
                    </label>
                    <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">STATUS</span>
                        </div>
                        <input type="checkbox" class="toggle toggle-success" id="TYPE_STATUS" name="TYPE_STATUS" data-map="TYPE_STATUS" />
                    </label>
                </div>
                <div class="mb-4 flex flex-col gap-3">
                    <button class="btn btn-primary" type="button" id="save-type">
                        <span class="loading loading-spinner hidden"></span>
                        <span class="btn-text">Save</span>
                    </button>
                    <label class="btn btn-error text-white" id="cancle">Cancel</label>
                </div>
            </form>
        </div>
    </div>
</div>