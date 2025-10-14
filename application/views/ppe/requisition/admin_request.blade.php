@extends('layout.template')
@section('styles')

@endsection
@section('contents')
<div class="w-full container mx-auto mt-8 p-2 rounded-lg">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-semibold">PPE Requisition List</h2>
    </div>
    <input type="hidden" id="emp_login" value="{{$_SESSION['user']->SEMPNO}}">
    <div class="bg-gray-100 shadow-md rounded-md my-6 p-4 overflow-x-auto">
        <div class="overflow-x-auto">
            <table class="table min-w-full border border-gray-300" id="table">
                <thead class="bg-gray-50">
                    <tr class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <th class="px-4 py-2 border">No.</th>
                        <th class="px-4 py-2 border">Req No.</th>
                        <th class="px-4 py-2 border">Req Date</th>
                        <th class="px-4 py-2 border">Emp Code</th>
                        <th class="px-4 py-2 border">Requester</th>
                        <th class="px-4 py-2 border">Category</th>
                        <th class="px-4 py-2 border">Model</th>
                        <th class="px-4 py-2 border">Req Type</th>
                        <th class="px-4 py-2 border">Qty Req</th>
                        <th class="px-4 py-2 border">Stock</th>
                        <th class="px-4 py-2 border">Status</th>
                        <th class="px-4 py-2 border">Action</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-gray-700">
                    
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
@section('scripts')
    <script src="{{ $GLOBALS['script'] }}ppeadmin.bundle.js?ver={{ date('Ymdhis') }}"></script>
@endsection