
import {host, tableOption, showMessage, getData, ajaxOptions, setBtnFilter, domScroll, initJoin, showpreload} from "../utils.js";
import DataTable    from "datatables.net-dt";
import "datatables.net-responsive";
import "datatables.net-responsive-dt";
// import "datatables.net-dt/css/dataTables.dataTables.min.css";
import "datatables.net-responsive-dt/css/responsive.dataTables.min.css";
import { createColumnFilters } from "../filter.js";


var table;
$(document).ready( async function () {
    const fyear = parseInt($('#fyear').html());
    const data = await getData({
        ...ajaxOptions,
        url: `${host}chemical/follow/getFormData`,
        data: {fyear: fyear}
    })
    console.log(data);
    
    table = await createTable(data);

});


$(document).on('click', '#previousFY',async function(){
    showpreload();
    const e = $('#fyear');
    const fyear = parseInt(e.html()) - 1;
    e.html(fyear)
    window.location.assign(`${host}chemical/follow/index/${fyear}`)
});

$(document).on('click', '#nextFY',async function(){
    showpreload();
    const e = $('#fyear');
    const fyear = parseInt(e.html()) + 1;
    e.html(fyear)
    window.location.assign(`${host}chemical/follow/index/${fyear}`)
});


$(document).on('click', '#copyLink', function(){
    const link = $(this).attr('data-link');
    navigator.clipboard.writeText(link).then(() => {
        $(this).fadeTo(300, 0, 'swing').promise().done(() => {
            $(this).attr('data-tip', 'Copied').fadeTo(300, 1, 'swing');
            setTimeout(() => {
                $(this).attr('data-tip', 'Copy to clipboard');
            }, 3000);
        });
    
      }).catch(err => {
        showMessage('Failed to copy link');
      });
});

/**
 * Create table
 * @param {array} data 
 * @returns 
 */
async function createTable(data) {
    const id = "#table";
    const opt = { ...tableOption };
    opt.dom = domScroll;
    opt.data = data;
    opt.columns = [
        { 
            data: "FORMNO",         
            title: "FORM NO.",  
            width: '20%',
            render: function (data, type, row, meta) {
                return `<a href="${host}chemical/webflow/index/${data}" target="_blank" class="text-blue-500 hover:underline" onclick="window.open(this.href, 'popup', 'width=' + window.screen.width + ',height=' + window.screen.height);">${data}</a>`;
            }

        },
        {
            data: "FORMNO",
            title: 'Link',
            width: '5%',
            render: function (data, type, row, meta){
                return `<a href="#" id="copyLink" class="text-blue-500 hover:underline tooltip" data-tip="Copy to clipboard" data-link="${host}chemical/webflow/index/${data}" ><i class="icofont-link"></i></a>`
            }
        },
        { data: "OWNER",  title: "เจ้าของพื้นที่",  width: '20%'},
        { data: "CHEMICAL_NAME",  title: "ชื่อสารเคมี",  width: '20%'},
        { 
            data: "FORM_TYPE",  
            title: "ประเภทฟอร์ม",  
            width: '20%',
            render: function(data, type, row, meta){
                return data == 1 ? 'ขอใช้สารเคมี' : (data == 2 ? 'ขอเปลี่ยนแปลงข้อมูลสารเคมี' : 'ขอยกเลิกใช้สารเคมี');
            }
        },
        { 
            data: "CST",  
            title: "status", 
            width: '10%',
            render: function (data, type, row, meta){
                const status = data == 1 ? 'Running' : (data == 2 ? 'Approve' : 'Reject');
                const cls    = data == 1 ? 'text-running' : (data == 2 ? 'text-approve' : 'text-reject');
                return `<span class='${cls}'>${status}</span>`
            }
        },
    ];
    opt.initComplete = function () {
         $(".dt-length").addClass("hidden");
         $(".dt-search").addClass("hidden");
        initJoin(id);
    };
    opt.columnDefs = [
        { orderable: false, targets: '_all' } // ปิดการเรียงในคอลัมน์ที่กำหนด
    ];
    const t = $(id).DataTable(opt);
    setBtnFilter(t,2);
    createColumnFilters(t,'0,2-3');
    return t;
}

