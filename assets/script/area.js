import $            from "jquery";
import { checkAuthen, clickTableGroup, domScroll, hidepreload, host, initJoin, removeClassError, RequiredElement, showMessage, showpreload, tableGroup, tableOption }      from "./utils.js";
import DataTable    from "datatables.net-dt";
import select2      from "select2";
import { Fancybox } from "@fancyapps/ui";
import "datatables.net-responsive";
import "datatables.net-responsive-dt";
// import "datatables.net-dt/css/dataTables.dataTables.min.css";
import "datatables.net-responsive-dt/css/responsive.dataTables.min.css";
import "select2/dist/css/select2.min.css";
import "@fancyapps/ui/dist/fancybox/fancybox.css";
import { createColumnFilters } from "./filter.js";


var areatable, empno;
var checkEmp = false;

$(document).ready(async function () {
    showpreload();
    const area = await getArea();
    areatable = await createTable(area);
    createColumnFilters(areatable, '1-4');
    $('#AREA_TYPE').select2({placeholder: "   Select Type.",allowClear: true, minimumResultsForSearch: Infinity});
    $('#AREA_CATEGORY').select2({placeholder: "   Select Category.",allowClear: true, minimumResultsForSearch: Infinity});
    $('#AREA_MANAGERCODE').select2({placeholder: "   เลือกแผนกผู้รับผิดชอบ",allowClear: true});
    // $('#AREA_SEC').select2({placeholder: "   Select Section.",allowClear: true});
    hidepreload();
});

$(document).on('change','#AREA_MANAGERCODE', function(){
    console.log($(this).find('option:selected').attr('data-name'));
    
    $('#AREA_MANAGER').val($(this).find('option:selected').attr('data-name'));
});

/**
 * Add area
 */
$(document).on("click", "#add-area", async function (e) {
    $('#headerArea').text('เพิ่มพื้นที่');
    $('#deleteImage').prop('checked',false).trigger('change').closest('div').addClass('hidden');
    $('#AREA_IMAGE').closest('div').removeClass('hidden');
    $("#area-form").trigger("reset");
    // $("#AREA_SEC").val(null).trigger('change');
    $("#AREA_TYPE").val(null).trigger('change');
    $("#AREA_CATEGORY").val(null).trigger('change');
    $("#AREA_MANAGERCODE").val(null).trigger('change');
    removeClassError($('#AREA_TYPE'));
    removeClassError($('#AREA_CATEGORY'));
    removeClassError($('#AREA_MANAGERCODE'));
});

$(document).on('keydown', '#AREA_EMPNO', function(e) {
    if (e.key === 'Enter') {
        $(this).trigger('blur'); 
    }
});
/**
 * Get owner data
 */
$(document).on('blur', '#AREA_EMPNO', async function (e) {
    if (e.type === 'focusout') {
        const val = $(this).val().trim();
        if(!val){
            resetOwner();
            return;
        } else if (empno !== val) {
            empno = val;
        } else {
            return;
        }
        // const empno = $(this).val();
        // if(!empno){
        //     return;
        // }
        const result = await new Promise((resolve, reject) => {
            $.ajax({
                url: `${host}area/getUserdata`,
                type: "post",
                dataType: "json",
                data: {empno:empno},
                beforeSend: function(){
                    showpreload();
                },
                success: function (res) {
                    resolve(res);
                },
                complete: function(xhr, status){
                    checkAuthen(xhr, status);
                    hidepreload();
                }
            });
        });
        console.log(result);
        if(result.length != 0){
            checkEmp = true;
            $('#AREA_OWNER').val(result[0].STNAME);
            $('#AREA_DIV').val(result[0].SDIV);
            $('#AREA_DEPT').val(result[0].SDEPT);
            $('#AREA_SEC').val(result[0].SSEC);
            $('#AREA_DIVCODE').val(result[0].SDIVCODE);
            $('#AREA_DEPTCODE').val(result[0].SDEPCODE);
            $('#AREA_SECCODE').val(result[0].SSECCODE);
        }else{
            showMessage('ไม่พบข้อมูลผู้รับผิดชอบ กรุณาลองใหม่อีกครั้ง');
            resetOwner();
        }
    }
});

function resetOwner(){
    empno = '';
    $('#AREA_OWNER').val('-');
    $('#AREA_DIV').val('-');
    $('#AREA_DEPT').val('-');
    $('#AREA_SEC').val('-');
    $('#AREA_DIVCODE').val('-');
    $('#AREA_DEPTCODE').val('-');
    $('#AREA_SECCODE').val('-');
}

/**
 * Edit area
 */
$(document).on("click", ".edit-area", async function (e) {
    $('#headerArea').text('แก้ไขพื้นที่');
    $('#deleteImage').prop('checked',false).trigger('change').closest('div').removeClass('hidden');
    $('#AREA_IMAGE').closest('div').removeClass('hidden');
    const data = areatable.row($(this).parents("tr")).data();
    
    const frm = $("#area-form");
    checkEmp = true;
    for (const [key, value] of Object.entries(data)) {
        const target = frm.find(`[data-map=${key}]`);
        // console.log(key, value, target);
        
        //Check if target is select
        if (target.is("select")) {
            target.val(value).trigger("change");
        } else if (key == 'AREA_IMAGE'){
            target.val('');
        } else {
            target.val(value);
        }
    }
});

/**
 * Select delete image button
 */
$(document).on('change', '#deleteImage', function(){
    if($(this).is(':checked')){
        $('#AREA_IMAGE').closest('div').addClass('hidden')
        $('#AREA_IMAGE').val('');
    } else {
        $('#AREA_IMAGE').closest('div').removeClass('hidden');
    }
});

// $(document).on('input blur', '.req', function(){
//     if($(this).val() != ''){
//         $(this).removeClass('input-error');
//     }else{
//         $(this).addClass('input-error');
//     }
// })


/**
 * Save 
 */
$(document).on('click', '#save-area', async function(){
    const btn = $(this);
    const frm = $("#area-form");
    let checkVal = true;
    btn.addClass("loaded");
    btn.find(".loading").removeClass("hidden");
    frm.find(".req").map(function (i, el) {
        if ($(el).val() == "") {
            checkVal = false;
            // $(el).addClass('input-error');
            RequiredElement($(el))
        }
    });
    
    if (!checkVal || !checkEmp) {
        showMessage("กรุณากรอกข้อมูลให้ครบถ้วน", 'warning');
        btn.removeClass("loaded");
        btn.find(".loading").addClass("hidden");
        return false;
    }
    // var fileInput = $('input[type="file"]')[0].files;
    // const data = frm.serializeArray();
    // console.log( data);

    var formData = new FormData($('#area-form')[0]);
    const deleteImage = $('#deleteImage').is(':checked') ? 1 : 0; 
    formData.set('deleteImage', deleteImage);
    //ตรวจสอบข้อมูล
    // for (var pair of formData.entries()) {
    //     console.log(pair[0] + ': ' + pair[1]);
    //   }

    const area = await save(formData);
    if(area.status == true){
        areatable = await createTable(area.data);
        createColumnFilters(areatable, '1-4');
        showMessage('บันทึกข้อมูลสำเร็จ','success');
    }else{
        showMessage('บันทึกข้อมูลไม่สำเร็จ กรุณาลองใหม่ภายหลัง','error');
    }

    btn.removeClass("loaded");
    btn.find(".loading").addClass("hidden");
    $("#drawer-area").prop("checked", false);
});

/**
 * Set delete
 */
$(document).on('click', '.confirm', function(){
    const data = areatable.row($(this).parents("tr")).data();
    const id   = data.AREA_ID;
    console.log(data);
    console.log(data.AREA_ID);
    $('#del').attr('d-id',id);
});

/** 
 * Delete area
 */
$(document).on('click', '.del', async function(){
    const id = $(this).attr('d-id');
    console.log(id);
    
    const result = await new Promise((resolve, reject) => {
        $.ajax({
            url: `${host}area/del`,
            type: "post",
            dataType: "json",
            data: {id:id},
            beforeSend: function() {
                showpreload();
            },
            success: function (res) {
                resolve(res);
            },
            complete: function(xhr, status){
                checkAuthen(xhr, status);
                hidepreload();
            }
        });
    });
    console.log(result, result.status, result.data);
    
    if(result.status == true){
        areatable = await createTable(result.data);
        createColumnFilters(areatable, '1-4');

        showMessage('ลบข้อมูลสำเร็จ','success');
    }else{
        showMessage('ลบข้อมูลไม่สำเร็จ กรุณาลองใหม่ภายหลัง','error');
    }
});


$(document).on('change', '#drawer-area', function(){
    
    if (!$(this).is(':checked')) {
        empno = '';
        $('#area-form').find('.req').each(function() {
            const target = $(this);
            // console.log(target);
            
            removeClassError(target);
        });
    }
 });

/**
 * cancel drawer
 */
// $(document).on('click', '#cancel', function(){
//    $('#drawer-area').prop('checked', false); 
//    $('#drawer-area').trigger('change');

// });


/**
 * preview image
 */
$(document).on('click','.preview-image', function(){
    const base64 = $(this).attr('d-base64');
    const img = [{src: `<img src="${base64}" alt="" style="width:100%;">`, type: "html"}];
    new Fancybox(img);
});

/**
 * Required select2
 */
$(document).on('change', 'select.req', async function(){
    RequiredElement($(this));
});
/**
 * Save area
 * @param {array} data 
 * @returns 
 */
function save(data){
    return new Promise((resolve) => {
        $.ajax({
            url: `${host}area/save`,
            type: "post",
            dataType: "json",
            processData: false, 
            contentType: false,
            data: data,
            beforeSend: function (){
                showpreload();
            },
            success: function (res) {
                resolve(res);
            },
            complete: function(xhr, status){
                checkAuthen(xhr, status);
                hidepreload();
            }
        });
    });
}

/**
 * Get area data
 * @returns 
 */
function getArea() {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: `${host}area/getArea`,
            type: "post",
            dataType: "json",
            beforeSend: function (){
                showpreload();
            },
            success: function (res) {
                resolve(res);
            },
            complete: function(xhr, status){
                checkAuthen(xhr, status);
                hidepreload();
            }
        });
    });
}

/**
 * Create table
 * @param {array} data 
 * @returns 
 */
async function createTable(data) {
    const id = "#tblMaster";
    const opt = { ...tableOption };
    // opt.ordering = false;
    opt.data = data;
    opt.order = [[0, "asc"]];
    opt.dom = domScroll;
    opt.columns = [
        { data: "AREA_CATEGORY_NAME",  title: "Category", visible: false},
        { data: "AREA_NAME", title: "ชื่อพื้นที่"},
        { data: "AREA_OWNER", title: "ผู้รับผิดชอบ"},
        { data: "AREA_MANAGER" , title: "แผนกรับผิดชอบ"},
        { data: "AREA_TYPE_NAME" , title: "ประเภทพื้นที่",},
        { 
            data: "baseURL" , 
            title: "Actions",
            className: "action all",
            width : '10px',
            render: function (data, type, row, meta){
                var icon;
                var c = '';
                if(!data){
                    c   = `pointer-events-none`;
                    icon = `<i class="icofont-not-allowed text-red-600"></i>`;
                }else {
                    icon = `<i class="icofont-image"></i>`;
                }
                    
                return `<div class="flex items-center justify-center gap-3">
                            <button class="btn btn-sm btn-ghost btn-circle tooltip tooltip-left text-xl preview-image ${c}"  data-tip="คลิกเพื่อดูรูป" d-base64="${data}">
                                ${icon}
                            </button>
                            <label for="drawer-area" class="drawer-button btn btn-sm btn-ghost btn-circle edit-area tooltip tooltip-left flex items-center "  data-tip="แก้ไข">
                                <i class="icofont-ui-edit"></i>
                            </label>
                            <button class="drawer-button btn btn-sm btn-ghost btn-circle confirm tooltip tooltip-left"  data-tip="ลบ" onclick="modal_delete.showModal()">
                                <i class="icofont-ui-delete"></i>
                            </button>
                        </div>`;
            }
        },
        // {
        //     data: "AREA_ID",
        //     sortable: false,
        //     title: 'แก้ไข/ลบ',
        //     className: "flex items-center justify-center",
        //     render: function (data) {
        //         return `<label for="drawer-area" class="drawer-button btn btn-sm btn-ghost btn-circle edit-area tooltip flex items-center"  data-tip="แก้ไข">
        //                     <i class="icofont-ui-edit"></i>
        //                 </label>
        //                 <button class="drawer-button btn btn-sm btn-ghost btn-circle confirm tooltip"  data-tip="ลบ" onclick="modal_delete.showModal()">
        //                     <i class="icofont-ui-delete"></i>
        //                 </button>`;
        //     },
        // },
        // {
        //     data: "AREA_ID",
        //     sortable: false,
        //     title: 'ลบ',
        //     // className: "text-center",
        //     render: function (data) {
        //         return `<button class="drawer-button btn btn-sm btn-ghost btn-circle confirm" onclick="modal_delete.showModal()">
        //                     <i class="icofont-ui-delete"></i>
        //                 </button>`;
        //     },
        // },
    ];

    opt.initComplete = function () {
        $(".table-option").append(`
            <label for="drawer-area" class="drawer-button btn btn-sm btn-primary" id="add-area">
                เพิ่มพื้นที่ใหม่
            </label>`);
        initJoin(id);
        // $(".dt-length").addClass("hidden");
        // $(".dt-input").addClass("input");
        // $('#tblMaster_wrapper').removeClass('dt-container');
    };
    opt.columnDefs = [
        { orderable: false, targets: '_all' } // ปิดการเรียงในคอลัมน์ที่กำหนด
    ];
    opt.drawCallback = function (settings) {
        tableGroup(5, this.api());
    }
    return $(id).DataTable(opt);
}

$(document).on('click', 'tr.group-row', function () {
    clickTableGroup(areatable, 'AREA_CATEGORY_NAME', $(this));
});


