<div id="fancyDialog" class="w-11/12 h-full hidden gap-5">
    <div class="flex">
        <div class="font-bold text-3xl FancyHeader"></div>
        <div class="ml-auto">
            <button id="showFancyImgForm" class="btn btn-primary max-w-sm">เพิ่มรูปภาพ</button>
            <form id="formAddFancyImage" action="#" class="gap-5 hidden"  enctype="multipart/form-data">
                <input type="file" accept="image/*" multiple class="file-input file-input-bordered max-w-sm" name="FancyImage[]" id="FancyImage">
                <div class="flex gap-1">
                    <button type="button" class="btn btn-neutral" id="cancelFancyImage" >ยกเลิก</button>
                    <button type="button" class="btn btn-primary" id="addFancyImage">ยืนยัน</button>
                </div>
            </form>
        </div>
    </div>

    <div id="fancyContainer" class="f-carousel h-full w-full max-w-[90vw] max-h-[65vh] hidden">
        <!-- รูปภาพจะถูกแสดงที่นี่ -->
    </div>
    <div id="navFancy" class="f-carousel gap-3 hidden max-h-20 navFancy"></div>
    {{-- <button id="deleteImage" class="btn hidden">ลบภาพ</button> --}}
</div>