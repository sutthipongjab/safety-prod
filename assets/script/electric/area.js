import $            from "jquery";
import DataTable    from "datatables.net-dt";
import select2      from "select2";
import { Fancybox } from "@fancyapps/ui";
import { ajaxOptions, getData, userInfoData, checkAuthen, domScroll, hidepreload, host, initJoin, removeClassError, showMessage, showpreload, tableOption  } from "../utils.js";
import "datatables.net-responsive";
import "datatables.net-responsive-dt";
// import "datatables.net-dt/css/dataTables.dataTables.min.css";
import "datatables.net-responsive-dt/css/responsive.dataTables.min.css";
import "select2/dist/css/select2.min.css";
import "@fancyapps/ui/dist/fancybox/fancybox.css";


var areatable, empno;
var checkEmp = false;

$(document).ready(async function () {
    console.log(userInfoData);
    
    const area = await getData(
        {
            ...ajaxOptions,
            url: `${host}electric/area/getArea`,
            data: { AREA_DEPTCODE : userInfoData.sdepcode , AREA_DIVCODE : userInfoData.sdivcode},
        }
    );
    areatable  = await createTable(area);
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
    $('#deleteImage').prop('checked',false).trigger('change');
    $('#AREA_IMAGE').closest('div').removeClass('hidden');
    const data = areatable.row($(this).parents("tr")).data();
    const frm = $("#area-form");
    checkEmp = true;
    for (const [key, value] of Object.entries(data)) {
        const target = frm.find(`[data-map=${key}]`);
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
            $(el).addClass('input-error');
        }
    });
    
    if (!checkVal || !checkEmp) {
        showMessage("กรุณากรอกข้อมูลให้ครบถ้วน", 'warning');
        btn.removeClass("loaded");
        btn.find(".loading").addClass("hidden");
        return false;
    }

    var formData = new FormData($('#area-form')[0]);
    const deleteImage = $('#deleteImage').is(':checked') ? 1 : 0; 
    formData.set('deleteImage', deleteImage);
    //ตรวจสอบข้อมูล
    // for (var pair of formData.entries()) {
    //     console.log(pair[0] + ': ' + pair[1]);
    //   }

    // const area = await save(formData);
    const area = await getData({
        ...ajaxOptions,
        url: `${host}electric/area/save`,
        data: formData,
        processData: false,
        contentType: false,
    });
    if(area.status == true){
        areatable = await createTable(area.data);
        showMessage('บันทึกข้อมูลสำเร็จ','success');
    }else{
        showMessage('บันทึกข้อมูลไม่สำเร็จ กรุณาลองใหม่ภายหลัง','error');
    }

    btn.removeClass("loaded");
    btn.find(".loading").addClass("hidden");
    $("#drawer-area").prop("checked", false);
});


$(document).on('change', '#drawer-area', function(){
    
    if (!$(this).is(':checked')) {
        empno = '';
        $('#area-form').find('.req').each(function() {
            const target = $(this);
            removeClassError(target);
        });
    }
 });


/**
 * preview image
 */
$(document).on('click','.preview-image', function(){
    const base64 = $(this).attr('d-base64');
    const img = [{src: `<img src="${base64}" alt="" style="width:100%;">`, type: "html"}];
    new Fancybox(img);
});


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
                        </div>`;
            }
        },
    ];

    opt.initComplete = function () {
        initJoin(id);
    };
    opt.columnDefs = [
        { orderable: false, targets: '_all' } // ปิดการเรียงในคอลัมน์ที่กำหนด
    ];
    return $(id).DataTable(opt);
}




