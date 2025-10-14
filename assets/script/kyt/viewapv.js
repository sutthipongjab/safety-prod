
import {host, showpreload, hidepreload, checkAuthen, tableOption, showMessage, userInfoData ,ajaxOptions, getData ,setDatePicker, dateFormat, select2Option, domScroll, initJoin, RequiredElement, requiredForm, removeClassError} from "../utils.js";
import DataTable    from "datatables.net-dt";
import 'datatables.net-dt';
import "datatables.net-dt/css/dataTables.dataTables.min.css";
// import "datatables.net-dt/css/dataTables.dataTables.min.css";
import { createColumnFilters } from "../filter.js";
import * as form from "../_form.js";


var table;
var NFRMNO, VORGNO, CYEAR, CYEAR2, NRUNNO, empno, cextData, mode;
$(document).ready( async function () {
    NFRMNO = $(".formno").attr("NFRMNO");
    VORGNO = $(".formno").attr("VORGNO");
    CYEAR = $(".formno").attr("CYEAR");
    CYEAR2 = $(".formno").attr("CYEAR2");
    NRUNNO = $(".formno").attr("NRUNNO");
    empno = $(".user-data").attr("empno");
    cextData = $(".user-data").attr("cextData");
    mode = $(".user-data").attr("mode");
    if (mode == "2") {
      $("#remark-text").removeClass("hidden");
      $("#actions-Form").removeClass("hidden");
    } else {
      $("#remark-text").addClass("hidden");
      $("#actions-Form").addClass("hidden");
    }
    const data = await getData({
        ...ajaxOptions,
        url: `${host}kyt/Form/getKytFrmWaitApv`,
        data: {empno: empno}
    }) 
    table = await createTable(data);
    const flow = await form.showFlow(NFRMNO, VORGNO, CYEAR, CYEAR2, NRUNNO);
    $("#flow").html(flow.html);
});


/**
 * Create table
 * @param {array} data 
 * @returns 
 */
async function createTable(data) {
    const id = "#table";
    const opt = { ...tableOption };
   // console.log(opt);
   opt.ordering = false;
    opt.paging = false; 
    //console.log(opt);
   // opt.dom = domScroll;
    opt.dom  = domScroll.replace('max-h-[60vh]', `max-h-[50vh]`);
    opt.data = data;
    opt.columns = [
        { 
            data: "CYEAR2",         
            title: "<span class='text-base'>Approve</span>",  
            className:"sticky-column",
            render: function (data, type, row, meta) {
                return `<span><input type="hidden" name="cyear2[]" class="cyear2" value="${data}"/><input type="radio" id="apv${data}${row.NRUNNO}" name=apv[${data}_${row.NRUNNO}] value="1" checked></span>`;
            }

        },
        {
            data: "NRUNNO",
            title: "<span class='text-base'>Reject</span>",
            className:"sticky-column",
            render: function (data, type, row, meta){
                return `<span><input type="hidden" name="nrunno[]" class="nrunno" value="${data}"/><input type="radio" id="rej${data}${row.NRUNNO}" name=apv[${row.CYEAR2}_${data}] value="2"></span>`
            }
        },
        { 
            data: "CYEAR",         
            title: "<span class='text-base'>Remark</span>",  
            className:"sticky-column",
            render: function (data, type, row, meta) {
                return `<div class="w-full space-y-1.5">
                <textarea rows="4" class="rem" id="rem${row.CYEAR2}${row.NRUNNO}" name="remark[]" placeholder=""
                    class="w-full aria-disabled:cursor-not-allowed outline-none focus:outline-none text-stone-800 dark:text-black placeholder:text-stone-600/60 ring-transparent border border-stone-200 transition-all ease-in disabled:opacity-50 disabled:pointer-events-none select-none text-sm py-2 px-2.5 ring shadow-sm bg-white rounded-lg duration-100 hover:border-stone-300 hover:ring-none focus:border-stone-400 focus:ring-none peer req"></textarea>
    </div>`;
            }

        },
        {
            data: "EMPNORISK",
            title: "<span class='text-base'>ผู้พบความเสี่ยง</span>",
            className:"sticky-column",
            render: function (data, type, row, meta){
                
                return `<span>(${data})${row.REQ}</span>`
            }
        },
        {
            data: "EMPNOHEAD",
            title: "<span class='text-base'>ชื่อ Leader</span>",
            render: function (data, type, row, meta){
                return `<span>(${data})${row.HEAD}</span>`
            }
        },
        { data: "GRPNAME",  title: "<span class='text-base'>ชื่อกลุ่ม</span>"},
        { data: "SSEC",  title: "<span class='text-base'>แผนก</span>"},
        { data: "ITEMS_NAME",  title: "<span class='text-base'>หมวดหมู่ความเสี่ยงที่พบ</span>"},
        { data: "DTRISK",  title: "<span class='text-base'>รายละเอียดความเสี่ยงที่พบ</span>"},
        { data: "PROTECT",  title: "<span class='text-base'>มาตรการป้องกันที่ดีที่สุด</span>"},
        { data: "PRECIS",  title: "<span class='text-base'>คำย่อ</span>"},
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
    //setBtnFilter(t,2);
   // createColumnFilters(t,'0,2-3');
    return t;
}

/**
 * action form  approve, reject form
 */
 $(document).on("click", "button[name='btnAction']", async function () {
    var cyear2 = $('.cyear2');
    var nrunno = $('.nrunno');
    var rem = $('.rem');
    for (var i = 0; i < cyear2.length; i++) {
        var strc = $(cyear2[i]).val();
        var strn = $(nrunno[i]).val();
        var strrem = $(rem[i]).val();
        if ($('#apv'+strc+strn).is(':checked')) {
            const formStatus = await form.doaction(
                NFRMNO,
                VORGNO,
                CYEAR,
                strc,
                strn,
                'approve',
                empno,
                strrem
              );
        }else{
            const formStatus = await form.doaction(
                NFRMNO,
                VORGNO,
                CYEAR,
                strc,
                strn,
                'reject',
                empno,
                strrem
              ); 
             
        }
    }
    const path = window.location.host.includes("amecwebtest")
    ? "formtest"
    : "form";
    const redirectUrl = `http://webflow.mitsubishielevatorasia.co.th/${path}/workflow/WaitApv.asp`;
    window.location = redirectUrl;
    
     //   console.log(obj);
    //});
    

    
  });
  

