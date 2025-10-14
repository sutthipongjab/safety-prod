import $            from "jquery";
import * as my      from "../utils.js";
import * as form    from "../_form.js";
import DataTable    from "datatables.net-dt";
import { Fancybox } from "@fancyapps/ui";
import "@fancyapps/ui/dist/fancybox/fancybox.css";
import "datatables.net-responsive";
import "datatables.net-responsive-dt";
// import "datatables.net-dt/css/dataTables.dataTables.min.css";
import "datatables.net-responsive-dt/css/responsive.dataTables.min.css";

var NFRMNO, VORGNO, CYEAR, CYEAR2, NRUNNO, tblPatrol;
$(document).ready( async function () {
    NFRMNO = $('.formno').attr('NFRMNO');
    VORGNO = $('.formno').attr('VORGNO');
    CYEAR  = $('.formno').attr('CYEAR');
    CYEAR2 = $('.formno').attr('CYEAR2');
    NRUNNO = $('.formno').attr('NRUNNO');
    const patrol = await getPatrol();
    const flow   = await form.showFlow(NFRMNO, VORGNO, CYEAR, CYEAR2, NRUNNO);
    console.log(patrol);
    
    const dFormat = my.dateFormat(patrol.data[0].PA_DATE);
    $('#monthYearTH').html(`ระบบการตรวจสอบความปลอดภัยของคณะกรรมการความปลอดภัย ประจำเดือน ${dFormat.dateTh}`);
    $('#monthYearEn').html(`SAFETY INSPECTION REPORT BY AMSC'S COMMITTEE IN ${dFormat.dateEn}`);

    var org = patrol.data[0].SSEC;
    $('#org').siblings('strong').html('แผนก(Section)')
    if(org == 'No Section'){
        org = patrol.data[0].SDEPT;
        $('#org').siblings('strong').html('ส่วน(Department)')
        if(org == 'No Department'){
            org = patrol.data[0].SDIV;
            $('#org').siblings('strong').html('ส่วน(Division)')
        }
    }
    $('#org').html(org);
    $('.owner').siblings('strong').html('เจ้าของพื้นที่(Owner Area)')
    $('.owner').html(patrol.data[0].STNAME);
    $('.ownerEn').html(patrol.data[0].SNAME);
    $('.dateFull').siblings('strong').html('วัน/เดือน/ปี')
    $('.dateFull').html(dFormat.fulldate);
    tblPatrol = await createTable(patrol.data);
    const monthYear = $('#monthYearTH').attr('dateth');
    $('.remark-text').removeClass('hidden');
    $('#back').removeClass('hidden');
    
    $('#flow').html(flow.html);
});

/**
 * Back to patrol form
 */
$(document).on('click', '#back', function(){
    my.showpreload();
    window.location.href = `${my.host}patrol/inspection`;
});


/**
 * preview image
 */
$(document).on('click','.preview-img', function(){
    const base64 = $(this).attr('src');
    const img = [{src: `<img src="${base64}" alt="" style="width:100%;">`, type: "html"}];
    new Fancybox(img);
});

async function getPatrol(){
    return new Promise((resolve, reject) => {
        $.ajax({
            url: `${my.host}patrol/inspection/getPatrol/${NFRMNO}/${VORGNO}/${CYEAR}/${CYEAR2}/${NRUNNO}`,
            type: "get",
            dataType: "json",
            beforeSend: function () {
                my.showpreload();
            },
            success: function (res) {
                resolve(res);
            },
            complete: function(xhr, status){
                my.checkAuthen(xhr, status);
                my.hidepreload();
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
    const id = "#tblPatrol";
    const opt = { ...my.tableOption };
    opt.ordering     = false;
    opt.searching    = false;
    opt.lengthChange = false;
    opt.data = data;
    opt.columns = [
        { 
            data: "ITEMS_NAME",     
            title: "หมวด (ITEMS)",          
            className: 'align-top', 
            // width: '25%',
            render: function (data, type, row, meta){
                const ename = row.ITEMS_ENAME != null ? `(${row.ITEMS_ENAME})` : '';
                return `<div>
                            <span>${data}</span>
                            <span class='text-blue-500'>${ename}</span>
                        </div>`;
            }
        },
        { 
            data: "AREA_NAME",      
            title: "บริเวณ AREA (DETECTED)", 
            className: 'align-top', 
            // width: '25%',
            render: function (data, ytpe, row, meta){
                return `<div>
                            <span>${data}</span>
                            <span class='text-blue-500'>(${row.AREA_ENAME})</span>
                        </div>`
            }
        },
        { data: "PA_DETECTED",  
          title: "สิ่งที่ควรปรับปรุง (ITEMS DETECTED)",
        //   width: '15%',
          className: 'align-top', 
          render: function (data, type, row, meta){
            if(row.baseURL == '') return data;
            return `<div class='flex flex-col'>
                        <div>${data}</div>
                        
                        <label class="form-control w-full max-w-sm showimg  tooltip tooltip-warning"  data-tip="คลิกเพื่อดูรูป">
                        <div class="my-5 drop-shadow-lg border-image">
                            <img class="rounded-lg preview-img" src="${row.baseURL}" />
                        </div>
                        </label>
                    </div>`
          }
        },
        { data: "CLASS", title: "CLASS", className: 'align-top !text-center', width: '5%'},
        { 
            data: "PA_SUGGESTION" , 
            title: "ข้อเสนอแนะในการปรับปรุง (SUGGESTION)", 
            className: 'align-top', 
            // width: '25%',
            render: function (data, type, row, meta){
                const ndata = data == null ? '-': data; 
                return `<div class="">
                            ${ndata}
                        </div>`;
            }
        },
        {
            data: "PA_CORRECTIVE" , 
            title: "การดำเนินการในการปรับปรุง (CORRECTIVE ACTION)",
            className: "align-top",
            // width : '5%',
            render: function (data, type, row, meta){
                const ndata = data == null ? '-': data; 
                return `<div class="">
                            ${ndata}
                        </div>`;
            }
        },
        {
            data: "PA_FINISH_DATE" , 
            title: "วันแล้วเสร็จ (FINISH DATE)",
            className: "align-top",
            // width : '5%',
            render: function (data, type, row, meta){
                const ndata = data == null ? '-' : data; 
                return `<div class="">
                            ${ndata}
                        </div>`;
            }
        },
        {
            data: "PA_MORNING_TALK" , 
            title: "วันที่ชี้แจง (MORNING TALK)",
            className: "align-top",
            // width : '5%',
            render: function (data, type, row, meta){
                const ndata = data == null ? '-' : data; 
                return `<div class="">
                            ${ndata}
                        </div>`;
            }
        },
        {
            data: "PA_AUDIT_EVALUATE" , 
            title: "ผลการประเมิน",
            className: "align-top",
            // width : '5%',
            render: function (data, type, row, meta){
                const ndata = data == null ? '-': (data == 1 ? 'Yes' : 'No');
                return `<div class="audit-result">
                            ${ndata}
                        </div>`;
            }
        }
    ];
    return $(id).DataTable(opt);
}

