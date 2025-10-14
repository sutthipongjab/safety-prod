@extends('layout/template')

@section('contents')
<div class="card mx-5">
    <div class="card-title">
        Add New Product
    </div>
    <div class="card-body">
        <form action="{{base_url('ppe/admin/products/insertProduct')}}" method="POST">
            <!-- <div class="form-group mb-4">
                <label for="item" class="block text-gray-700 text-sm font-bold mb-2">รายการ</label>
                <input type="text" class="input input-bordered w-full" id="item" name="item">
            </div> -->
            <div class="form-group mb-4">
                <label for="category" class="block text-gray-700 text-sm font-bold mb-2">หมวดหมู่</label>
                <select class="input input-bordered w-full" id="category" name="category">

                </select>
            </div>          
            <div class="form-group mb-4">
                <label for="size" class="block text-gray-700 text-sm font-bold mb-2">ขนาด</label>
                <input type="text" class="input input-bordered w-full" id="size" name="size">
            </div>
            <div class="form-group mb-4">
                <label for="width" class="block text-gray-700 text-sm font-bold mb-2">ความกว้าง</label>
                <input type="text" class="input input-bordered w-full" id="width" name="size">
            </div>
            <div class="form-group mb-4">
                <label for="height" class="block text-gray-700 text-sm font-bold mb-2">ความสูง</label>
                <input type="text" class="input input-bordered w-full" id="height" name="size">
            </div>
            <div class="form-group mb-4">
                <label for="price" class="block text-gray-700 text-sm font-bold mb-2">ราคา</label>
                <input type="text" class="input input-bordered w-full" id="price" name="size">
            </div>
            <!-- <div class="form-group mb-4">
                <label for="unit" class="block text-gray-700 text-sm font-bold mb-2">หน่วย</label>
                <input type="text" class="input input-bordered w-full" id="unit" name="unit">
            </div> -->
            <button type="submit" class="btn btn-primary">Add Product</button>
        </form>
    </div>
</div>
@endsection
@section('scripts')
<script src="{{ $GLOBALS['script'] }}ppetype.bundle.js?ver={{ date('Ymdhis') }}"></script>
@endsection