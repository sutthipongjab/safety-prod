@extends('layout/template')
@section('contents')
    {{-- <div class="flex mx-5">
        <div class="card w-full  shadow-2xl bg-base-100">
            <div class="card-body">
                <div class="overflow-x-auto">
                    <table class="table" id="table_Follow"></table>
                </div>
            </div>
        </div>
    </div> --}}
    <div class="flex flex-col flex-wrap md:flex-row mx-5 gap-5">
        <div class="flex flex-col w-full xl:w-[50vw] ">
            <div class="flex flex-col justify-between sm:flex-wrap md:flex-row gap-3 mb-5">
                <div class="stats shadow">
                    <div class="stat">
                      {{-- <div class="stat-figure text-primary">
                      </div> --}}
                      <div class="stat-title">FYEAR</div>
                      <div class="stat-value text-primary">
                            <h1 id="fyear" class="text-4xl font-bold text-primary z-10">{{$fyear}}</h1>
                      </div>
                      <div class="stat-desc z-10 join">
                            <button type="button" class="btn btn-xs btn-neutral join-item md:w-1/2 text-base" id="previousFY"><</button>
                            <button type="button" class="btn btn-xs btn-neutral join-item md:w-1/2 text-base" id="nextFY">></button>
                    </div>
                    </div>
                </div>
                <div class="stats shadow ">
                    <div class="stat">
                        <div class="stat-title">Export</div>
                        <div class="stat-value text-primary hover:text-black">
                            <i class="icofont-file-excel text-5xl cursor-pointer" id="exportExcel"></i>
                        </div>
                    </div>
                </div>
                {{-- <div class="card bg-base-100  custom-shadow-xl overflow-hidden h-40">
                    <div class="card-body justify-between">
                        <h5 class="card-title text-base z-10">FYEAR</h5>
                        <h1 id="fyear" class="text-4xl font-bold text-primary z-10">{{$fyear}}</h1>
                        <div class="flex z-10 join">
                            <button type="button" class="btn btn-xs btn-neutral join-item w-1/2 text-base" id="previousFY"><</button>
                            <button type="button" class="btn btn-xs btn-neutral join-item w-1/2 text-base" id="nextFY">></button>
                        </div>
                        <span class="icofont-calendar custom-bg-img "></span>
                    </div>
                </div> --}}
                <div class="stats shadow grow">
                    <div class="stat">
                        <div class="stat-figure text-neutral">
                            <i class="icofont-warning text-4xl"></i>
                        </div>
                        <div class="stat-title">พบความเสี่ยงทั้งหมด</div>
                        <div class="stat-value text-primary">
                            <p id="ctotal" class="z-10 text-4xl font-bold text-primary"></p>
                        </div>
                    </div>
                </div>
                {{-- <div class="card bg-base-100 flex-1 custom-shadow-xl overflow-hidden h-40">
                    <div class="card-body justify-between">
                        <h2 class="card-title text-base z-10">พบความเสี่ยงทั้งหมด</h2>
                        <div class="flex flex-row justify-between items-center">
                            <p id="ctotal" class="z-10 text-4xl font-bold text-primary"></p>
                            <i class="icofont-warning text-4xl"></i>
                        </div>
                    </div>
                </div> --}}
                <div class="stats shadow grow">
                    <div class="stat">
                        <div class="stat-figure text-neutral">
                            <i class="icofont-tasks text-4xl"></i>
                        </div>
                        <div class="stat-title">รอการ Approve</div>
                        <div class="stat-value text-primary">
                            <p id="cpending" class="z-10 text-4xl font-bold text-primary"></p>
                        </div>
                    </div>
                </div>
                {{-- <div class="card bg-base-100 flex-1 custom-shadow-xl overflow-hidden h-40">
                    <div class="card-body justify-between">
                        <div class="card-title text-base z-10">รอการ Approve</div>
                        <div class="flex flex-row justify-between items-center">
                            <p id="cpending" class="z-10 text-4xl font-bold text-primary"></p>
                            <i class="icofont-tasks text-4xl"></i>
                        </div>
                    </div>
                </div> --}}
            </div>
            <div class="card bg-base-100 flex-1 custom-shadow-xl  min-h-96">
                <div class="card-body">
                    <h2 class="card-title">เปรียบเทียบ</h2>
                    <div id="chart"></div>
                </div>
            </div>
        </div>
        <div class="card bg-base-100 flex-1 custom-shadow-xl  ">
            <div class="card-body">
                <h2 class="card-title">Top 10</h2>
                <div class="overflow-x-auto">
                    <table class="table" id="table_topten"></table>
                </div>
            </div>
        </div>
    </div>
    <div class="flex flex-col  mx-5 mt-5">
        {{-- monthly --}}
        <div class="flex flex-col md:flex-row md:flex-wrap gap-3 mb-5">
            <div class="card bg-base-100 flex-1 custom-shadow-xl max-h-80">
                <div class="card-body">
                    <h2 class="card-title">จำนวนที่พบความเสี่ยง (วัน)</h2>
                    <div id="monthlyChart"></div>
                </div>
            </div>
            <div class="card bg-base-100 flex-1 custom-shadow-xl  max-h-80">
                <div class="card-body">
                    <h2 class="card-title">แผนกที่พบความเสี่ยง (วัน)</h2>
                    <div id="monSecChart"></div>
                </div>
            </div>
            <div class="card bg-base-100 flex-1 custom-shadow-xl max-h-80 ">
                <div class="card-body">
                    <h2 class="card-title">หมวดที่พบความเสี่ยง (วัน)</h2>
                    <div id="monCatChart"></div>
                </div>
            </div>
        </div>
        {{-- yearly --}}
        <div class="flex flex-col md:flex-row md:flex-wrap gap-3 mb-5 ">
            <div class="card bg-base-100 flex-1 custom-shadow-xl max-h-80 ">
                <div class="card-body">
                    <h2 class="card-title">จำนวนที่พบความเสี่ยง (เดือน)</h2>
                    <div id="yearlyChart"></div>
                </div>
            </div>
            <div class="card bg-base-100 flex-1 custom-shadow-xl max-h-80 ">
                <div class="card-body">
                    <h2 class="card-title">แผนกที่พบความเสี่ยง (เดือน)</h2>
                    <div id="yearSecChart"></div>
                </div>
            </div>
            <div class="card bg-base-100 flex-1 custom-shadow-xl max-h-80 ">
                <div class="card-body">
                    <h2 class="card-title">หมวดที่พบความเสี่ยง (เดือน)</h2>
                    <div id="yearCatChart"></div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ $GLOBALS['script'] }}kytReport.bundle.js?ver={{ date('Ymdhis') }}"></script>
@endsection
