@extends('layout/template')
@section('contents')
    <div class="flex mx-5">
        <div class="card w-full  shadow-2xl bg-base-100">
        {{-- <div class="card bg-base-100 flex-1 shadow-xl max-md:min-w-[48%] h-40"> --}}
            <div class="card-body">
                {{-- <div class="join">
                    <button class="btn join-item">เพิ่ม</button>
                    <button class="btn join-item">แก้ไข</button>
                    <button class="btn join-item">ลบ</button>
                </div> --}}
                <div class="overflow-x-auto">
                    <table class="table" id="tblMaster"></table>
                </div>
            </div>
        </div>
    </div>

    {{-- drawer add and edit area --}}
    @include('areaMaster/drawer')
    
    {{-- modal delete area --}}
    {{-- <button class="btn" onclick="my_modal_1.showModal()">open modal</button> --}}
    @include('layout/modal_del')
    {{-- <dialog id="modal_delete" class="modal">
    <div class="modal-box">
        <h3 class="text-lg font-bold">ยืนยันการลบข้อมูล</h3>
        <p class="py-4">โปรดยืนยันการดำเนินการ: ท่านต้องการลบข้อมูลนี้ ใช่หรือไม่?</p>
        <div class="modal-action">
        <form method="dialog">
            <!-- if there is a button in form, it will close the modal -->
            <button class="btn bg-accent text-white del" id="del">ตกลง</button>
            <button class="btn btn-primary text-white">ยกเลิก</button>
        </form>
        </div>
    </div>
    </dialog> --}}


@endsection

@section('scripts')
    <script src="{{ $GLOBALS['script'] }}area.bundle.js?ver={{ date('Ymdhis') }}"></script>
@endsection
