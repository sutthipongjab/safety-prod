import $ from "jquery";
import DataTable    from "datatables.net-dt";
import "datatables.net-responsive";
import "datatables.net-dt/css/dataTables.dataTables.min.css";
import "datatables.net-responsive-dt/css/responsive.dataTables.min.css";
import select2 from "select2";
import "select2/dist/css/select2.min.css";
// import ExcelJS from '../exceljs';
import {userInfoData } from "../utils.js";
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


var kytData, fileName, fp;
$(document).ready(async function () {
    fp = setDatePicker({ 
        mode: "range", 
        dateFormat: "d/m/Y",
    });
    
    if((userInfoData.group_code == "DEV")||(userInfoData.group_code == "ADM"))
    {
        var section = await getSection();
    }else
    {   
        var section = await getSectionEmp(userInfoData.sempno);
    }
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
    if (sec.val()=="") {
        showMessage("กรุณาระบุแผนก", 'warning');
        return false;
    }
    
    const pdate = rangDate.val().split(' to ') 
    const sdate = pdate[0];
    const edate = pdate.length == 2 ? pdate[1] : pdate[0];

    fileName = `KYT Report ${sdate} ${pdate.length == 2 ? `- ${pdate[1]}` : ''}`;
   // console.log("xxxxx"+fileName);

    kytData = await getData(sdate, edate, sec.val())
    createTable('#tbl_kyt', kytData);
    $('.tblData').removeClass('hidden');    
});

$(document).on('click','#exportExcel', function(){
    const opt = {...excelOptions};
    opt.sheetName = 'KYT Report';
    const columns = [
         {header : 'ลำดับที่' , key : 'NO'},
         {header : 'วัน/เดือน/ปี' , key : 'DREQDATE'},
         {header : 'ชื่อผู้พบความเสี่ยง'    , key : 'EMPNORISKNAME'},
         {header : 'หัวหน้ากลุ่ม(Leader)'     , key : 'LEADDER'},
         {header : 'หมวดหมู่ความเสี่ยง'     , key : 'ITEMS_NAME'},
         {header : 'รายละเอียดที่พบ'     , key : 'DTRISK'},
         {header : 'มาตรการป้องกัน'     , key : 'PROTECT'},
         {header : 'คำย่อ KYT'     , key : 'PRECIS'},
        ];
    const workbook = defaultExcel(kytData, columns, opt);
    exportExcel(workbook, fileName);
});

$(document).on('change', 'select.req', async function(){
    const select = $(this);
    RequiredElement(select);
});

function getSectionEmp(empno)
{
    return new Promise((resolve) => {
        $.ajax({
            url: `${host}kyt/exportData/getSectionEmp`,
            type: "post",
            dataType: "json",
            data:{
                empno : empno
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

function getData(sdate, edate, sec){
    return new Promise((resolve) => {
        $.ajax({
            url: `${host}kyt/exportData/getData`,
            type: "post",
            dataType: "json",
            data:{
                sdate : sdate, 
                edate : edate, 
                sec   : sec
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
    console.log("create table");
    const opt = { ...tableOption };
    opt.ordering     = false;
    opt.searching    = false;
    opt.lengthChange = false;
    opt.info = false;
    opt.data = data;
    opt.columns = [
        { data: "EMPNOHEAD",    title: "<span class='text-base'>ชื่อ Leader</span>", width: '15%'
            ,
            render: function (data, type, row, meta){
                return `<span>(${data}) ${row.LEADDER}</span>`
            }
        },
        { data: "ITEMS_NAME", title: "<span class='text-base'>หมวดหมู่ความเส่ียง</span>", width: '25%'},
        { data: "DTRISK",         title: "<span class='text-base'>รายละเอียดความเสี่ยง</span>",    width: '25%'},
        { data: "PROTECT",        title: "<span class='text-base'>มาตราการป้องกัน</span>",     width: '25%'},
        { data: "PRECIS",        title: "<span class='text-base'>คำย่อ KYT</span>",     width: '10%'},
    ];
    opt.initComplete = function () {
        $(".table-option").append(`
            <label for="" class="btn btn-sm btn-primary  max-w-xs" id="exportExcel">
                Export
            </label>`);
    };
    return $(id).DataTable(opt);
}