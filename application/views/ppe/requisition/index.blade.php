@extends('layout/template')
@section('styles')

@endsection
@section('contents')
<div class="flex flex-col md:flex-row gap-5">
    <!-- สินค้า -->
    <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-3 gap-4 w-full md:w-3/5 lg:w-3/4" id="card-container">
        <!-- การ์ดสินค้าโหลดด้วย AJAX -->
    </div>

    <!-- ตะกร้าสินค้า -->
    <div class="w-full md:w-2/5 lg:w-1/4 bg-white p-4 shadow-md rounded-lg">
        <h2 class="text-lg font-bold">รายการ</h2>
        <ul id="cart-list" class="mb-4"></ul>
        <button id="checkout" class="w-full bg-orange-500 text-white p-2 rounded-lg">สรุปการเบิก</button>
    </div>
</div>



@endsection
@section('scripts')
<script src="{{ $GLOBALS['script'] }}pperequest.bundle.js?ver={{ date('Ymdhis') }}"></script>
@endsection