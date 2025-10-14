import DataTable from "datatables.net-dt";
import select2   from "select2";
import "select2/dist/css/select2.min.css";
import "datatables.net-responsive";
import "datatables.net-responsive-dt";
import "datatables.net-responsive-dt/css/responsive.dataTables.min.css";

import {domScroll, removeClassError, showMessage, RequiredElement, host, tableOption, initJoin, ajaxOptions, getData, tableGroup, clickTableGroup, select2Option, resetForm, setBtnFilter,}   from "./utils.js";


var table, colName;
var flagSelect = false;

$(document).ready(async function () {
    const res = await getData({
        ...ajaxOptions,
        url : `${host}status/getData`
    })
    console.log(res);
    table = await createTable(res.data);
    setBtnFilter(table,0);
    res.table.forEach(e => {
        $('#ST_TABLE').append(`<option value="${e.TABLE_NAME}">${e.TABLE_NAME}</option>`);
    });
    colName = res.column;
    $('#ST_TABLE').select2({...select2Option, placeholder:'Select Table'});
});





$(document).on('change', '#drawer', function(){
    
    if (!$(this).is(':checked')) {
        $('#type-form').find('input, select').each(function() {
            const target = $(this);
            removeClassError(target);
        });
    }
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
 $(document).on('change', 'select.req', async function(){
     const select = $(this);
     console.log(flagSelect, select);
     
     if (flagSelect) {
         // หาก trigger มาจากโปรแกรม ไม่ต้องทำอะไร
         flagSelect = false;
         return;
     }
     RequiredElement(select);
 });

 $(document).on('change', '#ST_TABLE',async function(){          
    $('#ST_COLUMN').html('<option></option>');
    const tableName = $(this).val();
    colName.forEach((c)=>{
        if(c.TABLE_NAME == tableName){
            $('#ST_COLUMN').prop('disabled',false).append(`<option value="${c.COLUMN_NAME}">${c.COLUMN_NAME}</option>`);
        }
    });
    $('#ST_COLUMN').select2({...select2Option, placeholder:'Select Column'});
 });



/**
 * Add type
 */
$(document).on("click", "#add", async function (e) {
    flagSelect = true;
    $('#headeritem').text('เพิ่ม');
    $('#ST_TABLE').val(null).trigger('change');
    $('#ST_COLUMN').val(null).prop('disabled',true).trigger('change');
    resetForm($('#form'));
});



/**
 * Edit type
 */
$(document).on("click", ".edit", async function (e) {
    $('#headeritem').text('แก้ไข');
    const data = table.row($(this).parents("tr")).data();
    console.log(data);
    const frm = $("#form");
    for (const [key, value] of Object.entries(data)) {
        const target = frm.find(`[data-map=${key}]`);
        // console.log(target.attr('data-map'));
         // //Check if target is select
        if (target.is("select")) {
            target.val(value).prop('disabled', false).trigger("change");
        } else {
            target.val(value);
        }
    }
});


/**
 * Save 
 */
$(document).on('click', '#save', async function(){
    const btn = $(this);
    const frm = $("#form");
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
    // //ตรวจสอบข้อมูล
    for (var pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }

    const ajax = {...ajaxOptions};
    
    const res = await getData({
        ...ajaxOptions,
        url : `${host}status/save`,
        data : formData,
        processData : false,
        contentType : false,
    })
    if(res.status == true){
        table = await createTable(res.data);
        setBtnFilter(table,0);
        showMessage('บันทึกข้อมูลสำเร็จ','success');
    }else{
        showMessage('บันทึกข้อมูลไม่สำเร็จ กรุณาลองใหม่ภายหลัง','error');
    }

    btn.removeClass("loaded");
    btn.find(".loading").addClass("hidden");
    $("#drawer").prop("checked", false);
});

/**
 * Set delete
 */
$(document).on('click', '.confirm', function(){
    const data = table.row($(this).parents("tr")).data();
    const id   = data.ST_ID;
    console.log(data);
    console.log(data.ST_ID);
    $('#del').attr('d-id',id);
});

/** 
 * Delete area
 */
$(document).on('click', '.del', async function(){
    const id = $(this).attr('d-id');
    console.log(id);

    const res = await getData({
        ...ajaxOptions,
        url : `${host}status/del`,
        data: {ST_ID: id}
    })
    if(res.status == true){
        table = await createTable(res.data);
        setBtnFilter(table,0);
        showMessage('ลบข้อมูลสำเร็จ','success');
    }else{
        showMessage('ลบข้อมูลไม่สำเร็จ กรุณาลองใหม่ภายหลัง','error');
    }
});



/**
 * Create table
 * @param {array} data 
 * @returns 
 */
async function createTable(data) {
    const id = "#table";
    const opt = { ...tableOption };
    opt.ordering = false;
    opt.lengthMenu = [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']]
    // opt.lengthChange = false;
    opt.data = data;
    opt.dom = domScroll;
    opt.order = [[0, "asc"]];
    opt.columns = [
        { data: "ST_TABLE", title: "TABLE", visible: false},
        { data: "ST_COLUMN", title: "COLUMN"},
        { data: "ST_CODE", title: "CODE"},
        { data: "ST_NO", title: "NO"},
        { data: "ST_STATUS", title: "STATUS"},
        { data: "ST_REMARK", title: "REMARK"},
        { 
            data: "ST_ID" , 
            title: "Actions",
            className: "all",
            width : '10px',
            render: function (data, type, row, meta){
                return `<div class="flex items-center justify-center gap-3">
                            <label for="drawer" class="drawer-button btn btn-sm btn-ghost btn-circle tooltip flex items-center edit"  data-tip="แก้ไข">
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
        tableGroup(6, this.api());
    }

    opt.initComplete = function () {
        $(".table-option").append(`
            <label for="drawer" class="drawer-button btn btn-sm btn-primary" id="add">
                เพิ่ม
            </label>`);
        initJoin(id);
    };
    return $(id).DataTable(opt);
}


$(document).on('click', 'tr.group-row', function () {
    clickTableGroup(table, 'ST_TABLE', $(this));
});




