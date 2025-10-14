<div class="drawer drawer-end">
    <input id="drawer-item" type="checkbox" class="drawer-toggle" />
    <div class="drawer-content">
    </div>
    <div class="drawer-side z-[100]">    
        <label for="drawer-item" aria-label="sidebar" class="drawer-overlay"></label>
        <div class="min-h-full w-full px-5 pt-5 md:w-96 bg-base-100 text-base-content">
            <form id="item-form" action="#" enctype="multipart/form-data">
                <h2 class="mb-4 text-2xl font-bold text-gray-500" id="headeritem">เพิ่ม/แก้ไข</h2>
                <input type="text" name="ITEMS_ID" data-map="ITEMS_ID" id="ITEMS_ID" class="hidden"/>
                <input type="text" name="userno"  id="userno" value="{{ $_SESSION['user']->SEMPNO}}" class="hidden"/>

                <div class="flex flex-col gap-3 mb-3">
                    <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">ชื่อหมวดหมู่(ภาษาไทย)</span>
                        </div>
                        <input type="text" class="input input-bordered w-full req th-only" name="ITEMS_NAME" id="ITEMS_NAME" data-map="ITEMS_NAME" placeholder="e.g. สายแลน" />
                        <p class='text-red-600 text-sm warn-th hidden'>กรุณากรอกภาษาไทยเท่านั้น</p>
                    </label>
                    <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">ชื่อหมวดหมู่(ภาษาอังกฤษ)</span>
                        </div>
                        <input type="text" class="input input-bordered w-full en-only" name="ITEMS_ENAME" id="ITEMS_ENAME" data-map="ITEMS_ENAME" placeholder="e.g. LAN cable"/>
                        <p class='text-red-600 text-sm warn-en hidden'>กรุณากรอกภาษาอังกฤษเท่านั้น</p>
                    </label>
                    <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">ประเภทหมวดหมู่</span>
                        </div>
                        <select class="w-full select select-bordered req" name="ITEMS_TYPE"  id="ITEMS_TYPE" data-map="ITEMS_TYPE">
                            <option value=""></option>
                            @foreach ($type as $key => $t)
                                <option value="{{$t->TYPE_ID}}" >{{$t->TYPE_NAME}}</option>
                            @endforeach
                        </select>
                    </label>
                </div>
                <div class="mb-4 flex flex-col gap-3">
                    <button class="btn btn-primary" type="button" id="save-item">
                        <span class="loading loading-spinner hidden"></span>
                        <span class="btn-text">Save</span>
                    </button>
                    <label class="btn btn-error text-white" id="cancle">Cancel</label>
                </div>
            </form>
        </div>
    </div>
</div>