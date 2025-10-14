@extends('layout/template')
@section('contents')
    <div class="flex flex-col gap-5 mx-5">
        <div class="card w-full  shadow-2xl bg-base-100">
            <div class="card-body">
                {{-- <button type="button" class="btn btn-primary w-1/4" id="template">Dowload Template</button> --}}
                <div role="tablist" class="tabs tabs-lifted w-full">
                    <input type="radio" name="my_tabs_2" role="tab" class="tab" aria-label="นำเข้าข้อมูล MASTER FORM" checked="checked" />
                    {{-- <a role="tab" class="tab tab-active" id="blogMaster">นำเข้าข้อมูล MASTER FORM</a> --}}
                    <div class="tab-content"  role="tabpanel">
                        <div class="flex flex-1 gap-5 flex-col mt-5">
                            <input type="file" class="file-input file-input-bordered w-full max-w-xs" accept=".xlsx, .xls" id="uploadF">
                            <div class="overflow-x-auto">
                                <table class="table" id="table_upload"></table>
                            </div>
                            {{-- <button type="button" class="btn btn-primary w-1/4 hidden" id="submitF">Upload</button> --}}
                        </div>
                    </div>
                    <input type="radio" name="my_tabs_2" role="tab" class="tab" aria-label="นำเข้าข้อมูล MASTER AREA" />
                    {{-- <a role="tab" class="tab" id='blogMasterSec'>นำเข้าข้อมูล MASTER AREA</a> --}}
                    <div class="hidden tab-content"  role="tabpanel">
                        <div class="flex flex-1 gap-5 flex-col mt-5">
                            <input type="file" class="file-input file-input-bordered w-full max-w-xs" accept=".xlsx, .xls" id="uploadA">
                            <div class="overflow-x-auto">
                                <table class="table" id="table_upload"></table>
                            </div>
                            {{-- <button type="button" class="btn btn-primary w-1/4 hidden" id="submitA">Upload</button> --}}
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        
    </div>
@endsection

@section('scripts')
    <script src="{{ $GLOBALS['script'] }}migrationElec.bundle.js?ver={{ date('Ymdhis') }}"></script>
@endsection
