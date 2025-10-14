@extends('layout/template')
@section('contents')
    <div class="flex flex-col gap-5 mx-5">
        <div class="card w-full  shadow-2xl bg-base-100">
            <div class="card-body">
                <button type="button" class="btn btn-primary w-1/4" id="template">Dowload Template</button>
                <div role="tablist" class="tabs tabs-lifted">
                    <a role="tab" class="tab tab-active" id="blogMaster">นำเข้าข้อมูล Master หลัก</a>
                    <a role="tab" class="tab" id='blogMasterSec'>นำเข้าข้อมูล master ของแต่ละแผนก</a>
                </div>
                <div class="blogMaster">
                    <div class="flex flex-1 gap-5 flex-col mt-5">
                        <input type="file" class="file-input file-input-bordered w-full max-w-xs" accept=".xlsx, .xls" id="upload">
                        <div class="overflow-x-auto">
                            <table class="table" id="table_upload"></table>
                        </div>
                        <button type="button" class="btn btn-primary w-1/4 hidden" id="submit">Upload</button>
                    </div>
                </div>
                <div class="blogMasterSec hidden">
                    <div class="flex flex-1 gap-5 flex-col mt-5">
                        <button type="button" class="btn btn-primary w-1/4" id="importP">Import Data</button>
                        <div class="overflow-x-auto">
                            <table class="table" id="table_import"></table>
                        </div>
                        <button type="button" class="btn btn-primary w-1/4 hidden" id="submitP">Upload</button>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
@endsection

@section('scripts')
    <script src="{{ $GLOBALS['script'] }}importData.bundle.js?ver={{ date('Ymdhis') }}"></script>
@endsection
