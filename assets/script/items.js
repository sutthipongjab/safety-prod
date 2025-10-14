import $         from "jquery";
import * as my   from "./utils.js";
import DataTable from "datatables.net-dt";
import select2   from "select2";
import "datatables.net-responsive";
import "datatables.net-responsive-dt";
// import "datatables.net-dt/css/dataTables.dataTables.min.css";
import "datatables.net-responsive-dt/css/responsive.dataTables.min.css";
import "select2/dist/css/select2.min.css";



var itemstable;
var checkEmp = false;

$(document).ready(async function () {
    my.showpreload();
    const items = await getItems();
    itemstable = await createTable(items);
    my.hidepreload();
    const s2opt = { ...my.select2Option };
    s2opt.placeholder = 'เลือกหมวดหมู่';
    $('#ITEMS_TYPE').select2(s2opt);
});


/**
 *  Select2 focus
 */
$(document).on('select2:open', function(e) {
    setTimeout(function() {
        const searchField = document.querySelector('.select2-search__field');
        if (searchField) {
            searchField.focus();
        } else {
            console.warn("Search field not found.");
        }
    }, 100); 
});

/**
 * Required select2
 */
var flagSelect = false;
$(document).on('change', 'select.req', async function(){
    const select = $(this);
    // my.RequiredElement(select);
    
    if (flagSelect) {
        // หาก trigger มาจากโปรแกรม ไม่ต้องทำอะไร
        flagSelect = false;
        return;
    }
    my.RequiredElement(select);

});

$(document).on('change', '#drawer-item', function(){
    
    if (!$(this).is(':checked')) {
        $('#item-form').find('input, select').each(function() {
            const target = $(this);
            my.removeClassError(target);
        });
        $('.warn-th').addClass('hidden');
        $('.warn-en').addClass('hidden');
    }
 });

 /**
 * Cancle drawer
 */
$(document).on('click', '#cancle', function(){
    $('#drawer-item').prop('checked', false); 
    $('#drawer-item').trigger('change');
    console.log($('#drawer-item').prop('checked'));

 });


/**
 * Add item
 */
$(document).on("click", "#add-item", async function (e) {
    $('#headeritem').text('เพิ่มหมวดหมู่');
    $("#item-form").trigger("reset");
    flagSelect = true;
    $("#ITEMS_TYPE").val(null).trigger('change');
});

// /**
//  * thai only
//  */
// $(document).on('input', ".th-only", function(){
//     const val = $(this).val();
//     if(val == ''){return;}
//     my.thaionly($(this));
// });

// /**
//  * english only
//  */
// $(document).on('input', ".en-only", function(){
//     const val = $(this).val();
//     if(val == ''){return;}
//     my.engonly($(this));
// });


/**
 * Edit item
 */
$(document).on("click", ".edit-item", async function (e) {
    $('#headeritem').text('แก้ไขหมวดหมู่');
    const data = itemstable.row($(this).parents("tr")).data();
    console.log(data);
    
    const frm = $("#item-form");
    for (const [key, value] of Object.entries(data)) {
        const target = frm.find(`[data-map=${key}]`);
        console.log(target.attr('data-map'));
        
        //Check if target is select
        if (target.is("select")) {
        target.val(value).trigger("change");
        } else {
            target.val(value);
        }
    }
});


/**
 * Save 
 */
$(document).on('click', '#save-item', async function(){
    const btn = $(this);
    const frm = $("#item-form");
    let checkVal = true;
    btn.addClass("loaded");
    btn.find(".loading").removeClass("hidden");
    
    frm.find(".req").map(function (i, el) {
        console.log(el);
        
        if ($(el).val() == "") {
            checkVal = false;
            // if ($(el).is("select")) {
                my.RequiredElement($(el));
            // } else {
            //     $(el).addClass('input-error');
            // }
        }
    });
    
    if (!checkVal) {
        my.showMessage("กรุณากรอกข้อมูลให้ครบถ้วน", 'warning');
        btn.removeClass("loaded");
        btn.find(".loading").addClass("hidden");
        return false;
    }

    var formData = new FormData(frm[0]);
    
    //ตรวจสอบข้อมูล
    for (var pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }

    const item = await save(formData);
    if(item.status == true){
        itemstable = await createTable(item.data);
        my.showMessage('บันทึกข้อมูลสำเร็จ','success');
    }else{
        my.showMessage('บันทึกข้อมูลไม่สำเร็จ กรุณาลองใหม่ภายหลัง','error');
    }

    btn.removeClass("loaded");
    btn.find(".loading").addClass("hidden");
    $("#drawer-item").prop("checked", false);
});

/**
 * Set delete
 */
$(document).on('click', '.confirm', function(){
    const data = itemstable.row($(this).parents("tr")).data();
    const id   = data.ITEMS_ID;
    console.log(data);
    console.log(data.ITEMS_ID);
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
            url: `${my.host}items/del`,
            type: "post",
            dataType: "json",
            data: {id:id},
            beforeSend: function() {
                my.showpreload();
            },
            success: function (res) {
                resolve(res);
            },
            complete: function(xhr, status){
                my.checkAuthen(xhr, status);
                my.hidepreload();
            }
        });
    });
    console.log(result, result.status, result.data);
    
    if(result.status == true){
        itemstable = await createTable(result.data);
        my.showMessage('ลบข้อมูลสำเร็จ','success');
    }else{
        my.showMessage('ลบข้อมูลไม่สำเร็จ กรุณาลองใหม่ภายหลัง','error');
    }
});




/**
 * Save area
 * @param {array} data 
 * @returns 
 */
function save(data){
    return new Promise((resolve) => {
        $.ajax({
            url: `${my.host}items/save`,
            type: "post",
            dataType: "json",
            processData: false, 
            contentType: false,
            data: data,
            beforeSend: function (){
                my.showpreload();
            },
            success: function (res) {
                resolve(res);
            },
            complete: function(xhr, status){
                my.checkAuthen(xhr, status);
                my.hidepreload();
            }
        });
    });
}

/**
 * Get area data
 * @returns 
 */
function getItems() {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: `${my.host}items/getItems`,
            type: "post",
            dataType: "json",
            beforeSend: function (){
                my.showpreload();
            },
            success: function (res) {
                resolve(res);
            },
            complete: function(xhr, status){
                my.checkAuthen(xhr, status);
                my.hidepreload();
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
    const opt = { ...my.tableOption };
    opt.ordering = false;
    opt.data = data;
    opt.dom = my.domScroll;
    // opt.columnDefs = { targets: [0], visible: false } ;// ซ่อนคอลัมน์ "Category"
    opt.order = [[0, "asc"]];
    opt.columns = [
        { data: "TYPE_NAME", title: "Category", visible: false},
        { data: "ITEMS_NAME", title: "ชื่อหมวดหมู่", className: '!pl-5'},
        { data: "ITEMS_ENAME", title: "ชื่อหมวดหมู่ (ภาษาอังกฤษ)"},
        { 
            data: "ITEMS_ID" , 
            title: "Actions",
            className: "all",
            width : '10px',
            render: function (data, type, row, meta){
                // var icon;
                // var c = '';
                // if(!data){
                //     c   = `pointer-events-none`;
                //     icon = `<i class="icofont-not-allowed text-red-600"></i>`;
                // }else {
                //     icon = `<i class="icofont-image"></i>`;
                // }
                    
                return `<div class="flex items-center justify-center gap-3">
                            <label for="drawer-item" class="drawer-button btn btn-sm btn-ghost btn-circle edit-item tooltip flex items-center "  data-tip="แก้ไข">
                                <i class="icofont-ui-edit"></i>
                            </label>
                            <button class="drawer-button btn btn-sm btn-ghost btn-circle confirm tooltip"  data-tip="ลบ" onclick="modal_delete.showModal()">
                                <i class="icofont-ui-delete"></i>
                            </button>
                        </div>`;
            }
        },
    ];
    opt.drawCallback = function (settings) {
        my.tableGroup(3, this.api());
        // var api = this.api();
        // var rows = api.rows({ page: 'current' }).nodes();
        // var lastGroup = null;

        // // ลบกลุ่มที่มีอยู่เดิม
        // $(rows).removeClass('group-row');

        // // วนลูปเพื่อสร้างกลุ่ม
        // api.column(0, { page: 'current' }).data().each(function (group, i) {
        //     if (lastGroup !== group) {
        //         $(rows).eq(i).before(`
        //             <tr class="group-row font-bold" data-group="${group}">
        //                 <td colspan="3"><i class="icofont-rounded-down"></i> ${group}</td>
        //             </tr>
        //         `);
        //         lastGroup = group;
        //     }
        // });
    }

    opt.initComplete = function () {
        $(".table-option").append(`
            <label for="drawer-item" class="drawer-button btn btn-sm btn-primary" id="add-item">
                เพิ่มหมวดหมู่
            </label>`);
        // $(".dt-length").addClass("hidden");
        my.initJoin(id);
        // $(".dt-input").addClass("input");
        // $('#tblMaster_wrapper').removeClass('dt-container');
    };
    return $(id).DataTable(opt);
}


$(document).on('click', 'tr.group-row', function () {
    // console.log($(this));
    
    // var group = $(this).data('group');
    // console.log(group);
    // var rows = itemstable.rows({ page: 'current' }).nodes();
    // console.log(rows);

    // $(rows).each(function () {
    //     console.log($(this).find('td').first().text());
        
    //     if ($(this).find('td').first().text() === group) {
    //         $(this).toggle();
    //     }
    // });
    my.clickTableGroup(itemstable, 'TYPE_NAME', $(this));
    // var group = $(this).data('group'); // รับค่าหมวดหมู่
    // console.log(group); 

    // var rows = itemstable.rows({ page: 'current' }).data(); // ดึงข้อมูลปัจจุบันทั้งหมด
    // console.log(rows);

    // var $icon = $(this).find('i'); // หาไอคอนใน row นี้

    // // Toggle ไอคอน
    // if ($icon.hasClass('icofont-rounded-right')) {
    //     $icon.removeClass('icofont-rounded-right').addClass('icofont-rounded-down');
    // } else {
    //     $icon.removeClass('icofont-rounded-down').addClass('icofont-rounded-right');
    // }

    // itemstable.rows({ page: 'current' }).every(function () {
    //     var rowData = this.data(); // ดึงข้อมูลแถวปัจจุบัน
    //     if (rowData['TYPE_NAME'] === group) { // ตรวจสอบค่า Category
    //         var rowNode = this.node(); // ดึง DOM node ของแถว
    //         $(rowNode).toggle('-translate-y-6'); // ซ่อนหรือแสดงแถว
    //     }
    // });
});




