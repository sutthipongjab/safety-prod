import $            from "jquery";
import {host, scheme, RequiredElement, showMessage, sendmail, sendMail, mailOpt, removeClassError,  openModal, closeModal,  select2Option, setDatePicker, dateFormat, showpreload, hidepreload, checkAuthen, tableOption} from "../utils.js";
import * as form    from "../_form.js";
import {checkFileFormat, fileImgFormat}    from "../_file.js";
// import {dateFormat} from "./inspection.js";
// import moment from "moment/moment.js";
import DataTable    from "datatables.net-dt";
import { Fancybox } from "@fancyapps/ui";
import select2      from "select2";
import "select2/dist/css/select2.min.css";
import "@fancyapps/ui/dist/fancybox/fancybox.css";
import "datatables.net-responsive";
import "datatables.net-responsive-dt";
// import "datatables.net-dt/css/dataTables.dataTables.min.css";
import "datatables.net-responsive-dt/css/responsive.dataTables.min.css";

var NFRMNO, VORGNO, CYEAR, CYEAR2, NRUNNO, empno, cextData, tblPatrol;
$(document).ready( async function () {
    NFRMNO = $('.formno').attr('NFRMNO');
    VORGNO = $('.formno').attr('VORGNO');
    CYEAR  = $('.formno').attr('CYEAR');
    CYEAR2 = $('.formno').attr('CYEAR2');
    NRUNNO = $('.formno').attr('NRUNNO');
    empno  = $('.user-data').attr('empno');
    cextData  = $('.user-data').attr('cextData');
    const patrol = await getPatrol();
    const flow   = await form.showFlow(NFRMNO, VORGNO, CYEAR, CYEAR2, NRUNNO);
    // console.log(patrol);
    
    await setData(patrol);
    tblPatrol = await createTable(patrol.data);
    const monthYear = $('#monthYearTH').attr('dateth');
    
    $('#flow').html(flow.html);
});

/**
 * preview image
 */
$(document).on('click','.preview-img', function(){
    const base64 = $(this).attr('src');
    const img = [{src: `<img src="${base64}" alt="" style="width:100%;">`, type: "html"}];
    new Fancybox(img);
});

$(document).on('change', '#employee', async function(){
    const select = $(this);
    RequiredElement(select);
});

/**
 * action form  approve, reject form
 */
$(document).on('click', "button[name='btnAction']", async function(){
    const action = $(this).val();
    const remark = $('#remark').val();
    console.log('approve',action);
    

    // console.log(action, remark, empno);
    if(action == 'approve'){
        switch (cextData) {
            case '01':
                const cor = await correctiveAction();
                if(cor) return;
                break;
            case '02':
                const dcor = await correctiveDetail();
                console.log(dcor);
                
                if(dcor) return;
                    
                break;
            case '03':
                const eva = await evaluate();
                if(eva) return;
                break;
        
            default:
                break;
        }
    }

    const formStatus = await form.doaction(NFRMNO, VORGNO, CYEAR, CYEAR2, NRUNNO, action, empno, remark);

    const path = window.location.host.includes('amecwebtest') ? 'formtest' : 'form';
    const redirectUrl = `http://webflow.mitsubishielevatorasia.co.th/${path}/workflow/WaitApv.asp`;
    console.log(redirectUrl);
    

    if(formStatus.status == true ){
        window.location = redirectUrl;
    }else{
        showMessage('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
        // sendmail(`Patrol Form Error :: do action ${JSON.stringify(formStatus)}`);
        const mail = {...mailOpt};
        mail.BODY = [
            mailForm(NFRMNO, VORGNO, CYEAR, CYEAR2, NRUNNO,'Do action'),
            `Patrol Form Error : do action ${JSON.stringify(formStatus)}`
        ];
        sendMail(mail);
    }
});

/**
 * Choose corrective person
 */
$(document).on('click', '.corrective-action', function(){
    const data = tblPatrol.row($(this).parents("tr")).data();
    console.log(data);
    const list = $(".list-corrective");
    for (const [key, value] of Object.entries(data)) {
        const target = list.find(`[data-map=${key}]`);
        removeClassError(target);
        // console.log(key, value);
        // console.log(target);
        target.val(value);
    }
    console.log('ข้อมูลก่อนการอัปเดต:', tblPatrol.data().toArray());

   console.log('data:', data);
   
    const img = $('#PA_IMAGE_AFTER');
    if(data.PA_IMAGE_AFTER != null){
        console.log('not null');
        
        const dataTransImage = new DataTransfer();
        dataTransImage.items.add(data.PA_IMAGE_AFTER);
        img[0].files =  dataTransImage.files;
        img.trigger('change');
    }else{
        img.val('');
        removeClassError(img);
        $('#showimg').attr('src','');
        $('.showimg-drawer').addClass('hidden');
    }
});

var imageUrl;
var imageFile;
$(document).on('change', '#PA_IMAGE_AFTER', function(e){
    checkFileFormat($(this), fileImgFormat , 'ไฟล์ไม่ถูกต้อง กรุณาแนบไฟล์รูปเท่านั้น', 'toast-start');
    const files = e.target.files;
    if (files.length > 0) {
            const file = files[0];
            // convertImgToBase64URL(file);
            imageUrl = URL.createObjectURL(file);
            
            imageFile = file;
            $('#showimg').attr('src',imageUrl);
            $('.showimg-drawer').removeClass('hidden');
    }else{
        $('#showimg').attr('src','');
        $('.showimg-drawer').addClass('hidden');
    }
});

/**
 * add corrective detail
 */
$(document).on('click', '#save-corrective', async function(){

    const id    = $('#PA_ID');
    const corr  = $('#PA_CORRECTIVE');
    const fdate = $('#PA_FINISH_DATE');
    const mtalk = $('#PA_MORNING_TALK');

    var checkVal = true;
    const list = $(".list-corrective");
    list.find(".req").map(function (i, el) {
        // console.log(el);
        
        if ($(el).val() == "") {
            checkVal = false;
            RequiredElement($(el));
        }
    });
    if (!checkVal) {
        showMessage("กรุณากรอกข้อมูลให้ครบถ้วน", 'warning');
        return;
    }
    

    // ดึงข้อมูลของแถวแรกที่ตรงกับเงื่อนไข
    tblPatrol.row(function (idx, data, node) {
        if(data.PA_ID === id.val()){
            data.PA_CORRECTIVE = corr.val();
            data.PA_EMP_CORRECTIVE = empno;
            data.PA_FINISH_DATE = fdate.val();
            data.PA_MORNING_TALK = mtalk.val();
            data.PA_IMAGE_AFTER = imageFile;
            data.PA_IMAGE_AFTERURL = imageUrl; 
            tblPatrol.row(idx).data(data).draw(false);
            $(node).find('td').eq(1).find('i').replaceWith('<i class="icofont-ui-check text-approve"></i>'); 
        }
        // return data.PA_ID === id;
    });
    // console.log('ข้อมูลหลังการอัปเดต:', tblPatrol.data().toArray());
    $("#drawer-corrective").prop("checked", false);
});

/**
 * Open modal evaluate 
 */
$(document).on('click', '.evaluate-btn', function(){
    const data = tblPatrol.row($(this).parents("tr")).data();
    console.log(data);
    const id = data.PA_ID;
    console.log(id);
    $('#evalSubmit').attr('PA_ID', id);
    $(`input[name="PA_AUDIT_EVALUATE"]`).prop('checked', false);
    removeClassError($('input[name="PA_AUDIT_EVALUATE"]'));
    openModal('#modal_evaluate');
});

/**
 * Choose evaluate yes or no
 */
$(document).on('click', '#evalSubmit',  function(){
    const id = $(this).attr('PA_ID');
    RequiredElement($('input[name="PA_AUDIT_EVALUATE"]'));
    const check  = $('input[name="PA_AUDIT_EVALUATE"]:checked');
    console.log( check);
    
    if(!check.val()){
        showMessage('กรุณาเลือกผลการประเมิน', 'warning');
        return;
    }

    tblPatrol.row(function (idx, data, node) {
        if(data.PA_ID === id){
            data.PA_AUDIT_EVALUATE = check.val();
            tblPatrol.row(idx).data(data).draw(false);
            $(node).find('td').eq(1).find('.audit-result').replaceWith(check.val() == 1 ? 'Yes' : 'No'); 
        }
    });
    closeModal('#modal_evaluate');
});

/**
 * Cancle drawer
 */
$(document).on('click', '#cancle', function(){
    $('#drawer-corrective').prop('checked', false); 
    $('#drawer-corrective').trigger('change');
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

$(document).on('click', '#matForm', function(){
    console.log(empno);
    const index = $(this).attr('mat-index');
    
    const amec = window.location.host.includes('amecwebtest') ? 'amecwebtest' : 'amecweb';
    // const redirectUrl = `http://${amec}.mitsubishielevatorasia.co.th/webflow/feform/MachineReq/MachineReq/MachineReq_FORMADD/ADD/${empno}`;
    // console.log(redirectUrl);
    //     window.location = redirectUrl;

    const h = screen.height;
    const w = screen.width;
    const link = `${scheme}://${amec}.mitsubishielevatorasia.co.th/webflow/feform/MachineReq/MachineReq/MachineReq_FORMADD/ADD/${empno}`;
    window.open(link, `matWindow${index}`, `height=${h},width=${w},top=0,left=0,resizable=yes,scrollbars=yes`);

    // const newWindow = window.open(`http://amecwebtest.mitsubishielevatorasia.co.th/webflow/feform/MachineReq/MachineReq/MachineReq_FORMADD/ADD/${empno}`, `matWindow${index}`, `height=${h},width=${w},top=0,left=0,resizable=yes,scrollbars=yes`);
    // const timer = setInterval(function() { 
    //     if(newWindow.closed) {
    //         clearInterval(timer);
    //         console.log('Window closed');
    //         // Perform any actions needed after the window is closed
    //     }
    // }, 1000);
});
/**
 * set data for show
 * @param {array} patrol 
 */
async function setData(patrol){
    switch (cextData) {
        case '01':
            $('.corrective-person').removeClass('hidden');
            if(patrol.employee.length > 0){
                patrol.employee.forEach(e => {
                    $('#employee').append(`<option value="${e.SEMPNO}">${e.SEMPPRT}${e.STNAME} (${e.SEMPNO})</option>`);
                });
            }
            const s2opt    = { ...select2Option };
            s2opt.placeholder = 'เลือกผู้ดำเนินการ';
            $('#employee').select2(s2opt);
            break;
        case '02':
            setDatePicker()
            break;
    
        default:
            break;
    }
    
    
    const dFormat = dateFormat(patrol.data[0].PA_DATE);
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
    $('.dateFull').siblings('strong').html('วัน/เดือน/ปี');
    $('.dateFull').html(dFormat.fulldate);
    $('.auditor').siblings('strong').html('ผู้ตรวจสอบ(Auditor)')
    $('.auditor').html(patrol.data[0].PA_AUDIT);
    $('#topic').html('รายงานตรวจสอบความปลอดภัย');
    $('.actions-Form').removeClass('hidden');
    $('.remark-text').removeClass('hidden');
    $('#back').removeClass('hidden');

}

/**
 * Back to patrol form
 */
$(document).on('click', '#back', function(){
    showpreload();
    window.location.href = `${host}patrol/inspection`;
});


/**
 * check corrective person
 * @returns 
 */
async function correctiveAction(){
    const emp = $('#employee');
    if(emp.val() == ''){
        showMessage('กรุณาเลือกผู้ดำเนินการ', 'warning');
        RequiredElement(emp);
        return true;
    } 
    const corrective = await updateCorrective();
    if(corrective){
        showMessage('บันทึกข้อมูลผู้ดำเนินการสำเร็จ','success');
        return false;
    }else{
        showMessage('เกิดข้อผิดพลาด บันทึกข้อมูลผู้ดำเนินการไม่สำเร็จ กรุณาลองใหม่อีกครั้ง');
        // sendmail(`Patrol Form Error :: updateCorrective ${JSON.stringify(corrective)}`);
        const mail = {...mailOpt};
        mail.BODY = [
            mailForm(NFRMNO, VORGNO, CYEAR, CYEAR2, NRUNNO,'update Corrective'),
            `Patrol Form Error : updateCorrective ${JSON.stringify(corrective)}`
        ];
        sendMail(mail);
        return true;
    }
}

/**
 * check and update corrective detail
 * @returns 
 */
async function correctiveDetail(){
    
    let check = false;
    let dataupdate = [];
    const formData = new FormData();
    formData.set('NFRMNO', NFRMNO);
    formData.set('VORGNO', VORGNO);
    formData.set('CYEAR', CYEAR);
    formData.set('CYEAR2', CYEAR2);
    formData.set('NRUNNO', NRUNNO);
    formData.set('userno', empno);
    tblPatrol.row(function (idx, data, node) {
        if(data.PA_CORRECTIVE == null || data.PA_EMP_CORRECTIVE == null || data.PA_FINISH_DATE == null || data.PA_IMAGE_AFTER == null) {
            check = true;
            showMessage('กรุณากรอกข้อมูลการดำเนินการในการปรับปรุงให้ครบถ้วน','warning');
        }else{
            dataupdate.push({
                PA_ID: data.PA_ID,
                PA_CORRECTIVE: data.PA_CORRECTIVE,
                PA_EMP_CORRECTIVE: data.PA_EMP_CORRECTIVE,
                PA_FINISH_DATE: data.PA_FINISH_DATE,
                PA_MORNING_TALK: data.PA_MORNING_TALK,
            });
            formData.append(`PA_IMAGE_AFTER[${idx}]`, data.PA_IMAGE_AFTER);
        }
        // if(data.PA_EMP_CORRECTIVE == null) return true;
        // if(data.PA_FINISH_DATE == null) return true;
        // return false;
    });
    
    formData.set('data',  JSON.stringify(dataupdate));
    for (var pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }
    if(check) return true;
    const corrective = await updateCorrectiveDetail(formData);
    // const corrective = await updateCorrectiveDetail(dataupdate);

    if(corrective){
        showMessage('บันทึกข้อมูลการดำเนินการในการปรับปรุงสำเร็จ','success');
        return false;
    }else{
        showMessage('เกิดข้อผิดพลาด บันทึกข้อมูลการดำเนินการในการปรับปรุงไม่สำเร็จ กรุณาลองใหม่อีกครั้ง');
        // sendmail(`Patrol Form Error :: updateCorrectiveDetail ${JSON.stringify(corrective)}`);
        const mail = {...mailOpt};
        mail.BODY = [
            mailForm(NFRMNO, VORGNO, CYEAR, CYEAR2, NRUNNO,'Update corrective detail'),
            `Patrol Form Error : updateCorrectiveDetail ${JSON.stringify(corrective)}`
        ];
        sendMail(mail);
        return true;
    }
};

async function evaluate(){
    let check = false;
    let dataupdate = [];
    tblPatrol.row(function (idx, data, node) {
        if(data.PA_AUDIT_EVALUATE == null ) {
            check = true;
            showMessage('กรุณาดำเนินการประเมินผลให้ครบถ้วนในทุกข้อ' , 'warning');
        }else{
            dataupdate.push({
                PA_ID: data.PA_ID,
                PA_AUDIT_EVALUATE: data.PA_AUDIT_EVALUATE
            });
        }
    });
    if(check) return true;
    const evaluate = await updateEvaluate(dataupdate);

    if(evaluate){
        showMessage('บันทึกข้อมูลผลประเมินปรุงสำเร็จ','success');
        return false;
    }else{
        showMessage('เกิดข้อผิดพลาด บันทึกข้อมูลผลประเมินไม่สำเร็จ กรุณาลองใหม่อีกครั้ง');
        // sendmail(`Patrol Form Error :: evaluate ${JSON.stringify(evaluate)}`);
        const mail = {...mailOpt};
        mail.BODY = [
            mailForm(NFRMNO, VORGNO, CYEAR, CYEAR2, NRUNNO,'Update evaluate'),
            `Patrol Form Error : evaluate ${JSON.stringify(evaluate)}`
        ];
        sendMail(mail);
        return true;
    }
}

/**
 * update corrective person
 * @returns 
 */
async function updateCorrective(){
    return new Promise((resolve, reject) => {
        $.ajax({
            url: `${host}patrol/webflow/updateCorrective`,
            type: "post",
            dataType: "json",
            data: {
                NFRMNO : NFRMNO,
                VORGNO : VORGNO,    
                CYEAR  : CYEAR, 
                CYEAR2 : CYEAR2,    
                NRUNNO : NRUNNO,
                empCore : $('#employee').val()
            },
            beforeSend: function () {
                showpreload();
            },
            success: function (res) {
                resolve(res);
            },
            complete: function(xhr, status){
                // checkAuthen(xhr, status);
                hidepreload();
            }
        });
    });
}

/**
 * update corrective detail
 * @param {array} data 
 * @returns 
 */
async function updateCorrectiveDetail(data){
    return new Promise((resolve, reject) => {
        $.ajax({
            url: `${host}patrol/webflow/updateCorrectiveDetail`,
            type: "post",
            // dataType: "json",
            processData: false, 
            contentType: false,
            // data: {
            //     // NFRMNO : NFRMNO,
            //     // VORGNO : VORGNO,    
            //     // CYEAR  : CYEAR, 
            //     // CYEAR2 : CYEAR2,    
            //     // NRUNNO : NRUNNO,
            //     data   : data
            // },
            data:data,
            beforeSend: function () {
                showpreload();
            },
            success: function (res) {
                resolve(res);
            },
            complete: function(xhr, status){
                // checkAuthen(xhr, status);
                hidepreload();
            }
        });
    });
}

async function updateEvaluate(data){
    return new Promise((resolve, reject) => {
        $.ajax({
            url: `${host}patrol/webflow/updateEvaluate`,
            type: "post",
            dataType: "json",
            data: {
                NFRMNO : NFRMNO,
                VORGNO : VORGNO,    
                CYEAR  : CYEAR, 
                CYEAR2 : CYEAR2,    
                NRUNNO : NRUNNO,
                data   : data
            },
            beforeSend: function () {
                showpreload();
            },
            success: function (res) {
                resolve(res);
            },
            complete: function(xhr, status){
                // checkAuthen(xhr, status);
                hidepreload();
            }
        });
    });
}

/**
 * get patrol data
 * @returns 
 */
async function getPatrol(){
    return new Promise((resolve, reject) => {
        $.ajax({
            url: `${host}patrol/webflow/getPatrol/${NFRMNO}/${VORGNO}/${CYEAR}/${CYEAR2}/${NRUNNO}`,
            type: "get",
            dataType: "json",
            beforeSend: function () {
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
    
    const id = "#tblPatrol";
    const Classhidden = cextData == '03' ? 'hidden' : '';
    const opt = { ...tableOption };
    opt.ordering     = false;
    opt.searching    = false;
    opt.lengthChange = false;
    opt.data = data;
    opt.columns = [
        cextData == '02' ? {
            data: null , 
            title: "ดำเนินการ",
            className: "align-top text-center",
            // width : '5%',
            render: function (data, type, row, meta){
                return `<div class="inline-flex">
                            <label for="drawer-corrective" class="corrective-action drawer-button btn btn-sm btn-ghost btn-circle tooltip  tooltip-right  flex items-center"  data-tip="ดำเนินการปรับปรุง">
                                <i class="icofont-ui-edit "></i>
                            </label>
                        </div>`;
            }
        } : null,
        cextData == '02' ? {
            data: null , 
            title: "สถานะ",
            className: "align-top text-center",
            // width : '5%',
            render: function (data, type, row, meta){
                return `<div class="inline-flex">
                            <label class="check-status mt-2 tooltip  tooltip-right  flex items-center"  data-tip="ยังไม่ได้ดำเนินการ">
                                <i class="icofont-ui-close text-reject"></i>
                            </label>
                        </div>`;
            }
        } : null,
        cextData == '03' ? {
            data: null , 
            title: "ประเมินผล",
            className: "align-top text-center",
            // width : '5%',
            render: function (data, type, row, meta){
                return `<div class="inline-flex">
                            <label class="evaluate-btn btn btn-xl btn-ghost btn-circle tooltip  tooltip-right  flex items-center"  data-tip="ประเมิน" >
                                <i class="icofont-notepad text-3xl"></i>
                            </label>
                        </div>`;
                // return `<div class="inline-flex">
                //             <label class="evaluate-btn btn btn-xl btn-ghost btn-circle tooltip  tooltip-right  flex items-center"  data-tip="ประเมิน" onclick="modal_evaluate.showModal()">
                //                 <i class="icofont-notepad text-3xl"></i>
                //             </label>
                //         </div>`;
            }
        } : null,
        cextData == '03' ? {
            data: "PA_AUDIT_EVALUATE" , 
            title: "ผลการประเมิน",
            className: "align-top text-center",
            render: function (data, type, row, meta){
                const ndata = data == null ? '-': (data == 1 ? 'Yes' : 'No');
                return `<div class="audit-result">
                            ${ndata}
                        </div>`;
            }
        } : null,
        { 
            data: "ITEMS_NAME",     
            title: "หมวด (Category)",          
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
            // data: "AREA_NAME",      
            // title: "บริเวณ AREA (DETECTED)", 
            // className: 'align-top', 
            // // width: '25%',
            // render: function (data, ytpe, row, meta){
            //     return `<div>
            //                 <span>${data}</span>
            //                 <span class='text-blue-500'>(${row.AREA_ENAME})</span>
            //             </div>`
            // }
            data: "PA_AREA",      
            title: "บริเวณ (Area detected)", 
            className: 'align-top', 
        },
        { data: "PA_DETECTED",  
          title: "สิ่งที่ควรปรับปรุง (Items detected)",
        //   width: '15%',
          className: 'align-top', 
          render: function (data, type, row, meta){
            if(row.baseURL == '') return data;
            return `<div class='flex flex-col'>
                        <p>${data}</p>
                        
                        <label class="form-control w-full max-w-sm showimg  tooltip tooltip-warning"  data-tip="คลิกเพื่อดูรูป">
                        <div class="my-5 drop-shadow-lg border-image">
                            <img class="rounded-lg preview-img" src="${row.baseURL}" />
                        </div>
                        </label>
                    </div>`
          }
        },
        { data: "CLASS", title: "ประเภท (Class)", className: 'align-top !text-center', },
        { 
            data: "PA_SUGGESTION" , 
            title: "ข้อเสนอแนะในการปรับปรุง (Suggestion)", 
            className: 'align-top', 
            // width: '25%',
            render: function (data, type, row, meta){
                const matRepair = row.PA_MAT == 1 ? 
                `<div>
                    <span>แจ้ง MAT ซ่อมแซม</span><br>
                    <span class="text-accent">(Inform to MAT Repair)</span>
                </div>`:'';
                const nData = data == null && row.PA_MAT == 0 ? '-' : (data != null ? `<p>${data}</p><br>` : '');
                return `<div>
                            ${nData}
                            ${matRepair}
                        </div>`;
            }
        },
        {
            data: "PA_CORRECTIVE" , 
            title: "การดำเนินการในการปรับปรุง (Corrective action)",
            className: "align-top",
            // width : '5%',
            render: function (data, type, row, meta){
                const imgHidden = row.PA_IMAGE_AFTER == null ? 'hidden' : '';
                if(cextData == '02'){
                    const ndata = data == null && row.PA_MAT == 0 ? '-' : (data != null ? `<p>${data}</p><br>` : '');
                    const matRepair = row.PA_MAT == 1 ? 
                    
                    `<div class="">
                        <button class="btn btn-accent" mat-index="${meta.row}"  id="matForm">MAT Web flow</button><br>
                    </div>`:'';
                    return `<div class="">
                                ${ndata}
                                ${matRepair}
                                <label class="form-control w-full max-w-sm showimg  tooltip tooltip-warning  ${imgHidden}"  data-tip="คลิกเพื่อดูรูป">
                                <div class="my-5 drop-shadow-lg border-image">
                                    <img class="rounded-lg preview-img" src="${row.PA_IMAGE_AFTERURL ||''}" />
                                </div>
                                </label>
                            </div>`;
                }else{
                    const ndata = data == null ? '-' : data; 
                    return `<div class="">
                                ${ndata}
                                <label class="form-control w-full max-w-sm showimg  tooltip tooltip-warning ${imgHidden}"  data-tip="คลิกเพื่อดูรูป">
                                <div class="my-5 drop-shadow-lg border-image">
                                    <img class="rounded-lg preview-img" src="${row.PA_IMAGE_AFTERURL ||''}" />
                                </div>
                                </label>
                            </div>`;
                }
            }
        },
        {
            data: "PA_FINISH_DATE" , 
            title: "วันแล้วเสร็จ (Finish date)",
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
            title: "วันที่ชี้แจง (Morning talk)",
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
            className: `align-top ${Classhidden}`,
            // width : '5%',
            render: function (data, type, row, meta){
                const ndata = data == null ? '-': (data == 1 ? 'Yes' : 'No');
                return `<div class="audit-result">
                            ${ndata}
                        </div>`;
            }
        }
        
    ].filter(column => column !== null);
    console.log(cextData);
    
    // switch (cextData) {
    //     case '02':
    //     case '03':
    //     case '':

    //         opt.columns[5] = {
    //             data: "PA_CORRECTIVE" , 
    //             title: "การดำเนินการในการปรับปรุง (CORRECTIVE ACTION)",
    //             className: "align-top",
    //             // width : '5%',
    //             render: function (data, type, row, meta){
    //                 const ndata = data == null ? '-': data; 
    //                 return `<div class="">
    //                             ${ndata}
    //                         </div>`;
    //             }
    //         }
    //         opt.columns[6] = {
    //             data: "PA_FINISH_DATE" , 
    //             title: "วันแล้วเสร็จ (FINISH DATE)",
    //             className: "align-top",
    //             // width : '5%',
    //             render: function (data, type, row, meta){
    //                 const ndata = data == null ? '-': data; 
    //                 return `<div class="">
    //                             ${ndata}
    //                         </div>`;
    //             }
    //         }
    //         opt.columns[7] = {
    //             data: "PA_MORNING_TALK" , 
    //             title: "วันที่ชี้แจง (MORNING TALK)",
    //             className: "align-top",
    //             // width : '5%',
    //             render: function (data, type, row, meta){
    //                 const ndata = data == null ? '-': data; 
    //                 return `<div class="">
    //                             ${ndata}
    //                         </div>`;
    //             }
    //         }
    //         break;
    
    //     default:
    //         break;
    // }
    return $(id).DataTable(opt);
}
