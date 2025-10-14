@extends('layout/template')
@section('contents')
    <div class="sticky top-16 z-[100] mx-5 mb-5 bg-white">
        <div class="flex">
            <div class="stats shadow">
                <div class="stat ">
                    <div class="stat-title">FYEAR</div>
                    <div class="stat-value text-primary">
                            <h1 id="fyear" class="text-4xl font-bold text-primary z-10">{{$fyear}}</h1>
                    </div>
                    <div class="stat-desc z-10 join">
                        <button type="button" class="btn btn-xs btn-neutral join-item md:w-1/2 text-base" id="previousFY"><</button>
                        <button type="button" class="btn btn-xs btn-neutral join-item md:w-1/2 text-base" id="nextFY">></button>
                    </div>
                </div>
                <div class="stat monthSelected hidden">
                    <div class="stat-figure text-primary">
                        <i class="icofont-calendar text-2xl"></i>
                    </div>
                    <div class="stat-title">Month</div>
                    <div class="stat-value text-primary">
                        <h1 id="monthSelected" class="text-4xl font-bold text-primary z-10"></h1>
                    </div>
                    <div class="stat-desc">เดือนที่เลือก</div>
                </div>
                <div class="stat deptSelected hidden">
                    <div class="stat-figure text-primary">
                        <i class="icofont-users-social text-2xl"></i>
                    </div>
                    <div class="stat-title">Department</div>
                    <div class="stat-value text-primary">
                        <h1 id="deptSelected" class="text-4xl font-bold text-primary z-10"></h1>
                    </div>
                    <div class="stat-desc">ส่วนที่เลือก</div>
                </div>
                <div class="stat secSelected hidden">
                    <div class="stat-figure text-primary">
                        <i class="icofont-users text-2xl"></i>
                    </div>
                    <div class="stat-title">Section</div>
                    <div class="stat-value text-primary">
                        <h1 id="secSelected" class="text-4xl font-bold text-primary z-10"></h1>
                    </div>
                    <div class="stat-desc">แผนกที่เลือก</div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col flex-wrap md:flex-row mx-5 gap-5 mb-5">
        <div class="flex flex-col w-full ">
            {{-- <div class="flex flex-col  sm:flex-wrap md:flex-row gap-3 mb-5">
                <div class="stats shadow ">
                    <div class="stat">
                        <div class="stat-title">FYEAR</div>
                        <div class="stat-value text-primary">
                                <h1 id="fyear" class="text-4xl font-bold text-primary z-10">{{$fyear}}</h1>
                        </div>
                        <div class="stat-desc z-10 join">
                            <button type="button" class="btn btn-xs btn-neutral join-item md:w-1/2 text-base" id="previousFY"><</button>
                            <button type="button" class="btn btn-xs btn-neutral join-item md:w-1/2 text-base" id="nextFY">></button>
                        </div>
                    </div>
                    <div class="stat monthSelected hidden">
                        <div class="stat-figure text-primary">
                            <i class="icofont-calendar text-2xl"></i>
                        </div>
                        <div class="stat-title">Month</div>
                        <div class="stat-value text-primary">
                            <h1 id="monthSelected" class="text-4xl font-bold text-primary z-10"></h1>
                        </div>
                        <div class="stat-desc">เดือนที่เลือก</div>
                    </div>
                    <div class="stat deptSelected hidden">
                        <div class="stat-figure text-primary">
                            <i class="icofont-users-social text-2xl"></i>
                        </div>
                        <div class="stat-title">Department</div>
                        <div class="stat-value text-primary">
                            <h1 id="deptSelected" class="text-4xl font-bold text-primary z-10"></h1>
                        </div>
                        <div class="stat-desc">ส่วนที่เลือก</div>
                    </div>
                    <div class="stat secSelected hidden">
                        <div class="stat-figure text-primary">
                            <i class="icofont-users text-2xl"></i>
                        </div>
                        <div class="stat-title">Section</div>
                        <div class="stat-value text-primary">
                            <h1 id="secSelected" class="text-4xl font-bold text-primary z-10"></h1>
                        </div>
                        <div class="stat-desc">แผนกที่เลือก</div>
                    </div>
                </div>
            </div> --}}
            <div class="card bg-base-100 flex-1 custom-shadow-xl  min-h-96">
                <div class="card-body">
                    <h2 class="card-title">เปรียบเทียบ</h2>
                    <div id="chart"></div>
                </div>
            </div>
        </div>
        <div class="card bg-base-100 flex-1 custom-shadow-xl w-full hidden deptChart">
            <div class="card-body ">
                <h2 class="card-title dept">Department</h2>
                
                <div class=" flex flex-col gap-3 md:flex-row justify-center md:justify-around">
                    <div class="">
                        <span>Class A</span>
                        <div id="chartA" class="md:w-full"></div>
                    </div>
                    <div class="">
                        <span>Class B</span>
                        <div id="chartB" class="md:w-full"></div>
                    </div>
                </div>
                <div class="min-h-64 md:min-h-96 overflow-scroll ">
                    <div id="deptChart" class="w-[500px] md:w-full"></div>
                </div>
                <div class="min-h-64 md:min-h-96 overflow-scroll hidden secChart ">
                    <div id="secChart" class=""></div>
                </div>
            </div>
        </div>
        <div class="flex flex-col gap-5 w-full">
            {{-- <div class="card bg-base-100 flex-1 custom-shadow-xl w-full hidden secChart">
                <div class="card-body ">
                    <h2 class="card-title dept">Department</h2>
                    <div class="min-h-64 md:min-h-96 overflow-scroll ">
                        <div id="secChart" class="w-full"></div>
                    </div>
                </div>
            </div> --}}
            <div class="card bg-base-100 flex-1 custom-shadow-xl w-full hidden secClassDetail">
                <div class="card-body ">
                    <h2 class="card-title secClass">Section Class</h2>
                    <div class="flex flex-col md:flex-row gap-5 justify-center overflow-scroll">
                        <div class="h-64 md:h-96 w-[500px] md:w-[48%]  ">
                            <span>Class A</span>
                            <div id="chtCatClaA" class="w-full"></div>
                        </div>
                        <div class="h-64 md:h-96 w-[500px] md:w-[48%]   ">
                            <span>Class B</span>
                            <div id="chtCatClaB" class="w-full"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card bg-base-100 flex-1 custom-shadow-xl w-full hidden secDetail">
                <div class="card-body ">
                    <h2 class="card-title sec">Section</h2>
                    <div class="flex flex-col gap-5">
                        <div class="min-h-64 md:min-h-96 overflow-scroll ">
                            <div id="yearSecChart" class="w-full"></div>
                        </div>
                        <span>หมวดหมู่รวมทั้ง 2 คลาส</span>
                        <div class="min-h-64 md:min-h-96  overflow-scroll ">
                            <div id="chtCatMon" class="w-[500px] md:w-full"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ $GLOBALS['script'] }}patrolReport.bundle.js?ver={{ date('Ymdhis') }}"></script>
@endsection
