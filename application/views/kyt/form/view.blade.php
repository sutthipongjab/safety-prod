{{-- @extends('layout/webflowTemplate') --}}

@php
    $template = $webSafety == 1 ? 'layout/template' : 'layout/webflowTemplate';
@endphp
@extends($template)
@section('contents')
<div class="flex mx-5">
    <div class="card w-full  shadow-2xl bg-base-100">
        <div class="card-body">
            <form action="#" id="kyt-form" enctype="multipart/form-data">
                <div class="overflow-x-auto">
                    <div class="text-center font-bold text-xl">แบบฟอร์ม บันทึกการทำกิจกรรม KYT ประจำวัน (KYT Daily
                        Record)</div>
                    <div class="flex max-sm:flex-col gap-2 mb-5">

                        <label class="form-control w-full max-w-xs">
                            <div class="label">
                                <span class="label-text">ผู้พบความเสี่ยง</span>
                                <!--span class="label-text-alt">Top Right label</span-->
                            </div>
                            <p class="text-sm">({{ $kytfrm[0]->EMPNORISK }}) {{ $kytfrm[0]->REQ }}</p>
                            <!--div class="label">
                        <span class="label-text-alt">Bottom Left label</span>
                        <span class="label-text-alt">Bottom Right label</span>
                    </!--div -->
                        </label>
                        <label class="form-control w-full max-w-xs">
                            <div class="label">
                                <span class="label-text">ชื่อ Leader</span>
                                <!--span class="label-text-alt">Top Right label</span-->
                            </div>
                            <p class="text-sm">({{ $kytfrm[0]->EMPNOHEAD }}) {{ $kytfrm[0]->HEAD }}</p>
                            <!--div class="label">
                        <span class="label-text-alt">Bottom Left label</span>
                        <span class="label-text-alt">Bottom Right label</span>
                    </!--div -->
                        </label>
                        <label class="form-control w-full max-w-xs">
                            <div class="label">
                                <span class="label-text">ชื่อกลุ่ม</span>
                                <!--span class="label-text-alt">Top Right label</span-->
                            </div>
                            <p class="text-sm">{{ $kytfrm[0]->GRPNAME }}</p>
                            <!--div class="label">
                        <span class="label-text-alt">Bottom Left label</span>
                        <span class="label-text-alt">Bottom Right label</span>
                    </!--div -->
                        </label>
                    </div>
                    <div class="flex max-sm:flex-col gap-2 mb-5">
                        <label class="form-control w-full max-w-xs sec">
                            <div class="label">
                                <span class="label-text">แผนก</span>
                            </div>
                            <p class="text-sm">{{ $kytfrm[0]->SSEC }}</p>
                        </label>
                        <label class="form-control w-full max-w-xs grprisk">
                            <div class="label">
                                <span class="label-text">หมวดหมู่ความเสี่ยงที่พบ</span>
                            </div>
                            <p class="text-sm">{{ $kytfrm[0]->ITEMS_NAME }}</p>
                        </label>
                    </div>


                    <div class="flex max-sm:flex-col gap-2 mb-5">
                        <div class="w-full space-y-1.5 ">
                            <label for="dtrisk"
                                class="font-sans antialiased text-sm text-stone-800 dark:text-black font-semibold">รายละเอียดความเสี่ยงที่พบ</label>
                            <p class="text-sm ">{{ nl2br($kytfrm[0]->DTRISK) }}</p>
                        </div>
                    </div>
                    <div class="flex max-sm:flex-col gap-2 mb-5">
                        <div class="w-full space-y-1.5">
                            <label for="protect"
                                class="font-sans antialiased text-sm text-stone-800 dark:text-black font-semibold">มาตรการป้องกันที่ดีที่สุด</label>
                            <p class="text-sm">{{ nl2br($kytfrm[0]->PROTECT) }}</p>
                        </div>
                    </div>
                    <div class="flex max-sm:flex-col gap-2 mb-5">
                        <div class="w-full space-y-1.5">
                            <label for="precis"
                                class="font-sans antialiased text-sm text-stone-800 dark:text-black font-semibold">คำย่อ
                                KYT</label>
                            <p class="text-sm">{{ nl2br($kytfrm[0]->PRECIS) }}</p>
                        </div>
                    </div>
                    <div id="remark-text" class="hidden max-sm:flex-col gap-2 mb-5 ">
                        <div class="w-full space-y-1.5">
                            <label for="precis"
                                class="font-sans antialiased text-sm text-stone-800 dark:text-black font-semibold">Remark</label>
                            <textarea rows="4" id="remark" name="remark" placeholder=""
                                class="w-full aria-disabled:cursor-not-allowed outline-none focus:outline-none text-stone-800 dark:text-black placeholder:text-stone-600/60 ring-transparent border border-stone-200 transition-all ease-in disabled:opacity-50 disabled:pointer-events-none select-none text-sm py-2 px-2.5 ring shadow-sm bg-white rounded-lg duration-100 hover:border-stone-300 hover:ring-none focus:border-stone-400 focus:ring-none peer req"></textarea>
                        </div>
                    </div>
                    @if ($webSafety != 1)
                        <div id="actions-Form" class="hidden max-sm:flex-col gap-3">
                            <button type="button" class="btn btn-primary mg-s-12" name="btnAction"
                                value="approve">Approve</button>
                            <button type="button" class="btn btn-neutral mg-s-12" name="btnAction"
                                value="reject">Reject</button>
                        </div>
                    @endif
                </div>
            </form>
        </div>
        <div class="card-footer m-8">
            <div class="formno hidden" NFRMNO="{{ $kytfrm[0]->NFRMNO }}" VORGNO="{{ $kytfrm[0]->VORGNO }}"
                CYEAR="{{ $kytfrm[0]->CYEAR }}" CYEAR2="{{ $kytfrm[0]->CYEAR2 }}"
                NRUNNO="{{ $kytfrm[0]->NRUNNO }}"></div>
            <div class="user-data hidden" empno="{{ $empno }}" mode="{{ $mode }}" cextData="{{ $cextData }}"></div>
            <div id="flow">
                <div class="h-32 w-full"></div>
            </div>

        </div>
    </div>
</div>
{{-- modal save --}}
@include('layout/modal_save')
@endsection

@section('scripts')
<script
    src="{{ $GLOBALS['script'] }}kytview.bundle.js?ver={{ date('Ymdhis') }}">
</script>
@endsection