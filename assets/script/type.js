import $         from "jquery";
import DataTable from "datatables.net-dt";
import select2   from "select2";
import "datatables.net-responsive";
import "datatables.net-responsive-dt";
// import "datatables.net-dt/css/dataTables.dataTables.min.css";
import "datatables.net-responsive-dt/css/responsive.dataTables.min.css";
import "select2/dist/css/select2.min.css";

import {domScroll, showpreload, hidepreload, select2Option, removeClassError, showMessage, RequiredElement, host, checkAuthen, tableOption, initJoin, ajaxOptions, getData, tableGroup, clickTableGroup}   from "./utils.js";


var table;
var flag = false;

$(document).ready(async function () {
    showpreload();
    const getDataOpt = {...ajaxOptions};
    getDataOpt.url = `${host}type/getType`;
    await getData(getDataOpt).then(async (data) => {
        console.log(data);
        table = await createTable(data);
        
    });
    // const items = await getItems();
    // table = await createTable(items);
    hidepreload();
    // const s2opt = { ...select2Option };
    // s2opt.placeholder = 'เลือกหมวดหมู่';
    // $('#type').select2(s2opt);
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
    
    if (flagSelect) {
        // หาก trigger มาจากโปรแกรม ไม่ต้องทำอะไร
        flagSelect = false;
        return;
    }
    RequiredElement(select);

});

$(document).on('change', '#drawer-type', function(){
    
    if (!$(this).is(':checked')) {
        $('#type-form').find('input, select').each(function() {
            const target = $(this);
            removeClassError(target);
        });
    }
 });

 /**
 * Cancle drawer
 */
$(document).on('click', '#cancle', function(){
    $('#drawer-type').prop('checked', false); 
    $('#drawer-type').trigger('change');
    // console.log($('#drawer-type').prop('checked'));

 });

$(document).on('change', '#type', function(){
    const value = $(this).val();
    // if(flag){
        value != 'newType' ? $('#TYPE_MASTER').val(value).prop('disabled', true) : $('#TYPE_MASTER').val('').prop('disabled', false);
    // }else{
    //     value != 'newType' ? $('#TYPE_MASTER').val(value) : '';
    // }
});

$(document).on('change', '#TYPE_STATUS', function(){
    $(this).val($(this).is(':checked') ? 1 : 0);
});

/**
 * Add type
 */
$(document).on("click", "#add-type", async function (e) {
    $('#headeritem').text('เพิ่ม Type');
    $("#type-form").trigger("reset");
    $('#type').closest('label').removeClass('hidden');
    flagSelect = true;
    flag = true;
    // $("#ITEMS_TYPE").val(null).trigger('change');
    $('#TYPE_STATUS').val(1).prop('checked', true);
    $('#TYPE_MASTER').prop('disabled', false);

});



/**
 * Edit type
 */
$(document).on("click", ".edit-type", async function (e) {
    flag = false;
    $('#headeritem').text('แก้ไข Type');
    const data = table.row($(this).parents("tr")).data();
    console.log(data);
    // $('#type').val(data.TYPE_MASTER).trigger('change');
    $('#type').closest('label').addClass('hidden');
    const frm = $("#type-form");
    for (const [key, value] of Object.entries(data)) {
        const target = frm.find(`[data-map=${key}]`);
        // console.log(target.attr('data-map'));
        
        if(key == 'TYPE_STATUS'){
            value == 1 ? target.val(value).prop('checked', true) : target.val(0).prop('checked', false);
        }else{
            target.val(value);
        }
        
        // //Check if target is select
        // if (target.is("select")) {
        //     target.val(value).trigger("change");
        // } else {
        //     target.val(value);
        // }
    }
});


/**
 * Save 
 */
$(document).on('click', '#save-type', async function(){
    const btn = $(this);
    const frm = $("#type-form");
    let checkVal = true;
    btn.addClass("loaded");
    btn.find(".loading").removeClass("hidden");
    
    frm.find(".req").map(function (i, el) {
        if ($(el).val() == "") {
            checkVal = false;
            RequiredElement($(el));
        }
    });
    
    if (!checkVal) {
        showMessage("กรุณากรอกข้อมูลให้ครบถ้วน", 'warning');
        btn.removeClass("loaded");
        btn.find(".loading").addClass("hidden");
        return false;
    }

    var formData = new FormData(frm[0]);
    const typeStatus = $('#TYPE_STATUS').is(':checked') ? 1 : 0; // เก็บค่า 1 หรือ 0
    formData.set('TYPE_STATUS', typeStatus);
    const typeMaster = $('#TYPE_MASTER').val(); // เก็บค่า 1 หรือ 0
    formData.set('TYPE_MASTER', typeMaster);
    // //ตรวจสอบข้อมูล
    for (var pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }

    const ajax = {...ajaxOptions};
    ajax.url = `${host}type/save`;
    ajax.data = formData;
    ajax.processData = false;
    ajax.contentType = false;
    await getData(ajax).then(async (res) => {  
        console.log(res);
        if(res.status == true){
            table = await createTable(res.data);
            showMessage('บันทึกข้อมูลสำเร็จ','success');
        }else{
            showMessage('บันทึกข้อมูลไม่สำเร็จ กรุณาลองใหม่ภายหลัง','error');
        }
    });

    btn.removeClass("loaded");
    btn.find(".loading").addClass("hidden");
    $("#drawer-type").prop("checked", false);
});

/**
 * Set delete
 */
$(document).on('click', '.confirm', function(){
    const data = table.row($(this).parents("tr")).data();
    const id   = data.TYPE_ID;
    console.log(data);
    console.log(data.TYPE_ID);
    $('#del').attr('d-id',id);
});

/** 
 * Delete area
 */
$(document).on('click', '.del', async function(){
    const id = $(this).attr('d-id');
    console.log(id);

    const ajax = {...ajaxOptions};
    ajax.url = `${host}type/del`;
    ajax.data = {id:id};
    await getData(ajax).then(async (res) => {
        if(res.status == true){
            table = await createTable(res.data);
            showMessage('ลบข้อมูลสำเร็จ','success');
        }else{
            showMessage('ลบข้อมูลไม่สำเร็จ กรุณาลองใหม่ภายหลัง','error');
        }
    });
});



/**
 * Create table
 * @param {array} data 
 * @returns 
 */
async function createTable(data) {
    const id = "#tblMaster";
    const opt = { ...tableOption };
    opt.ordering = false;
    opt.lengthMenu = [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']]
    // opt.lengthChange = false;
    opt.data = data;
    opt.dom = domScroll;
    // opt.columnDefs = { targets: [0], visible: false } ;// ซ่อนคอลัมน์ "Category"
    opt.order = [[0, "asc"]];
    opt.columns = [
        { data: "TYPE_MASTER", title: "HEAD", visible: false},
        { data: "TYPE_ID", title: "ID"},
        { data: "TYPE_CODE", title: "CODE"},
        { data: "TYPE_NO", title: "No"},
        { data: "TYPE_NAME", title: "NAME"},
        { data: "TYPE_DETAIL", title: "DETAIL"},
        { data: "TYPE_STATUS", title: "STATUS"},
        { 
            data: "TYPE_ID" , 
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
                            <label for="drawer-type" class="drawer-button btn btn-sm btn-ghost btn-circle edit-type tooltip flex items-center "  data-tip="แก้ไข">
                                <i class="icofont-ui-edit"></i>
                            </label>
                            <button class=" btn btn-sm btn-ghost btn-circle confirm tooltip"  data-tip="ลบ" onclick="modal_delete.showModal()">
                                <i class="icofont-ui-delete"></i>
                            </button>
                        </div>`;
            }
        },
    ];
    opt.drawCallback = function (settings) {
        tableGroup(7, this.api());
        // var api = this.api();
        // var rows = api.rows({ page: 'current' }).nodes();
        // var lastGroup = null;

        // // ลบกลุ่มที่มีอยู่เดิม
        // $(rows).removeClass('group-row');

        // // วนลูปเพื่อสร้างกลุ่ม
        // api.column(1, { page: 'current' }).data().each(function (group, i) {
        //     if (lastGroup !== group) {
        //         $(rows).eq(i).before(`
        //             <tr class="group-row font-bold" data-group="${group}">
        //                 <td colspan="7"><i class="icofont-rounded-down"></i> ${group}</td>
        //             </tr>
        //         `);
        //         lastGroup = group;
        //     }
        // });
    }

    opt.initComplete = function () {
        $(".table-option").append(`
            <label for="drawer-type" class="drawer-button btn btn-sm btn-primary" id="add-type">
                เพิ่มหมวดหมู่
            </label>`);
        initJoin(id);
        // $(".dt-length").addClass("hidden");
        // $(".dt-input").addClass("input");
        // $('#tblMaster_wrapper').removeClass('dt-container');
    };
    return $(id).DataTable(opt);
}


$(document).on('click', 'tr.group-row', function () {
    clickTableGroup(table, 'TYPE_MASTER', $(this));
    // var group = $(this).data('group'); // รับค่าหมวดหมู่
    // console.log(group); 

    // var rows = table.rows({ page: 'current' }).data(); // ดึงข้อมูลปัจจุบันทั้งหมด
    // console.log(rows);

    // var $icon = $(this).find('i'); // หาไอคอนใน row นี้

    // // Toggle ไอคอน
    // if ($icon.hasClass('icofont-rounded-right')) {
    //     $icon.removeClass('icofont-rounded-right').addClass('icofont-rounded-down');
    // } else {
    //     $icon.removeClass('icofont-rounded-down').addClass('icofont-rounded-right');
    // }

    // table.rows({ page: 'current' }).every(function () {
    //     var rowData = this.data(); // ดึงข้อมูลแถวปัจจุบัน
    //     if (rowData['TYPE_MASTER'] === group) { // ตรวจสอบค่า Category
    //         var rowNode = this.node(); // ดึง DOM node ของแถว
    //         $(rowNode).toggle('-translate-y-6'); // ซ่อนหรือแสดงแถว
    //     }
    // });
});




