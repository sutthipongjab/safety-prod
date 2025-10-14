import $ from "jquery";
import "select2";
import "select2/dist/css/select2.min.css";

import {host, showpreload, hidepreload, checkAuthen, tableOption, showMessage, userInfoData ,ajaxOptions, getData ,setDatePicker, dateFormat, select2Option, domScroll, initJoin, RequiredElement, requiredForm, removeClassError, resetForm, formRemoveError, sendmail, sendMail, mailOpt, mailForm} from "../utils.js";
// import {writeExcelTemp, writeOpt, exportExcel, colToNumber, numberToCol, fill, border, alignment} from '../_excel.js';
import {checkFileFormat} from '../_file.js';
import {showFlow, doaction}  from "../_form.js";

var NFRMNO, VORGNO, CYEAR, CYEAR2, NRUNNO, empno, cextData, newChm, formType, formReturn, uniqueVendor;

$(document).ready( async function () {
    NFRMNO = $('.formno').attr('NFRMNO');
    VORGNO = $('.formno').attr('VORGNO');
    CYEAR  = $('.formno').attr('CYEAR');
    CYEAR2 = $('.formno').attr('CYEAR2');
    NRUNNO = $('.formno').attr('NRUNNO');
    empno  = $('.user-data').attr('empno');
    cextData = $('.user-data').attr('cextData');
    newChm   = $('.form-detail').attr('newChm');
    formType = $('.form-detail').attr('formType');
    formReturn = $('.form-detail').attr('formReturn');
    const flow   = await showFlow(NFRMNO, VORGNO, CYEAR, CYEAR2, NRUNNO);
    await setDocument();
    $('#flow').html(flow.html);
    
    
    
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

/**
 * Required select2
 */
$(document).on('change', 'select.req', async function(){
    RequiredElement($(this));
});

$(document).on('change', '#HEALTH', function(){
    isSelectionRequired($('#BEI'), $(this).val());
    toggleWidthSelect($(this), $(this).val());
});
$(document).on('change', '#ENVIRONMENT', function(){
    isSelectionRequired($('#EVM_PARAMETER'), $(this).val());
    toggleWidthSelect($(this), $(this).val());
});
$(document).on('change', '#PPE', function(){
    isSelectionRequired($('#PPE_EQUIPMENT'), $(this).val());
    toggleWidthSelect($(this), $(this).val());
});

$(document).on('change', '#EFC_RESULT', function(){
    const e = $('#EFC_REASON');
    $(this).val() == 0 ? e.closest('label').removeClass('hidden') : e.val('').closest('label').addClass('hidden');
});


$(document).on('change', '#HAZARDOUS', function(){
    isSelectionRequired($('#CAS_NO'), $(this).val());
    isSelectionRequired($('#SUBSTANCE_NAME'), $(this).val());
    isSelectionRequired($('#SUBSTANCE_WEIGHT'), $(this).val());
    isSelectionRequired($('#SUBSTANCE_TYPE'), $(this).val());
});
$(document).on('change', '#CARCINOGENS', function(){
    isSelectionRequired($('#CARCINOGENS_DETAIL'), $(this).val());
    isSelectionRequired($('#CARCINOGENS_TYPE'), $(this).val());
});

$(document).on('blur', '#PUR_CODE', async function(){
    if($(this).val().trim() == ''){
        $(this).val('');
        RequiredElement($(this));
        return;
    }
    const chkUsr = await getData({
        // ...ajaxOptions,
        type: "post",
        dataType: "json",
        url: `${host}chemical/webflow/getUser`,
        data: {empno:$(this).val()}
    });
    console.log(chkUsr);

    if(chkUsr.length == 0){
        showMessage('รหัสพนักงานไม่ถูกต้อง กรุณากรอกใหม่อีกครั้ง','warning')
        $(this).val('');
        RequiredElement($(this));
    }else{
        $('#PUR_INCHARGE').val(chkUsr[0].SNAME);
    }
});

/**
 * 
 * @param {string} e 
 * @param {number} v 
 */
function isSelectionRequired(e, v){
    // v == 1 ? e.closest('label').removeClass('hidden') : e.closest('label').addClass('hidden');
    removeClassError(e);
    if(v == 1){
        e.closest('label').removeClass('hidden');
    }else{
        e.closest('label').addClass('hidden');
        e.val('').trigger('change');
    }

}

/**
 * 
 * @param {string} e 
 * @param {number} v 
 */
function toggleWidthSelect(e, v){
    console.log(screen.width);
    
    if (screen.width > 1280) {
    e.select2({...select2Option, minimumResultsForSearch: Infinity, width: 'auto'})
    v == 1 ? e.closest('label').toggleClass('w-1/2 w-full') : e.closest('label').removeClass('w-1/2').addClass('w-full');
    }   
}


/**
 * Check file pdf
 */
$(document).on('change', '#SDS_FILE', function(){
    checkFileFormat($(this), '.pdf', 'ไฟล์ไม่ถูกต้อง กรุณาแนบไฟล์ PDF เท่านั้น');
});
$(document).on('change', '#VENDOR', async function(){
    const v = $(this).val();
    if(!uniqueVendor){
        const vendor = await getData({
            ...ajaxOptions, 
            url: `${host}chemical/webflow/getVendor`
        });
        uniqueVendor = vendor.filter((value, index, self) =>
            index === self.findIndex((t) => (
              t.VENCODE === value.VENCODE
            ))
        );
    }
    // const vendorDetails = uniqueVendor.find(vendor => vendor.VENCODE === v);
    const vendorDetails = uniqueVendor.find(vendor => vendor.VENCODE === v);
    console.log(vendorDetails);
    
    if (vendorDetails) {
        $('#VENDOR_ADDRESS').html(vendorDetails.ADDRESS);
        $('#VENDOR_NAME').val(vendorDetails.VENNAME);
    }
});

/**
 * action form approve, reject form
 */
$(document).on('click', "button[name='btnAction']", async function () {
    try {
        const action = $(this).val();
        const remark = $('#remark').val();
        console.log('approve',action);
        

        // console.log(action, remark, empno);
        if(action == 'approve'){
            console.log('formType', formType);
            if(formType == 1){
                
                switch (cextData) {
                case '00':
                    const safety = await safetySave();
                    console.log('safety save',safety);
                    if(safety) return;
                    break;
                case '02':
                    const efc = await efcSave();
                    console.log('efc save',efc);
                    
                    if(efc) return;
                    break;
                case '04':
                    const bp = await bpSave();
                    console.log('bp save',bp);
                    
                    if(bp) return;
                    break;
                case '06':
                    const st = await safetySave2();
                    console.log('st2 save',st);
                    
                    if(st) return;
                    break;
                case '07':

                    const save = await getData({
                        ...ajaxOptions,
                        url: `${host}chemical/webflow/finalSave`,
                        data: { 
                            NFRMNO: NFRMNO,
                            VORGNO: VORGNO,
                            CYEAR: CYEAR,
                            CYEAR2: CYEAR2,
                            NRUNNO: NRUNNO,
                            apr: empno
                         }
                    });
                    console.log('final save', save);
                    
                    if(!save.status) return;
                    break;
            
                default:
                    break;
                }
            }else if(formType == 2){    
                if(cextData == '01'){
                    const edit = await saveEdit();
                    console.log('edit save',edit);
                    
                    if(edit) return;
                }
            }else if(formType == 3){
                if(cextData == '01'){
                    const cancel = await saveCancel();
                    console.log('cancel save',cancel);
                    
                    if(cancel) return;
                }
            }
        }
        console.log('ไม่ติด required');
        
        const formStatus = await doaction(NFRMNO, VORGNO, CYEAR, CYEAR2, NRUNNO, action, empno, remark);

        const path = window.location.host.includes('amecwebtest') ? 'formtest' : 'form';
        const redirectUrl = `http://webflow.mitsubishielevatorasia.co.th/${path}/workflow/WaitApv.asp`;
        console.log(redirectUrl);
        showpreload();

        if(formStatus.status == true ){
            showMessage(`${$(this).text()}!`, 'success');
            window.location = redirectUrl;
        }else{
            hidepreload();
            throw new Error('ไม่สามารถ Approve ได้ ');
            // showMessage('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
            // const mail = {...mailOpt};
            // mail.BODY = [`Chemical Form Error : do action ${JSON.stringify(formStatus)}`];
            // sendMail(mail); 
        }
    } catch (e) {
        showMessage(`เกิดข้อผิดพลาด: ${e.message} กรุณาลองใหม่อีกครั้งหรือติดต่อ Admin Tel:2038`);
        const mail = {...mailOpt};
        mail.BODY = [
            `Chemical Form Error : do action`,
            mailForm(NFRMNO, VORGNO, CYEAR, CYEAR2, NRUNNO),
            e
        ];
        sendMail(mail); 
    }
});


async function setDocument(){
    if(formType == 1){
        
        $('.information').find('input').prop('checked',true);
        newChm == '1' ? $('.form-Name').html('แบบฟอร์มขอใช้สารเคมีใหม่') : $('.form-Name').html('แบบฟอร์มขอใช้สารเคมี');
        console.log('cex',cextData);
        const s2opt = {...select2Option, minimumResultsForSearch: Infinity, width: 'auto'};
        // if(cextData != '' && cextData !='00'){
        //     // console.log($('.safety1-detail'));
            
        //     $('.safety1-detail').removeClass('hidden');
        // }
        switch (cextData) {
            case '00':
                $('.detail-divider').toggleClass('hidden flex');
                $('.safety-form1').toggleClass('hidden flex');
                // $('.safety-form1-Load').toggleClass('hidden flex');
                setDatePicker({
                    altInput: true,
                    altFormat: 'd-M-y',
                    dateFormat: 'd/m/Y',
                });
                $('#BP').select2(select2Option);
                $('#EFC').select2(select2Option);
                $('#PPE_EQUIPMENT').select2({...select2Option, placeholder: "กรุณาเลือกอุปกรณ์"});
                $('#FIRE_EQUIPMENT').select2({...select2Option, placeholder: "กรุณาเลือกอุปกรณ์"});
                // const s2opt = {...select2Option, minimumResultsForSearch: Infinity, width: 'auto'};
                $('#SPECIAL_CONTROLLER').select2(s2opt);
                $('#PPE').select2(s2opt);
                $('#ENVIRONMENT').select2(s2opt);
                $('#HEALTH').select2(s2opt);
                break;
            case '01': // safety ddem
                // $('.safety1-detail').toggleClass('hidden flex');
                // $('.safety1-detail').toggleClass('hidden flex').find('.collapse').addClass('collapse-open');
                // $('.safety1-detail').toggleClass('hidden flex').find('input').prop('checked',true);
                $('.detail').removeClass('lg:w-1/3');
                $('.fill-in').addClass('hidden');
                $('.safety1-detail').removeClass('hidden');
                break;
            case '02': //efc
                $('.safety1-detail').removeClass('hidden');
                $('.detail-divider').toggleClass('hidden flex');
                $('.efc-form').toggleClass('hidden flex');
                $('#EFC_WASTE').select2(s2opt);
                $('#EFC_RESULT').select2(s2opt);
                break;
            case '03': // efc sem
                $('.detail').removeClass('lg:w-1/3');
                $('.fill-in').addClass('hidden');
                $('.safety1-detail').removeClass('hidden');
                $('.efc-detail').removeClass('hidden');
                break;
            case '04': // bp
                // const vendor = await getData({
                //     ...ajaxOptions, 
                //     url: `${host}chemical/webflow/getVendor`
                // });
                // uniqueVendor = vendor.filter((value, index, self) =>
                //     index === self.findIndex((t) => (
                //       t.VENCODE === value.VENCODE
                //     ))
                //   );
                  
                // //   console.log(uniqueVendor);
                // // console.log(vendor);
                // let options = uniqueVendor.map(v => `<option value="${v.VENCODE}">(${v.VENCODE})${v.VENNAME}</option>`).join('');
                // $('#VENDOR').html(`<option value=""></option>${options}`);
                $('#VENDOR').select2({...select2Option, placeholder: "กรุณาเลือกผู้ขาย"});
                
                $('.safety1-detail').removeClass('hidden');
                $('.efc-detail').removeClass('hidden');
                $('.bp-form').toggleClass('hidden flex');
                $('.detail-divider').toggleClass('hidden flex');
                break;
            case '06':
                // const cls = await getData({
                //     ...ajaxOptions, 
                //     url: `${host}chemical/webflow/getClass`,
                //     data: { code: 'CMC' }
                // });
                // const cmca = await getData({
                //     ...ajaxOptions, 
                //     url: `${host}chemical/webflow/getClass`,
                //     data: { code: 'CMCA' }
                // });
                // // console.log(cls);
                // let optCls = cls.map(c => `<option value="${c.TYPE_ID}">${c.TYPE_NAME} : ${c.TYPE_DETAIL}</option>`).join('');
                // let optCMCA = cmca.map(c => `<option value="${c.TYPE_ID}">${c.TYPE_NAME} : ${c.TYPE_DETAIL}</option>`).join('');
                // $('#CLASS').html(`<option value=""></option>${optCls}`);
                $('#CLASS').select2({...select2Option, placeholder: "กรุณาเลือก Class"});
                // $('#CARCINOGENS_TYPE').html(`<option value=""></option>${optCMCA}`);
                // $('#CARCINOGENS_TYPE').select2({...select2Option, placeholder: "กรุณาเลือกกลุ่มการก่อมะเร็ง"});
                $('#HAZARDOUS').select2(s2opt);
                $('#CARCINOGENS').select2(s2opt);
                $('#SUBSTANCE_TYPE').select2(s2opt);
                $('#CARCINOGENS_TYPE').select2({...s2opt, placeholder: "กรุณาเลือกกลุ่มการก่อมะเร็ง"});

                $('.safety1-detail').removeClass('hidden');
                $('.detail-divider').toggleClass('hidden flex');
                $('.efc-detail').removeClass('hidden');
                $('.bp-detail').removeClass('hidden');
                $('.safety-form2').toggleClass('hidden flex');
                break;
            // case '07':
            //     showInfo()
            //     break;
            case 'view':
            case '05':
            case '07':
                showInfo();
                break;
            default:
                // console.log('default');
                
                // if(!cextData && cextData != '00'){
                    $('.fill-in').addClass('hidden');
                    $('.detail').removeClass('lg:w-1/3');
                // } 
                break;
            }
        $('.ownerLoading').toggleClass('hidden flex');
        $('.detail').toggleClass('hidden flex');
        $('.form-Name').removeClass('hidden');
        $('.actions-Form').removeClass('hidden');
    } else {
        $('.detail').toggleClass('hidden flex');
        $('.form-Name').removeClass('hidden');
        $('.ownerLoading').toggleClass('hidden flex');
        $('.actions-Form').removeClass('hidden');
    }
}

function showInfo(){
    $('.fill-in').addClass('hidden');
    $('.detail').removeClass('lg:w-1/3');
    $('.safety1-detail').removeClass('hidden');
    $('.efc-detail').removeClass('hidden');
    if(newChm == 1) {
        $('.bp-detail').removeClass('hidden');
        if(cextData!='05')$('.safety2-detail').removeClass('hidden');
    }
}

/**
 * Safety Save
 * @returns 
 */
async function safetySave(){
    try {
        const frm  = $(".safety-form1");
        const fields = [
            { element: $('#LAW'),                   message: 'กรุณาระบุชื่อกฎหมายที่เกี่ยวข้อง' },
            { element: $('#HEALTH'),                message: 'กรุณาเลือกตรวจสุขภาพเพิ่มเติม "จำเป็น" หรือ "ไม่จำเป็น"' },
            { element: $('#ENVIRONMENT'),           message: 'กรุณาเลือกตรวจสิ่งแวดล้อมเพิ่มเติม "จำเป็น" หรือ "ไม่จำเป็น"' },
            { element: $('#PPE'),                   message: 'กรุณาเลือกอุปกรณ์ PPE ที่ต้องใช้ "จำเป็น" หรือ "ไม่จำเป็น"' },
            { element: $('#SPECIAL_CONTROLLER'),    message: 'กรุณาเลือกผู้ควบคุมพิเศษ "จำเป็น" หรือ "ไม่จำเป็น"' },
            { element: $('#FIRE_EQUIPMENT'),        message: 'กรุณาเลือกอุปกรณ์ป้อวกันอัคคีภัย' },
            { element: $('#EFFECTIVE_DATE'),        message: 'กรุณาระบุ EFFECTIVE DATE' },
            { element: $('#EFC'),                   message: 'กรุณาเลือกผู้ดำเนินการ EFC' },
            // { element: $('#BP'),                    message: 'กรุณาเลือกผู้ดำเนินการ B/P' },
        ];
        if(newChm == 1) fields.push({ element: $('#BP'), message: 'กรุณาเลือกผู้ดำเนินการ B/P' });
        if($('#HEALTH').val() == 1) fields.splice(1,0,{ element: $('#BEI'), message: 'กรุณาระบุ BEI' });
        if($('#ENVIRONMENT').val() == 1) fields.splice(2,0,{ element: $('#EVM_PARAMETER'), message: 'กรุณาระบุ PARAMETOR ที่ต้องตรวจ' });
        if($('#PPE').val() == 1) fields.splice(3,0,{ element: $('#PPE_EQUIPMENT'), message: 'กรุณาเลือกอุปกรณ์' });
        console.log(fields);
        
        
        if(!await requiredForm('.safety-form1', fields)) return true;

        
        var formData = new FormData(frm[0]);
        formData.set('EFFECTIVE_DATE', $('#EFFECTIVE_DATE').val());
        formData.set('PPE_EQUIPMENT', $('#PPE_EQUIPMENT').val()?.toString().replace(/,/g, '|'));
        formData.set('FIRE_EQUIPMENT', $('#FIRE_EQUIPMENT').val()?.toString().replace(/,/g, '|'));
        formData.set('NFRMNO', NFRMNO);
        formData.set('VORGNO', VORGNO);
        formData.set('CYEAR', CYEAR);
        formData.set('CYEAR2', CYEAR2);
        formData.set('NRUNNO', NRUNNO);
        formData.set('newChm', newChm);

        for (var pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }

        const ajax = {
            ...ajaxOptions, 
            url: `${host}chemical/webflow/safetySave`,
            data: formData, 
            processData: false, 
            contentType: false
        };
        const res = await getData(ajax);
        console.log(res);
        if(res.status == true){
            showMessage('บันทึกข้อมูลเรียบร้อย', 'success');
            return false;
        }else{
            throw new Error('บันทึกข้อมูลไม่สำเร็จ');
        }
    } catch (e) {
        console.error('safetySave', e);
        showMessage(`เกิดข้อผิดพลาด: ${e.message} กรุณาลองใหม่อีกครั้งหรือติดต่อ Admin Tel:2038`);
        const mail = {...mailOpt};
        mail.BODY = [
            mailForm(NFRMNO, VORGNO, CYEAR, CYEAR2, NRUNNO, 'ส่วนความปลอดภัย กรอกข้อมูล'),
            `CEXDATA ${cextData}`,
            `Chemical Form : ${e}`
        ];
        sendMail(mail); 
        return true;
        
    }
}

async function efcSave(){
    try {
        const frm  = $(".efc-form");
        const fields = [
            { element: $('#EFC_WASTE'),  message: 'กรุณาเลือกการควบคุมน้ำเสีย, ของเสีย "จำเป็น" หรือ "ไม่จำเป็น"' },
            { element: $('#EFC_RESULT'), message: 'กรุณาระบุผลการตรวจสอบ' },
        ];
        if($('#EFC_RESULT').val() == 0) fields.splice(1,0,{ element: $('#EFC_REASON'), message: 'กรุณาระบุเหตุผลที่ไม่ผ่าน' });
        console.log(fields);
        
        if(!await requiredForm('.efc-form', fields)) return true;

        var formData = new FormData(frm[0]);
        formData.set('EFC_REASON', $('#EFC_REASON').val());
        formData.set('NFRMNO', NFRMNO);
        formData.set('VORGNO', VORGNO);
        formData.set('CYEAR', CYEAR);
        formData.set('CYEAR2', CYEAR2);
        formData.set('NRUNNO', NRUNNO);


        for (var pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }

        const ajax = {
            ...ajaxOptions, 
            url: `${host}chemical/webflow/efcSave`,
            data: formData, 
            processData: false, 
            contentType: false
        };
        const res = await getData(ajax);
        console.log(res);
        if(res.status == true){
            showMessage('บันทึกข้อมูลเรียบร้อย', 'success');
            return false;
        }else{
            throw new Error('บันทึกข้อมูลไม่สำเร็จ');
        }
    } catch (e) {
        console.error('efcSave', e);
        showMessage(`เกิดข้อผิดพลาด: ${e.message} กรุณาลองใหม่อีกครั้งหรือติดต่อ Admin Tel:2038`);
        const mail = {...mailOpt};
        mail.BODY = [
            mailForm(NFRMNO, VORGNO, CYEAR, CYEAR2, NRUNNO,'ส่วนสิ่งแวดล้อม กรอกข้อมูล'),
            `CEXDATA ${cextData}`,
            `Chemical Form : ${e}`
        ];
        sendMail(mail); 
        return true;
        
    }
}

async function bpSave(){
    try {
        const frm  = $(".bp-form");
        const fields = [
            { element: $('#PUR_CODE'),  message: 'กรุณาระบุรหัสพนักงาน' },
            { element: $('#SDS_FILE'), message: 'กรุณาแนบไฟล์' },
            { element: $('#VENDOR'), message: 'กรุณาระบุบริษัทผู้ขาย' },
            { element: $('#VENDOR_TAX_NO'), message: 'กรุณาระบุเลขประจำตัวผู้เสียภาษี' },
        ];
        console.log(fields);
        
        if(!await requiredForm('.bp-form', fields)) return true;

        const chkUsr = await getData({
            ...ajaxOptions,
            url: `${host}chemical/webflow/getUser`,
            data: {empno:$('#PUR_CODE').val()}
        });
        console.log(chkUsr);

        if(chkUsr.length == 0){
            showMessage('รหัสพนักงานไม่ถูกต้อง กรุณากรอกใหม่อีกครั้ง','warning')
            $('#PUR_CODE').val('');
            RequiredElement($('#PUR_CODE'));
            return true;
        }else{
            $('#PUR_INCHARGE').val(chkUsr[0].SNAME);
        }
        

        var formData = new FormData(frm[0]);
        formData.set('NFRMNO', NFRMNO);
        formData.set('VORGNO', VORGNO);
        formData.set('CYEAR', CYEAR);
        formData.set('CYEAR2', CYEAR2);
        formData.set('NRUNNO', NRUNNO);
        formData.set('VENDOR_ADDRESS', $('#VENDOR_ADDRESS').html())
        formData.append('userno', empno);


        for (var pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }

        const ajax = {
            ...ajaxOptions, 
            url: `${host}chemical/webflow/bpSave`,
            data: formData, 
            processData: false, 
            contentType: false
        };
        const res = await getData(ajax);
        console.log(res);
        if(res.status == true){
            showMessage('บันทึกข้อมูลเรียบร้อย', 'success');
            return false;
        }else{
            throw new Error('บันทึกข้อมูลไม่สำเร็จ');
        }
    } catch (e) {
        console.error('bpSave', e);
        showMessage(`เกิดข้อผิดพลาด: ${e.message} กรุณาลองใหม่อีกครั้งหรือติดต่อ Admin Tel:2038`);
        const mail = {...mailOpt};
        mail.BODY = [
            mailForm(NFRMNO, VORGNO, CYEAR, CYEAR2, NRUNNO,'ส่วนจัดซื้อ กรอกข้อมูล'),
            `CEXDATA ${cextData}`,
            `Chemical Form : ${e}`
        ];
        sendMail(mail); 
        return true;
        
    }
}

async function safetySave2(){
    try {
        const frm  = $(".safety-form2");
        const fields = [
            { element: $('#CLASS'),  message: 'กรุณาเลือก Class' },
            { element: $('#HAZARDOUS'),  message: 'กรุณาระบุรายการวัตถุอันตราย' },
            { element: $('#CARCINOGENS'), message: 'กรุณาระบุสารที่สามารถก่อมะเร็ง' },
        ];
        if($('#HAZARDOUS').val() == 1) fields.push(
            { element: $('#CAS_NO'), message: 'กรุณาระบุ CAS No.' },
            { element: $('#SUBSTANCE_NAME'), message: 'กรุณาระบุชื่อสาร' },
            { element: $('#SUBSTANCE_WEIGHT'), message: 'กรุณาระบุน้ำหนัก' },
            { element: $('#SUBSTANCE_TYPE'), message: 'กรุณาเลือกประเภทวัตถุอันตราย' },
        );
        if($('#CARCINOGENS').val() == 1) fields.push(
            { element: $('#CARCINOGENS_DETAIL'), message: 'กรุณาระบุสารที่สามารถก่อมะเร็ง' },
            { element: $('#CARCINOGENS_TYPE'), message: 'กรุณาเลือกกลุ่มการเกิดมะเร็ง' },

        );
        console.log(fields);
        
        if(!await requiredForm('.safety-form2', fields)) return true;

        var formData = new FormData(frm[0]);
        formData.set('NFRMNO', NFRMNO);
        formData.set('VORGNO', VORGNO);
        formData.set('CYEAR', CYEAR);
        formData.set('CYEAR2', CYEAR2);
        formData.set('NRUNNO', NRUNNO);
        // formData.set('CAS_NO', $('#CAS_NO').val())
        // formData.set('SUBSTANCE_NAME', $('#SUBSTANCE_NAME').val())
        // formData.set('SUBSTANCE_WEIGHT', $('#SUBSTANCE_WEIGHT').val())
        // formData.set('SUBSTANCE_TYPE', $('#SUBSTANCE_TYPE').val())
        // formData.set('CARCINOGENS_DETAIL', $('#CARCINOGENS_DETAIL').val())
        // formData.set('CARCINOGENS_TYPE', $('#CARCINOGENS_TYPE').val())


        for (var pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }

        const ajax = {
            ...ajaxOptions, 
            url: `${host}chemical/webflow/safetySave2`,
            data: formData, 
            processData: false, 
            contentType: false
        };
        const res = await getData(ajax);
        console.log(res);
        if(res.status == true){
            showMessage('บันทึกข้อมูลเรียบร้อย', 'success');
            return false;
        }else{
            throw new Error('บันทึกข้อมูลไม่สำเร็จ');
        }
    } catch (e) {
        console.error('safety2 save', e);
        showMessage(`เกิดข้อผิดพลาด: ${e.message} กรุณาลองใหม่อีกครั้งหรือติดต่อ Admin Tel:2038`);
        const mail = {...mailOpt};
        mail.BODY = [
            mailForm(NFRMNO, VORGNO, CYEAR, CYEAR2, NRUNNO,'ส่วนความปลอดภัย กรอกข้อมูลครั้งสุดท้าย'),
            `CEXDATA ${cextData}`,
            `Chemical Form : ${e}`
        ];
        sendMail(mail); 
        return true;
        
    }
}

async function saveEdit(){
    try {
        const res = await getData({
            ...ajaxOptions,
            url: `${host}chemical/webflow/saveEdit`,
            data: { 
                NFRMNO: NFRMNO,
                VORGNO: VORGNO,
                CYEAR: CYEAR,
                CYEAR2: CYEAR2,
                NRUNNO: NRUNNO,
                userno: empno
            }
        });
        console.log(res);
        if(res.status == true){
            showMessage('บันทึกข้อมูลเรียบร้อย', 'success');
            return false;
        }else{
            throw new Error('บันทึกข้อมูลไม่สำเร็จ');
        }
    } catch (e) {
        console.error('edit save', e);
        showMessage(`เกิดข้อผิดพลาด: ${e.message} กรุณาลองใหม่อีกครั้งหรือติดต่อ Admin Tel:2038`);
        const mail = {...mailOpt};
        mail.BODY = [
            mailForm(NFRMNO, VORGNO, CYEAR, CYEAR2, NRUNNO,'เปลี่ยนแปลงข้อมูลสารเคมีไม่สำเร็จ'),
            `CEXDATA ${cextData}`,
            `Chemical Form : ${e}`
        ];
        sendMail(mail); 
        return true;
        
    }
}

async function saveCancel(){
    try {
        const res = await getData({
            ...ajaxOptions,
            url: `${host}chemical/webflow/saveCancel`,
            data: { 
                NFRMNO: NFRMNO,
                VORGNO: VORGNO,
                CYEAR: CYEAR,
                CYEAR2: CYEAR2,
                NRUNNO: NRUNNO,
                userno: empno
            }
        });
        console.log(res);
        if(res.status == true){
            showMessage('บันทึกข้อมูลเรียบร้อย', 'success');
            return false;
        }else{
            throw new Error('บันทึกข้อมูลไม่สำเร็จ');
        }
    } catch (e) {
        console.error('edit save', e);
        showMessage(`เกิดข้อผิดพลาด: ${e.message} กรุณาลองใหม่อีกครั้งหรือติดต่อ Admin Tel:2038`);
        const mail = {...mailOpt};
        mail.BODY = [
            mailForm(NFRMNO, VORGNO, CYEAR, CYEAR2, NRUNNO,'ยกเลิกการบันทึกข้อมูลสารเคมีไม่สำเร็จ'),
            `CEXDATA ${cextData}`,
            `Chemical Form : ${e}`
        ];
        sendMail(mail); 
        return true;
    }
}
