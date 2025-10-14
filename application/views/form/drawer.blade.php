<div class="drawer drawer-end">
    <input id="drawer-form" type="checkbox" class="drawer-toggle" />
    <div class="drawer-content">
    </div>
    <div class="drawer-side z-[100]">    
        <label for="drawer-form" aria-label="sidebar" class="drawer-overlay"></label>
        <div class="min-h-full w-full px-5 pt-5 md:w-96 bg-base-100 text-base-content">
            <form id="form" action="#" enctype="multipart/form-data">
                <h2 class="mb-4 text-2xl font-bold text-gray-500" id="formHeader"></h2>

                <input type="text" name="CATEGORY_NAME" data-map="CATEGORY_NAME" id="CATEGORY_NAME" class="hidden"/>
                <input type="text" name="FORMNO" data-map="FORMNO" id="FORMNO" class="hidden"/>
                <input type="text" name="userno"  id="userno" value="{{ $_SESSION['user']->SEMPNO}}" class="hidden"/>

                <div class="mb-4">
                    <label for="FRM_NAME" class="block text-gray-700 text-sm font-bold mb-2">ชื่อแบบฟอร์ม(ภาษาไทย)</label>
                    <input type="text" class="input input-bordered w-full" name="FORMNAME" id="FORMNAME" data-map="FORMNAME" placeholder="e.g. แบบฟอร์มตรวจสอบอุปกร์ไฟฟ้าประจำเดือน" />
                    <p class='text-red-600 text-sm warn-th hidden'>กรุณากรอกภาษาไทยเท่านั้น</p>
                </div>
                
                <div class="mb-4">
                    <label for="FRM_ENAME" class="block text-gray-700 text-sm font-bold mb-2">ชื่อแบบฟอร์ม(ภาษาอังกฤษ)</label>
                    <input type="text" class="input input-bordered w-full" name="FORMENAME" id="FORMENAME" data-map="FORMENAME" placeholder="e.g. Monthly Electrical Equipment Inspection Form"/>
                    <p class='text-red-600 text-sm warn-en hidden'>กรุณากรอกภาษาอังกฤษเท่านั้น</p>
                </div>

                <div class="mb-4">
                    <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">หมวดหมู่</span>
                        </div>
                        <select class="w-full select select-bordered req" name="CATEGORY"  id="CATEGORY" data-map="CATEGORY">
                            <option value=""></option>
                            @foreach ($category as $key => $c)
                                <option value="{{$c->TYPE_ID}}" >{{$c->TYPE_NAME}}</option>
                            @endforeach
                        </select>
                    </label>
                </div>
                
                <div class="mb-4">
                    <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">ประเภท</span>
                        </div>
                        <select class="w-full select select-bordered" name="TYPE"  id="TYPE" data-map="TYPE">
                            <option value=""></option>
                            @foreach ($type as $key => $t)
                                <option value="{{$t->TYPE_ID}}" >{{$t->TYPE_NAME}}</option>
                            @endforeach
                        </select>
                    </label>
                </div>
                <div class="mb-4 hidden status">
                    <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">STATUS</span>
                        </div>
                        <input type="checkbox" class="toggle toggle-success" id="STATUS" name="STATUS" data-map="STATUS" />
                    </label>
                </div>
                
                <div class="mb-4 flex flex-col gap-3">
                    <button class="btn btn-primary save" type="button" data-type="form">
                        <span class="loading loading-spinner hidden"></span>
                        <span class="btn-text">Save</span>
                    </button>
                    <label class="btn btn-error " for="drawer-form" id="cancel">Cancel</label>
                </div>
            </form>
        </div>
    </div>
</div>