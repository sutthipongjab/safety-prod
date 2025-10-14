import $            from "jquery";
import DataTable    from "datatables.net-dt";
import  { checkAuthen, domScroll, hidepreload, host, initJoin, showpreload, tableOption } from "../utils.js";
import "datatables.net-responsive";
import "datatables.net-responsive-dt";
// import "datatables.net-dt/css/dataTables.dataTables.min.css";
import "datatables.net-responsive-dt/css/responsive.dataTables.min.css";
import {createColumnFilters} from '../filter.js';

var tableFollow;
$(document).ready(async function () {
    const data = await getData();
    
    tableFollow = await createTable(data);
    
    createColumnFilters(tableFollow,'0-5');
});


async function getData(){
    return new Promise((resolve) => {
        $.ajax({
            url: `${host}kyt/follow/getData`,
            type: "post",
            dataType: "json",
            processData: false, 
            contentType: false,
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
    console.log(data);
    
    const id = "#table_Follow";
    const opt = { ...tableOption };
    // opt.ordering     = false;
    // opt.searching    = false;
    // opt.lengthChange = false;
    opt.dom  = domScroll;
    opt.data = data;
    opt.columns = [
        { 
            data: "FORMNO",     
            title: "FORM NO.",     
            className: 'align-top whitespace-nowrap', 
            width: '20%',
            render: function (data, type, row, meta) {
                        return `<a href="${host}kyt/form?no=${row.NFRMNO}&orgNo=${row.VORGNO}&y=${row.CYEAR}&y2=${row.CYEAR2}&runNo=${row.NRUNNO}&webSafety=1&empno=" class="text-blue-500 hover:underline">${data}</a>`;
            }
            
        },
        { data: "GRPNAME",  title: "ชื่อกลุ่ม",  className: 'align-top whitespace-nowrap', width: '20%'},
        { data: "RISKTNAME",  title: "ผู้พบความเสี่ยง",  className: 'align-top whitespace-nowrap', width: '20%'},
        { data: "SSEC",       title: "แผนกที่พบ",     className: 'align-top whitespace-nowrap', width: '20%'},
        { data: "ITEMS_NAME", title: "ความเสี่ยงที่พบ",  className: 'align-top whitespace-nowrap', width: '20%'},
        { 
            data: "CST",        
            title: "Status",       
            className: 'align-top', 
            width: '20%',
            render:function(data, type, row, meta){
                return `<div class="text-running">Running</div>`
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
