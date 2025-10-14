<input type="checkbox" id="detail" class="modal-toggle" />
<div class="modal w-full" >
  <div class="modal-box w-full md:max-w-[90vw]">
    
    <div class="formName text-2xl"></div>
    <div class="flex flex-col">
        <div class="form-header flex gap-3 z-10  sticky top-0 bg-white py-5">
            <div class="form-name flex-1 text-2xl font-bold"></div>
            {{-- <label for="drawer-topic"  class=" btn btn-primary max-w-max sticky top-0" id="add-topic">
                เพิ่มหัวข้อ
            </label> --}}
            {{-- <label for="detail" class="btn btn-neutral" id="close-modal-detail"><i class="icofont-close"></i></label> --}}
            
            <label for="detail" class="btn btn-ghost btn-circle absolute right-0 top-0" id="close-modal-detail"><i class="icofont-close"></i></label>
        </div>
        <div class="flex w-full flex-col gap-1 sktTblPreload">
            <div class="flex">
                <div class="flex-1 skeleton h-8 max-w-40"></div>
                <div class="skeleton h-8 w-16 ml-auto"></div>
            </div>
            <div class="skeleton h-14"></div>
            <div class="skeleton h-14"></div>
            <div class="skeleton h-14"></div>
            <div class="skeleton h-14"></div>
            <div class="skeleton h-14"></div>
            <div class="skeleton h-14"></div>
            <div class="skeleton h-14"></div>
        </div>
        <div class="form-content max-h-[calc(100vh-15em)] hidden">
            <table class="table" id="tblTopic"></table>
            <div class=" join join-vertical content"></div>
        </div>
        {{-- <table class="table" id="tblTopic">
            <thead>
                <tr>
                    <th rowspan="2">ITEM</th>
                    <th rowspan="2" colspan="2">รายละเอียด</th>
                    <th colspan="4">ผลการตรวจสอบ</th>
                </tr>
                <tr>
                    <th>ปกติ</th>
                    <th>ผิดดปกติ</th>
                    <th>ไม่มี</th>
                    <th>รายละเอียดความผิดปกติ</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table> --}}
        <div class="form-footer flex justify-end sticky bg-white bottom-0">
            @include('layout/fancyDialog')
        </div>
    </div>

    {{-- <div class="modal-action sticky bottom-0 bg-white pt-5">
        <label for="detail" class="btn btn-neutral">ปิด</label>
    </div> --}}
  </div>
</div>