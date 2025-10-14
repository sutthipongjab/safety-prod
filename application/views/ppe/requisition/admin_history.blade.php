@extends('layout.template')
@section('styles')

@endsection
@section('contents')

    <link rel="stylesheet" href="{{ $GLOBALS['cdn'] }}flowbite/flowbite.min.css">
    <script src="{{ $GLOBALS['cdn'] }}flowbite/flowbite.min.js"></script>
    <div class="container mx-auto p-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
            <div id="date-range-picker" date-rangepicker datepicker-format="dd/mm/yyyy" class="flex items-center">
                <div class="relative">
                    <input id="datepicker-range-start" name="start" type="text" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5" placeholder="Select date start">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                        </svg>
                    </div>
                </div>
                <span class="mx-4 text-gray-500">to</span>
                <div class="relative">
                    <input id="datepicker-range-end" name="end" type="text" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5" placeholder="Select date end">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                        </svg>
                    </div>
                </div>
                <button class="btn bg-blue-600 hover:bg-blue-500 ml-4 text-white" id="Search-date">Search <i class="icofont-search"></i></button>
                <button class="btn btn-warning ml-2" id="reset-date">Reset</button>
            </div>
            <div class="flex items-center gap-3">
                <label for="export-month" class="text-sm font-medium text-gray-700">เลือกเดือน:</label>
                <input type="month" id="export-month" name="export-month" class="border rounded-lg p-2 text-sm text-gray-700 focus:ring-blue-500 focus:border-blue-500">
                <button class="btn btn-success" id="export_excel">Export to Excel</button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="table w-full border rounded-lg shadow-md" id="table_history">
            </table>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ $GLOBALS['script'] }}ppeadmin.bundle.js?ver={{ date('Ymdhis') }}"></script>
@endsection