import dayjs from "dayjs";
import 'dayjs/locale/th'           // 👉 โหลด locale ภาษาไทย
import localizedFormat from 'dayjs/plugin/localizedFormat' // 👉 plugin แสดงรูปแบบวันที่แบบท้องถิ่น
import { setElecData, setElecForm } from "../_createForm";
import { carouselAuto, carouselNavOpt, fancy } from "../_fancyBox";
import { doaction, doactionWebservice, showFlow } from "../_form";
import { ajaxOptions, getData, hidepreload, host, mailForm, mailOpt, requiredForm, scheme, sendMail, showMessage, showpreload } from "../utils";


var NFRMNO, VORGNO, CYEAR, CYEAR2, NRUNNO, empno, mode, cstep, formCategory, formNo, formName, formMode, formAreaName, formCreate, extData ;
$(async function(){
    NFRMNO = $('.formno').attr('NFRMNO');
    VORGNO = $('.formno').attr('VORGNO');
    CYEAR  = $('.formno').attr('CYEAR');
    CYEAR2 = $('.formno').attr('CYEAR2');
    NRUNNO = $('.formno').attr('NRUNNO');
    empno  = $('.user-data').attr('empno');
    mode   = $('.user-data').attr('mode');
    cstep  = $('.user-data').attr('cstep');
    extData = $('.user-data').attr('cextData');
    formCategory = $('.form-detail').attr('formCatagory');
    formNo       = $('.form-detail').attr('formNo');
    formName     = $('.form-detail').attr('formName');
    formAreaName = $('.form-detail').attr('formArea');
    formCreate = $('.form-detail').attr('formCreate');
    formMode     = cstep == '--' ? 'edit' : 'view';
    let formData;
    console.log(cstep, formMode);
    
    const flow = await showFlow(NFRMNO, VORGNO, CYEAR, CYEAR2, NRUNNO);
    const data = await getData({
        ...ajaxOptions,
        url: `${host}electric/webflow/getForm`,
        data: {
            areaNo: formNo,
            areaCategory : formCategory
        },
    });
    if(formMode == 'view') $('.btnReturn').removeClass('hidden');
    // if(formMode == 'view'){
        formData = await getData({
            ...ajaxOptions,
            url: `${host}electric/webflow/getFormData`,
            data: {
                NFRMNO: NFRMNO,
                VORGNO: VORGNO,
                CYEAR: CYEAR,
                CYEAR2: CYEAR2,
                NRUNNO: NRUNNO
            },
        });
    // }
    dayjs.extend(localizedFormat)     // ✅ ใช้ plugin
    dayjs.locale('th')  // ✅ ตั้ง locale เป็นภาษาไทย
    $('#form').html(await setElecForm(data, 'webflow', formMode));
    // if(formMode == 'view') await setElecData(formData, $('#form'));
    await setElecData(formData, $('#form'));
    $('.formHeader').html(`${formName} ${dayjs(formCreate).format('MMMM')} (${formAreaName})`);
    $('.formLoading').removeClass('flex').addClass('hidden');
    if(mode == 3){
        $('.actions-Form').addClass('hidden');
    } else {
        $('.actions-Form').removeClass('hidden');
        $('#matForm').removeClass('hidden');
    } 
    // mode == 3 ? $('.actions-Form').addClass('hidden') : $('.actions-Form').removeClass('hidden');
    $('#flow').html(flow.html);
    data.forEach(t => {
        carouselAuto(`navFancy_${t.FRMT_NO}`, carouselNavOpt);
        fancy(`gallery_${t.FRMT_NO}`);
    });
});

$(document).on('click', "button[name='btnAction']", async function(){
    try{
        const action = $(this).val();
        const remark = $('#remark').val();
        console.log('action : ',action);

        const path = window.location.host.includes('amecwebtest') ? 'formtest' : 'form';
        const redirectUrl = `http://webflow.mitsubishielevatorasia.co.th/${path}/workflow/WaitApv.asp`;

        const frm = $("#form");
        if(formMode == 'edit'){
            requiredForm(frm);
            if(!await requiredForm(frm)) return true;

            
            console.log('test');

            const formData = new FormData(frm[0]);
            formData.set('NFRMNO', NFRMNO);
            formData.set('VORGNO', VORGNO);
            formData.set('CYEAR', CYEAR);
            formData.set('CYEAR2', CYEAR2);
            formData.set('NRUNNO', NRUNNO);
            formData.set('ET_CATEGORY', formCategory);
            formData.set('ET_NO', formNo);
    
            // for (var pair of formData.entries()) {
            //     console.log(pair[0] + ': ' + pair[1]);
            // }

            const res = await getData({
                ...ajaxOptions,
                url: `${host}electric/webflow/save`,
                data: formData, 
                processData: false, 
                contentType: false
            });
            console.log(res);
            if(res.status == true){
                showMessage('บันทึกข้อมูลเรียบร้อย', 'success');
            }else if (res.status == 2){
                throw new Error('ข้อมูลซ้ำ');
            }else{
                throw new Error('บันทึกข้อมูลไม่สำเร็จ');
            }
        }     
        
        if( extData == '01'){
            const submitAllForm = await getData({
                ...ajaxOptions,
                url: `${host}electric/webflow/submitAllForm`,
                data: {extData, NFRMNO, VORGNO, CYEAR}
            });
            console.log('submitAllForm', submitAllForm);
            if(submitAllForm.status){
                for (const f of submitAllForm.form) {
                    const formStatus = await doactionWebservice(f.NFRMNO, f.VORGNO, f.CYEAR, f.CYEAR2, f.NRUNNO, action, empno, remark);
                    if(!formStatus.status){
                        throw new Error(f.message || 'ไม่สามารถ Approve ได้');
                    }
                }
                window.location = redirectUrl;
            }
        }else{
            const formStatus = await doactionWebservice(NFRMNO, VORGNO, CYEAR, CYEAR2, NRUNNO, action, empno, remark);
        
            // const path = window.location.host.includes('amecwebtest') ? 'formtest' : 'form';
            // const redirectUrl = `http://webflow.mitsubishielevatorasia.co.th/${path}/workflow/WaitApv.asp`;
            showpreload();

            if(formStatus.status == true ){
                showMessage(`${$(this).text()}!`, 'success');
                window.location = redirectUrl;
            }else{
                hidepreload();
                throw new Error('ไม่สามารถ Approve ได้ '); 
            }
        }
    } catch (e) {
        showMessage(`เกิดข้อผิดพลาด: ${e.message} กรุณาลองใหม่อีกครั้งหรือติดต่อ Admin Tel:2038`);
        const mail = {...mailOpt};
        mail.BODY = [
            `Electric Form Error : do action`,
            mailForm(NFRMNO, VORGNO, CYEAR, CYEAR2, NRUNNO),
            e
        ];
        sendMail(mail); 
    }
});

$(document).on('click', '#matForm', function(){
    const amec = window.location.host.includes('amecwebtest') ? 'amecwebtest' : 'amecweb';
    const h = screen.height;
    const w = screen.width;
    const link = `${scheme}://${amec}.mitsubishielevatorasia.co.th/webflow/feform/MachineReq/MachineReq/MachineReq_FORMADD/ADD/${empno}`;
    window.open(link, `matWindow`, `height=${h},width=${w},top=0,left=0,resizable=yes,scrollbars=yes`);
});
