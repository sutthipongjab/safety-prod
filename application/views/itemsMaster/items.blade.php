@extends('layout/template')
@section('contents')
    <div class="flex mx-5">
        <div class="card w-full  shadow-2xl bg-base-100">
            <div class="card-body">
                <div class="overflow-x-auto">
                    <table class="table" id="tblMaster"></table>
                </div>
            </div>
        </div>
    </div>

    {{-- drawer add and edit --}}
    @include('itemsMaster/drawerItems')
    
    {{-- modal delete  --}}
    @include('layout/modal_del')
    {{-- <dialog id="my_modal_1" class="modal">
    <div class="modal-box">
        <h3 class="text-lg font-bold">ยืนยันการลบข้อมูล</h3>
        <p class="py-4">โปรดยืนยันการดำเนินการ: ท่านต้องการลบข้อมูลนี้ ใช่หรือไม่?</p>
        <div class="modal-action">
        <form method="dialog">
            <button class="btn bg-red-500 text-white del-item" id="del-item">ตกลง</button>
            <button class="btn btn-primary">ยกเลิก</button>
        </form>
        </div>
    </div>
    </dialog> --}}
@endsection

@section('scripts')
    <script src="{{ $GLOBALS['script'] }}items.bundle.js?ver={{ date('Ymdhis') }}"></script>
@endsection
