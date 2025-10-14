import $ from "jquery";
import DataTable    from "datatables.net-dt";
import "datatables.net-responsive";
import "datatables.net-dt/css/dataTables.dataTables.min.css";
import "datatables.net-responsive-dt/css/responsive.dataTables.min.css";
import select2 from "select2";
import "select2/dist/css/select2.min.css";
// import ExcelJS from '../exceljs';
import {excelOptions, exportExcel, defaultExcel} from '../_excel.js';
import {
    host, 
    showpreload, 
    hidepreload, 
    showMessage, 
    checkAuthen, 
    setDatePicker, 
    select2Option,
    RequiredElement,
    requiredForm,
    tableOption
} from "../utils.js";
import {
    // extractUser,
    // getDivision,
    // getDepartment,
    getSection,
    // getEmployee,
  } from "../webservice.js";


var patrolData, fileName, fp;
$(document).ready(async function () {
    const section = await getSection();
    fp = setDatePicker({ 
        mode: "range", 
        dateFormat: "d/m/Y",
    });
    section.forEach(sec => {
        $('#sec').append(new Option(sec.SSEC, sec.SSECCODE));
    });
    const s2opt = { ...select2Option };
    s2opt.placeholder = 'เลือกแผนก';
    $('#sec').select2(s2opt);
});

$(document).on('click', '#dateClear', function(){
    $('#rangDate').val('');
    // console.log(fp);
    fp.clear();
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

$(document).on('click', '#search',async function(){
    const sec      = $('#sec');
    const rangDate = $('#rangDate');
    const pClass   = $("input[name='class']:checked");
    
    // const fields = [
    //     { element: rangDate, message: 'กรุณากรอกวันที่' },
    //     { element: sec, message: 'กรุณาเลือกแผนก' },
    //     { element: pClass, message: 'กรุณาเลือก Class' },
    // ];
    // requiredForm('.search-input', fields);

    // for (const field of fields) {
    //     if (!field.element.val()) {
    //         showMessage(field.message, 'warning');
    //         return;
    //     }
    // }
    const pdate = rangDate.val().split(' to ') 
    const sdate = pdate[0];
    const edate = pdate.length == 2 ? pdate[1] : pdate[0];

    fileName = `Patrol Report ${sdate} ${pdate.length == 2 ? `- ${pdate[1]}` : ''}`;
    console.log(fileName);
    
    console.log(pdate, sdate, edate, sec.val(), pClass.val()||'');
    patrolData = await getData(sdate, edate, sec.val(), pClass.val()||'')
    createTable('#tbl_patrol', patrolData);
    $('.tblData').removeClass('hidden');    
});

$(document).on('click','#exportExcel', function(){
    const opt = {...excelOptions};
    opt.sheetName = 'Patrol Report';
    const columns = [
         {header : 'ชื่อหมวดหมู่' , key : 'ITEMS_NAME'},
         {header : 'เจ้าของพื้นที่' , key : 'OWNER_SECTION'},
         {header : 'ประเภท'    , key : 'CLASS'},
         {header : 'จำนวน'     , key : 'AMOUNT'},
        ];
    const workbook = defaultExcel(patrolData, columns, opt);
    exportExcel(workbook, fileName);
});

$(document).on('change', 'select.req', async function(){
    const select = $(this);
    RequiredElement(select);
});

function getData(sdate, edate, sec, pClass){
    return new Promise((resolve) => {
        $.ajax({
            url: `${host}patrol/exportData/getData`,
            type: "post",
            dataType: "json",
            data:{
                sdate : sdate, 
                edate : edate, 
                sec   : sec, 
                pClass : pClass
            },
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

function createTable(id, data){
    const opt = { ...tableOption };
    opt.ordering     = false;
    opt.searching    = false;
    opt.lengthChange = false;
    opt.info = false;
    opt.data = data;
    opt.columns = [
        { data: "ITEMS_NAME",    title: "ชื่อหมวดหมู่", width: '25%'},
        { data: "OWNER_SECTION", title: "เจ้าของพื้นที่", width: '25%'},
        { data: "CLASS",         title: "ประเภท",    width: '25%'},
        { data: "AMOUNT",        title: "จำนวน",     width: '25%'},
    ];
    opt.initComplete = function () {
        $(".table-option").append(`
            <label for="" class="btn btn-sm btn-primary  max-w-xs" id="exportExcel">
                Export
            </label>`);
    };
    return $(id).DataTable(opt);
}