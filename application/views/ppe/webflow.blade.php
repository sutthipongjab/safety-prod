@extends('layout/webflowTemplate')
@section('styles')

@endsection
@section('contents')
    <div class="w-full mx-auto p-6 bg-slate-50 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-center mb-6 text-blue-800">แบบฟอร์ม เบิกอุปกรณ์ป้องกันอันตรายส่วนบุคคล</h2>

        <div class="flex justify-center">
            <table class="w-1/2 table-auto border-collapse rounded-lg overflow-hidden shadow-sm bg-blue-50">
                <tr>
                    <td class="border px-4 py-3 font-semibold text-blue-900">REQUEST NO :</td>
                    <td class="border px-4 py-3 text-blue-800">{{ $frmno }}</td>
                </tr>
                <tr>
                    <td class="border px-4 py-3 font-semibold text-blue-900">INPUT BY :</td>
                    <td class="border px-4 py-3 text-blue-800">{{ $data[0]->EMP_IN_NAME . " ( " . $data[0]->EMP_INPUT . " )" }}</td>
                </tr>
                <tr>
                    <td class="border px-4 py-3 font-semibold text-blue-900">REQUEST BY :</td>
                    <td class="border px-4 py-3 text-blue-800">{{ $data[0]->EMP_REQ_NAME . " ( " . $data[0]->EMP_REQUEST . " )" }}</td>
                </tr>
                <tr>
                    <td class="border px-4 py-3 font-semibold text-blue-900">REQUEST DATE :</td>
                    <td class="border px-4 py-3 text-blue-800">{{ $data[0]->CREATE_DATE }}</td>
                </tr>
            </table>
        </div>
        <div class="fmst" data-nfrmno="{{ $fmst[0]->NNO }}" data-vorgno="{{ $fmst[0]->VORGNO }}" data-cyear="{{ $fmst[0]->CYEAR }}" data-cyear2="{{ $_GET['y2'] }}" data-nrunno="{{ $_GET['runNo'] }}" data-empno="{{ $_GET['empno'] }}"></div>
        <table class="w-full table-auto border-collapse rounded-lg overflow-hidden shadow-sm mt-6" id="report-table">
            <thead>
                <tr class="bg-blue-600 text-white">
                    <th class="border px-4 py-3 text-left text-sm">ที่ No.</th>
                    <th class="border px-4 py-3 text-left text-sm">ประเภท (Model)</th>
                    <!-- <th class="border px-4 py-3 text-left text-sm">ชนิด (Type)</th> -->
                    <th class="border px-4 py-3 text-left text-sm">ขนาด (Size)</th>
                    <th class="border px-4 py-3 text-left text-sm">จำนวน (Quantity)</th>
                    <th class="border px-4 py-3 text-left text-sm">หน่วย (Unit)</th>
                    <th class="border px-4 py-3 text-left text-sm">เหตุผลการเบิก (Reason)</th>
                </tr>
            </thead>
            <tbody id="data-table-body">

                @foreach ($data as $i => $val)
                    @if(!empty($edit) && ($mode == '2' || in_array($empno, $arr_edit)))
                        <tr class="{{ $i % 2 == 0 ? 'bg-blue-50' : 'bg-white' }} edit-row">
                            <td class="border px-4 py-3 text-blue-900 text-sm">{{ $i + 1}}</td>
                            <td class="border px-4 py-3 text-blue-900 text-sm">
                                <input type="hidden" class="urd-id" value="{{ $val->URD_ID }}">
                                <select name="" id="" class="select w-full outline category">
                                    <option value="{{ $val->UNIFORM_CATEGORY }}">{{ $val->CATNAME }}</option>
                                </select>
                            </td>
                            <td class="border px-4 py-3 text-blue-900 text-sm">
                                <select name="" id="" class="select w-full size outline">
                                    <option value="{{ $val->UNIFORM_TYPE }}">{{ $val->PROD_SIZES }}</option>
                                </select>
                            </td>
                            <td class="border px-4 py-3 text-blue-900 text-sm"><input type="text" class="input w-full qty outline" value="{{ $val->QTY }}"></td>
                            <td class="border px-4 py-3 text-blue-900 text-sm">{{ $val->CATUNIT }}</td>
                            <td class="border px-4 py-3 text-blue-900 text-sm">
                                <select name="" id="" class="select w-full reason outline">
                                    <option value="{{ $val->REQUEST_PPE_TYPE }}">{{ $val->RT_DETAIL }}</option>
                                </select>
                            </td>
                        </tr>
                    @else
                        <tr class="{{ $i % 2 == 0 ? 'bg-blue-50' : 'bg-white' }}">
                            <td class="border px-4 py-3 text-blue-900 text-sm">{{ $i + 1}}</td>
                            <td class="border px-4 py-3 text-blue-900 text-sm">{{ $val->CATNAME }}</td>
                            <!-- <td class="border px-4 py-3 text-blue-900 text-sm">{{ $val->CATDESC }}</td> -->
                            <td class="border px-4 py-3 text-blue-900 text-sm">{{ $val->PROD_SIZES }}</td>
                            <td class="border px-4 py-3 text-blue-900 text-sm">{{ $val->QTY }}</td>
                            <td class="border px-4 py-3 text-blue-900 text-sm">{{ $val->CATUNIT }}</td>
                            <td class="border px-4 py-3 text-blue-900 text-sm">{{ $val->RT_DETAIL }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
        @if($mode == '2')

            <div class="mt-6 flex justify-center">
                <label for="remark" class="font-semibold text-blue-900 mr-5">Remark :</label>
                <textarea name="" id="remark" class="textarea textarea-bordered w-80"></textarea>
            </div>
            <div class="mt-6 p-4 flex gap-4 justify-center">
                <button class="btn btn-success bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-5 rounded-md shadow-sm text-sm apv_btn" data-act="approve">Approve</button>
                <button class="btn btn-error bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-5 rounded-md shadow-sm text-sm apv_btn" data-act="reject">Reject</button>
                @if($edit == '1')
                    <button class="btn bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-5 rounded-md shadow-sm text-sm remark_btn" data-act="Remark">Remark</button>
                @endif
            </div>
        @else
            @if(!empty($edit) && in_array($empno, $arr_edit))
                <div class="mt-6 flex justify-center">
                    <button class="btn btn-success" id="submit_edit">Submit</button>
                </div>
            @endif
        @endif

        <div class="mt-6 p-4 bg-blue-50 rounded-lg shadow-inner" id="flow">

        </div>
    </div>

@endsection
@section('scripts')
    <script src="{{ $GLOBALS['script'] }}ppewebform.bundle.js?ver={{ date('Ymdhis') }}"></script>
@endsection