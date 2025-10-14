<div class="drawer drawer-end">
    <input id="drawer-detail" type="checkbox" class="drawer-toggle" />
    <div class="drawer-content">
    </div>
    <div class="drawer-side z-[9999]">    
        <label for="drawer-detail" aria-label="sidebar" class="drawer-overlay"></label>
        <div class="min-h-full w-full px-5 pt-5 md:w-96 bg-base-100 text-base-content">
            <form id="formDetail" action="#" enctype="multipart/form-data">
                <h2 class="mb-4 text-2xl font-bold text-gray-500" id="detailHeader"></h2>

                <input type="text" name="CATEGORY" data-map="CATEGORY" id="CATEGORY" class="hidden"/>
                <input type="text" name="FORMNO" data-map="FORMNO" id="FORMNO" class="hidden"/>
                <input type="text" name="TOPIC_NO" data-map="TOPIC_NO" id="TOPIC_NO" class="hidden"/>
                <input type="text" name="SEQ" data-map="SEQ" id="SEQ" class="hidden"/>
                <input type="text" name="userno"  id="userno" value="{{ $_SESSION['user']->SEMPNO}}" class="hidden"/>
                <div class="mb-4 flex flex-col gap-3">
                    <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">รายละเอียด(ภาษาไทย)</span>
                        </div>
                        <input type="text" class="input input-bordered w-full" name="DETAIL" id="DETAIL" data-map="DETAIL" placeholder="e.g. การตรวจสอบปลั๊กและเต้ารับ" />
                    </label>
                    <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">รายละเอียด(ภาษาอังกฤษ)</span>
                        </div>
                        <input type="text" class="input input-bordered w-full" name="DETAILEN" id="DETAILEN" data-map="DETAILEN" placeholder="e.g. Check plug appearance and socket."/>
                    </label>
                    <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">ประเภท</span>
                        </div>
                        <select class="w-full select select-bordered req" name="DETAIL_TYPE"  id="DETAIL_TYPE" data-map="DETAIL_TYPE">
                            <option value=""></option>
                            @foreach ($detailType as $key => $t)
                                <option value="{{$t->TYPE_ID}}" >{{$t->TYPE_DETAIL}}</option>
                            @endforeach
                        </select>
                    </label>
                    <button class="btn btn-primary save" type="button" data-type="detail">
                        <span class="loading loading-spinner hidden"></span>
                        <span class="btn-text">Save</span>
                    </button>
                    <label class="btn btn-error " for="drawer-detail" id="cancel">Cancel</label>
                </div>
            </form>
        </div>
    </div>
</div>