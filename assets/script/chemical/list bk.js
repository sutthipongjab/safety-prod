import $ from "jquery";
import "select2";
import "select2/dist/css/select2.min.css";
import 'datatables.net-dt';
import "datatables.net-dt/css/dataTables.dataTables.min.css";
import jsPDF from 'jspdf'
import autoTable from 'jspdf-autotable'
import {host, showpreload, hidepreload, checkAuthen, tableOption, showMessage, userInfoData ,ajaxOptions, getData ,setDatePicker, dateFormat, select2Option, domScroll, initJoin, RequiredElement, requiredForm, removeClassError} from "../utils.js";
import {writeExcelTemp, writeOpt, exportExcel, colToNumber, numberToCol, fill, border, alignment} from '../_excel.js';
import {getfileInPath, getArrayBufferFile} from '../_file.js';
import {createColumnFilters} from '../filter.js';
import {createStamp, loadFont, optAutoTable} from '../_jsPDF.js';

var sectionList, revisionList, table, table_re, table_reSec, table_submit, rebuildID ,uniqueSec, freesiaUPC;

const columnRebuild = [
    {
        data: 'selected',
        title: `<input type="checkbox" class="chk checkbox" id="select-all" />`,
        className: "text-center",
        sortable: false,
        render(data, type, row) {
            return `<input type="checkbox" class="chk checkbox select-row" value="${row.AMEC_SDS_ID}"  ${data != undefined ? "checked" : ""}>`;
        },
    },
    {data:'AMEC_SDS_ID',       title: 'AMEC SDS ID'},
    {data:'EFFECTIVE_DATE',    title: 'Effective Date'},
    {data:'RECEIVED_SDS_DATE', title: 'Received SDS Date'},
    {data:'CHEMICAL_NAME',     title: 'Chemical Name'},
    {data:'VENDOR',            title: 'MANUFACTURER / VENDOR'},
    {data:'PUR_INCHARGE',      title: 'PUR. INCHARGE'},
    {data:'UN_CLASS',          title: 'UN CLASS'},
    {data:'REV',               title: 'Rev.'},

];

const columnRebuildSec = [
    { data: "AMEC_SDS_ID", title: "ID"},
    { data: "CHEMICAL_NAME", title: "ชื่อสารเคมี", visible: false},
    { data: "PRODUCT_CODE", title: "รหัสสารเคมี"},
    { data: "REV", title: "REV"},
    { data: "EFFECTIVE_DATE", title: "EFFECTIVE DATE"},
    { data: "RECEIVED_SDS_DATE", title: "RECEIVED SDS DATE"},
    { data: "USED_FOR", title: "การใช้ประโยชน์"},
    { data: "USED_AREA", title: "จุดใช้งาน"},
    { data: "KEEPING_POINT", title: "จุดจัดเก็บ"},
    { data: "QTY", title: "จำนวน"},
    { data: "CLASS", title: "CLASS"},
    
];


$(document).ready(async function () {
    // console.log(userInfoData);
    freesiaUPC = await loadFont(host, 'freesiaUPC/upcfl.ttf')
    
    await getMaster();
    // createColumnFilters(table, '1-9')
    
});



async function getMaster(){
    const ajaxOpt = { ...ajaxOptions };
    ajaxOpt.url   = `${host}chemical/chemicalList/getOwnOrg/`;
    ajaxOpt.data  = {empno:userInfoData.sempno,group:userInfoData.group_code};
    const data = await getData(ajaxOpt);
    if(data.status == 1){
        sectionList  = data.sec;
        revisionList = data.rev;
        setTable(data.data,data.sec);
    }else{
        showMessage('ไม่พบข้อมูล ระบบกำลังนำทางสู่หน้าขอใช้สารเคมี');
        showpreload();
        setTimeout(() => {
            window.location.href = `${host}chemical/request`;
            
        }, 5000);
    }
    // console.log(data);
}


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
    // console.log(select);
    RequiredElement(select);

});

/**
 * Add chemical
 */
$(document).on("click", "#add-chemical", async function (e) {
    flagSelect = true;
    $('#headeritem').text('เพิ่มสารเคมี');
    $("#chemical-master").trigger("reset");
    $("#UN_CLASS").val(null).trigger('change');
});

/**
 * Edit chemical
 */
$(document).on("click", ".edit-chemical", async function (e) {
    // flag = false;
    $('#headeritem').text('แก้ไขข้อมูลสารเคมี');
    const data = table.row($(this).parents("tr")).data();
    console.log(data);

    const frm = $("#chemical-master");
    for (const [key, value] of Object.entries(data)) {
        const target = frm.find(`[data-map="${key}"]`);

        if (target.is("select")) {
            target.val(value).trigger("change");
        } else if (['RECEIVED_SDS_DATE','EFFECTIVE_DATE'].includes(key)){
            target.val(value).siblings('input').val(value);
        }else {
            target.val(value);
        }
    }
});

$(document).on('click', '#save', async function(){

    const rs   = $('#RECEIVED_SDS_DATE');
    const ef   = $('#EFFECTIVE_DATE');
    const pro  = $('#PRODUCT_CODE');
    const ven  = $('#VENDOR');
    const pur  = $('#PUR_INCHARGE');
    const name = $('#CHEMICAL_NAME');
    const cla  = $('#UN_CLASS');
    const rev  = $('#REV');

    const fields = [
        { element: rs,   message: 'กรุณาเลือกวันที่ RECEIVED SDS DATE' },
        { element: ef,   message: 'กรุณาเลือกวันที่ EFFECTIVE DATE' },
        { element: pro,  message: 'กรุณาระบุ PRODUCT CODE / ITEM NO.' },
        { element: ven,  message: 'กรุณาระบุ MANUFACTURER / VENDOR' },
        { element: pur,  message: 'กรุณาระบุ PUR. INCHARGE' },
        { element: name, message: 'กรุณาระบุ CHEMICAL NAME/TRADE NAME' },
        { element: cla,  message: 'กรุณาเลือก Class' },
        { element: rev,  message: 'กรุณาระบุ REV' },
    ];
    if(!await requiredForm('#chemical-master', fields, 'toast-start')) return;

    const btn = $(this);
    const frm = $("#chemical-master");
    btn.addClass("loaded");
    btn.find(".loading").removeClass("hidden");
    var formData = new FormData(frm[0]);
    // const typeStatus = $('#TYPE_STATUS').is(':checked') ? 1 : 0; // เก็บค่า 1 หรือ 0
    // formData.set('TYPE_STATUS', typeStatus);
    // const typeMaster = $('#TYPE_MASTER').val(); // เก็บค่า 1 หรือ 0
    // formData.set('TYPE_MASTER', typeMaster);
    formData.set('USER_UPDATE', userInfoData.sempno);

    // // //ตรวจสอบข้อมูล
    for (var pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }

    const ajax = {...ajaxOptions};
    ajax.url   = `${host}Chemical/chemicalList/save`;
    ajax.data  = formData;
    ajax.processData = false;
    ajax.contentType = false;
    await getData(ajax).then(async (res) => {  
        console.log('save',res);
        if(res.status == true){
            // console.log(1);
            
            await setTable(res.data, res.sec);
            showMessage('บันทึกข้อมูลสำเร็จ','success');
        }else{
            // console.log(2);
            showMessage('บันทึกข้อมูลไม่สำเร็จ กรุณาลองใหม่ภายหลัง','error');
        }
    });

    btn.removeClass("loaded");
    btn.find(".loading").addClass("hidden");
    $("#drawer-master").prop("checked", false);
});

/**
 * Set delete
 */
$(document).on('click', '.confirm', function(){
    const data = table.row($(this).parents("tr")).data();
    console.log(data);
    const id   = data.AMEC_SDS_ID;
    console.log(data.AMEC_SDS_ID);
    $('#del').attr('d-id',id);
});

/** 
 * Delete area
 */
$(document).on('click', '.del', async function(){
    const id = $(this).attr('d-id');
    console.log(id);

    const ajax = {...ajaxOptions};
    ajax.url = `${host}Chemical/chemicalList/del`;
    ajax.data = {id:id};
    await getData(ajax).then(async (res) => {
        if(res.status == true){
            await setTable(res.data, res.sec);
            showMessage('ลบข้อมูลสำเร็จ','success');
        }else{
            showMessage('ลบข้อมูลไม่สำเร็จ กรุณาลองใหม่ภายหลัง','error');
        }
    });
});

/**
 * Clear class error
 */
$(document).on('change', '#drawer-master', function(){
    if (!$(this).is(':checked')) {
        $('#chemical-master').find('.req').each(function() {
            const target = $(this);
            removeClassError(target);
        });
    }
 });


/**
 * Cancle drawer
 */
$(document).on('click', '#cancle', function(){
    $('#drawer-master').prop('checked', false); 
    $('#drawer-master').trigger('change');
});



$(document).on('click', '#re-chemical', async function(){
    const ajax = {...ajaxOptions};
    ajax.url = `${host}Chemical/chemicalList/getOld`;
    await getData(ajax).then(async (data) => {
        if(data.length > 0){
            table_re = await createTable('#table_rebuild', data, columnRebuild, '50vh');
            createColumnFilters(table_re, '1-8');
            $('.step-1-skeleton').addClass('hidden');
        }else{
            showMessage('ไม่พบข้อมูล','warning');
            $('#modal_rebuild').prop('checked', false);
        }
    });
});

/**
 * Select rows all
 */
$(document).on("click", "#select-all", function (e) {
    // const chk = $(this).is(":checked");
    // table_re.rows({ search: "none" }).every(function () {
    //     $(this.node()).find(".chk").prop("checked", chk);
    //     chk ? $(this.node()).addClass("selected") : $(this.node()).removeClass("selected");
    // });

    // table_re.rows({ search: "applied" }).data().each(function (value, index) {
    //     console.log("Row data:", value);
    // });

    // table_re.rows({ search: "applied" }).every(function () {
    //     const $row = $(this.node());
    //     console.log($row); // ตรวจสอบว่าเข้าถึง DOM ได้หรือไม่
    // });

    const check = $(this).is(":checked");
    table_re.rows({ search: "applied" }).every(function () {
      const data = this.data();
      console.log(data, data.selected);
      
      if (check) {
        data.selected = true;
      } else {
        delete data.selected;
      }
      this.data(data).draw();
    });
  
    const allRow = table_re.rows().data();
    const selected = allRow.filter((el) => el.selected === true);
    if (selected.length != allRow.length && selected.length != 0) {
      $("#select-all").prop("indeterminate", true);
    }
});

// /**
//  * Select row
//  */
$(document).on("click", ".select-row", function (e) {
    // table_re.rows().every(function () {
        //         $(this.node()).find(".chk").prop("checked") ? $(this.node()).addClass("selected") : $(this.node()).removeClass("selected");
        //     });
    let data = table_re.row($(this).parents("tr")).data();
    const check = $(this);
    console.log(check);
    
    if (check.is(":checked")) {
      data = { ...data, selected: true };
    } else {
      delete data.selected;
    }
    table_re.row($(this).parents("tr")).data(data);
});

$(document).on("click", ".sec-active", function () {
    let data = table_reSec.row($(this).parents("tr")).data();
    console.log(data);
    
    const check = $(this);
    console.log(check, check.is(":checked"));
    
    if (check.is(":checked")) {
      data = { ...data, selected: true };
    } else {
        delete data.selected;
    }
    table_reSec.row($(this).parents("tr")).data(data);
    
});

/**
 * Control step
 */
$(document).on('click', '#previous', function(e){
    e.preventDefault();
    const step = parseInt($(this).attr('step'));
    if(step == 2){
        // $('#next').attr('step',1).removeClass('hidden').html('ถัดไป');
        $('#next').attr('step',1).removeClass('hidden');
        $(this).attr('step',1).addClass('hidden');

        $('.step-active').addClass('hidden').removeClass('step-active');
        $('.step-1').addClass('step-active').removeClass('hidden'); 
        $('.step-h-1').addClass('step-primary');
        $('.step-h-2').removeClass('step-primary');

        if(table_reSec){
            table_reSec.destroy();
            $('#table_rebuild_sec').empty()
            table_reSec = null;
        }
        $('.step-2-skeleton').removeClass('hidden');
        $('.text-remark').addClass('hidden');
    }
    else if(step == 3){
        if(table_submit){
            table_submit.destroy();
            $('#table_submit').empty()
            table_submit = null;
        }
        $('#next').attr('step',2).html('ถัดไป');
        $(this).attr('step',2);

        $('.step-active').addClass('hidden').removeClass('step-active');
        $('.step-2').addClass('step-active').removeClass('hidden'); 
        $('.step-h-2').addClass('step-primary');
        $('.step-h-3').removeClass('step-primary');
    }
});
$(document).on('click', '#next', async function(e){
    e.preventDefault();
    const step = parseInt($(this).attr('step'));
    
    if(step == 1){
        const data = table_re.rows().data().toArray();
        const selected = data.filter((el) => el.selected === true);
        if (selected.length == 0) {
          showMessage("กรุณาเลือกสารเคมีที่ต้องการเปิดการใช้งานอีกครั้ง", 'error', 'toast-start');
          return ;
        }

        $('#previous').attr('step',2).removeClass('hidden');    
        $(this).attr('step',2);
        // $(this).attr('step',2).html('ยืนยัน');
        $('.step-active').addClass('hidden').removeClass('step-active');
        $('.step-2').addClass('step-active').removeClass('hidden'); 
        $('.step-h-2').addClass('step-primary');
      
        console.log(selected);
        
        rebuildID = selected.map( (el) => {
            return el.AMEC_SDS_ID;
        });

        const sec = selected.map( (el) => {
            return Object.keys(el).filter(key => (key.includes('SEC') || key.includes('DEPT') || key.includes('Sec')) && el[key] === 'Y').map(key => (key));
        });
        uniqueSec = [...new Set(sec.flat())];

        console.log(rebuildID,sec,uniqueSec);

        const ajax = {...ajaxOptions};
        ajax.url = `${host}Chemical/chemicalList/getSecRebuild`;
        ajax.data = {id:JSON.stringify(rebuildID), sec:JSON.stringify(uniqueSec)};
        await getData(ajax).then(async (d) => {
            // console.log(d);
            // columnRebuildSec = [
            //     { data: "AMEC_SDS_ID", title: "ID"},
            //     { data: "CHEMICAL_NAME", title: "ชื่อสารเคมี", visible: false},
            //     { data: "PRODUCT_CODE", title: "รหัสสารเคมี"},
            //     { data: "REV", title: "REV"},
            //     { data: "EFFECTIVE_DATE", title: "EFFECTIVE DATE"},
            //     { data: "RECEIVED_SDS_DATE", title: "RECEIVED SDS DATE"},
            //     { data: "USED_FOR", title: "การใช้ประโยชน์"},
            //     { data: "USED_AREA", title: "จุดใช้งาน"},
            //     { data: "KEEPING_POINT", title: "จุดจัดเก็บ"},
            //     { data: "QTY", title: "จำนวน"},
            //     { data: "CLASS", title: "CLASS"},
                
            // ];
            const colSec = uniqueSec.map((s) =>{
                // console.log(s); 
                return {
                    data:s, 
                    title:s, 
                    className: "write-vertical-lr border",
                    render: function(data, type, row, meta){
                        const disable = data == 'N' ? 'disabled' : '';
                        return `<input type="checkbox" class=" checkbox sec-active" ${disable} ${row.selected != undefined && data == 'Y' ? "checked" : ""}/>`;
                        // return data == 'Y' ? `<input type="checkbox" class=" checkbox sec-active" ${disable} ${row.selected != undefined ? "checked" : ""}/>`: '';
                        // return `<input type="checkbox" class="chk checkbox sec-active" value="${row.AMEC_SDS_ID}|${s}" ${disable} />`;
                    },
                    orderable: false
                }
            });
            table_reSec = await createTable('#table_rebuild_sec', d, [...columnRebuildSec, ...colSec], '50vh');
            createColumnFilters(table_reSec, '1-10');
            $('.step-2-skeleton').addClass('hidden');
            $('.text-remark').removeClass('hidden');
            
        });
        
        // $("#create-users").prop("checked", false);
        // window.location.reload();
    }else if(step == 2){
        $('#previous').attr('step',3);
        $(this).attr('step',3).html('ยืนยัน')
        $('.text-remark').addClass('hidden');
        // $(this).attr('step',3).addClass('hidden');

        $('.step-active').addClass('hidden').removeClass('step-active');
        $('.step-3').addClass('step-active').removeClass('hidden'); 
        $('.step-h-3').addClass('step-primary');

        const dataSec = table_reSec.rows().data().toArray();
        const selectedSec = dataSec.filter((el) => el.selected === true);

        const data = table_re.rows().data().toArray();
        const selectedMaster = JSON.parse(JSON.stringify(data.filter((el) => rebuildID.includes(el.AMEC_SDS_ID))));
        if(selectedSec.length == 0){
            selectedMaster.map((el) => {
                return Object.keys(el)
                    .filter(
                        key => (key.includes('SEC') || key.includes('DEPT') || key.includes('Sec')) && el[key] === 'Y'
                    ).map(key => {
                        el[key] = 'N';
                        return key;
                    });
            });
            // console.log(mapD);
            
            table_submit = await createTable('#table_submit', selectedMaster, columnRebuild.slice(1), '50vh');
            createColumnFilters(table_submit, '1-8');
        }else{
            // const mapData = selectedSec.map((el) => {
            //     return {
            //         ...el,
            //         ...Object.fromEntries(
            //             uniqueSec.map((sec) => [sec, el[sec] === 'Y' ? 'Y' : ''])
            //         ),
            //     };
            // });

            const mapD = selectedMaster.map((el) => {
                return {
                    ...el,
                    ...Object.fromEntries(
                        uniqueSec.map((sec) => 
                            [sec, selectedSec.some((secEl) => 
                                secEl.AMEC_SDS_ID === el.AMEC_SDS_ID && secEl[sec] === 'Y') ? 'Y' : 'N'])
                    ),
                };
            });
            console.log(mapD);
            
            console.log('check', selectedSec, selectedMaster, columnRebuildSec, uniqueSec);
            const colSec = uniqueSec.map((s) =>{
                // console.log(s); 
                return {
                    data:s, 
                    title:s, 
                    className: "write-vertical-lr border",
                    render: function(data, type, row, meta){
                        const dNew = data == 'N' ? '' : `
                                <div class="">
                                    <i class="icofont-check text-green-500 text-[1.25rem] border-2 rounded-full border-black"></i>
                                </div>
                                `;
                                return `<div class="text-center">${dNew}</div>`;
                    },
                    orderable: false
                }
            })
            
            table_submit = await createTable('#table_submit', mapD, [...columnRebuild.slice(1), ...colSec], '50vh');
            // console.log(table_submit);
            createColumnFilters(table_submit, '1-7');

            // table_submit = await createTable('#table_submit', selectedSec, [...columnRebuildSec, ...colSec], '50vh');
        }

        $('.step-3-skeleton').addClass('hidden');
        console.log(rebuildID);
        
    }else if(step == 3){
        console.log(table_submit);
        
        const data = table_submit.rows().data().toArray();
        console.log(data);
        
        const ajax = {...ajaxOptions};
        ajax.url = `${host}Chemical/chemicalList/statusOn`;
        ajax.data = {data:JSON.stringify(data),sec:JSON.stringify(uniqueSec)};
        await getData(ajax).then(async (res) => {
            
            if(res.status == true){
                showMessage('บันทึกข้อมูลสำเร็จ','success');
                $('#cancleModal').trigger('click');
                getMaster();
            }else{
                showMessage('บันทึกข้อมูลไม่สำเร็จ กรุณาลองใหม่ภายหลัง','error');
            }
        });
    }
});

$(document).on('click', '#cancleModal', function(){
    // button
    $('#previous').attr('step',1).addClass('hidden');
    $('#next').attr('step',1).removeClass('hidden').html('ถัดไป');

    // step header
    $('.step-h-2').removeClass('step-primary');
    $('.step-h-3').removeClass('step-primary');

    // step detail
    $('.step-active').addClass('hidden').removeClass('step-active');
    $('.step-1').addClass('step-active').removeClass('hidden');

    // table
    if(table_re){
        table_re.destroy();
        $('#table_rebuild').empty()
        table_re = null;
    }
    if(table_reSec){
        table_reSec.destroy();
        $('#table_rebuild_sec').empty()
        table_reSec = null;
    }
    if(table_submit){
        table_submit.destroy();
        $('#table_submit').empty()
        table_submit = null;
    }
    // show skeleton
    $('.step-1-skeleton').removeClass('hidden');
    $('.step-2-skeleton').removeClass('hidden');
    $('.step-3-skeleton').removeClass('hidden');

    $('.text-remark').addClass('hidden');

    // close modal
    $('#modal_rebuild').prop('checked', false);
    // console.log(table_re);
});


$(document).on('click', '#cancleRev', function(){
    $('#modal_rev').prop('checked', false);
});

$(document).on('click', '#modal_rev', function(){
    console.log('modal_rev');
    $('#current-revision').val(revisionList.MASTER).focus();
});

$(document).on('click', '#saveRev', async function(){
    const rev = $('#current-revision').val();
    if(rev == ''){
        showMessage('กรุณากรอก Revision','warning');
        return;
    }
    const ajax = {...ajaxOptions};
    ajax.url = `${host}Chemical/chemicalList/updateRev`;
    ajax.data = {rev:rev, own:'MASTER', USER_UPDATE: userInfoData.sempno};
    await getData(ajax).then(async (res) => {
        if(res.status == true){
            revisionList = res.rev;
            showMessage('บันทึกข้อมูลสำเร็จ','success');
            $('.revision-master').html(`Rev. No. ${rev.toUpperCase()}`);
            $('#cancleRev').trigger('click');
            // getMaster();
        }else if(res.status == 3){
            showMessage('กรุณากรอก Revision ใหม่','warning');

        }else{
            showMessage('บันทึกข้อมูลไม่สำเร็จ กรุณาลองใหม่ภายหลัง');
        }
    });
});




/**
 * Set table
 * @param {object} data 
 * @param {object} sec 
 */
async function setTable(data, sec){
    // console.log(data);
    // console.log(Object.entries(data), Object.values(data), Object.keys(data));
    // console.log('setTable',data, sec);
    
    let html = '';
    if(userInfoData.group_code == 'DEV' || userInfoData.group_code == 'ADM'){
    // if(userInfoData.group_code == 'ADM'){
        // console.log(1);
        const revHtml = $('.revision-master').length == 0 ?`<div class="font-bold flex items-center gap-5">
            <span class="revision-master">Rev. No. ${revisionList.MASTER} </span>
            <label for="modal_rev" class="drawer-button btn btn-xs  btn-neutral flex items-center tooltip tooltip-left" data-tip="แก้ไข Revision" id="rev-control">
                <i class="icofont-edit"></i>
            </label>
        </div>` : '';

        html =    
        `
        ${revHtml}
        <div class="overflow-x-auto">
            <table class="table" id="table_master"></table>
        </div>`;
        $('.card-body').append(html);
        for (const [key, value] of Object.entries(data)) {
            // console.log(key, value);
            if(key == 'Master'){
                const columns = [
                    { 
                        data: "AMEC_SDS_ID" , 
                        title: "Actions",
                        className: "sticky-column lg:left-0",
                        width: "95px",
                        render: function (data, type, row, meta){
                            return `<div class="flex items-center justify-center gap-3">
                            <label for="drawer-master" class="drawer-button btn btn-sm btn-ghost btn-circle edit-chemical tooltip flex items-center "  data-tip="แก้ไข">
                                        <i class="icofont-ui-edit"></i>
                                    </label>
                                    <button class="drawer-button btn btn-sm btn-ghost btn-circle confirm tooltip"  data-tip="ยกเลิก" onclick="modal_delete.showModal()">
                                        <i class="icofont-ui-delete"></i>
                                    </button>
                                    </div>`;
                        }
                    },
                    // {
                    //     data: null,
                    //     title: "No.",
                    //     render: function (data, type, row, meta) {
                    //         return meta.row + 1;
                    //     },
                    // },
                    {
                        data:'AMEC_SDS_ID',       
                        title:'AMEC SDS ID',
                        className: "sticky-column lg:left-[95px] no-sort",
                        width: "105px",
    
                    }, 
                    {data:'RECEIVED_SDS_DATE', title:'RECEIVED SDS DATE'}, 
                    {data:'EFFECTIVE_DATE',    title:'EFFECTIVE DATE'}, 
                    {
                        data:'PRODUCT_CODE',      
                        title:'Product Code / Item No.',
                        className: "sticky-column lg:left-[190px]",
                        width: "150px",
    
                    }, 
                    {
                        data:'CHEMICAL_NAME',     
                        title:'CHEMICAL NAME/TRADE NAME',
                        className: "sticky-column lg:left-[340px]",
                    }, 
                    {data:'VENDOR',            title:'MANUFACTURER / VENDOR'},
                    {data:'PUR_INCHARGE',      title:'PUR. INCHARGE'},
                    {data:'UN_CLASS',          title:'UN CLASS'},
                    {data:'REV',               title:'Rev.'},
                    
                ];
                const colSec = sec.map((s) =>{
                    // console.log(s); 
                    return {
                        data:s.OWNER.slice(0,-1), 
                        title:s.OWNER, 
                        className: "write-vertical-lr border",
                        render: function(data, type, row, meta){
                            if (meta.row === undefined) {
                                return `<div class="vertical-header">${data}</div>`;
                            }
                            const dNew = data == 'N' ? '' : `
                            <div class="">
                                <i class="icofont-check text-green-500 text-[1.25rem] border-2 rounded-full border-black"></i>
                            </div>
                            `;
                            // const dNew = data == 'N' ? '' : data;
                            return `<div class="text-center">${dNew}</div>`;
                        },
                        orderable: false
                    }
                })
                // console.log( [...columns, ...colSec]);
                
                table = await createTable('#table_master', value, [...columns, ...colSec]);
                createColumnFilters(table, '1-9');
                // setDatePicker();
                setDatePicker({
                    altInput: true,
                    altFormat: 'd-M-y',
                    dateFormat: 'd/m/Y',
                });
                const s2opt = { ...select2Option };
                s2opt.placeholder = 'เลือก class';
                $('#UN_CLASS').select2(s2opt);
            }
        }
    }else{
        // console.log(2);
        const columns = [
            {data:'AMEC_SDS_ID',        title: 'ID'}, 
            {data:'CHEMICAL_NAME',      title: 'ชื่อสารเคมี'}, 
            {data:'REV',                title: 'REV'},
            {data:'EFFECTIVE_DATE',     title: 'Effective Date'}, 
            {data:'RECEIVED_SDS_DATE',  title: 'Received SDS Date'}, 
            {data:'USED_FOR',           title: 'การใช้ประโยชน์', className: "min-w-[250px]"}, 
            {data:'USED_AREA',          title: 'จุดใช้งาน'},
            {data:'KEEPING_POINT',      title: 'จุดจัดเก็บ'},
            {data:'QTY',                title: 'จำนวน'},
            {
                data:'REC4052',            
                title: 'REC4052',
                render: function(data, type, row, meta){
                    return data == 1 ? 'OK' : 'N/A';
                }
            },
            {
                data:'REC4054',            
                title: 'REC4054',
                render: function(data, type, row, meta){
                    return data == 1 ? 'OK' : 'N/A';    
                }
            },
            {data:'CLASS',              title: 'Class'},
        ];
        html =`<div role="tablist" class="tabs tabs-lifted w-full grid-cols-[0fr]">`;
        for (const [key, value] of Object.entries(data)) {
            console.log(data);
            
            const index = Object.keys(data).indexOf(key);
            const check = index == 0 ? 'checked' : ''; 
            const org = key.slice(0, -1).replace(/\s+/g, '_');
            console.log(org);
            
            html += `<input type="radio" name="my_tabs_2" role="tab" class="tab whitespace-nowrap" aria-label="${key}" ${check} />
                    <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-box p-6">
                        <div class="font-bold">
                            <span class="revision-master">Rev. No. ${revisionList[key]}</span>
                        </div>
                        <div class="overflow-x-auto w-full">
                            <table class="table" id="${org}"></table>
                        </div>
                    </div>`;
        }
        html += `</div>`;
        $('.card-body').append(html);
        Object.entries(data).forEach(async ([key, value], index) => {
            const org = key.slice(0, -1).replace(/\s+/g, '_');
            console.log(org);
            
            table = await createTable(`#${org}`, value, columns);
            createColumnFilters(table, '0-11');
        });
    }
}



/**
 * Table upload
 * @param {string} tableID 
 * @param {object} data 
 * @returns 
 */
async function createTable(tableID, data, columns, maxH = '60vh'){
    console.log('create table',tableID, data, columns);
    
    const opt = { ...tableOption };
    // opt.autoWidth = true;
    // opt.ordering     = false;
    // opt.searching    = false;
    // opt.lengthChange = false;
    // opt.pageLength = 15,
    opt.lengthMenu = [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']]
    opt.order = [[0, 'asc']];
    opt.data = data;
    opt.dom  = domScroll.replace('max-h-[60vh]', `max-h-[${maxH}]`);
    // opt.dom= '<"top flex flex-wrap gap-3 mb-2"<"flex-1 join "lf><"table-option join items-center">><"overflow-scroll max-h-[60vh]"rt><"bottom flex justify-between mt-5"pi>'
    // opt.dom= '<"top flex justify-between mb-2"<"join "<"join-item border"l><"join-item border"f>><"table-option flex items-center">><"overflow-scroll max-h-[60vh]"rt><"bottom flex justify-between mt-5"pi><"clear">'
    opt.columns = columns
    // console.log(tableID,opt);
    opt.initComplete = function () {
        // console.log($(tableID+'_wrapper').parent().html())
        const addChe = tableID == '#table_master' ? 
            `<label for="drawer-master" class="drawer-button btn btn-sm join-item btn-primary flex items-center tooltip tooltip-left " data-tip="เพิ่มสารเคมี" id="add-chemical">
                
                <i class="icofont-plus-circle text-xl"></i>
            </label>
            <label for="modal_rebuild" class="drawer-button btn btn-sm join-item btn-neutral flex items-center tooltip tooltip-left" data-tip="เปิดใช้งานสารเคมีเก่า" id="re-chemical">
                <i class="icofont-duotone icofont-rebuild text-xl"></i>
            </label> 
            ` :'';
            // <label for="" class="drawer-button btn btn-sm join-item btn-primary " id="re-chemical" onclick="modal_rebuild.showModal()">
            //     เปิดใช้งานสารเคมีเก่า
            // </label>
            console.log(tableID);
            
        if(tableID != '#table_rebuild' && tableID != '#table_rebuild_sec' && tableID != '#table_submit'){
            
            $(`${tableID}_wrapper .table-option`).append(`
                <label for="" class="btn btn-sm join-item btn-success flex items-center max-w-xs tooltip tooltip-left" data-tip="ดาว์นโหลดไฟล์ Excel" id="exportExcel" tableID="${tableID}">
                    <i class="icofont-file-excel text-xl "></i>
                </label>
                <label for="" class="btn btn-sm join-item btn-error flex items-center  max-w-xs tooltip tooltip-left" data-tip="ดาว์นโหลดไฟล์ PDF" id="exportPDF" tableID="${tableID}">
                    <i class="icofont-file-pdf text-xl" ></i>
                </label>
                ${addChe}`);
            // $(`${tableID}_wrapper .table-option`).append(`
            //     <label for="" class="btn btn-sm join-item btn-primary rounded-full max-w-xs" id="exportExcel" tableID="${tableID}">
            //         Export
            //     </label>
            //     <label for="drawer-item" class="drawer-button btn btn-sm join-item btn-primary rounded-full" id="add-chemical">
            //         เพิ่มสารเคมี
            //     </label>`);
            // $(tableID).find('thead').addClass('sticky top-0 bg-white z-10');
            // $(".dt-length").find('select').addClass("join-item");
            // $(".dt-search").find('input').addClass("join-item");
        }
        initJoin(tableID);
        // this.api().columns()
        // this.api()
        //   .columns()
        //   .every(function () {
        //     const column = this;
        //     const header = $(column.header());
        //     const select = $('<select class="hidden"><option value="">All</option></select>')
        //       .appendTo(header)
        //       .on('change', function () {
        //         const val = $.fn.dataTable.util.escapeRegex($(this).val());
        //         column.search(val ? `^${val}$` : '', true, false).draw();
        //       });

        //     // Add unique values to the dropdown
        //     column
        //       .data()
        //       .unique()
        //       .sort()
        //       .each(function (d) {
        //         select.append(`<option value="${d}">${d}</option>`);
        //       });
        //   });
    };
    // if(tableID.includes('#table_rebuild_sec','#table_submit')){
    // if(tableID == '#table_rebuild_sec' || tableID == '#table_submit' && columns.every(col => col.data !== 'VENDOR')){
    if(tableID == '#table_rebuild_sec'){
        opt.drawCallback = function (settings) {
            var api = this.api();
            var rows = api.rows({ page: 'current' }).nodes();
            var lastGroup = null;

            // ลบกลุ่มที่มีอยู่เดิม
            $(rows).removeClass('group-row');

            // console.log(columns, columns.length);
            // if (columns.every(col => col.data !== 'VENDOR')) {
            //     console.log('Data does not have key VENDOR');
            // } else {
            //     console.log('Data has key VENDOR', api.columns());
            // }
            
            // วนลูปเพื่อสร้างกลุ่ม
            api.column(1, { page: 'current' }).data().each(function (group, i) {
                if (lastGroup !== group) {
                    $(rows).eq(i).before(`
                        <tr class="group-row font-bold" data-group="${group}">
                            <td colspan="${api.columns().count() - 1}">
                                <span class=" sticky left-0">
                                    <i class="icofont-rounded-down"></i> ${group}
                                </span>
                            </td>
                        </tr>
                    `);
                    lastGroup = group;
                }
            });
        }
    }
    opt.columnDefs = [
        { orderable: false, targets: '_all' } // ปิดการเรียงในคอลัมน์ที่กำหนด
    ];
    
    return $(tableID).DataTable(opt);
}

    


/**
 * Group chemical name in table rebuild section
 */
$(document).on('click', 'tr.group-row', function () {
    // if($('#next').attr('step' == 3)){
    //     const t = table_submit;
    // }else{
    //     const t = table_reSec;
    // }
    var group = $(this).data('group'); // รับค่าหมวดหมู่
    console.log(group); 

    var rows = table_reSec.rows({ page: 'current' }).data(); // ดึงข้อมูลปัจจุบันทั้งหมด
    console.log(rows);

    var $icon = $(this).find('i'); // หาไอคอนใน row นี้

    // Toggle ไอคอน
    if ($icon.hasClass('icofont-rounded-right')) {
        $icon.removeClass('icofont-rounded-right').addClass('icofont-rounded-down');
    } else {
        $icon.removeClass('icofont-rounded-down').addClass('icofont-rounded-right');
    }

    table_reSec.rows({ page: 'current' }).every(function () {
        var rowData = this.data(); // ดึงข้อมูลแถวปัจจุบัน
        console.log(rowData);
        
        if (rowData['CHEMICAL_NAME'] === group) { // ตรวจสอบค่า Category
            var rowNode = this.node(); // ดึง DOM node ของแถว
            $(rowNode).toggle('-translate-y-6'); // ซ่อนหรือแสดงแถว
        }
    });
});

/**
 * Export excel
 */
$(document).on('click', '#exportExcel',async function(){
    const tableID = $(this).attr('tableID');
    const table = $(tableID).DataTable();
    var data = table.rows().data().toArray();
    if(data.length > 0){
        data = data.map(item => {
            delete item.STATUS;
            return item;
        });
        let columns = [];
        // const opt = {...writeOpt};
        if(tableID == '#table_master'){
            exportMaster(data);
        }else{
            exportMasterSec(data, tableID.replace('#','').replace('_',' '));
        }
    }else{
        showMessage('ไม่พบข้อมูล','warning');
    }
});

/**
 * Set data to export excel safety chemical master
 * @param {object} data 
 */
async function exportMaster(data){
    // console.log(data);
    
    const template = await getfileInPath('assets/file/Template','Chemical list.xlsx')
    console.log('template',template);
    if(template.length > 0){
        const file = template[0].buffer;
        const opt = {...writeOpt};
        let isProcessed = false;
        opt.startCol = colToNumber('B');
        opt.startRow = 8;
        opt.customSheet = async (workbook) => {
            if (isProcessed) return; // ถ้าเคยทำงานแล้ว ให้หยุด
            isProcessed = true;
            const sheet = workbook.worksheets[0];
            const currentYear = new Date().getFullYear();
            sheet.headerFooter = {
                oddHeader: `&L Rev. No. ${revisionList.MASTER}`, // ตรงกลางของ Header
            };
            sheet.getCell(1, 1).value = `LIST OF SDS_CHEMICAL USER OR HANDLING in ${currentYear}`;
            sheet.autoFilter = {
                from: `A${opt.startRow-1}`, 
                to:   `${numberToCol(Object.keys(data[0]).length+1)}${opt.startRow-1}`, 
            };
            await sheet.duplicateRow(opt.startRow, data.length, true);
            data.forEach( (d, index) => {
                const rowIndex = opt.startRow + index; 
                let colIndex   = opt.startCol;
                const Ystyle       = {fill: fill('FFA4FFA4'), border: border(), alignment: alignment('center')};
                const headSecStyle = {font: {bold: true}, fill: fill('FFBDD7EE'), border: border(), alignment: alignment('center','bottom','90')};
                const thinCen      = {border: border(), alignment: alignment('center')};
                sheet.getCell(rowIndex, 1).value = index+1; // No.
                Object.entries(d).forEach(([key, value]) => {
                    if ((key.includes('SEC') || key.includes('DEPT') || key.includes('Sec')) ) {
                        sheet.getCell(opt.startRow-1, colIndex).value = key;
                        sheet.getCell(opt.startRow-1, colIndex).style = JSON.parse(JSON.stringify(headSecStyle));
                        
                        if(value == 'Y'){
                            sheet.getCell(rowIndex, colIndex).style = JSON.parse(JSON.stringify(Ystyle)); 
                        }else{
                            sheet.getCell(rowIndex, colIndex).style = JSON.parse(JSON.stringify(thinCen));
                        }
                    }

                    if(key.includes('AMEC_SDS_ID')){
                        sheet.getCell(rowIndex, colIndex+2).value = parseInt(value); 
                        sheet.getCell(rowIndex, colIndex+2).style = JSON.parse(JSON.stringify(thinCen));
                    }else if(['RECEIVED_SDS_DATE', 'EFFECTIVE_DATE'].includes(key)){
                        sheet.getCell(rowIndex, colIndex-1).value = value; 
                    }else if(key.includes('UN_CLASS')){
                        sheet.getCell(rowIndex, colIndex).value = parseInt(value); 
                        sheet.getCell(rowIndex, colIndex).style = JSON.parse(JSON.stringify(thinCen));
                    }else if(key.includes('REV')){
                        sheet.getCell(rowIndex, colIndex).style = JSON.parse(JSON.stringify(thinCen));
                        sheet.getCell(rowIndex, colIndex).value = value; 
                    }else{
                        sheet.getCell(rowIndex, colIndex).value = value; 
                    }
                    colIndex++;
                });
            });
        };
        const wb = await writeExcelTemp(file.buffer,opt);
        console.log('wb',wb);
        exportExcel(wb,'Chemical list');
    }else{
        showMessage('ไม่พบไฟล์ Template ติดต่อ admin 2038');
    }
            
}

/**
 * Set data to export excel safety chemical master section
 * @param {object} data 
 * @param {string} fileName e.g. QC1 SEC
 */
async function exportMasterSec(data, fileName){
    console.log(data);
    const template = await getfileInPath('assets/file/Template','Chemical list section.xlsx')
    console.log('template',template);
    if(template.length > 0){
        const file = template[0].buffer;
        const opt = {...writeOpt};
        let isProcessed = false;
        opt.startCol = colToNumber('B');
        opt.startRow = 8;
        opt.customSheet = async (workbook) => {
            if (isProcessed) return; // ถ้าเคยทำงานแล้ว ให้หยุด
            isProcessed = true;
            const sheet = workbook.worksheets[0];
            sheet.headerFooter = {
                oddHeader: `&L Rev. No. ${revisionList[fileName+'.']}`, // ตรงซ้ายของ Header
            };
            sheet.getCell(1, 1).value = `LIST OF ${fileName} CHEMICAL SDS REGISTRATION RECORDS`;

            sheet.autoFilter = {
                from: `A${opt.startRow-1}`, 
                to:   `${numberToCol(Object.keys(data[0]).length+1)}${opt.startRow-1}`, 
            };
            
            console.log(data.length);
            
            sheet.getCell(opt.startRow+1, colToNumber('J')).value = { formula: `=SUM(J${opt.startRow}:J${opt.startRow+data.length-1})` }; 

            await sheet.duplicateRow(opt.startRow, data.length, true);

            data.forEach( (d, index) => {
                const rowIndex = opt.startRow + index; 
                let colIndex   = opt.startCol;
                    
                sheet.getCell(rowIndex, 1).value = index+1; // No.

                Object.entries(d).forEach(([key, value]) => {
                    if(['QTY', 'AMEC_SDS_ID', 'CLASS'].includes(key)){
                        sheet.getCell(rowIndex, colIndex).value = parseInt(value); 
                    }else if(['REC4052', 'REC4054'].includes(key)){
                        sheet.getCell(rowIndex, colIndex).value = parseInt(value) == 1 ? 'OK' : 'N/A';
                    }else{
                        sheet.getCell(rowIndex, colIndex).value = value; 
                    }
                    colIndex++;
                });
            });
        };
        const wb = await writeExcelTemp(file.buffer,opt);
        console.log('wb',wb);
        exportExcel(wb, `Chemical list ${fileName}`)
        // const columns = [
        //     {key:'AMEC_SDS_ID',        header: 'AMEC SDS ID'}, 
        //     {key:'CHEMICAL_NAME',      header: 'Chemical Name'}, 
        //     {key:'REV',                header: 'Revision'},
        //     {key:'EFFECTIVE_DATE',     header: 'Effective Date'}, 
        //     {key:'RECEIVED_SDS_DATE',  header: 'Received SDS Date'}, 
        //     {key:'USED_FOR',           header: 'Used For', className: "min-w-[250px]"}, 
        //     {key:'USED_AREA',          header: 'Used Area'},
        //     {key:'KEEPING_POINT',      header: 'Keeping Point'},
        //     {key:'QTY',                header: 'Quantity'},
        //     {key:'REC4052',            header: 'REC4052'},
        //     {key:'REC4054',            header: 'REC4054'},
        //     {key:'CLASS',              header: 'Class'},
        // ];
        // const opt = {...excelOptions};
        // opt.sheetName = 'TH ตัด';
        // const workbook = defaultExcel(data, columns, opt);
        // exportExcel(workbook, fileName){;
    }else{
        showMessage('ไม่พบไฟล์ Template ติดต่อ admin 2038');
    }
}



$(document).on('click', '#exportPDF',async function(){
    //Lazy Loading เพื่อลดขนาดไฟล์ และเวลาโหลด
    // const { jsPDF } = await import('jspdf');
    // const { autoTable } = await import('jspdf-autotable');

    const ajax = {...ajaxOptions};
    ajax.url = `${host}Chemical/chemicalList/getDataForPDF`;
    ajax.data = {owner: $(this).attr('tableID').replace('#','').replace('_',' ')};
    await getData(ajax).then(async (res) => {
        console.log(res);
        const currentYear = new Date().getFullYear();
        const currentSMon = new Date().toLocaleString('default', { month: 'short' });
        const currentDay  = new Date().getDate();
        let fileName = `List of chemical Rev`;

        // const freesiaUPC = await loadFont(host, 'freesiaUPC/upcfl.ttf')
        const x = 282.5;
        const y = 20;
        const headerHeight = 18;
        
        const classData = res.class.map((item) => {
            return {Class: `${item.TYPE_NAME} ${item.TYPE_DETAIL}`}
        });
        const columnClass = [{ header: "Remark: UN Classification of Hazardous Substances", dataKey: "Class" }];
       
        const doc = new jsPDF({
            orientation: 'landscape',
        })

        doc.addFileToVFS("upcfl.ttf", freesiaUPC);
        doc.addFont("upcfl.ttf", "freesiaUPC", "normal");
        doc.setFont("freesiaUPC");
        
        doc.setFontSize(20)
        doc.text(`LIST OF SDS_CHEMICAL USER OR HANDLING in ${currentYear}`, 6, 10)

        //สร้างรูปทรงสี่เหลี่ยม x y w h
        doc.rect(5, 6, 120, 6);
        doc.rect(273, 6, 19, 22);
        doc.rect(273, 6, 19, 5);

        // // กำหนดสีสำหรับตราประทับ
        // doc.setDrawColor(255, 0, 0); // สีแดงสำหรับเส้น
        // doc.setLineWidth(0.5);

        // // วาดวงกลม (ตำแหน่ง x, y, รัศมี)
        // doc.circle(x, y, 9, 'S'); // 'S' หมายถึงลักษณะการวาดเป็นเส้น
        // // วาดเส้นแนวนอน (ตำแหน่ง x1, y1, x2, y2)
        // doc.line(x-8.5, y-2, x+8.5, y-2); // เส้นด้านบน
        // doc.line(x-8.5, y+3, x+8.5, y+3); // เส้นด้านล่าง
        
        // // ใส่ข้อความในแต่ละช่อง
        // doc.setFontSize(14);
        // doc.text(`S/T ${res.manager[0].SPOSNAME}`, x, 10, {align : 'center'});
        // doc.setTextColor(255, 0, 0); // สีแดงสำหรับข้อความ
        // doc.setFontSize(18);
        // doc.text('AMEC', x, y-3, { align: 'center' }); // ข้อความด้านบน
        // doc.setFontSize(10);
        // doc.text(`${currentDay} ${currentSMon} ${currentYear}`, x, y+1.5, { align: 'center' }); // ข้อความตรงกลาง
        // doc.setFontSize(14);
        // doc.setCharSpace(-0.3);
        // doc.text(res.manager[0].SNAME.split(' ')[0], x, y+7, { align: 'center' }); // ข้อความด้านล่าง
        // doc.setCharSpace(0);
        // doc.setTextColor(0, 0, 0);
        

            
        if(userInfoData.group_code == 'DEV' || userInfoData.group_code == 'ADM'){
            const data = JSON.parse(JSON.stringify(table.rows().data().toArray()));
            data.forEach((item, index) => {
                item.No = index + 1;
            });
            // console.log(data);
            
            const columnsData = [
                { header: 'No.',                      dataKey: 'No' },
                { header: 'RECEIVED SDS DATE',        dataKey: 'RECEIVED_SDS_DATE' },
                { header: 'EFFECTIVE DATE',           dataKey: 'EFFECTIVE_DATE' },
                { header: 'AMEC SDS ID',              dataKey: 'AMEC_SDS_ID' },
                { header: 'Product Code / Item No.',  dataKey: 'PRODUCT_CODE' },
                { header: 'CHEMICAL NAME/TRADE NAME', dataKey: 'CHEMICAL_NAME' },
                { header: 'MANUFACTURER / VENDOR',    dataKey: 'VENDOR' },
                { header: 'PUR. INCHARGE',            dataKey: 'PUR_INCHARGE' },
                { header: 'UN CLASS',                 dataKey: 'UN_CLASS' },
                { header: 'REV',                      dataKey: 'REV' },
            ];
            const colSec = res.sec.map((s) =>{
                return {
                    header:s.OWNER, 
                    dataKey:s.OWNER.slice(0,-1), 
                }
            });
            const columns = [...columnsData,...colSec];
            const columnStyles = {
                No:                { cellWidth: 7 },
                RECEIVED_SDS_DATE: { cellWidth: 18 },
                EFFECTIVE_DATE:    { cellWidth: 18 },
                AMEC_SDS_ID:       { cellWidth: 10 },
                PRODUCT_CODE:      { cellWidth: 15 },
                CHEMICAL_NAME:     { cellWidth: 'auto' },
                VENDOR:            { cellWidth: 'auto' },
                PUR_INCHARGE:      { cellWidth: 'auto' },
                UN_CLASS:          { cellWidth: 4 },
                REV:               { cellWidth: 4 },
            };
            columns.forEach((col, index) => {
                if (index > 9) {
                    columnStyles[col.dataKey] = { cellWidth: 4}; // ตั้งค่า cellWidth
                }
            });
            
            createStamp(doc, x, y, res.manager[0].SPOSNAME, `${currentDay} ${currentSMon} ${currentYear}`, res.manager[0].SNAME.split(' ')[0])
            console.log(optAutoTable);
            
            const opt = {...optAutoTable};
            opt.headStyles.minCellHeight = headerHeight,
            opt.headStyles.overflow = 'hidden',
            opt.startY = 30;
            opt.columns = columns,
            opt.columnStyles = columnStyles, 
            opt.body = data,
            opt.didDrawCell = (data) => {
                if (data.section === 'head' && data.column.index > 7) {
                    const text = columns[data.column.index].header;
              
                    // คำนวณตำแหน่งให้อยู่ตรงกลาง
                    const x = data.cell.x + data.cell.width / 2+1;
                    const y = data.cell.y + data.cell.height - 1; // ลดตำแหน่ง Y เพื่อให้ข้อความหมุนอยู่ด้านล่างเซลล์
              
                    if(data.column.index <= 9){
                        doc.setFillColor(201, 233, 210); 
                    }else{
                        doc.setFillColor(120, 157, 188);
                    }
                    // ลบข้อความเดิม
                    doc.rect(data.cell.x, data.cell.y, data.cell.width, headerHeight, 'F');
              
                    // หมุนข้อความ
                    doc.text(text, x, y, { 
                        angle: 90, 
                    });
                    // Add border
                    doc.setDrawColor(0);
                    doc.setLineWidth(0.1);
                    doc.rect(data.cell.x, data.cell.y, data.cell.width, headerHeight);
                }
            },
            opt.didParseCell = (data) => {
                if (data.section === 'body') {
                    if(data.column.index > 9){
                        if (data.cell.raw === 'Y') {
                            data.cell.styles.fillColor = [77, 235, 191]; // สีพื้นหลังเขียวอ่อน
                        }
                        data.cell.styles.halign = 'center'; // จัดตำแหน่งข้อความตรงกลาง
                    }
                    if(data.column.index < 5 || (data.column.index >= 7 && data.column.index <= 9 )){
                        data.cell.styles.halign = 'center'; // จัดตำแหน่งข้อความตรงกลาง
                    }
                }
                if (data.section === 'head' && data.column.index <= 7){
                    data.cell.styles.fillColor = [173, 216, 230]; // สีพื้นหลังใหม่ (ฟ้าอ่อน)
                    data.cell.styles.overflow = 'linebreak';

                }
            },
            opt.didDrawPage = (data) => {
                doc.setFontSize(10)
                doc.text(`REV. NO. ${revisionList.MASTER}`, 5, 5);
            }
            doc.autoTable(opt);
            // doc.autoTable({
                // headStyles: { 
                //     minCellHeight: headerHeight,
                //     overflow: 'hidden',
                //     halign: 'center', // จัดข้อความแนวนอนให้อยู่ตรงกลาง
                //     valign: 'middle', // จัดข้อความแนวตั้งให้อยู่กึ่งกลาง
                // },
                // styles: { 
                //     font: "freesiaUPC",
                //     fontSize : 12,
                //     cellPadding: 1,
                //     lineColor : 'black',
                //     lineWidth : 0.1,
                //     cellPadding : 0.5
                // },
                // theme: 'plain',
                // margin: { left: 5, right: 5, bottom: 5 },
                // startY: 30, // เริ่มจากตำแหน่งใต้หัวตาราง
                // columns: columns,
                // columnStyles: columnStyles, 
                // body: data,
                // didDrawCell: (data) => {
                //     if (data.section === 'head' && data.column.index > 7) {
                //         const text = columns[data.column.index].header;
                  
                //         // คำนวณตำแหน่งให้อยู่ตรงกลาง
                //         const x = data.cell.x + data.cell.width / 2+1;
                //         const y = data.cell.y + data.cell.height - 1; // ลดตำแหน่ง Y เพื่อให้ข้อความหมุนอยู่ด้านล่างเซลล์
                  
                //         if(data.column.index <= 9){
                //             doc.setFillColor(201, 233, 210); 
                //         }else{
                //             doc.setFillColor(120, 157, 188);
                //         }
                //         // ลบข้อความเดิม
                //         doc.rect(data.cell.x, data.cell.y, data.cell.width, headerHeight, 'F');
                  
                //         // หมุนข้อความ
                //         doc.text(text, x, y, { 
                //             angle: 90, 
                //         });
                //         // Add border
                //         doc.setDrawColor(0);
                //         doc.setLineWidth(0.1);
                //         doc.rect(data.cell.x, data.cell.y, data.cell.width, headerHeight);
                //     }
                // },
                // didParseCell: (data) => {
                //     if (data.section === 'body') {
                //         if(data.column.index > 9){
                //             if (data.cell.raw === 'Y') {
                //                 data.cell.styles.fillColor = [77, 235, 191]; // สีพื้นหลังเขียวอ่อน
                //             }
                //             data.cell.styles.halign = 'center'; // จัดตำแหน่งข้อความตรงกลาง
                //         }
                //         if(data.column.index < 5 || (data.column.index >= 7 && data.column.index <= 9 )){
                //             data.cell.styles.halign = 'center'; // จัดตำแหน่งข้อความตรงกลาง
                //         }
                //     }
                //     if (data.section === 'head' && data.column.index <= 7){
                //         data.cell.styles.fillColor = [173, 216, 230]; // สีพื้นหลังใหม่ (ฟ้าอ่อน)
                //         data.cell.styles.overflow = 'linebreak';

                //     }
                // },
                // didDrawPage: function (data) {
                //     doc.setFontSize(10)
                //     doc.text(`REV. NO. ${revisionList.MASTER}`, 5, 5);
                //   }
            // });
            fileName = `List of chemical Rev ${revisionList.MASTER}.pdf`;
            // const loadFont = async () => {
            //     // const response = await fetch(`${host}assets/dist/fonts/freesiaUPC/upcfl.ttf`); // ใส่ path ของไฟล์ฟอนต์
            //     const response = await fetch(`${host}assets/dist/fonts/freesiaUPC/upcfl.ttf`); // ใส่ path ของไฟล์ฟอนต์
            //     const fontData = await response.arrayBuffer();
            //     const fontBase64 = btoa(
            //       String.fromCharCode(...new Uint8Array(fontData))
            //     );
              
            //     // console.log(response, fontData, fontBase64);
            //     console.log(response, fontData, fontBase64);
                
            //     // เพิ่มฟอนต์ใน jsPDF
            //     const doc = new jsPDF();
            //     doc.addFileToVFS("upcfl.ttf", fontBase64);
            //     doc.addFont("upcfl.ttf", "freesiaUPC", "normal");
            //     doc.setFont("freesiaUPC");
              
            //     // ใช้ฟอนต์ใน AutoTable
            //     doc.autoTable({
            //       styles: { font: "freesiaUPC", fontSize: 12 },
            //       headStyles: { font: "freesiaUPC", fontSize: 14 },
            //     //   body: [
            //     //     { column1: "ภาษาไทย", column2: "ทดสอบ" },
            //     //     { column1: "jsPDF", column2: "ฟอนต์ภาษาไทย" },
            //     //   ],
            //     //   columns: [
            //     //     { header: "หัวข้อ 1", dataKey: "column1" },
            //     //     { header: "หัวข้อ 2", dataKey: "column2" },
            //     //   ],
            //     column: footerText,
            //     body:footerText,
            //     });
            //     doc.save("example-freesiaUPC.pdf");
            // };
            // loadFont();
        }else{
            const tableID = $(this).attr('tableID');
            const table = $(tableID).DataTable();
            const own = tableID.replace('#','').replace('_',' ');
            var data = JSON.parse(JSON.stringify(table.rows().data().toArray()));
            data.forEach((item, index) => {
                item.No = index + 1;
                item.REC4052 = item.REC4052 == '1' ? 'OK' : 'N/A';
                item.REC4054 = item.REC4054 == '1' ? 'OK' : 'N/A';
            });
            console.log(data);
            
            const columnsData = [
                { header: 'No.',                   dataKey: 'No' },
                { header: 'ID',                    dataKey: 'AMEC_SDS_ID' },
                { header: 'ชื่อสารเคมี / ชื่อทางการค้า', dataKey: 'CHEMICAL_NAME' },
                { header: 'REV',                   dataKey: 'REV' },
                { header: 'EFFECTIVE DATE',        dataKey: 'EFFECTIVE_DATE' },
                { header: 'RECEIVED SDS DATE',     dataKey: 'RECEIVED_SDS_DATE' },
                { header: 'การใช้ประโยชน์',          dataKey: 'USED_FOR' },
                { header: 'จุดใช้งาน ',              dataKey: 'USED_AREA' },
                { header: 'จุดจัดเก็บ',               dataKey: 'KEEPING_POINT' },
                { header: 'จำนวน',                 dataKey: 'QTY' },
                { header: 'REC 4052',              dataKey: 'REC4052' },
                { header: 'REC 4054',              dataKey: 'REC4054' },
                { header: 'CLASS',                 dataKey: 'CLASS' },
            ];
            const columnStyles = {
                No:                { cellWidth: 7 },
                AMEC_SDS_ID:       { cellWidth: 10 },
                CHEMICAL_NAME:     { cellWidth: 'auto' },
                REV:               { cellWidth: 10 },
                EFFECTIVE_DATE:    { cellWidth: 18 },
                RECEIVED_SDS_DATE: { cellWidth: 18 },
                USED_FOR:          { cellWidth: 'auto' },
                USED_AREA:         { cellWidth: 'auto' },
                KEEPING_POINT:     { cellWidth: 'auto' },
                QTY:               { cellWidth: 10 },
                REC4052:           { cellWidth: 10 },
                REC4054:           { cellWidth: 10 },
                CLASS:             { cellWidth: 10 },
            };

            createStamp(doc, x, y, res.manager[0].SPOSNAME, res.manager[0].aprDate, res.manager[0].SNAME.split(' ')[0])
            const opt = {...optAutoTable}
            opt.headStyles.minCellHeight = headerHeight,
            opt.headStyles.fillColor = [173, 216, 230]
            opt.startY = 30;
            opt.columns = columnsData,
            opt.columnStyles = columnStyles, 
            opt.body = data,
            opt.didParseCell = (data) => {
                if (data.section === 'body') {
                    if([0, 1, 3, 9, 10, 11, 12].includes(data.column.index)){
                        data.cell.styles.halign = 'center'; // จัดตำแหน่งข้อความตรงกลาง
                    }
                }
            },
            opt.didDrawPage = (data) => {
                doc.setFontSize(10)
                doc.text(`REV. NO. ${revisionList[own+'.']}`, 5, 5);
            }
            doc.autoTable(opt);
            // doc.autoTable({ 
            //     headStyles: { 
            //         minCellHeight: headerHeight,
            //         overflow: 'linebreak',
            //         halign: 'center', // จัดข้อความแนวนอนให้อยู่ตรงกลาง
            //         valign: 'middle', // จัดข้อความแนวตั้งให้อยู่กึ่งกลาง
            //         fillColor : [173, 216, 230]
            //     },
            //     styles: { 
            //         font: "freesiaUPC",
            //         fontSize : 12,
            //         cellPadding: 1,
            //         lineColor : 'black',
            //         lineWidth : 0.1,
            //         cellPadding : 0.5
            //     },
            //     theme: 'plain',
            //     margin: { left: 5, right: 5, bottom: 5 },
            //     startY: 30, // เริ่มจากตำแหน่งใต้หัวตาราง
            //     columns: columnsData,
            //     columnStyles: columnStyles, 
            //     body: data,
            //     didParseCell: (data) => {
            //         if (data.section === 'body') {
            //             if([0, 1, 3, 9, 10, 11, 12].includes(data.column.index)){
            //                 data.cell.styles.halign = 'center'; // จัดตำแหน่งข้อความตรงกลาง
            //             }
            //         }
            //     },
            //     didDrawPage: function (data) {
            //         doc.setFontSize(10)
            //         doc.text(`REV. NO. ${revisionList[own+'.']}`, 5, 5);
            //     }
            // });
            fileName = `${own} List of chemical Rev ${revisionList[own+'.']}.pdf`;
        }
        console.log(optAutoTable);
        
        const optCls = {...optAutoTable};
        optCls.headStyles = { font: "freesiaUPC", fontSize: 14, fillColor: [220, 220, 220]  };
        optCls.columns =  columnClass;
        optCls.body = classData;
        optCls.startY = doc.lastAutoTable.finalY; 

        doc.autoTable(optCls);
        // doc.autoTable({
        //     styles: { 
        //         font: "freesiaUPC",
        //         fontSize : 12,
        //         cellPadding: 1,
        //         lineColor : 'black',
        //         lineWidth : 0.1,
        //     },
        //     headStyles: { font: "freesiaUPC", fontSize: 14, fillColor: [220, 220, 220]  },
        //     theme: 'plain',
        //     columns: columnClass,
        //     body:classData,
        //     margin: { left: 5, right: 5, bottom: 5 },
        //     // showHead: "everyPage", // บังคับให้แสดงหัวตารางในทุกหน้า
        //     startY: doc.lastAutoTable.finalY,
        // });
        doc.save(fileName)
    });

});





 
