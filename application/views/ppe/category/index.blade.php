@extends('layout/template')

@section('styles')
    <style>
        .img {
            cursor: pointer;
        }
    </style>
@endsection

@section('contents')
    <div class="mx-5">
        <table class="table w-full" id="table"></table>
    </div>
    <!-- Modal -->
    <!-- <input type="checkbox" id="my-modal" class="modal-toggle" /> -->
    <dialog class="modal" id="my_modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg">Add New Category</h3>
            <form action="{{base_url('ppe/admin/category/insert_category')}}" id="add_category_form" class="py-4" method="POST">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Category Name</span>
                    </label>
                    <input type="text" name="name" class="input input-bordered" required />
                    <input type="hidden" name="seq" id="catseq">
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Category Description</span>
                    </label>
                    <input type="text" name="description" class="input input-bordered" required />
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Unit</span>
                    </label>
                    <input type="text" name="unit" class="input input-bordered" required />
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Meter</span>
                    </label>
                    <input type="text" name="meter" class="input input-bordered" required />
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Upload File</span>
                    </label>
                    <input type="file" name="file" class="file-input file-input-bordered" />
                </div>
                <div class="modal-action">
                    <!-- if there is a button in form, it will close the modal -->
                    <button class="btn" id="close_btn" type="button" onclick="my_modal.close();">Close</button>
                    <!-- <label for="my-modal" class="btn">Cancel</label> -->
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </dialog>

    <!-- Edit Modal -->
    <input type="checkbox" id="edit-modal" class="modal-toggle" />
    <div class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg">Edit Category</h3>
            <form action="{{base_url('ppe/admin/category/update_category')}}" id="edit_category_form" class="py-4" method="POST" enctype="multipart/form-data">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Category Name</span>
                    </label>
                    <input type="text" name="name" id="input_name_edit" class="input input-bordered" required />
                    <input type="hidden" name="id" id="input_id_edit" />
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Category Description</span>
                    </label>
                    <input type="text" name="description" id="input_description_edit" class="input input-bordered" required />
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Unit</span>
                    </label>
                    <input type="text" name="unit" id="input_unit_edit" class="input input-bordered" required />
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Upload File</span>
                    </label>
                    <input type="file" name="file" class="file-input file-input-bordered" />
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Remark</span>
                    </label>
                    <textarea id="remark" name="remark" class="textarea textarea-bordered" placeholder="Remark"></textarea>
                </div>
                <div class="form-control hidden" id="div_preview">
                    <label class="label">
                        <span class="label-text">Preview</span>
                    </label>
                    <img id="preview_image" src="#" alt="Preview Image" style="width: 100px; height: auto;" />
                </div>
                <div class="modal-action">
                    <label for="edit-modal" class="btn">Cancel</label>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ $GLOBALS['script'] }}ppecategory.bundle.js?ver={{ date('Ymdhis') }}"></script>
@endsection