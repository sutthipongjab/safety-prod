@extends('layout/template')
@section('contents')
<div class="flex mx-5">
    <div class="card w-full  shadow-2xl bg-base-100">
        <div class="card-body">
            <form action="#" id="kyt-form" enctype="multipart/form-data">
                <div class="overflow-x-auto">
                    <div class="hidden form-info" NFRMNO="{{ $formInfo[0]->NNO }}"
                        VORGNO="{{ $formInfo[0]->VORGNO }}" CYEAR="{{ $formInfo[0]->CYEAR }}"
                        userno="{{ $_SESSION['user']->SEMPNO }}">
                    </div>
                    <div class="flex max-sm:flex-col gap-2 mb-5">
                        <label class="form-control w-full max-w-xs">
                            <div class="label">
                                <span class="label-text">รหัสพนักงาน ผู้พบความเสี่ยง</span>
                                <!--span class="label-text-alt">Top Right label</span-->
                            </div>
                            <input type="text" placeholder="กรอกรหัสพนังาน" name="empno" id="empno"
                                class="input input-bordered w-full max-w-xs req" />
                            <!--div class="label">
                        <span class="label-text-alt">Bottom Left label</span>
                        <span class="label-text-alt">Bottom Right label</span>
                    </!--div -->
                        </label>
                        <label class="form-control w-full max-w-xs">
                            <div class="label">
                                <span class="label-text">รหัสพนักงาน Leader</span>
                                {{-- <span class="label-text">รหัสพนักงาน หัวหน้ากลุ่ม KYT</span> --}}
                                <!--span class="label-text-alt">Top Right label</span-->
                            </div>
                            <input type="text" placeholder="กรอกรหัสพนังาน" name="empnog"
                                class="input input-bordered w-full max-w-xs req" />
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
                            <input type="text" placeholder="กรอกชื่อกลุ่ม" name="group"
                                class="input input-bordered w-full max-w-xs req" />
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
                            <select class="w-full select select-bordered req" name="sec" id="sec">
                                <option value=""></option>
                                @foreach($section as $key => $s)
                                    <option value="{{ $s->SSECCODE }}" seccode="{{ $s->SSECCODE }}">{{ $s->SSEC }}
                                    </option>
                                @endforeach
                            </select>
                        </label>
                        <label class="form-control w-full max-w-xs grprisk">
                            <div class="label">
                                <span class="label-text">หมวดหมู่ความเสี่ยงที่พบ</span>
                            </div>
                            <select class="w-full select select-bordered req " name="risk" id="risk">
                                <option value=""></option>
                                @foreach($grprisk as $key => $gr)
                                    <option value="{{ $gr->ITEMS_ID }}" riskcode="{{ $gr->ITEMS_ID }}">
                                        {{ $gr->ITEMS_NAME }}
                                    </option>
                                @endforeach
                            </select>
                        </label>
                    </div>

                    <div class="flex max-sm:flex-col gap-2 mb-5">

                        <div class="w-full space-y-1.5">
                            <label for="dtrisk"
                                class="font-sans antialiased text-sm text-stone-800 dark:text-black font-semibold">รายละเอียดความเสี่ยงที่พบ</label>
                            <textarea rows="4" id="dtrisk" name="dtrisk"
                                placeholder="(ตัวอย่าง : พับแขนเสื้อเวลาทำงาน. ไม่สวมแว่นตาเมื่อใช้ปืนยิงตะปู)"
                                class="w-full aria-disabled:cursor-not-allowed outline-none focus:outline-none text-stone-800 dark:text-black placeholder:text-stone-600/60 ring-transparent border border-stone-200 transition-all ease-in disabled:opacity-50 disabled:pointer-events-none select-none text-sm py-2 px-2.5 ring shadow-sm bg-white rounded-lg duration-100 hover:border-stone-300 hover:ring-none focus:border-stone-400 focus:ring-none peer req"></textarea>
                        </div>
                    </div>
                    <div class="flex max-sm:flex-col gap-2 mb-5">
                        <div class="w-full space-y-1.5">
                            <label for="protect"
                                class="font-sans antialiased text-sm text-stone-800 dark:text-black font-semibold">มาตรการป้องกันที่ดีที่สุด</label>
                            <textarea rows="4" id="protect" name="protect" placeholder=""
                                class="w-full aria-disabled:cursor-not-allowed outline-none focus:outline-none text-stone-800 dark:text-black placeholder:text-stone-600/60 ring-transparent border border-stone-200 transition-all ease-in disabled:opacity-50 disabled:pointer-events-none select-none text-sm py-2 px-2.5 ring shadow-sm bg-white rounded-lg duration-100 hover:border-stone-300 hover:ring-none focus:border-stone-400 focus:ring-none peer req"></textarea>
                        </div>
                    </div>
                    <div class="flex max-sm:flex-col gap-2 mb-5">
                        <div class="w-full space-y-1.5">
                            <label for="precis"
                                class="font-sans antialiased text-sm text-stone-800 dark:text-black font-semibold">คำย่อ
                                KYT</label>
                            <textarea rows="4" id="precis" name="precis" placeholder=""
                                class="w-full aria-disabled:cursor-not-allowed outline-none focus:outline-none text-stone-800 dark:text-black placeholder:text-stone-600/60 ring-transparent border border-stone-200 transition-all ease-in disabled:opacity-50 disabled:pointer-events-none select-none text-sm py-2 px-2.5 ring shadow-sm bg-white rounded-lg duration-100 hover:border-stone-300 hover:ring-none focus:border-stone-400 focus:ring-none peer req"></textarea>
                        </div>
                    </div>
                    <div class="flex justify-left mt-4 gap-1">
                        <div for="" class="btn btn-primary mg-s-12" id="req-kyt">
                            Request
                        </div>
                        <div for="" class="btn btn-neutral mg-s-12" id="cancel-kyt">
                            Cancel
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- modal save --}}
@include('layout/modal_save')
@endsection

@section('scripts')
<script src="{{ $GLOBALS['script'] }}kyt.bundle.js?ver={{ date('Ymdhis') }}">
</script>
@endsection