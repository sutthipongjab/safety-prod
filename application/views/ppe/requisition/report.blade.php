@extends('layout/template')
@section('styles')

@endsection
@section('contents')
    <div class="w-full mx-auto p-6 bg-white rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold text-center mb-6">แบบฟอร์ม เบิกอุปกรณ์ป้องกันอันตรายส่วนบุคคล</h2>

        <div class="grid grid-cols-2 gap-6 mb-6">
            <div class="flex flex-col">
                <label for="employee-id" class="text-lg">รหัสพนักงาน:</label>
                <input id="employee-id" type="text" class="input input-bordered" placeholder="กรอกรหัสพนักงาน">
                <input id="key" type="hidden" class="input input-bordered" value="{{$_SESSION['user']->SEMPNO}}">
            </div>
            <div class="flex flex-col">
                <label for="name" class="text-lg">ชื่อ - สกุล:</label>
                <input id="name" type="text" class="input input-bordered" placeholder="XXXXXXXXX XXXXXXXXXX" readonly>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6 mb-6">
            <div class="flex flex-col">
                <label for="department" class="text-lg">แผนก:</label>
                <input id="department" type="text" class="input input-bordered" placeholder="XXXXXX" readonly>
            </div>
            <div class="flex flex-col">
                <label for="date" class="text-lg">วัน/เดือน/ปี:</label>
                <input id="date" type="date" class="input input-bordered" value="{{ date('Y-m-d') }}">
            </div>
        </div>
        <div class="form-mst" data-NFRMNO="{{$fmst[0]->NNO}}" data-VORGNO="{{$fmst[0]->VORGNO}}" data-CYEAR="{{$fmst[0]->CYEAR}}"></div>

        <table class="table w-full table-auto border-collapse border border-gray-300 mb-6" id="report-table">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border px-4 py-2">ที่ No.</th>
                    <th class="border px-4 py-2">ประเภท (Model)</th>
                    <th class="border px-4 py-2">ชนิด (Type)</th>
                    <th class="border px-4 py-2">ขนาด (Size)</th>
                    <th class="border px-4 py-2">จำนวน (Quantity)</th>
                    <th class="border px-4 py-2">หน่วย (Unit)</th>
                    <th class="border px-4 py-2">เหตุผลการเบิก (Reason)</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>

        <div class="mt-6 flex justify-center">
            <label for="remark" class="font-semibold text-blue-900 mr-5">Remark :</label>
            <textarea name="" id="remark" class="textarea textarea-bordered w-80"></textarea>
        </div>

        <div class="text-center mt-6">
            <button class="btn btn-primary" id="submit_form" disabled>บันทึก</button>
            <button class="btn btn-warning" onclick="window.history.back();">ย้อนกลับ</button>
        </div>

        <div id="flow"></div>
    </div>
@endsection
@section('scripts')
    <script src="{{ $GLOBALS['script'] }}pperequest.bundle.js?ver={{ date('Ymdhis') }}"></script>
@endsection