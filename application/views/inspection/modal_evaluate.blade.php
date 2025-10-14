<dialog id="modal_evaluate" class="modal">
    <div class="modal-box max-w-56">
        <h3 class="text-lg font-bold text-center mb-4">ประเมินผล</h3>
        <div class="form-control">
            <label class="label cursor-pointer">
                <span class="text-sm">ผ่าน(Yes)</span>
                <input type="radio" name="PA_AUDIT_EVALUATE"  class="radio  req" value="1" />
            </label>
        </div>
        <div class="form-control">
            <label class="label cursor-pointer">
                <span class="text-sm">ไม่ผ่าน(No)</span>
                <input type="radio" name="PA_AUDIT_EVALUATE"  class="radio  req" value="0" />
            </label>
        </div>
        <div class="modal-action justify-center">
            {{-- <form method="dialog"> --}}
                <button class="btn btn-neutral " id="closeModal">ยกเลิก</button>
                <button class="btn btn-primary " id="evalSubmit">ตกลง</button>
            {{-- </form> --}}
        </div>
    </div>
</dialog>