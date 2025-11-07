import $ from "jquery";
import "select2";
import "select2/dist/css/select2.min.css";

import {host, showMessage, userInfoData ,ajaxOptions, getData , select2Option, requiredForm, resetForm, formRemoveError, sendmail, sendMail, mailOpt} from "../utils.js";
// import {writeExcelTemp, writeOpt, exportExcel, colToNumber, numberToCol, fill, border, alignment} from '../_excel.js';
import {checkFileFormat} from '../_file.js';
import {deleteForm, createForm}  from "../_form.js";
// import {getEmployee}  from "../webservice.js";
import {addInput, removeInput, resetInput, setInput} from "./function.js";

var chmData = [];
var owner, dataEmp;
var newChemical = false;
var reUse = false;
// var optUsr = {status:'1'};

$(document).ready( async function () {
    console.log(userInfoData.dept.replace(/\s+/g, '_'));

    
    
    // if(['FEDEPT.','ELADEPT.'].includes(userInfoData.dept.replace(/\s+/g, ''))){
    //     owner = userInfoData.dept;
    // }else{
    //     owner = userInfoData.sec;
    // }

    const ajax = {
        url: `${host}chemical/request/getChemical/`,
        type: "post",
        dataType: "json",   
        data: {
            // deptcode: userInfoData.sdepcode, 
            empno: userInfoData.sempno
        },
    }
    const res = await getData(ajax);
    console.log(res);
    let radio = '';
    chmData = res.data;
    $('.owner').closest('label').siblings('.ownerLoading').addClass('hidden');
    // if(['FEDEPT.','ELADEPT.'].includes(userInfoData.dept.replace(/\s+/g, ''))){
    if(['FEDEPT.','ELADEPT.'].includes(res.user.SDEPT.replace(/\s+/g, ''))){
        // owner = userInfoData.dept;
        owner = res.user.SDEPCODE;
        $('.searching').removeClass('hidden').addClass('flex');
        $('.owner').html(res.user.SDEPT);
        // optUsr.dept = owner;
    }else{
        // owner = userInfoData.sec;
        owner = res.user.SSECCODE;
        $('.searching').removeClass('hidden').addClass('flex');
        $('.owner').html(res.user.SSEC);
        // optUsr.sec = owner;
        // if(userInfoData.sseccode == '00'){
        if(res.user.SSECCODE == '00'){
            res.org.map(org => {
                radio +=`<div class="form-control">
                    <label class="label cursor-pointer w-28">
                        <span class="label-text">${org.SSEC}</span>
                        <input type="radio" name="OWNER" class="radio req" value="${org.SSECCODE}" />
                    </label>
                </div>`;
            });
            $('.owner').html(radio);
            $('.searching').addClass('hidden').removeClass('flex');
        }
    } 
    // if(owner != '00'){
    //     setUser();
    //     // dataEmp = await getEmployee(optUsr);
    //     // console.log(dataEmp);
    //     // let options = dataEmp.map(v => `<option value="${v.SEMPNO}">(${v.SEMPNO})${v.SNAME}</option>`).join('');
    //     // $('#USER_CONTROL').html(`<option value=""></option>${options}`);
    //     // $('#USER_CONTROL').select2(select2Option);
    // }
    $('.owner').closest('label').removeClass('hidden');

    // const s2opt = { ...select2Option };
    // s2opt.placeholder = 'เลือกสารเคมี';
    // s2opt.width ='resolve'
    // $('#chemicalList').select2(s2opt);
});


// /**
//  *  Select2 focus
//  */
// $(document).on('select2:open', function(e) {
//     setTimeout(function() {
//         const searchField = document.querySelector('.select2-search__field');
//         if (searchField) {
//             searchField.focus();
//         } else {
//             console.warn("Search field not found.");
//         }
//     }, 100); 
// });

// async function setUser(){
//     dataEmp = await getEmployee(optUsr);
//     let options = dataEmp.map(v => `<option value="${v.SEMPNO}">(${v.SEMPNO})${v.SNAME}</option>`).join('');
//     $('#USER_CONTROL').html(`<option value=""></option>${options}`);
//     $('#USER_CONTROL').select2(select2Option);
// }
/**
 * select owner
 */
$(document).on('change', 'input[name="OWNER"]', function() {
    owner = $(this).val();
    // optUsr.sec = owner;
    // setUser();
    console.log('Radio button selected:', owner);
    $('.searching').removeClass('hidden').addClass('flex');
    $('#keySearch').val('').trigger('input');
});


/**
 * Search chemical
 */
$(document).on('input', '#keySearch', function(){
    const key = $(this).val();
    if (!key || key.trim() === ""){
        $('#chemicalList').closest('label').addClass('hidden');
        $('.detail').addClass('hidden');

        return;
    }
    // console.log(chmData);
    
    const filteredData = chmData.filter(item => item.name.toLowerCase().includes(key.toLowerCase()));
    // const filteredData = chmData.filter(item => item.toLowerCase().includes(key.toLowerCase()));
    console.log(filteredData);
    
    if (filteredData.length > 0) {
        let options = filteredData.map(item => `<option value="${item.AMEC_SDS_ID}" class="mb-1 text-[1rem]">${item.name}</option>`).join('');
        $('#chemicalList').html(`${options}`);

        // let options = filteredData.map(item => `<option value="${item.AMEC_SDS_ID}">${item.name}</option>`).join('');
        // console.log(options);
        // $('#chemicalList').html(`<option value=""></option>${options}`);
        // $('#chemicalList').select2('open');
        

        // showMessage('มีรายการสารเคมีนี้ ในฐานข้อมูลบริษัทฯ แล้ว ต้องการขอมีใช้ ในแผนกของท่าน', 'warning');
        $('#chemicalList').closest('label').removeClass('hidden');
        $('.header').html('ขอใช้สารเคมีในแผนก');
        $('.detail').addClass('hidden');
        newChemical = false;

    } else {
        $('#chemicalList').closest('label').addClass('hidden');
        showMessage('ยังไม่มี สารเคมีนี้ ในฐานข้อมูลบริษัทฯ', 'warning');
        // $('#chemicalList').select2('close');
        // showMessage('ยังไม่มี สารเคมีนี้ ในฐานข้อมูลบริษัทฯ ต้องการขอมีสารเคมีใหม่', 'warning');
        $('.header').html('ขอสารเคมีใหม่');
        $('.detail').removeClass('hidden');
        newChemical = true;
        setDetail();
    }
    console.log('new chemical = ',newChemical);
    

    // const ajax = {
    //     url: `${host}chemical/request/searchChe/`,
    //     type: "post",
    //     dataType: "json",
    //     data: {key: key, sec: userInfoData.sec},    
    // }
    // getData(ajax).then(async (data) => { 
    //     console.log(data);
    //     if(data.length != 0 ){
    //         showMessage('มีรายการสารเคมีนี้ ในฐานข้อมูลบริษัทฯ แล้ว ต้องการขอมีใช้ ในแผนกของท่าน','warning');

    //         // $('.detail').removeClass('hidden');
    //     }else{
    //         showMessage('ยังไม่มี สารเคมีนี้ ในฐานข้อมูลบริษัทฯ ต้องการขอมีสารเคมีใหม่','warning');
    //         // $('.detail').addClass('hidden');
    //     }
    //     $('.detail').removeClass('hidden');

    // });
});

/**
 * select chemical
 */
$(document).on('change', '#chemicalList', function(){
    // resetForm('.detail');
    console.log('select chemical');
    
    const id = $(this).val();
    if(id == '') {
        $('.detail').addClass('hidden');
        return;
    }
    const ajax = {...ajaxOptions};
    ajax.url = `${host}chemical/request/searchChe/`;
    ajax.data = {id: id, owner: owner, empno: userInfoData.sempno};
    // const ajax = {
    //     url: `${host}chemical/request/searchChe/`,
    //     type: "post",
    //     dataType: "json",
    //     data: {id: id, owner: owner, empno: userInfoData.sempno},    
    // // }
    $('.detail').addClass('hidden');
    $('.detailLoading').addClass('flex').removeClass('hidden');
    getData(ajax).then(async (res) => { 
        console.log(res);
        if(res.status == 1){
            await setDetail(res.data[0]);

            // res.data[0].STATUS == 1 ? showMessage('สารเคมีนี้ อยู่ในสถานะ อนุมัติแล้ว ไม่สามารถเพิ่มข้อมูลได้')
            //                         : showMessage('แผนกของท่านเคยมีประวัติการใช้สารเคมีนี้แล้ว กรุณาตรวจสอบข้อมูลให้ถูกต้อง', 'warning');
            if(res.data[0].STATUS == 1){
                showMessage('สารเคมีนี้ อยู่ในสถานะ อนุมัติแล้ว ไม่สามารถเพิ่มข้อมูลได้');
                $('.detail').addClass('hidden');
            }else{
                showMessage('แผนกของท่านเคยมีประวัติการใช้สารเคมีนี้แล้ว กรุณาตรวจสอบข้อมูลให้ถูกต้อง', 'warning');
                $('.detail').removeClass('hidden');
                // reUse = true;
            }
        }else{
            const data = chmData.find(item => item.AMEC_SDS_ID == id);
            await setDetail(data);
            showMessage('ยังไม่มี สารเคมีนี้ในแผนกของท่านกรุณากรอกข้อมูล', 'warning');
            $('.detail').removeClass('hidden');
        }
        $('.detailLoading').addClass('hidden').removeClass('flex');

    });
    // const data = chmData.find(item => item.AMEC_SDS_ID == id);
    // console.log(data);
    // $('#chemical_detail').removeClass('hidden');
    // $('#name').html(data.name);
    // $('#cas').html(data.cas);
    // $('#grade').html(data.grade);
    // $('#unit').html(data.unit);
    // $('#remark').html(data.remark);
    // $('#sds').attr('href', data.sds);
    // $('#sds').attr('download', data.name);
    // $('#sds').html(data.sds);
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

// /**
//  * Add user control
//  */
// $(document).on('click', '.add-usrCT', function(){
//     console.log(1);
    
//     const html = `<input type="text" placeholder="กรอกรหัสผู้ควบคุม e.g. 13249" name="USER_CONTROL[]" class="input input-bordered w-full req" />`;
//     addInput($(this), html);
// });


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

// /**
//  * Remove used control
//  */
// $(document).on('click', '.remove-usrCT', function(){
//     removeInput($(this));
// });

/**
 * Check file pdf
 */
$(document).on('change', '#KEEPING_POINT_FILE', function(){
    checkFileFormat($(this), '.pdf', 'ไฟล์ไม่ถูกต้อง กรุณาแนบไฟล์ PDF เท่านั้น');
});




/**
 * Click Request chemical
 */
$(document).on('click', '#submitform', async function(){
    try {
        const frm  = $(".detail");

        await resetInput('input[name="USED_AREA[]"]', '.remove-area', true);
        await resetInput('input[name="KEEPING_POINT[]"]', '.remove-keep', true);
        // $('input[name="USED_AREA[]"]').each(function() {
        //     if ($(this).val().trim() === "" && $('input[name="USED_AREA[]"]').length > 1) $(this).remove();
        // });
        
        // $('input[name="KEEPING_POINT[]"]').each(function() {
        //     if ($(this).val().trim() === "" && $('input[name="KEEPING_POINT[]"]').length > 1) $(this).remove();
        // });

        const useA = $('input[name="USED_AREA[]"]')
        const keep = $('input[name="KEEPING_POINT[]"]')
        const QTY  = useA.length;

        // if(useA.length == 1) $('.remove-area').addClass('hidden');
        // if(keep.length == 1) $('.remove-keep').addClass('hidden');

        console.log(useA.length, keep.length);

        const fields = [
            { element: $('#CHEMICAL_NAME'),      message: 'กรุณาระบุชื่อสารเคมี' },
            { element: $('#QUANTITY_KG'),        message: 'กรุณาระบุปริมาณที่ใช้' },
            { element: $('#USED_FOR'),           message: 'กรุณาระบุการใช้งาน' },
            { element: useA,                     message: 'กรุณาระบุจุดใช้งาน' },
            { element: keep,                     message: 'กรุณาระบุจุดจัดเก็บ' },
            { element: $('#KEEPING_POINT_FILE'), message: 'กรุณาแนบไฟล์' },
            // { element: $('#KEEPING_POINT_FILE'), message: 'กรุณาแนบไฟล์' },
        ];
        if(!await requiredForm('.detail', fields)) return;

        var formData = new FormData(frm[0]);
        formData.set('QTY', QTY);
        formData.set('CHEMICAL_NAME', $('#CHEMICAL_NAME').val());
        formData.append('userno', userInfoData.sempno);
        formData.append('newChemical', newChemical ? 1 : 0);
        formData.append('ownercode', owner);
        // formData.set('USER_CONTROL', $('#USER_CONTROL').val()?.toString().replace(/,/g, '|'));

        const NFRMNO = $('.form-info').attr('NFRMNO');
        const VORGNO = $('.form-info').attr('VORGNO');
        const CYEAR  = $('.form-info').attr('CYEAR');
        console.log('NFRMNO', NFRMNO, 'VORGNO', VORGNO, 'CYEAR', CYEAR, 'userno', userInfoData.sempno);
        const formInfo = await createForm(NFRMNO, VORGNO, CYEAR, userInfoData.sempno, userInfoData.sempno);
        console.log(formInfo);
        for(const key in formInfo.message){
            formData.append(key, formInfo.message[key]);
        }

            
        for (var pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
        
        console.log($('input[name="USED_AREA"]').val(''));
        if(newChemical){
            console.log('new');
        }
        const ajax = {...ajaxOptions};
        ajax.url  = `${host}chemical/request/createForm/`;
        ajax.data = formData;
        ajax.processData = false;
        ajax.contentType = false;
        const res = await getData(ajax);
        console.log(res);
        if(!res.status){
            showMessage('เกิดข้อผิดพลาด สร้างฟอร์มไม่สำเร็จ กรุณาลองใหม่ภายหลัง');
            sendmail('Create chemical Form :: Fail');
            deleteForm(formInfo.message.formtype, formInfo.message.owner, formInfo.message.cyear, formInfo.message.cyear2, formInfo.message.runno);
        }else{
            showMessage('สร้างฟอร์มสำเร็จ','success')
            setDetail();
            $('#chemicalList').closest('label').addClass('hidden');
            $('.detail').addClass('hidden');
            $('#keySearch').val('');

            // window.location.href = `${host}chemical/inspection`;
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
    // ขอเพิ่ม
    if(data.length != 0){
        formRemoveError('.detail')
        $('#AMEC_SDS_ID').val(data.AMEC_SDS_ID);
        $('#EFFECTIVE_DATE').val(data.EFFECTIVE_DATE ? data.EFFECTIVE_DATE : '');
        $('#CHEMICAL_NAME').val(data.CHEMICAL_NAME).prop('disabled', true);
        $('#QUANTITY_KG').val(data.QUANTITY_KG ? data.QUANTITY_KG : '');
        $('#QUANTITY_TYPE').val(data.QUANTITY_TYPE ? data.QUANTITY_TYPE : '1').trigger('change');
        $('#USED_FOR').val(data.USED_FOR ? data.USED_FOR : '');
        setInput('input[name="USED_AREA[]"]', '.add-area', data.USED_AREA ? data.USED_AREA : '');
        setInput('input[name="KEEPING_POINT[]"]', '.add-keep', data.KEEPING_POINT ? data.KEEPING_POINT : '');
    }else{
        // ขอใหม่
        $('#CHEMICAL_NAME').prop('disabled', false);
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
//         // clear   ? (($(this).val().trim() === "" && $(input).length > 1) 
//         //             ? $(this).remove() : null)
//         //         : (($(input).length > 1) 
//         //             ? $(this).remove() : null);
//         // if ($(this).val().trim() === "" && $(input).length > 1) $(this).remove();
//     });
    
//     // $('input[name="KEEPING_POINT[]"]').each(function() {
//     //     if ($(this).val().trim() === "" && $('input[name="KEEPING_POINT[]"]').length > 1) $(this).remove();
//     // });

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








 

