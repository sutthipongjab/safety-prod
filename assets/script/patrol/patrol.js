import $            from "jquery";
import {showpreload, hidepreload, host, checkAuthen, tableOption, showMessage} from "../utils.js";
import flatpickr    from "flatpickr";
import monthSelectPlugin from "flatpickr/dist/plugins/monthSelect";
import DataTable    from "datatables.net-dt";
import "datatables.net-responsive";
import "datatables.net-responsive-dt";
// import "datatables.net-dt/css/dataTables.dataTables.min.css";
import "datatables.net-responsive-dt/css/responsive.dataTables.min.css";
import "flatpickr/dist/flatpickr.min.css";
import "flatpickr/dist/plugins/monthSelect/style.css";


var tblPatrol;
$(document).ready( async function () {
    const monthYear = $('.monthYearPicker');
    flatpickr(monthYear, {
        plugins: [
            new monthSelectPlugin({
                shorthand: true, //defaults to false
                dateFormat: "Y-m",
            })
        ],
        defaultDate: new Date(), // initial date
    });
    console.log(monthYear.val());
    const data = await getData(monthYear.val());
    console.log(data);
    
    tblPatrol = await createTable(data);
    // console.log(data);
});

$(document).on('click', '#add-patrol', function () {
    showpreload();
    window.location.href = `${host}patrol/inspection/createPatrol`;
});

$(document).on('change', '.monthYearPicker', async function () {
    const data = await getData($(this).val());
    tblPatrol = await createTable(data);
});

$(document).on('click', '#copyLink', function(){
    const link = $(this).attr('data-link');
    navigator.clipboard.writeText(link).then(() => {
        // showMessage('Copy link on clipboard','info')
        // $(this).fadeTo(1000, 0.5).promise().done(() => {
        //     $(this).attr('data-tip', 'Copied');
        // });
        // setTimeout(() => {
        //     $(this).fadeTo(1000, 1.0).promise().done(() =>{
        //         $(this).attr('data-tip', 'Copy to clipboard');
        //     });
        // }, 3000);
        $(this).fadeTo(300, 0, 'swing').promise().done(() => {
            $(this).attr('data-tip', 'Copied').fadeTo(300, 1, 'swing');
            setTimeout(() => {
            //     $(this).fadeTo(800, 1.0, 'swing').promise().done(() => {
                    $(this).attr('data-tip', 'Copy to clipboard');
            //     });
            }, 3000);
        });
    
      }).catch(err => {
        showMessage('Failed to copy link');
      });
});

/**
 * Fetches patrol data for a given month and year.
 *
 * @param {string} monthYear - The month and year for which to fetch patrol data, formatted as "MM-YYYY".
 * @returns {Promise<Object>} A promise that resolves to the patrol data.
 */
async function getData(monthYear) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: `${host}patrol/inspection/getPatrols`,
            type: "post",
            dataType: "json",
            data: { monthYear: monthYear },
            beforeSend: function () {
                showpreload();
            },
            success: function (res) {
                console.log("Response from API:", res); // ตรวจสอบผลลัพธ์
                resolve(res);
            },
            error: function (xhr, status, error) {
                console.error("Error fetching data:", error);
                reject(error); // ส่งข้อผิดพลาดกลับ
            },
            complete: function (xhr, status) {
                checkAuthen(xhr, status);
                hidepreload();
            },
        });
    });
}

/**
 * Create table
 * @param {array} data 
 * @returns 
 */
async function createTable(data) {
    const id = "#tblPatrol";
    const opt = { ...tableOption };
    // opt.ordering     = false;
    opt.searching    = false;
    opt.lengthChange = false;
    opt.data = data;
    opt.columns = [
        { 
            data: "FORMNO",         
            title: "FORM NO.",  
            width: '20%',
            render: function (data, type, row, meta) {
                return `<a href="${host}patrol/inspection/detail/${row.NFRMNO}/${row.VORGNO}/${row.CYEAR}/${row.CYEAR2}/${row.NRUNNO}" class="text-blue-500 hover:underline">${data}</a>`;
            }

        },
        {
            data: null,
            title: 'Link',
            width: '5%',
            render: function (data, type, row, meta){
                return `<a href="#" id="copyLink" class="text-blue-500 hover:underline tooltip" data-tip="Copy to clipboard" data-link="${host}patrol/webflow/index/${row.FORMNO}" ><i class="icofont-link"></i></a>`
            }
        },
        { data: "OWNER_SECTION",  title: "เจ้าของพื้นที่",  width: '25%'},
        { data: "PA_DATE",  title: "วันที่ตรวจสอบ", width: '15%'},
        { data: "PA_AUDIT",  title: "ผู้ตรวจสอบ", width: '25%'},
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
        $(".table-option").append(`
            <label for="" class="btn btn-sm btn-primary  max-w-xs" id="add-patrol">
                เพิ่มรายการตรวจสอบ
            </label>`);
    };
    return $(id).DataTable(opt);
}

