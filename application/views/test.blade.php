@extends('layout/template')
@section('contents')
    <div class="flex mx-5">
        <div class="card w-full  shadow-2xl bg-base-100">
            <div class="card-body">
                <label class="form-control w-full max-w-xs">
                    <div class="label">
                        <span class="label-text">ทดสอบ excel js</span>
                    </div>
                    <button id="exceljs" class="btn btn-primary  w-full max-w-xs" >excelJS</button>
                </label>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ $GLOBALS['script'] }}test.bundle.js?ver={{ date('Ymdhis') }}"></script>
@endsection
