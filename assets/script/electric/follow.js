
import {host, tableOption, showMessage, getData, ajaxOptions, setBtnFilter, domScroll, initJoin} from "../utils.js";
import flatpickr    from "flatpickr";
import monthSelectPlugin from "flatpickr/dist/plugins/monthSelect";
import DataTable    from "datatables.net-dt";
import "datatables.net-responsive";
import "datatables.net-responsive-dt";
// import "datatables.net-dt/css/dataTables.dataTables.min.css";
import "datatables.net-responsive-dt/css/responsive.dataTables.min.css";
import "flatpickr/dist/flatpickr.min.css";
import "flatpickr/dist/plugins/monthSelect/style.css";
import { createColumnFilters } from "../filter.js";


var table;
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
    const data = await getDataForm(monthYear.val());
    console.log(data);
    
    table = await createTable(data);

});


$(document).on('change', '.monthYearPicker', async function () {
    const data = await getDataForm($(this).val());
    table = await createTable(data);
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
 * Fetches patrol data for a given month and year.
 *
 * @param {string} monthYear - The month and year for which to fetch patrol data, formatted as "MM-YYYY".
 * @returns {Promise<Object>} A promise that resolves to the patrol data.
 */
async function getDataForm(monthYear) {
    return getData({
        ...ajaxOptions,
        url: `${host}electric/follow/getData`,
        data: { monthYear: monthYear },
    });
}

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
                return `<a href="${host}electric/webflow/index/${data}" target="_blank" class="text-blue-500 hover:underline" onclick="window.open(this.href, 'popup', 'width=' + window.screen.width + ',height=' + window.screen.height);">${data}</a>`;
            }

        },
        {
            data: "FORMNO",
            title: 'Link',
            width: '5%',
            render: function (data, type, row, meta){
                return `<a href="#" id="copyLink" class="text-blue-500 hover:underline tooltip" data-tip="Copy to clipboard" data-link="${host}electric/webflow/index/${data}" ><i class="icofont-link"></i></a>`
            }
        },
        { data: "AREA_MANAGER",  title: "เจ้าของพื้นที่",  width: '25%'},
        { data: "AREA_NAME",  title: "พื้นที่",  width: '25%'},
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
    createColumnFilters(t,'0,2-4');

    return t;
}

