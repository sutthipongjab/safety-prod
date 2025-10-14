import $            from "jquery";
import select2      from "select2";
// import { Fancybox } from "@fancyapps/ui";
import "select2/dist/css/select2.min.css";
// import "@fancyapps/ui/dist/fancybox/fancybox.css";
import { ajaxOptions, getData, host, mailOpt, sendMail, showMessage } from "../utils";
import { carouselAuto, carouselNavOpt, fancy } from "../_fancyBox";
import { setElecForm } from "../_createForm";
import { createForm2 } from "../_form";

var empno, areaType, areaCategory, areaNo, areaID;

$(function () {
    $('#AREA').select2({allowClear: true, placeholder: 'กรุณาเลือกพื้นที่'});
    
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

$(document).on('change', '#AREA', async function(){
    try {
        const preview = $('.formPreview');
        const val      = $(this).val();
        areaID = $(this).find('option:selected').data('area-id');
        areaType = $(this ).find('option:selected').data('area-type');
        areaCategory = $(this).find('option:selected').data('area-category');
        empno = $(this).find('option:selected').data('area-empno');
        console.log(areaCategory, areaType);
        
        if(val){
           
            const data = await getData({
                ...ajaxOptions,
                url: `${host}form/getForm`,
                data: {
                    areaType: areaType,
                    areaCategory : areaCategory
                },
            });
            $('.preview').html(await setElecForm(data));
            // $('.preview').append(`<input type="text" class="hidden" id="requester" value="${empno}">`);
            data.forEach(t => {
                // carouselAuto(`navFancy_${t.FRMT_NO}`, { ...carouselNavOpt, axis: "y" });
                areaNo = t.FRMT_RUNNO;
                carouselAuto(`navFancy_${t.FRMT_NO}`, carouselNavOpt);
                fancy(`gallery_${t.FRMT_NO}`);
            });
            preview.removeClass('hidden');
            $('#submit').removeClass('hidden');
        }else{
            preview.addClass('hidden');
            $('#submit').addClass('hidden');
        }
    } catch (e) {
        console.error(e);
        
    }
});


/**
 * Save 
 */
$(document).on('click', '#submit', async function(){
    try {
        const frm = $("#formPreview");
        const req = $('#requester').val();
        console.log(req);
        
        var formData = new FormData(frm[0]);
        console.log(formData);
        formData.set('FORM_REQUEST',empno);
        formData.set('FORM_INPUT',empno);
        formData.set('FORM_NO',areaNo);
        formData.set('FORM_CATEGORY',areaCategory);
        formData.set('FORM_AREA',areaID);
        const NFRMNO = $('.form-info').attr('NFRMNO');
        const VORGNO = $('.form-info').attr('VORGNO');
        const CYEAR  = $('.form-info').attr('CYEAR');
        console.log('NFRMNO', NFRMNO, 'VORGNO', VORGNO, 'CYEAR', CYEAR, 'userno');
        const formInfo = await createForm2(NFRMNO, VORGNO, CYEAR, empno, empno, '' , 1);
        console.log(formInfo);
        for(const key in formInfo.message){
            formData.append(key, formInfo.message[key]);
        }
        // //ตรวจสอบข้อมูล
        for (var pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
        const res = await getData({
            ...ajaxOptions,
            url : `${host}electric/inspection/createForm`,
            data : formData,
            processData : false,
            contentType : false,
        })
        if(res.status == true){
            showMessage('ส่งฟอร์มสำเร็จ','success');
        }else{
            showMessage('ส่งฟอร์มไม่สำเร็จ กรุณาลองใหม่ภายหลัง');
        }
    } catch (e) {
        showMessage(`เกิดข้อผิดพลาด: ${e.message} กรุณาลองใหม่อีกครั้งหรือติดต่อ Admin Tel:2038`);
        const mail = {...mailOpt};
        mail.BODY = [
            `ส่ง Electric form ไม่สำเร็จ  ${e}`,
            `${host}inspection/createForm`
        ];
        sendMail(mail); 
    }
});



