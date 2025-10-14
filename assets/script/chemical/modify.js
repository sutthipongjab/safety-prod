import $ from "jquery";
import "select2";
import "select2/dist/css/select2.min.css";

import {host, showMessage, userInfoData ,ajaxOptions, getData , select2Option, requiredForm, resetForm, formRemoveError, sendMail, mailOpt, showpreload, hidepreload, removeClassError} from "../utils.js";
import {checkFileFormat} from '../_file.js';
import {deleteForm, createForm}  from "../_form.js";
import {getEmployee}  from "../webservice.js";
import {addInput, removeInput, resetInput, setInput} from "./function.js";

var chmData = [];
var owner, pageType;
var dataEmp = [];
var optUsr = {status:'1'};



$(document).ready( async function () {
    try {  
        pageType = $('.site-info').attr('page-tize');
        if(pageType == 'cancel') $('.h-topic').html('ยกเลิกการใช้สารเคมี');
        console.log(userInfoData.dept.replace(/\s+/g, '_'));
        const ajax = {
            url: `${host}chemical/modify/getChemical/`,
            type: "post",
            dataType: "json",   
            data: {
                empno: userInfoData.sempno
            },
        }
        const res = await getData(ajax);
        console.log(res.status);
        
        if(res.status){
            let radio = '';
            chmData = res.data;
            $('.owner').closest('label').siblings('.ownerLoading').addClass('hidden');
            // if(['FEDEPT.','ELADEPT.'].includes(userInfoData.dept.replace(/\s+/g, ''))){
            if(['FEDEPT.','ELADEPT.'].includes(res.user.SDEPT.replace(/\s+/g, ''))){
                // owner = userInfoData.dept;
                owner = res.user.SDEPCODE;
                $('.searching').removeClass('hidden').addClass('flex');
                $('.owner').html(res.user.SDEPT);
                optUsr.dept = owner;
            }else{
                // owner = userInfoData.sec;
                owner = res.user.SSECCODE;
                $('.searching').removeClass('hidden').addClass('flex');
                $('.owner').html(res.user.SSEC);
                optUsr.sec = owner;
                if(res.user.SSECCODE == '00'){
                    res.org.forEach(org => {

                        if (org.code in res.data) {
                            radio += `<div class="form-control">
                                <label class="label cursor-pointer w-28">
                                    <span class="label-text">${org.name}</span>
                                    <input type="radio" name="OWNER" class="radio req" value="${org.code}" />
                                </label>
                            </div>`;
                        }
                    });
                    // res.org.map(org => {
                    //     radio +=`<div class="form-control">
                    //         <label class="label cursor-pointer w-28">
                    //             <span class="label-text">${org.name}</span>
                    //             <input type="radio" name="OWNER" class="radio req" value="${org.code}" />
                    //         </label>
                    //     </div>`;
                    // });
                    $('.owner').html(radio);
                    $('.searching').addClass('hidden').removeClass('flex');
                }
            } 
            $('#chemicalList').select2({ ...select2Option, width: 'resolve', placeholder: 'เลือกสารเคมี' });
            $('.owner').closest('label').removeClass('hidden');
            if(owner != '00'){
                setItem(owner);
                dataEmp = await getEmployee(optUsr);
                if(pageType == 'modify') setUser();
            }
            // if(owner != '00')setItem(owner);
            // dataEmp = await getEmployee(optUsr);
            // if(owner != '00') setUser();
            
        }else{
            $('.owner').closest('label').siblings('.ownerLoading').addClass('hidden');
            $('.ownerLoading').after(`<a href="${host}chemical/request" class="text-blue-600">ขอใช้สารเคมี <u>คลิก</u></a>`);
            showMessage('ไม่พบข้อมูลสารเคมีในแผนกของท่าน กรุณาทำการขอใช้สารเคมี');
        }
    } catch (e) {
        console.log('error');
        console.log(e);
        console.log(e.stack);
        showMessage('เกิดข้อผิดพลาด กรุณาติดต่อ Admin Tel:2038')
        const mail = {...mailOpt};
        mail.BODY = [`Modify : ${e}`];
        sendMail(mail); 
    }
});

function setItem(owner){
    console.log(chmData[owner]);
    
    let opt = chmData[owner].map(v => `<option value="${v.AMEC_SDS_ID}">(${v.AMEC_SDS_ID})${v.CHEMICAL_NAME}</option>`).join('');
    $('#chemicalList').html(`<option value=""></option>${opt}`);
    $('#chemicalList').select2(select2Option);
}
async function setUser(){
    let options = dataEmp.map(v => `<option value="${v.SEMPNO}">(${v.SEMPNO})${v.SNAME}</option>`).join('');
    $('#USER_CONTROL').html(`<option value=""></option>${options}`);
    $('#USER_CONTROL').select2({...select2Option,  width: '100%'});
    
}
/**
 * select owner
 */
$(document).on('change', 'input[name="OWNER"]', async function() {
    showpreload();
    owner      = $(this).val();
    optUsr.sec = $(this).val();
    dataEmp    = await getEmployee(optUsr);
    setDetail();
    setItem(owner);
    if(pageType == 'modify') setUser();
    console.log('Radio button selected:', owner);
    $('.detail').addClass('hidden');
    $('.searching').removeClass('hidden').addClass('flex');
    $('#chemicalList').select2('open');
    hidepreload();
});


/**
 * select chemical
 */
$(document).on('change', '#chemicalList', async function(){
    console.log('select chemical');
    while (dataEmp.length === 0) {
        showpreload();
        await new Promise(resolve => setTimeout(resolve, 1000)); // Wait for 1 second
    }
    hidepreload();
    const id = $(this).val();
    chmData[owner].map(v => {
        if(v.AMEC_SDS_ID == id){
            setDetail(v);
        }
    });
    $('.detail').removeClass('hidden');

});

/**
 * Clear select2 to [] when clear
 */
$(document).on("select2:clear", '#USER_CONTROL', function(e) {
    $(this).val([]).trigger("change"); // บังคับให้เป็น [] เสมอ
});

/**
 * Add keeping point
 */
$(document).on('click', '.add-keep', function(){
    const html = `<input type="text" placeholder="H203R(N6), 4C" name="KEEPING_POINT[]" class="input input-bordered w-full req" />`;
    addInput($(this), html);
});

/**
 * Add used area
 */
$(document).on('click', '.add-area', function(){
    const html = `<input type="text" placeholder="Pit Ass'y" name="USED_AREA[]" class="input input-bordered w-full req" />`;
    addInput($(this), html);
});


/**
 * Remove keeping point
 */
$(document).on('click', '.remove-keep', function(){
    removeInput($(this));
});

/**
 * Remove used area
 */
$(document).on('click', '.remove-area', function(){
    removeInput($(this));
});


/**
 * Click Request chemical
 */
$(document).on('click', '#submitform', async function(){
    try {
        const frm  = $(".detail");
        const NFRMNO = $('.form-info').attr('NFRMNO');
        const VORGNO = $('.form-info').attr('VORGNO');
        const CYEAR  = $('.form-info').attr('CYEAR');
        console.log('NFRMNO', NFRMNO, 'VORGNO', VORGNO, 'CYEAR', CYEAR, 'userno', userInfoData.sempno);
        // await resetInput('input[name="USED_AREA[]"]', '.remove-area', true);
        // await resetInput('input[name="KEEPING_POINT[]"]', '.remove-keep', true);
        // const useA = $('input[name="USED_AREA[]"]')
        // const keep = $('input[name="KEEPING_POINT[]"]')

        // console.log(useA.length, keep.length);

        let fields = [
            // { element: $('#CHEMICAL_NAME'),      message: 'กรุณาระบุชื่อสารเคมี' },
            // { element: $('#USED_FOR'),           message: 'กรุณาระบุการใช้งาน' },
            // { element: useA,                     message: 'กรุณาระบุจุดใช้งาน' },
        ];
        var formData = new FormData(frm[0]);
        formData.append('userno', userInfoData.sempno);
        formData.append('ownercode', owner);
        // formData.set('CHEMICAL_NAME', $('#CHEMICAL_NAME').val());
        
        if(pageType == 'modify') {
            formData.set('type', 'modify');
            await resetInput('input[name="USED_AREA[]"]', '.remove-area', true);
            await resetInput('input[name="KEEPING_POINT[]"]', '.remove-keep', true);
            const useA = $('input[name="USED_AREA[]"]')
            const keep = $('input[name="KEEPING_POINT[]"]')

            console.log(useA.length, keep.length);

            formData.set('USER_CONTROL', $('#USER_CONTROL').val()?.toString().replace(/,/g, '|'));
            fields = [
                { element: $('#CHEMICAL_NAME'), message: 'กรุณาระบุชื่อสารเคมี' },
                { element: $('#USED_FOR'),      message: 'กรุณาระบุการใช้งาน' },
                { element: useA,                message: 'กรุณาระบุจุดใช้งาน' },
                { element: keep,                message: 'กรุณาระบุจุดจัดเก็บ' },
                { element: $('#USER_CONTROL'),  message: 'กรุณาระบุผู้ควบคุม' },
            ];
            // fields.push({ element: keep, message: 'กรุณาระบุจุดจัดเก็บ' });
            // fields.push({ element: $('#USER_CONTROL'), message: 'กรุณาระบุผู้ควบคุม' });
        }else{
            formData.set('type', 'cancel');
            fields = [
                { element: $('#REASON_CANCEL'), message: 'กรุณาระบุเหตุผลการยกเลิกใช้สารเคมี' },
            ]
            // fields.push({ element: $('#REASON_CANCEL'), message: 'กรุณาระบุเหตุผลการยกเลิกใช้สารเคมี' });
        }
        if(!await requiredForm('.detail', fields)) return;

        
        const formInfo = await createForm(NFRMNO, VORGNO, CYEAR, userInfoData.sempno, userInfoData.sempno);
        console.log(formInfo);
        for(const key in formInfo.message){
            formData.append(key, formInfo.message[key]);
        }
        for (var pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
        
        const ajax = {...ajaxOptions};
        ajax.url  = `${host}chemical/modify/createForm/`;
        ajax.data = formData;
        ajax.processData = false;
        ajax.contentType = false;
        const res = await getData(ajax);
        console.log(res);
        if(!res.status){
            showMessage('เกิดข้อผิดพลาด สร้างฟอร์มไม่สำเร็จ กรุณาลองใหม่ภายหลัง');
            sendMail({...mailOpt, BODY: ['Create chemical Form :: Fail']});
            deleteForm(formInfo.message.formtype, formInfo.message.owner, formInfo.message.cyear, formInfo.message.cyear2, formInfo.message.runno);
        }else{
            showMessage('สร้างฟอร์มสำเร็จ','success')
            setDetail();
            $('#chemicalList').val('').trigger('change');
            $('.detail').addClass('hidden');
            removeClassError($('#chemicalList'));
        }
    } catch (e) {
        // console.log(e);
        showMessage('เกิดข้อผิดพลาด กรุณาติดต่อ Admin Tel:2038')
        const mail = {...mailOpt};
        mail.BODY = [`Create chemical Form : ${e}`];
        sendMail(mail); 
    }

});

/**
 * Set detail
 * @param {object} data 
 */
async function setDetail(data=[]){
    console.log(data);
    
    await resetInput('input[name="USED_AREA[]"]', '.remove-area');
    await resetInput('input[name="KEEPING_POINT[]"]', '.remove-keep');
    if(data.length != 0){
        formRemoveError('.detail')
        $('#AMEC_SDS_ID').val(data.AMEC_SDS_ID);
        $('#CHEMICAL_NAME').val(data.CHEMICAL_NAME).prop('disabled', true);
        $('#USED_FOR').val(data.USED_FOR ? data.USED_FOR : '');
        setInput('input[name="USED_AREA[]"]', '.add-area', data.USED_AREA ? data.USED_AREA : '');
        console.log(data.USER_CONTROL);
        
        // $('#USER_CONTROL').val(data.USER_CONTROL ? data.USER_CONTROL.split('|') : '').trigger('change');
        if(pageType == 'modify') {
            console.log(data.USER_CONTROL ? data.USER_CONTROL.split('|') : []);
            $('#USER_CONTROL').val(data.USER_CONTROL ? data.USER_CONTROL.split('|') : []).trigger('change');
            setInput('input[name="KEEPING_POINT[]"]', '.add-keep', data.KEEPING_POINT ? data.KEEPING_POINT : '');
        }else{
            $('#USED_FOR').prop('disabled', true);
            $('input[name="USED_AREA[]"]').prop('disabled', true);
            $('.add-area').addClass('hidden');
            $('.remove-area').addClass('hidden');
            $('#REASON_CANCEL').val('');
        }
        console.log('test',$('#USER_CONTROL').val());
        
    }else{
        // $('#CHEMICAL_NAME').prop('disabled', false);
        resetForm('.detail');
    }
}

// /**
//  * Add input
//  * @param {object} e 
//  * @param {string} html 
//  */
// function addInput(e, html){
//     e.closest('div').siblings('.inputGroup').append(html);
//     e.siblings('i').removeClass('hidden');
//     // console.log(e.siblings('i')); 
// }

// /**
//  * Remove input
//  * @param {object} e 
//  */
// function removeInput(e){
//     const input = e.closest('div').siblings('.inputGroup').find('input');
//     // console.log(input.length);
//     if(input.length == 2){
//         e.closest('div').siblings('.inputGroup').find('input').last().remove();
//         e.addClass('hidden');
//     }else{
//         e.closest('div').siblings('.inputGroup').find('input').last().remove();
//     }
// }

// /**
//  * reset multi input
//  * @param {string} input e.g. input[name="USED_AREA[]"]
//  * @param {string} button e.g. .remove-area
//  */
// async function resetInput(input, button = '', clear = false){
//     console.log('reset');
    
//     $(input).each(function() {
//         console.log('val',$(this).val().trim());
//         console.log('length',$(input).length);
        
//         if(clear){
//             if ($(this).val().trim() === "" && $(input).length > 1) $(this).remove();
//         }else{
//             if ($(input).length > 1) $(this).remove();
//         }
//     });

//     const updateInput = $(input)

//     if(button != '' && updateInput.length == 1) $(button).addClass('hidden');
// }

// /**
//  * set input on select list
//  * @param {string} input e.g. input[name="USED_AREA[]"]
//  * @param {string} button e.g. .add-area
//  * @param {string} data e.g. Water Water Treatment | aaa | xxx
//  */
// function setInput(input, button, data){
//     console.log('set');

//     console.log(data);
//     const arr = data.split('|');
//     console.log(arr);
    
//     if(arr.length > 1){
//         for (let index = 0; index < arr.length-1; index++) {
//             $(button).trigger('click');
//         }
//         $(input).each(function(i, el){
//             $(el).val(arr[i]);
//         });
//     }else{
//         $(input).val(data);
//     }
    

// }








 

