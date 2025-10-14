<div class="drawer drawer-end">
    <input id="drawer-area" type="checkbox" class="drawer-toggle" />
    <div class="drawer-content">
    </div>
    <div class="drawer-side z-[100]">    
        <label for="drawer-area" aria-label="sidebar" class="drawer-overlay"></label>
        <div class="min-h-full w-full px-5 pt-5 md:w-96 bg-base-100 text-base-content">
            <form id="area-form" action="#" enctype="multipart/form-data">
                <h2 class="mb-4 text-2xl font-bold text-gray-500" id="headerArea">เพิ่ม/แก้ไข พื้นที่</h2>
                <input type="text" name="AREA_ID" data-map="AREA_ID" id="AREA_ID" class="hidden"/>
                <input type="text" name="AREA_DIVCODE" data-map="AREA_DIVCODE" id="AREA_DIVCODE" class="hidden"/>
                <input type="text" name="AREA_DEPTCODE" data-map="AREA_DEPTCODE" id="AREA_DEPTCODE" class="hidden"/>
                <input type="text" name="AREA_SECCODE" data-map="AREA_SECCODE" id="AREA_SECCODE" class="hidden"/>
                <input type="text" name="userno"  id="userno" value="{{ $_SESSION['user']->SEMPNO}}" class="hidden"/>


                <div class="mb-4">
                    <label for="AREA_EMPNO" class="block text-gray-700 text-sm font-bold mb-2 ">ผู้รับผิดชอบ(รหัสพนักงาน)</label>
                    <input type="text" class="input input-bordered  w-full req" name="AREA_EMPNO" id="AREA_EMPNO" data-map="AREA_EMPNO" placeholder="e.g. 24008" />
                </div>
                <div class="mb-4">
                    <label for="AREA_OWNER" class="block text-gray-700 text-sm font-bold mb-2 ">ชื่อผู้รับผิดชอบ</label>
                    <input type="text" class="input w-full" readonly value="-" name="AREA_OWNER" id="AREA_OWNER" data-map="AREA_OWNER" />
                </div>
                <div class="mb-4">
                    <label for="AREA_DIV" class="block text-gray-700 text-sm font-bold mb-2 ">ฝ่าย(Division)</label>
                    <input type="text" class="input w-full" readonly value="-" name="AREA_DIV" id="AREA_DIV" data-map="AREA_DIV" />
                </div>
                <div class="mb-4">
                    <label for="AREA_DEPT" class="block text-gray-700 text-sm font-bold mb-2 ">ส่วน(Department)</label>
                    <input type="text" class="input w-full" readonly value="-" name="AREA_DEPT" id="AREA_DEPT" data-map="AREA_DEPT" />
                </div>
                <div class="mb-4">
                    <label for="AREA_SEC" class="block text-gray-700 text-sm font-bold mb-2 ">แผนก(Section)</label>
                    <input type="text" class="input w-full" readonly value="-" name="AREA_SEC" id="AREA_SEC" data-map="AREA_SEC" />
                </div>
                <div class="mb-4">
                    <label class="form-control w-full max-w-sm">
                        <div class="label">
                            <span class="label-text">ลบรูปภาพ</span>
                        </div>
                        <input type="checkbox" class="toggle toggle-success" id="deleteImage" name="deleteImage" />
                    </label>
                </div>

                <div class="mb-4">
                    <label for="AREA_IMAGE" class="block text-gray-700 text-sm font-bold mb-2 ">รูปพื้นที่รับผิดชอบ</label>
                    <input type="file" class="file-input file-input-bordered  w-full " accept="image/*" name="AREA_IMAGE" id="AREA_IMAGE" data-map="AREA_IMAGE" />
                </div>

                <div class="mb-4 flex flex-col gap-3">
                    <button class="btn btn-primary" type="button" id="save-area">
                        <span class="loading loading-spinner hidden"></span>
                        <span class="btn-text">Save</span>
                    </button>
                    <label class="btn btn-error " for="drawer-area" id="cancel">Cancel</label>
                </div>
            </form>
        </div>
    </div>
</div>