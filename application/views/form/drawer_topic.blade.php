<div class="drawer drawer-end">
    <input id="drawer-topic" type="checkbox" class="drawer-toggle" />
    <div class="drawer-content">
    </div>
    <div class="drawer-side z-[9999]">    
        <label for="drawer-topic" aria-label="sidebar" class="drawer-overlay"></label>
        <div class="min-h-full w-full px-5 pt-5 md:w-96 bg-base-100 text-base-content">
            <form id="formTopic" action="#" enctype="multipart/form-data">
                <h2 class="mb-4 text-2xl font-bold text-gray-500" id="topicHeader"></h2>

                <input type="text" name="FRMT_CATEGORY" data-map="FRMT_CATEGORY" id="FRMT_CATEGORY" class="hidden"/>
                <input type="text" name="FRMT_RUNNO" data-map="FRMT_RUNNO" id="FRMT_RUNNO" class="hidden"/>
                <input type="text" name="FRMT_NO" data-map="FRMT_NO" id="FRMT_NO" class="hidden"/>
                <input type="text" name="userno"  id="userno" value="{{ $_SESSION['user']->SEMPNO}}" class="hidden"/>
                <div class="mb-4 flex flex-col gap-3">
                    <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">ชื่อหัวข้อ(ภาษาไทย)</span>
                        </div>
                        <input type="text" class="input input-bordered w-full" name="FRMT_TOPIC" id="FRMT_TOPIC" data-map="FRMT_TOPIC" placeholder="e.g. การตรวจสอบปลั๊กและเต้ารับ" />
                    </label>
                    <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">ชื่อหัวข้อ(ภาษาอังกฤษ)</span>
                        </div>
                        <input type="text" class="input input-bordered w-full" name="FRMT_TOPICEN" id="FRMT_TOPICEN" data-map="FRMT_TOPICEN" placeholder="e.g. Check plug appearance and socket."/>
                    </label>
                    {{-- <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">ภาพประกอบ <i class="icofont-info-circle tooltip" data-tip="สามารถแนบได้หลายไฟล์"></i> </span>
                        </div>
                        <input type="file" class="file-input file-input-bordered w-full req" accept="image/*" name="PA_IMAGE" id="PA_IMAGE" />
                    </label> --}}
                    <button class="btn btn-primary save" type="button" data-type="topic">
                        <span class="loading loading-spinner hidden"></span>
                        <span class="btn-text">Save</span>
                    </button>
                    <label class="btn btn-error " for="drawer-topic" id="cancel">Cancel</label>
                </div>
            </form>
        </div>
    </div>
</div>