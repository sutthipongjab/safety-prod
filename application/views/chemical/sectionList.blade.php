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
                {{-- <div class="font-bold">
                    <span class="revision-master">Rev. No. *</span>
                    <span class="rev-edit"></span>
                </div> --}}
            </div>
        </div>
    </div>
    {{-- @include('chemical/drawerMaster') --}}
    {{-- @include('chemical/modal_rebuild') --}}
    {{-- @include('chemical/modal_rev') --}}
    {{-- @include('layout/modal_del') --}}
    @include('chemical/modal_detail')
@endsection

@section('scripts')
    <script src="{{ $GLOBALS['script'] }}chemicalSectionList.bundle.js?ver={{ date('Ymdhis') }}"></script>
@endsection
