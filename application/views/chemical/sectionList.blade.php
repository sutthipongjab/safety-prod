@extends('layout/template')
@section('contents')
<div class="flex mx-5">
    <div class="card w-full  shadow-2xl bg-base-100">
        <div class="card-body">
            <label class="form-control w-full max-w-xs">
                <div class="label">
                    <span class="label-text">เลือกแผนก</span>
                </div>
                <select name="" id="owner" class="select select-bordered max-w-xs">
                    <option value=""></option>
                    @foreach ($rev as $r)
                    <option value="{{$r->OWNERCODE}}" owner="{{$r->OWNER}}">{{$r->OWNER}}</option>

                    @endforeach
                </select>
            </label>

            <div class="divider hidden"></div>
            <div class="data-table"></div>
        </div>
    </div>
</div>
<input type="checkbox" id="edit" class="modal-toggle" />
<div class="modal" role="dialog">
    <div class="modal-box">
        <h3 class="font-bold mb-5 text-2xl">แก้ไขข้อมูลสารเคมี</h3>
        <form id="form-edit-sds">
            <input type="text" name="AMEC_SDS_ID" data-map="AMEC_SDS_ID" id="AMEC_SDS_ID" class="hidden" />
            <label class="form-control w-full max-w-sm">
                <div class="label">
                    <span class="label-text">RECEIVED SDS DATE</span>
                </div>
                <input type="text" placeholder="กรอกวันที่" id="RECEIVED_SDS_DATE" name="RECEIVED_SDS_DATE"
                data-map="RECEIVED_SDS_DATE" class="input input-bordered w-full max-w-sm fdate req">
            </label>
            {{-- <label class="form-control w-full max-w-sm">
                <div class="label">
                    <span class="label-text">SDS FILE</span>
                </div>
                <input type="file" id="SDS_TRANSLATE" name="SDS_TRANSLATE" class="file-input file-input-bordered w-full max-w-sm fdate">
            </label> --}}
            <div class="modal-action">
                <label for="edit" class="btn">Cancel</label>
                <button type="button" id="save" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>
@include('chemical/modal_detail')
@endsection

@section('scripts')
<script src="{{ $GLOBALS['script'] }}chemicalSectionList.bundle.js?ver={{ date('Ymdhis') }}"></script>
@endsection