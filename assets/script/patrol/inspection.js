import $            from "jquery";
import * as my      from "../utils.js";
import * as form    from "../_form.js";
import {checkFileFormat, fileImgFormat}   from "../_file.js";
import flatpickr    from "flatpickr";
import moment       from "moment";
import select2      from "select2";
import { Fancybox } from "@fancyapps/ui";
import DataTable    from "datatables.net-dt";
import "datatables.net-responsive";
import "datatables.net-responsive-dt";
// import "datatables.net-dt/css/dataTables.dataTables.min.css";
import "datatables.net-responsive-dt/css/responsive.dataTables.min.css";

import "flatpickr/dist/flatpickr.min.css";
import "select2/dist/css/select2.min.css";
import "@fancyapps/ui/dist/fancybox/fancybox.css";

$(document).ready( async function () {
    
    const datePicker = await my.setDatePicker();
    // $('.s2').select2({placeholder: "   Select Section.",allowClear: true});
    const s2opt = { ...my.select2Option };
    // s2opt.minimumResultsForSearch = Infinity;
    // s2opt.dropdownParent = $('.own-sec')
    s2opt.placeholder = 'เลือกแผนก';
    $('#ownerSec').select2(s2opt);
    // s2opt.placeholder = 'เลือกพื้นที่';
    // $('#PA_AREA').select2(s2opt);
    s2opt.placeholder = 'เลือกหมวด';
    $('#PA_ITEMS').select2(s2opt);
    tblList = await createTable(dataList);
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

$(document).on('change', '#drawer-inspection', function(){
    
    if (!$(this).is(':checked')) {
        console.log(1);
        $('.list-inspection').find('.req').each(function() {
            const target = $(this);
            my.removeClassError(target);
        });
    }
 });

/**
 * Cancle drawer
 */
$(document).on('click', '#cancle', function(){
    $('#drawer-inspection').prop('checked', false); 
    $('#drawer-inspection').trigger('change'); 
 });

 // /**
//  * Required select2
//  */
var flagSelect = false;
$(document).on('change', 'select.req', async function(){
    const select = $(this);
    // my.RequiredElement(select);
    
    if (flagSelect) {
        // หาก trigger มาจากโปรแกรม ไม่ต้องทำอะไร
        flagSelect = false;
        return;
    }
    my.RequiredElement(select);

});

/**
 *  select date and format date
 */
$(document).on('change', '#checkDate', function(){
    const input = $(this);
    const date  = input.val();
    console.log(date);
    if(date){
        const dFormat = my.dateFormat(date);
        $('#monthYearTH').text(dFormat.dateTh);
        $('#monthYearEn').text(dFormat.dateEn);
        $('.dateFull').text(dFormat.fulldate);
        input.attr({
            myTH: dFormat.dateTh,
            myEN: dFormat.dateEn,
            dateFull: dFormat.fulldate,
        });
    }else{
        $('#monthYearTH').text('');
        $('#monthYearEn').text('');
        $('.dateFull').text('-');
        input.attr({
            myTH: '',
            myEN: '',
            dateFull: '',
        });
    }
});


/**
 * แบบกรอกรหัสพนักงาน
 */
// $(document).on('keydown', '#empnno', function(e) {
//     if (e.key === 'Enter') {
//         $(this).blur(); 
//     }
// });

// $(document).on('blur', '#empnno', async function(e){
//     if (e.type === 'focusout') {
//         const input = $(this);
//         const empno = input.val();

//         function resetInput(){
//             $('.organization, .owner-container').addClass('hidden');
//             input.attr({
//                 thName: '',
//                 enName: '',
//                 div: '',
//                 dept: '',
//                 sec: ''
//             });
//         }
        
//         if(!empno){
//             resetInput();
//             return;
//         }
//         const result = await new Promise((resolve, reject) => {
//             $.ajax({
//                 url: `${my.host}inspection/getUserdata`,
//                 type: "post",
//                 dataType: "json",
//                 data: {empno:empno},
//                 beforeSend: function(){
//                     my.showpreload();
//                 },
//                 success: function (res) {
//                     resolve(res);
//                 },
//                 complete: function(xhr, status){
//                     my.checkAuthen(xhr, status);
//                     my.hidepreload();
//                 }
//             });
//         });
//         console.log(result);
//         if(result.length != 0) {
//             const ownerData = result[0];
        
//             // กำหนดข้อความและ value ให้กับ element
//             $('.owner').text(ownerData.STNAME);
//             $('.ownerEn').text(ownerData.SNAME);
//             $('#div').val(ownerData.SDIV).siblings().text(ownerData.SDIV);
//             $('#dept').val(ownerData.SDEPT).siblings().text(ownerData.SDEPT);
//             $('#sec').val(ownerData.SSEC).siblings().text(ownerData.SSEC);
        
//             // แสดง element ที่ซ่อนอยู่
//             $('.organization, .owner-container').removeClass('hidden');
        
//             // ตั้ง attribute ทีละหลายค่า
//             input.attr({
//                 thName: ownerData.STNAME,
//                 enName: ownerData.SNAME,
//                 div: ownerData.SDIV,
//                 dept: ownerData.SDEPT,
//                 sec: ownerData.SSEC
//             });
//         } else {
//             resetInput();
//             // แสดงข้อความแจ้งเตือน
//             my.showMessage('ไม่พบข้อมูลเจ้าของพื้นที่ กรุณาลองใหม่อีกครั้ง');
//         }
//     }
// });

// $(document).on('blur', '#ownerSec', async function(){
//     const select = $(this);
//     console.log(select.val());
    
//     my.RequiredElement(select);
// });

/**
 * Get data user fron section
 */
$(document).on('change', '#ownerSec', async function(){
    const select = $(this);
    const sec   = select.val();
    // const seccode = select.find('option:selected').attr('seccode')
    // console.log(select.find('option:selected').attr('seccode'));
    // console.log(sec);
    my.RequiredElement(select);
    function resetInput(){
        $('.organization, .owner-container').addClass('hidden');
        select.attr({
            thName  : '',
            enName  : '',
            div     : '',
            divcode : '',
            dept    : '',
            deptcode: '',
            sec     : '',
            seccode : '',
            org     : '',
            SEMPNO  : ''
        });
    }
    if(!sec){
        resetInput();
        return;
    }
    
    const result = await new Promise((resolve, reject) => {
        $.ajax({
            url: `${my.host}patrol/inspection/getSem`,
            type: "post",
            dataType: "json",
            data: {seccode:sec},
            beforeSend: function(){
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
    console.log('data user section',result);
    if(result.length != 0) {
        const ownerData = result[0];
    
        var org = ownerData.SSEC;
        $('#org').siblings('strong').text('แผนก(Section)')
        if(org == 'No Section'){
            org = ownerData.SDEPT;
            $('#org').siblings('strong').text('ส่วน(Department)')
            if(org == 'No Department'){
                org = ownerData.SDIV;
                $('#org').siblings('strong').text('ส่วน(Division)')
            }
        }
        // กำหนดข้อความและ value ให้กับ element
        $('.owner').text(ownerData.STNAME);
        $('.ownerEn').text(ownerData.SNAME);
        $('#org').text(org);
        
        // แสดง element ที่ซ่อนอยู่
        $('.organization, .owner-container').removeClass('hidden');
    
        // ตั้ง attribute ทีละหลายค่า
        select.attr({
            thName  : ownerData.STNAME,
            enName  : ownerData.SNAME,
            div     : ownerData.SDIV,
            divcode : ownerData.SDIVCODE,
            dept    : ownerData.SDEPT,
            deptcode: ownerData.SDEPCODE,
            sec     : ownerData.SSEC,
            seccode : ownerData.SSECCODE,
            org     : org,
            SEMPNO  : ownerData.SEMPNO
        });
    } else {
        resetInput();
        // แสดงข้อความแจ้งเตือน
        my.showMessage('ไม่พบข้อมูลเจ้าของพื้นที่ กรุณาลองใหม่อีกครั้ง');
    }
});

/**
 * Reset and check value when click open drawer
 */
$(document).on('click', '#add-inspection', function(){
    if(checkOwner()){
        // console.log($("#drawer-inspection").prop("checked"));
        
        $("#drawer-inspection").prop("checked", true);
        return;
    }
    $('#save-list').removeClass('hidden');
    $('#edit-list').addClass('hidden');
});
// $(document).on('change', '#drawer-inspection', function(){
//     if(checkOwner()){
//         // console.log($("#drawer-inspection").prop("checked"));
        
//         $("#drawer-inspection").prop("checked", false);
//         return;
//     }
// });

/**
 * Edit inspection
 */
$(document).on('click', '.edit-inspection', function(){
    const tr = $(this).closest('tr');
    const index = tblList.row(tr).index();
    const data = dataSave[index];
    console.log(data);
    console.log(index);
    $('#edit-list').attr('data-index', index);
    $('#PA_ITEMS').val(data.PA_ITEMS).trigger('change');
    $('#PA_AREA').val(data.PA_AREA).trigger('change');
    $('#PA_DETECTED').val(data.PA_DETECTED);
    // $('#PA_CLASS').val(data.PA_CLASS).prop('checked', true);
    $(`input[name="PA_CLASS"][value="${data.PA_CLASS}"]`).prop('checked', true).trigger('change');
    $('#PA_SUGGESTION').val(data.PA_SUGGESTION);
    $(`input[name="PA_MAT"][value="${data.PA_MAT}"]`).prop('checked', true).trigger('change');
    // $('#PA_IMAGE').val(data.PA_IMAGE);
    const img = $('#PA_IMAGE');
    const dataTransImage = new DataTransfer();
    dataTransImage.items.add(data.PA_IMAGE);
    img[0].files =  dataTransImage.files;
    // console.log( dataTransImage);
    $('#PA_IMAGE').trigger('change');
    
    $('#save-list').addClass('hidden');
    $('#edit-list').removeClass('hidden');
});

$(document).on('click', '.delete-inspection', function(){
    const tr = $(this).closest('tr');
    const index = tblList.row(tr).index();
    $('#del').attr('d-index',index);
});


/**
 * insert image from select image
 */
var imageUrl;;
var imageFile;
$(document).on('change', '#PA_IMAGE', function(e){
    checkFileFormat($(this), fileImgFormat, 'ไฟล์ไม่ถูกต้อง กรุณาแนบไฟล์รูปเท่านั้น', 'toast-start');
    const files = e.target.files;
    if (files.length > 0) {
            const file = files[0];
            convertImgToBase64URL(file);
            // const imageUrl = URL.createObjectURL(file);
            
            // // itemfile.push(imageUrl);
            // imageUrl = imageUrl;
            // imageFile = file;
            // $('#showimg').attr('src',imageUrl);
            $('#showimg').attr('src',imageUrl);
            $('.showimg-drawer').removeClass('hidden');
    }else{
        $('#showimg').attr('src','');
        $('.showimg-drawer').addClass('hidden');
    }
});



/**
 * preview image
 */
$(document).on('click','.preview-img', function(){
    const base64 = $(this).attr('src');
    const img = [{src: `<img src="${base64}" alt="" style="width:100%;">`, type: "html"}];
    new Fancybox(img);
});

/**
 * Click save to create datatable
 */
var imageSave = [];
var dataSave = [];
var dataList = [];
var tblList;
$(document).on('click', '#save-list', async function(){
    // $('.list-inspection').find('input, select, textarea').each(function() {
    //     const target = $(this);
    //     my.RequiredElement(target);
    // });
    if(!checkList()) return;

    const item = $('#PA_ITEMS');
    const area = $('#PA_AREA');
    const det  = $('#PA_DETECTED');
    const img  = $('#PA_IMAGE');
    const cla  = $('#PA_CLASS:checked');
    const sug  = $('#PA_SUGGESTION');
    const mat  = $('#PA_MAT:checked');

    // // if(!item.val()){
    // //     my.showMessage('กรุณาเลือกหมวด');
    // //     return;
    // // }
    // // if(!area.val()){
    // //     my.showMessage('กรุณาเลือกบริเวณที่ตรวจสอบ');
    // //     return;
    // // }
    // // if(!det.val()){
    // //     my.showMessage('กรุณาระบุสิ่่งที่ควรปรับปรุง');
    // //     return;
    // // }
    // // if(!cla.val()){
    // //     my.showMessage('กรุณาเลือก Class');
    // //     return;
    // // }
    // // if(!sug.val()){
    // //     my.showMessage('กรุณาระบุข้อเสนอแนะ');
    // //     return;
    // // }

    // const fields = [
    //     { element: item, message: 'กรุณาเลือกหมวด' },
    //     { element: area, message: 'กรุณาเลือกบริเวณที่ตรวจสอบ' },
    //     { element: det, message: 'กรุณาระบุสิ่งที่ควรปรับปรุง' },
    //     { element: det, message: 'กรุณาระบุสิ่งที่ควรปรับปรุง' },
    //     { element: img, message: 'กรุณาเพิ่มรูปภาพ' },
    //     { element: cla, message: 'กรุณาเลือก Class' },
    //     { element: sug, message: 'กรุณาระบุข้อเสนอแนะ' },
    // ];
    
    // // วนลูปตรวจสอบ
    // for (const field of fields) {
    //     if (!field.element.val()) {
    //         my.showMessage(field.message);
    //         return;
    //     }
    // }

    dataSave.push({
        'PA_ITEMS'     : item.val()||'', 
        'PA_AREA'      : area.val()||'',
        'PA_DETECTED'  : det.val()||'',
        'PA_IMAGE'     : imageFile||'',
        'PA_CLASS'     : cla.val()||'',
        'PA_SUGGESTION': sug.val()||'',
        'PA_MAT'       : mat.val()||'',
    });
    imageSave.push(imageFile);
    
    dataList.push({
        'PA_ITEMS'     : item.find('option:selected').attr('d-text')||'', 
        'PA_AREA'      : area.val()||'',
        // 'PA_AREA'      : area.find('option:selected').attr('d-text')||'',
        'PA_DETECTED'  : det.val()||'',
        'PA_IMAGE'     : imageUrl||'',
        'PA_CLASS'     : cla.attr('d-text')||'',
        'PA_SUGGESTION': sug.val()||'',
        'PA_MAT'       : mat.val()||'',
    });
    console.log( 'dataSave', dataSave);
    console.log( 'dataList', dataList);
    tblList = await createTable(dataList);
    
    $("#drawer-inspection").prop("checked", false);
});

$(document).on('click', '#edit-list', async function(){
    if(!checkList()) return;
    const item = $('#PA_ITEMS');
    const area = $('#PA_AREA');
    const det  = $('#PA_DETECTED');
    const img  = $('#PA_IMAGE');
    const cla  = $('#PA_CLASS:checked');
    const sug  = $('#PA_SUGGESTION');
    const mat  = $('#PA_MAT:checked');
    
    const file = img[0].files[0];
    // console.log(file);
    
    convertImgToBase64URL(file);
    const index = $(this).attr('data-index');
    // console.log(index);
    
    dataSave[index] = {
        'PA_ITEMS'     : item.val()||'',
        'PA_AREA'      : area.val()||'',
        'PA_DETECTED'  : det.val()||'',
        'PA_IMAGE'     : imageFile||'',
        'PA_CLASS'     : cla.val()||'',
        'PA_SUGGESTION': sug.val()||'',
        'PA_MAT'       : mat.val()||'',
    };
    imageSave[index] = imageFile;
    dataList[index] = {
        'PA_ITEMS'     : item.find('option:selected').attr('d-text')||'',
        'PA_AREA'      : area.val()||'',
        // 'PA_AREA'      : area.find('option:selected').attr('d-text')||'',
        'PA_DETECTED'  : det.val()||'',
        'PA_IMAGE'     : imageUrl||'',
        'PA_CLASS'     : cla.attr('d-text')||'',
        'PA_SUGGESTION': sug.val()||'',
        'PA_MAT'       : mat.val()||'',
    };
    // console.log( 'dataSave', dataSave);
    // console.log( 'dataList', dataList);
    tblList = await createTable(dataList);
    $("#drawer-inspection").prop("checked", false);
});

$(document).on('click', '#del', async function(){
    const index = $(this).attr('d-index');
    dataSave.splice(index, 1);
    dataList.splice(index, 1);
    tblList = await createTable(dataList);
    console.log( 'dataSave', dataSave);
    console.log( 'dataList', dataList);
});

/**
 * Submit form
 */
$(document).on('click', '#submitform', async function(){
    try {
        const date  = $('#checkDate');
        const orw   = $('#ownerSec');
        const audit = $('#auditor');
        if(checkOwner()){
            $("#drawer-inspection").prop("checked", false);
            return;
        }
        if(dataSave.length == 0){
            my.showMessage('กรุณาเพิ่มรายการตรวจสอบ', 'warning');
            $("#drawer-inspection").prop("checked", true);
            $('#save-list').removeClass('hidden');
            $('#edit-list').addClass('hidden');
            return;
        }
        const formData = new FormData();
        formData.append('data', JSON.stringify(dataSave));
        formData.append('PA_OWNER', orw.attr('SEMPNO'));
        formData.append('PA_DATE', date.val());
        formData.append('PA_SEC', orw.val());
        formData.append('PA_AUDIT', audit.val());
        imageSave.forEach((file, index) => {
            formData.append(`PA_IMAGE[${index}]`, file); // ใช้ชื่อ key 'files[]' เพื่อให้ PHP อ่านเป็น array
        });

        // //ตรวจสอบข้อมูล
        // for (var pair of formData.entries()) {
            //     console.log(pair[0] + ': ' + pair[1]);
            //   }

        const NFRMNO = $('.form-info').attr('NFRMNO');
        const VORGNO = $('.form-info').attr('VORGNO');
        const CYEAR = $('.form-info').attr('CYEAR');
        const userno = $('.form-info').attr('userno');
        // formData.append('NFRMNO', NFRMNO);
        // formData.append('VORGNO', VORGNO);
        // formData.append('CYEAR', CYEAR);
        formData.append('userno', userno);
        console.log('NFRMNO', NFRMNO, 'VORGNO', VORGNO, 'CYEAR', CYEAR, 'userno', userno);
        const formInfo = await form.createForm(NFRMNO, VORGNO, CYEAR, userno, userno);
        console.log(formInfo);

        for(const key in formInfo.message){
            formData.append(key, formInfo.message[key]);
        }
        // formData.append('NFRMNO', formInfo.message.formtype);
        // formData.append('VORGNO', formInfo.message.owner);
        // formData.append('CYEAR',  formInfo.message.cyear);
        // formData.append('CYEAR2', formInfo.message.cyear2);
        // formData.append('NRUNNO', formInfo.message.runno);
        const res = await save(formData);
        if(!res.status){
            my.showMessage('เกิดข้อผิดพลาด สร้างฟอร์มไม่สำเร็จ กรุณาลองใหม่ภายหลัง');
            my.sendmail('Create patrol :: Fail');
            form.deleteForm(formInfo.message.formtype, formInfo.message.owner, formInfo.message.cyear, formInfo.message.cyear2, formInfo.message.runno);
        }else{
            my.showMessage('สร้างฟอร์มสำเร็จ','success')
            window.location.href = `${my.host}patrol/inspection`;
        }
    } catch (error) {
        my.sendmail('Create patrol :: '+error);
    }
});

/**
 * Back to inspection
 */
$(document).on('click', '#back', function(){
    my.showpreload();
    // window.location.href = `${my.host}patrol/inspection`;
    window.history.back();
});

/**
 * Save area
 * @param {array} data 
 * @returns 
*/
function save(data){
    // console.log('data', data);
    return new Promise((resolve) => {
        $.ajax({
            url: `${my.host}patrol/inspection/save`,
            type: "post",
            dataType: "json",
            processData: false, 
            contentType: false,
            data:data,
            beforeSend: function (){
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
 * Converts an image file to a Base64 URL and stores it in global variables.
 * @param {File} file - The image file to be converted.
 * @returns {string} The Base64 URL of the image.
 */
function convertImgToBase64URL(file){
    const imgUrl = URL.createObjectURL(file);
    imageUrl  = imgUrl;
    imageFile = file;
    return imgUrl;
}

// /**
//  * Format date
//  * @param {string} date 
//  * @returns 
//  */
// function dateFormat(date){
//     moment.locale("en");
//     const fulldate = moment(date).format("D MMM. YYYY");
//     const dateEn = moment(date).format("MMMM YYYY");
//     moment.locale("th");
//     const dateTh = moment(date).format("MMMM YYYY");
//     return {fulldate: fulldate, dateEn: dateEn, dateTh:dateTh};
// }

/**
 * check owner
 * @returns
 */
function checkOwner(){
    const date  = $('#checkDate');
    const org   = $('#ownerSec');
    const audit = $('#auditor');

    my.RequiredElement(date);
    my.RequiredElement(org)
    my.RequiredElement(audit);

    if(!date.val() || !audit.val() || !org.val()){
        $(this).prop('checked',false); 
    }
    if(!date.val()){
        my.showMessage('กรุณาระบุวันที่', 'warning');
        return true; 
    }
    if(!org.val()){
        my.showMessage('กรุณาเลือกแผนกที่รับผิดชอบ', 'warning');
        return true; 
    }
    if(!audit.val()){
        my.showMessage('กรุณาระบุชื่อผู้ตรวจสอบ', 'warning');
        return true; 
    }
    $('.list-inspection').find('input, select, textarea').each(function() {
        // console.log($(this));
        const target = $(this);
        my.removeClassError(target);
        $('.showimg-drawer').addClass('hidden');
        if (target.is("select")) {
            flagSelect = true;
            target.val(null).trigger('change');
        }else if (target.prop('type') === 'radio'){
            target.prop('checked', false);
        }else{
            $(this).val(''); 
        }
    });
    return false;
}

/**
 * check list
 * @returns 
 */
function checkList(){
    $('.list-inspection').find('input, select, textarea').each(function() {
        const target = $(this);
        my.RequiredElement(target);
    });

    const item = $('#PA_ITEMS');
    const area = $('#PA_AREA');
    const det  = $('#PA_DETECTED');
    const img  = $('#PA_IMAGE');
    const cla  = $('#PA_CLASS:checked');
    // const sug  = $('#PA_SUGGESTION');
    const mat  = $('#PA_MAT:checked');

    const fields = [
        { element: item, message: 'กรุณาเลือกหมวด' },
        { element: area, message: 'กรุณาเลือกบริเวณที่ตรวจสอบ' },
        { element: det, message: 'กรุณาระบุสิ่งที่ควรปรับปรุง' },
        { element: det, message: 'กรุณาระบุสิ่งที่ควรปรับปรุง' },
        { element: img, message: 'กรุณาเพิ่มรูปภาพ' },
        { element: cla, message: 'กรุณาเลือก Class' },
        // { element: sug, message: 'กรุณาระบุข้อเสนอแนะ' },
        { element: mat, message: 'กรุณาเลือกการแจ้งซ่อม' },
    ];
    
    // วนลูปตรวจสอบ
    for (const field of fields) {
        if (!field.element.val()) {
            my.showMessage(field.message, 'warning');
            return false;
        }
    }
    return true;
}


/**
 * Create table
 * @param {array} data 
 * @returns 
 */
async function createTable(data) {
    const id = "#tblList";
    const opt = { ...my.tableOption };
    opt.ordering     = false;
    opt.searching    = false;
    opt.lengthChange = false;
    opt.data = data;
    opt.columns = [
        { data: "PA_ITEMS",     title: "หมวด (Category)",          className: 'align-top', width: '25%'},
        { data: "PA_AREA",      title: "บริเวณ (Area detected)", className: 'align-top', width: '25%'},
        { data: "PA_DETECTED",  
          title: "สิ่งที่ควรปรับปรุง (Items detected)",
          width: '15%',
          render: function (data, type, row, meta){
            // console.log(row);
            
            return `<div class='flex flex-col'>
                        <p>${data}</p>
                        
                        <label class="form-control w-full max-w-sm showimg  tooltip tooltip-warning"  data-tip="คลิกเพื่อดูรูป">
                        <div class="my-5 drop-shadow-lg border-image">
                            <img class="rounded-lg preview-img" src="${row.PA_IMAGE}" />
                        </div>
                        </label>
                    </div>`
          }
        },
        // { data: "PA_IMAGE" , title: "img"},
        { data: "PA_CLASS", title: "ประเภท (Class)", className: 'align-top', width: '5%'},
        { 
            data: "PA_SUGGESTION" , 
            title: "ข้อเสนอแนะในการปรับปรุง (Suggestion)", 
            className: 'align-top', 
            width: '25%',
            render: function (data, type, row, meta){
                const matRepair = row.PA_MAT == 1 ? 
                `<div>
                    <span>แจ้ง MAT ซ่อมแซม</span><br>
                    <span class="text-accent">(Inform to MAT Repair)</span>
                </div>`:'';
                const newData = data == '' && row.PA_MAT == 0 ? '-' : (data != '' ? `<p>${data}</p><br>` : '');
                return `<div>
                            ${newData}
                            ${matRepair}
                        </div>`
            }
        },
        { 
            data: null , 
            title: "Actions",
            className: "align-top all",
            width : '5%',
            render: function (data, type, row, meta){
                return `<div class="flex items-center gap-3">
                            <label for="drawer-inspection" class="drawer-button btn btn-sm btn-ghost btn-circle edit-inspection tooltip flex items-center "  data-tip="แก้ไข">
                                <i class="icofont-ui-edit"></i>
                            </label>
                            <button class="drawer-button btn btn-sm btn-ghost btn-circle confirm tooltip delete-inspection"  data-tip="ลบ" onclick="modal_delete.showModal()">
                                <i class="icofont-ui-delete"></i>
                            </button>
                        </div>`;
            }
        },
    ];
    opt.initComplete = function () {
        $(".table-option").append(`
            <label for="drawer-inspection" class="drawer-button btn btn-sm btn-primary  max-w-xs" id="add-inspection">
                เพิ่มรายการตรวจสอบ
            </label>`);
    };
    return $(id).DataTable(opt);
}

