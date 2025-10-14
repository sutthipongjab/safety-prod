<input type="checkbox" id="modal_rebuild" class="modal-toggle" />
{{-- <dialog id="modal_rebuild" class="modal"> --}}
<div class="modal" role="dialog">
    <div class="modal-box md:max-w-[90vw] max-h-screen">
        <h2 class="text-2xl font-bold mb-5">เปิดใช้งานสารเคมี</h2>
        {{-- <p class="py-4">โปรดยืนยันการดำเนินการ: ท่านต้องการบันทึกข้อมูลนี้ ใช่หรือไม่?</p> --}}
        
        <div class="text-bold text-red-500 text-remark hidden mb-3">*** การเลือกตัวเลือกเป็นเพียงทางเลือกเสริม ผู้ใช้งานสามารถดำเนินการยืนยันได้ทันที<br>*** หากเลือกจะเป็นการคืนสถานะการใช้งานสารเคมีของแต่ละแผนก</div>
        
        <div class="flex justify-center mb-3">
            <ul class="steps">
            <li class="step step-h-1 step-primary">เลือกสารเคมี</li>
            <li class="step step-h-2">เลือกแผนกที่เคยใช้สาร</li>
            <li class="step step-h-3">ตรวจสอบข้อมูลก่อนยืนยัน</li>
            {{-- <li class="step">Receive Product</li> --}}
            </ul>
        </div>
        {{-- @include('layout/preload') --}}
        <div class="step-1 step-active">
            <div class="flex w-full flex-col gap-4 step-1-skeleton">
                <div class="skeleton h-32 w-full"></div>
                <div class="skeleton h-4 w-full"></div>
                <div class="skeleton h-4 w-full"></div>
                <div class="skeleton h-4 w-full"></div>
            </div>
            <div class="overflow-x-auto w-full">
                <table class="table" id="table_rebuild"></table>
            </div>
        </div>
        <div class="step-2 hidden">
            <div class="flex w-full flex-col gap-4 step-2-skeleton">
                <div class="skeleton h-32 w-full"></div>
                <div class="skeleton h-4 w-full"></div>
                <div class="skeleton h-4 w-full"></div>
                <div class="skeleton h-4 w-full"></div>
            </div>
            <div class="overflow-x-auto w-full">
                <table class="table" id="table_rebuild_sec"></table>
            </div>
        </div>
        <div class="step-3 hidden">
            <div class="flex w-full flex-col gap-4 step-3-skeleton">
                <div class="skeleton h-32 w-full"></div>
                <div class="skeleton h-4 w-full"></div>
                <div class="skeleton h-4 w-full"></div>
                <div class="skeleton h-4 w-full"></div>
            </div>
            <div class="overflow-x-auto w-full">
                <table class="table" id="table_submit"></table>
            </div>
        </div>
        <div class="modal-action">
            <form method="dialog">
                <div class="join gap-[2px]">
                    <button class="btn btn-neutral  join-item" id="cancleModal">ยกเลิก</button>
                    <button class="btn btn-primary  join-item hidden" step="1" id="previous">ย้อนกลับ</button>
                    <button class="btn btn-primary  join-item" step="1" id="next">ถัดไป</button>
                </div>
                {{-- <button class="btn btn-primary  hidden" id="submitform">ตกลง</button> --}}
            </form>
        </div>
    </div>
</div>