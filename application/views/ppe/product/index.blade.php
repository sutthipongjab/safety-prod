@extends('layout/template')
@section('contents')
    <div id="loading-screen" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 border-4 border-white border-t-transparent rounded-full animate-spin"></div>
            <p class="text-white text-lg">กำลังโหลดข้อมูล...</p>
        </div>
    </div>

    <div class="mx-5">
        <div class="flex flex-wrap gap-3 mb-4" id="btn-group">

        </div>
        <table class="table w-full" id="table">
            <thead class="bg-gray-50 text-center">
                <!-- data from ajax -->
            </thead>

            <!-- data from ajax -->
        </table>
    </div>
    <dialog id="my_modal_1" class="modal">
        <div class="modal-box">
            <form action="{{base_url('ppe/admin/category/insert_category')}}" id="add_uniform" class="py-4" method="POST">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Category Name</span>
                    </label>
                    <select id="category" name="category" class="select select-bordered w-full" required>

                        <!-- Options will be populated dynamically -->
                    </select>
                    <input type="hidden" name="seq" id="seq">
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Size / Model</span>
                    </label>
                    <input type="text" name="size" id="size" class="input input-bordered" required />
                </div>
                <!-- <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">width</span>
                                    </label>
                                    <input type="text" name="width" class="input input-bordered" />
                                </div>
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">height</span>
                                    </label>
                                    <input type="text" name="height" class="input input-bordered" />
                                </div> -->
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text">price</span>
                    </label>
                    <input type="text" name="price" class="input input-bordered" required />
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Quantity</span>
                    </label>
                    <input type="number" name="quantity" class="input input-bordered" required />
                </div>

                <div role="alert" class="alert alert-error hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>มีข้อมูลนี้อยู่แล้ว!</span>
                </div>


                <div class="modal-action">
                    <!-- if there is a button in form, it will close the modal -->
                    <button class="btn" id="close_btn" type="button" onclick="my_modal_1.close();">Close</button>
                    <!-- <label for="my-modal" class="btn">Cancel</label> -->
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </dialog>
    <dialog id="edit_qty_modal" class="modal">
        <div class="modal-box">
            <form action="{{base_url('ppe/admin/product/update_qty')}}" id="edit_qty_form" class="py-4" method="POST">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Category</span>
                    </label>
                    <input type="text" name="category" id="category_edit" class="input input-bordered bg-gray-100" readonly />
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Model</span>
                    </label>
                    <input type="text" name="model" id="model_edit" class="input input-bordered bg-gray-100" readonly />
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Size</span>
                    </label>
                    <input type="text" name="size" id="size_edit" class="input input-bordered bg-gray-100" readonly />
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Quantity</span>
                    </label>
                    <input type="number" name="quantity" id="quantity_edit" class="input input-bordered" required />
                </div>

                <div role="alert" class="alert alert-error hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>มีข้อมูลนี้อยู่แล้ว!</span>
                </div>

                <div class="modal-action">
                    <button class="btn" id="close_edit_qty_btn" type="button" onclick="edit_qty_modal.close();">Close</button>
                    <button type="submit" class="btn btn-primary" id="btn-save-edit">Save</button>
                </div>
            </form>
        </div>
    </dialog>


    <dialog id="stock_modal" class="modal">
        <div class="modal-box">
            <h2 class="text-xl font-bold mb-4">รายการรับเข้า</h2>
            <form action="{{base_url('ppe/admin/product/add_stock')}}" id="add_stock_form" class="py-4" method="POST">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Category</span>
                    </label>
                    <input type="text" name="category" id="category_add_stock" class="input input-bordered bg-gray-100" readonly />
                    <input type="hidden" name="id" id="id_add_stock" />
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Model</span>
                    </label>
                    <input type="text" name="model" id="model_add_stock" class="input input-bordered bg-gray-100" readonly />
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Size</span>
                    </label>
                    <input type="text" name="size" id="size_add_stock" class="input input-bordered bg-gray-100" readonly />
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Quantity to Add</span>
                    </label>
                    <input type="number" name="quantity" id="quantity_add_stock" class="input input-bordered" value="1" required />
                    <input type="hidden" id="remain_add_stock">
                </div>
                <div class="form-control">
                    <label for="" class="label">
                        <span class="label-text">Remark</span>
                    </label>
                    <textarea name="remark" id="remark_add_stock" class="textarea textarea-bordered" required></textarea>
                </div>

                <div role="alert" class="alert alert-error hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>มีข้อมูลนี้อยู่แล้ว!</span>
                </div>

                <div class="modal-action">
                    <button class="btn" id="close_add_stock_btn" type="button" onclick="stock_modal.close();">Close</button>
                    <button type="submit" class="btn btn-primary" id="btn-save-add-stock">Save</button>
                </div>
            </form>
        </div>
    </dialog>

    <dialog id="stock_modal_out" class="modal">
        <div class="modal-box">
            <h2 class="text-xl font-bold mb-4">รายการจ่ายออก</h2>
            <form action="{{base_url('ppe/admin/product/remove_stock')}}" id="remove_stock_form" class="py-4" method="POST">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Category</span>
                    </label>
                    <input type="text" name="category" id="category_remove_stock" class="input input-bordered bg-gray-100" readonly />
                    <input type="hidden" name="id" id="id_remove_stock" />
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Model</span>
                    </label>
                    <input type="text" name="model" id="model_remove_stock" class="input input-bordered bg-gray-100" readonly />
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Size</span>
                    </label>
                    <input type="text" name="size" id="size_remove_stock" class="input input-bordered bg-gray-100" readonly />
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Quantity to Remove</span>
                    </label>
                    <input type="number" name="quantity" id="quantity_remove_stock" class="input input-bordered" value="1" required />
                    <input type="hidden" id="remain_remove_stock">
                </div>
                <div class="form-control">
                    <label for="" class="label">
                        <span class="label-text">Remark</span>
                    </label>
                    <textarea name="remark" id="remark_remove_stock" class="textarea textarea-bordered" required></textarea>
                </div>

                <div role="alert" class="alert alert-error hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>มีข้อมูลนี้อยู่แล้ว!</span>
                </div>

                <div class="modal-action">
                    <button class="btn" id="close_remove_stock_btn" type="button" onclick="stock_modal_out.close();">Close</button>
                    <button type="submit" class="btn btn-primary" id="btn-save-remove-stock">Save</button>
                </div>
            </form>
        </div>
    </dialog>
@endsection

@section('scripts')
    <script src="{{ $GLOBALS['script'] }}ppeproduct.bundle.js?ver={{ date('Ymdhis') }}"></script>
@endsection