// import $, { ajax }            from "jquery";
import DataTable    from "datatables.net-dt";
import select2      from "select2";
// import { Fancybox } from "@fancyapps/ui";
import { addClassError, ajaxOptions, checkAuthen, clickTableGroup, domScroll, getData, hidepreload, host, initJoin, mailOpt, removeClassError, RequiredElement, requiredForm, sendMail, showMessage, showpreload, tableGroup, tableOption, userInfoData }      from "./utils.js";
import "datatables.net-responsive";
import "datatables.net-responsive-dt";
// import "datatables.net-dt/css/dataTables.dataTables.min.css";
import "datatables.net-responsive-dt/css/responsive.dataTables.min.css";
import "select2/dist/css/select2.min.css";
// import "@fancyapps/ui/dist/fancybox/fancybox.css";
import { checkFileFormat, fileImgFormat } from "./_file.js";
import { openImgDialog, setFancyImage } from "./_fancyBox.js";
import { get } from "jquery";

var table, tblTopic, empno, formType, formTypeID;
const columnMaster = [
    { data: "CATEGORY_NAME",  title: "หมวดหมู่", visible: false},
    { data: "FORMNO" , title: "หมายเลขฟอร์ม"},
    { data: "FORMNAME", title: "ชื่อฟอร์ม"},
    { data: "FORMENAME", title: "ชื่อฟอร์ม (ภาษาอังกฤษ)"},
    { data: "TYPE_NAME", title: "ประเภทฟอร์ม"},
    { 
        data: "STATUS", 
        title: "STATUS",
        render: function (data, type, row, meta){
            return data == 1 ? '<span class="text-blue-500">Enable</span>' : '<span class="text-red-500">Disable</span>';
        }
    },
    { 
        data: null , 
        title: "Actions",
        className: "action all",
        width : '10px',
        render: function (data, type, row, meta){
                
            return `<div class="flex items-center justify-center gap-3">
                        <label for="detail" class="btn btn-sm btn-ghost btn-circle tooltip tooltip-left flex items-center detail" data-tip="รายละเอียด">
                            <i class="icofont-file-document"></i>
                        </label>
                        <label for="drawer-form" class="drawer-button btn btn-sm btn-ghost btn-circle tooltip tooltip-left flex items-center edit" data-type="form"  data-tip="แก้ไขฟอร์ม">
                            <i class="icofont-ui-edit"></i>
                        </label>
                        <button class="drawer-button btn btn-sm btn-ghost btn-circle tooltip tooltip-left confirm" data-type="form"  data-tip="ลบ" onclick="modal_delete.showModal()">
                            <i class="icofont-ui-delete text-red-500"></i>
                        </button>
                    </div>`;
        }
    },
];

const columnTopic = [
    {
        className: "details-control",
        defaultContent: "",
    },
    { 
        data: "FRMT_TOPIC",  
        title: "หัวข้อ",
        className: 'open-details cursor-pointer',
        render: function (data, type, row, meta){
            return data || row.FRMT_NO;
        }
    },
    { data: "FRMT_TOPICEN" , title: "หัวข้อ (ภาษาอังกฤษ)", className: 'open-details cursor-pointer'},
    { 
        data: null , 
        title: "Actions",
        className: "action all",
        width : '10px',
        render: function (data, type, row, meta){
                
            return `<div class="flex items-center justify-center gap-3">
            
                        <label for="" class="drawer-button btn btn-sm btn-ghost btn-circle tooltip tooltip-left flex items-center openImgDialog"  data-tip="จัดการภาพประกอบ">
                            <i class="icofont-duotone icofont-pictures"></i>
                        </label>
                        <label for="drawer-detail" class="drawer-button btn btn-sm btn-ghost btn-circle tooltip tooltip-left flex items-center add" data-type="detail" d-category="${data.FRMT_CATEGORY}" d-formno="${data.FRMT_RUNNO}" d-topic="${data.FRMT_NO}" data-tip="เพิ่มรายละเอียด">
                            <i class="icofont-plus-circle"></i>
                        </label>
                        <label for="drawer-topic" class="drawer-button btn btn-sm btn-ghost btn-circle tooltip tooltip-left flex items-center edit" data-type="topic"  data-tip="แก้ไขหัวข้อ">
                            <i class="icofont-ui-edit"></i>
                        </label>
                        <button class="drawer-button btn btn-sm btn-ghost btn-circle tooltip tooltip-left confirm" data-type="topic"   data-tip="ลบ" onclick="modal_delete.showModal()">
                            <i class="icofont-ui-delete text-red-500"></i>
                        </button>
                    </div>`;
        }
    },
];


$(document).ready(async function () {
    formType   = $('.formType').data('formtype');
    formTypeID = $('.formType').data('formid');
    console.log('form data : ',formType, formTypeID);
    
    const form = await getData({
        ...ajaxOptions,
        url: `${host}form/getFormMaster`,
        data : {formType: formType}
    });
    // console.log(form);
    table = await createTable(form, '#tblMaster', columnMaster);
    $('#TYPE').select2({placeholder: "   Select Type.",allowClear: true, minimumResultsForSearch: Infinity});
    $('#CATEGORY').select2({placeholder: "   Select Category.",allowClear: true, minimumResultsForSearch: Infinity});
    $('#DETAIL_TYPE').select2({placeholder: "   Select Type.dd",allowClear: true, minimumResultsForSearch: Infinity});
});



/**
 * Add 
 */
$(document).on("click", ".add", async function (e) {
    const type = $(this).attr('data-type');
    console.log($('#form').find('input'));
    
    let frm;
    switch (type) {
        case 'detail':
            $('#detailHeader').text('เพิ่มรายละเอียด');
            // $("#formDetail").trigger("reset");
            frm = $("#formDetail");
            frm.trigger("reset");
            
            $('#formDetail #CATEGORY').val($(this).attr('d-category'));
            $('#formDetail #FORMNO').val($(this).attr('d-formno'));
            $('#formDetail #TOPIC_NO').val($(this).attr('d-topic'));
            $("#DETAIL_TYPE").val(null).trigger('change');
            removeClassError($('#DETAIL_TYPE'));
            break;
        case 'topic':
            $('#topicHeader').text('เพิ่มหัวข้อ');
            // $("#formTopic").trigger("reset");
            frm = $("#formTopic");
            frm.trigger("reset");
            $('#FRMT_CATEGORY').val($(this).attr('d-category'));
            $('#formTopic #FRMT_RUNNO').val($(this).attr('d-formno'));
            break;
        default:
            frm = $("#form");
            frm.trigger("reset");
            $('#formHeader').text('เพิ่มฟอร์ม');
            $('.status').addClass('hidden');
            if(formType){
                $('select[name="CATEGORY"]').val(formTypeID).prop('disabled', true).trigger("change");
                $('select[name="CATEGORY"]').closest('.select2-container').addClass('bg-gray-200');
            }else{
                $('select[name="CATEGORY"]').prop('disabled', false).trigger('change');
            }
            $("#TYPE").val(null).trigger('change');
            // $("#CATEGORY").val(null).trigger('change');
            removeClassError($('#TYPE'));
            removeClassError($('#CATEGORY'));
            break;
    }
    // frm.trigger("reset");
    frm.find('input').each(function() {
        removeClassError($(this));
    });
});



/**
 * Edit 
 */
$(document).on("click", ".edit", async function (e) {
    const type = $(this).attr('data-type');
    switch (type) {
        case 'detail':
            $('#detailHeader').text('แก้ไขรายละเอียด');
            const index = $(this).attr('data-index');
            const tr  = $(this).closest('table').closest('tr').prev();
            const row = tblTopic.row(tr);
            // const data = row.data().detail[index];
            // console.log(data);
            // const frm = $("#formDetail");
            setEdit($("#formDetail"), row.data().detail[index]);
            break;
    
        case 'topic':
            $('#topicHeader').text('แก้ไขหัวข้อ');
            // const data = tblTopic.row($(this).parents("tr")).data();
            // console.log(data);
            // const frm = $("#formTopic");
            setEdit($("#formTopic"), tblTopic.row($(this).parents("tr")).data());
            break;
        default:
            $('#formHeader').text('แก้ไขฟอร์ม');
            $('.status').removeClass('hidden');
            $('select[name="CATEGORY"]').prop('disabled', true).trigger('change');
            // $('select[name="CATEGORY"]').select2('enable', true);
            $('select[name="CATEGORY"]').closest('.select2-container').addClass('bg-gray-200');

            // const data =  table.row($(this).parents("tr")).data();
            // console.log(data);
            // const frm = $("#form");
            setEdit($("#form"), table.row($(this).parents("tr")).data());
            break;
    }
    // for (const [key, value] of Object.entries(data)) {
    //     const target = frm.find(`[data-map=${key}]`);
    //     console.log(key, value, target);
        
    //     //Check if target is select
    //     if (target.is("select")) {
    //         target.val(value).trigger("change");
    //     } else if(key == 'STATUS'){
    //         value == 1 ? target.val(value).prop('checked', true) : target.val(0).prop('checked', false);
    //     } else {
    //         target.val(value);
    //     }
    // }
});

// $(document).on('click','.edit-topic', async function(){
//     $('#topicHeader').text('แก้ไขหัวข้อ');
//     const data = tblTopic.row($(this).parents("tr")).data();
//     console.log(data);
//     const frm = $("#formTopic");
//     setEdit(frm, data);
// });

// $(document).on('click','.edit-detail', async function(){
//     $('#detailHeader').text('แก้ไขรายละเอียด');
//     const index = $(this).attr('data-index');
//     const tr  = $(this).closest('table').closest('tr').prev();
//     const row = tblTopic.row(tr);
//     const data = row.data().detail[index];
//     console.log(data);
//     const frm = $("#formDetail");
//     setEdit(frm, data);
// });

/**
 * set value to drawer edit
 * @param {object} form 
 * @param {object} data 
 */
function setEdit(form, data){
    for (const [key, value] of Object.entries(data)) {
        const target = form.find(`[data-map=${key}]`);
        // console.log(key, value, target);
        
        //Check if target is select
        if (target.is("select")) {
            target.val(value).trigger("change");
        } else if(key == 'STATUS'){
            value == 1 ? target.val(value).prop('checked', true) : target.val(0).prop('checked', false);
        } else {
            target.val(value);
        }
    }
}

/**
 * Manage class error
 */
$(document).on('blur', '#DETAIL, #DETAILEN, #FRMT_TOPIC, #FRMT_TOPICEN, #FORMNAME, #FORMENAME', async function(){
    console.log($(this));
    if ($(this).val() === '' || $(this).val().length === 0) {
        addClassError($(this));
    }else{
        removeClassError($(this));
    }
});

/**
 * Save 
 */
$(document).on('click', '.save', async function(){
    const type = $(this).attr('data-type');
    const btn = $(this);
    let frm, dfrm;

    switch (type) {
        case 'detail':
                frm = $("#formDetail");
                dfrm = $("#drawer-detail");
                const detail = $('#DETAIL');
                const detailEn = $('#DETAILEN');
                if(detail.val().trim() == '' && detailEn.val().trim() == ''){
                    detail.trigger('blur');
                    detailEn.trigger('blur');
                    showMessage('กรุณากรอกรายละเอียด','warning');
                    return;
                }
            break;
        case 'topic':
            frm = $("#formTopic");
            dfrm = $("#drawer-topic");
            const topic = $('#FRMT_TOPIC');
            const topicEn = $('#FRMT_TOPICEN');
            if(topic.val().trim() == '' && topicEn.val().trim() == ''){
                topic.trigger('blur');
                topicEn.trigger('blur');
                showMessage('กรุณากรอกข้อมูลหัวข้อ','warning');
                return;
            }
        break;
        default:
                frm = $("#form");
                dfrm = $("#drawer-form");
                const form = $('#FORMNAME');
                const formEn = $('#FORMENAME');
                if(form.val().trim() == '' && formEn.val().trim() == ''){
                    form.trigger('blur');
                    formEn.trigger('blur');
                    showMessage('กรุณากรอกชื่อแบบฟอร์ม','warning');
                    return;
                }
            break;
    }
    btn.addClass("loaded");
    btn.find(".loading").removeClass("hidden");

    if(! await requiredForm(frm)){
        btn.removeClass("loaded");
        btn.find(".loading").addClass("hidden");
        return; 
    }

    var formData = new FormData(frm[0]);
    
    const status = $('#STATUS').is(':checked') ? 1 : 0;
    formData.set('STATUS', status);
    formData.set('formtype', type);
    if(type == 'form') formData.set('CATEGORY', $('select[name="CATEGORY"] :selected').val());
    // formData.set('CATEGORY', type);
    // formData.set('FORMNO', type);
    for (var pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }

    const res = await getData({
        ...ajaxOptions,
        url: `${host}form/save`,
        data: formData,
        processData: false,
        contentType: false,
    });
    if(res.status == true){
        // console.log(tblTopic.rows().data().toArray());
        
        await setData(res.data, type, res.action);
        // console.log(tblTopic.rows().data().toArray());
        // table = await createTable(res.data, '#tblMaster', columnMaster);
        showMessage('บันทึกข้อมูลสำเร็จ','success');
        dfrm.prop("checked", false);
    }else{
        showMessage('บันทึกข้อมูลไม่สำเร็จ กรุณาลองใหม่ภายหลัง','error');
    }

    btn.removeClass("loaded");
    btn.find(".loading").addClass("hidden");
});

/**
 * Set data when insert, update and delete data
 * @param {object} data 
 * @param {string} type e.g. detail topic form
 * @param {string} action e.g. insert update delete
 */
async function setData(data, type, action){
    console.log(type, action);
    
    switch (type) {
        case 'detail':
            console.log('detail');
            
            tblTopic.rows().indexes().toArray().some(function(index){
                var d = tblTopic.row(index).data();
                console.log(d.FRMT_CATEGORY , data.act.FRMD_CATEGORY , d.FRMT_RUNNO , data.act.FRMD_RUNNO , d.FRMT_NO , data.act.FRMD_NO);
                
                if(d.FRMT_CATEGORY === data.act.FRMD_CATEGORY && d.FRMT_RUNNO === data.act.FRMD_RUNNO && d.FRMT_NO === data.act.FRMD_NO){
                    const row = tblTopic.row(index);
                    const tr = $(row.node());
                    row.child.hide();
                    // tr.removeClass('shown');
                    // var newData = {...row.data(), detail: data.detail};  // Clone ข้อมูลเก่า แล้วเพิ่ม detail ใหม่
                    // row.data(newData).draw(false);
                    row.data().detail = data.detail;
                    row.child(setDetail(row.data())).show();
                    if(row.data().detail.length == 0) row.child.hide();
                    // tr.addClass('shown');
                    return true;
                }
                // console.log('not found');
                
            });
            // switch (action) {
            //     case 'update':
            //         tblTopic.rows().indexes().toArray().some(function(index){
            //             var d = tblTopic.row(index).data();
            //             // console.log(d.FRMT_CATEGORY , data.act.FRMD_CATEGORY , d.FRMT_RUNNO , data.act.FRMD_RUNNO , d.FRMT_NO , data.act.FRMD_NO);
                        
            //             if(d.FRMT_CATEGORY === data.act.FRMD_CATEGORY && d.FRMT_RUNNO === data.act.FRMD_RUNNO && d.FRMT_NO === data.act.FRMD_NO){
            //                 const row = tblTopic.row(index);
            //                 row.data().detail = data.detail;
            //                 console.log(row.data());
            //                 row.child(setDetail(row.data())).show();
            //                 return true;
            //             }
            //         });
            //         break;
            //     case 'insert':
                    
            //         break;
            
            //     default:
            //         break;
            // }
            break;
        case 'topic':
            console.log('topic');
            
            switch (action) {
                case 'update':
                    tblTopic.rows().indexes().toArray().some(function(index){
                        var rowData = tblTopic.row(index).data();
                        if (rowData.FRMT_CATEGORY === data.FRMT_CATEGORY && rowData.FRMT_RUNNO === data.FRMT_RUNNO && rowData.FRMT_NO === data.FRMT_NO) { 
                            console.log('Row index:', index);
                            tblTopic.row(index).data(data).draw(false);
                            return true;
                        }
                    });
                    break;
                case 'insert':
                    console.log('test');
                    
                    tblTopic.row.add(data).draw(false);
                    break;
                default:
                    tblTopic.rows().indexes().toArray().some(function(index) {
                        var rowData = tblTopic.row(index).data();
                        if (rowData.FRMT_CATEGORY === data.FRMT_CATEGORY && rowData.FRMT_RUNNO === data.FRMT_RUNNO && rowData.FRMT_NO === data.FRMT_NO) {
                            console.log('Row index:', index);
                            tblTopic.row(index).remove().draw(false);
                            return true;
                        }
                    });
                    break;
            }
            break;
    
        default:
            console.log('default');
            
            switch (action) {
                case 'update':
                    table.rows().indexes().toArray().some(function(index){
                        var rowData = table.row(index).data();
                        console.log(rowData.CATEGORY, data.CATEGORY, rowData.FORMNO, data.FORMNO);
                        if (rowData.CATEGORY === data.CATEGORY && rowData.FORMNO === data.FORMNO) { 
                            console.log('Row index:', index);
                            table.row(index).data(data).draw(false);
                            return true;
                        }
                    });
                    // table.rows().every(function() {
                    //     var rowData = this.data(); 
                    //     console.log(rowData); // log
                    //     if (rowData.CATEGORY_NAME === data.CATEGORY_NAME && rowData.FORMNO === data.FORMNO) { 
                    //         const rowIndex = this.index();
                    //         console.log('Row index:', rowIndex);
                    //         table.row(rowIndex).data(data).draw(false);
                            
                    //     }
                    // });
                    break;
                case 'insert':
                    table.row.add(data).draw(false);
                    break;
                default:
                    table.rows().indexes().toArray().some(function(index) {
                        var rowData = table.row(index).data();
                        
                        console.log(rowData.CATEGORY, data.CATEGORY, rowData.FORMNO, data.FORMNO);
                        
                        if (rowData.CATEGORY === data.CATEGORY && rowData.FORMNO === data.FORMNO) {
                            console.log('Row index:', index);
                            table.row(index).remove().draw(false);
                            return true; // หยุด loop ทันทีที่ลบแถวแล้ว
                        }
                    });
                    // table.draw(false);
                    break;
            }
            break;
    }
}

/**
 * Set key to delete
 */
$(document).on('click', '.confirm', function(){
    // const data = table.row($(this).parents("tr")).data();
    const type = $(this).attr('data-type');
    let runno ='', no ='', category ='', seq = '', data ='';
    switch (type) {
        case 'detail':
            console.log($(this).closest('tr'));
            const index = $(this).attr('data-index');
            const tr  = $(this).closest('table').closest('tr').prev();
            const row = tblTopic.row(tr);
            data = row.data().detail[index];
            category  = data.CATEGORY;
            runno     = data.FORMNO;
            no        = data.TOPIC_NO;
            seq       = data.SEQ;
            break;
        case 'topic' :
            data = tblTopic.row($(this).parents("tr")).data()
            category = data.FRMT_CATEGORY;
            runno    = data.FRMT_RUNNO;
            no       = data.FRMT_NO
            break;
        default:
            data = table.row($(this).parents("tr")).data()
            category = data.CATEGORY;
            runno    = data.FORMNO;
            break;
    }
    console.log(data);
    $('#del').attr('d-category', category);
    $('#del').attr('d-runno',runno);
    $('#del').attr('d-no',no);
    $('#del').attr('d-seq',seq);
    $('#del').attr('data-type', type);
});

/** 
 * Delete 
 */
$(document).on('click', '.del', async function(){
    const category = $(this).attr('d-category');
    const runno    = $(this).attr('d-runno');
    const no       = $(this).attr('d-no');
    const seq       = $(this).attr('d-seq');
    const type     = $(this).attr('data-type');
    console.log(no, category);

    const res = await getData({
        ...ajaxOptions,
        url: `${host}form/del`,
        data: { FORMNO: runno, CATEGORY: category, formtype: type, no: no, seq: seq },
    });
    
    if(res.status == true){
        await setData(res.data, type, res.action);

        // table = await createTable(res.data, '#tblMaster', columnMaster);
        showMessage('ลบข้อมูลสำเร็จ','success');
    }else{
        showMessage('ลบข้อมูลไม่สำเร็จ กรุณาลองใหม่ภายหลัง','error');
    }
});

/**
 * Set data to modal detail
 */
$(document).on('click', '.detail', async function(){
    $('.sktTblPreload').removeClass('hidden');
    const data = table.row($(this).parents("tr")).data();
    console.log(data);
    $('.form-name').html(data.FORMNAME);
    const res = await getData({
        ...ajaxOptions,
        url: `${host}form/getFormDetail`,
        data: { no: data.FORMNO, category: data.CATEGORY },
    });
    console.log(res);

    const detail = {};
    const topic = {};
    res.detail.forEach(d => {
        if (!detail[d.TOPIC_NO]) {
            detail[d.TOPIC_NO] = [];
        }
        if (!topic[d.TOPIC_NO]) {
            topic[d.TOPIC_NO] = {
                TOPIC: d.TOPIC,
                TOPICEN: d.TOPICEN,
                CATEGORY: d.CATEGORY,
                FORMNO: d.FORMNO, 
            };
        }
        detail[d.TOPIC_NO].push(d);
    });
    console.log(detail);
    console.log(topic);

    res.topic.forEach(t => {
        for (const [key, value] of Object.entries(detail)) {
            if (t.FRMT_NO == key)t.detail  = value;
        }
    });

    tblTopic = await createTable(res.topic, '#tblTopic', columnTopic, data.CATEGORY, data.FORMNO);
    $('.sktTblPreload').addClass('hidden');
    $('.form-content').removeClass('hidden');
    tblTopic.columns.adjust().draw(false); 

    // let html = '';
    // for (const [key, value] of Object.entries(detail)) {
    //     console.log(key, value);
        // html +=`
        //         <div class="collapse collapse-arrow bg-base-200 join-item">
        //         <input type="radio" name="topic-name" />
        //             <div class="collapse-title text-xl font-medium">
        //                 <span>${topic[key].TOPIC || key+'.'}</span> 
        //                 <span class="text-blue-500">${topic[key].TOPICEN || ''}</span>
        //             </div>
        //             <div class="collapse-content bg-white flex flex-col gap-2">
        //                 <table class="table table-zebra">
        //                     <thead>
        //                         <tr>
        //                             <th>หัวข้อ</th>
        //                             <th>หัวข้อ (ภาษาอังกฤษ)</th>
        //                             <th>ประเภท</th>
        //                             <th>Actions</th>
        //                         </tr>
        //                     </thead>
        //                     <tbody>
        //                         ${value.map(v => 
        //                             `
        //                             <tr class="">
        //                                 <td>${v.DETAIL || ''}</td>
        //                                 <td>${v.DETAILEN || ''}</td>
        //                                 <td>${v.DETAIL_NAME || ''}</td>
        //                                 <td></td>
        //                             </tr> `
        //                         ).join('')}
        //                     </tbody>
        //                 </table>
        //             </div>
        //         </div>`;
    // }
    // console.log(html);
    
    // $('.form-content').html(html);
});

/**
 * hidden form content
 */
$(document).on('click', '#close-modal-detail', function(){
    $('.form-content').addClass('hidden');
});

/**
 * Set detail data by topic
 */
$(document).on('click', 'td.details-control, td.open-details', function(){

    const tr = $(this).closest('tr');
    const row = tblTopic.row(tr);
    // console.log(row.data());
    
    if (row.child.isShown()) {

        row.child.hide();
        tr.removeClass('shown');
    } else {
        console.log(row.data());
        if(row.data().detail && row.data().detail.length > 0){
            row.child(setDetail(row.data())).show();
            tr.addClass('shown');
        }else{
            showMessage('ไม่พบรายละเอียด กรุณาเพิ่มข้อมูลรายละเอียด','warning');
            tr.find('.add').trigger('click');
        }
    }
    // var tr  = $(this).closest('tr');
    // var row = table.row( tr );
    // if ( row.child.isShown() ) {
    //     row.child.hide();
    //     tr.removeClass('shown');
    // }else {
    //     row.child(setDrawing(row.data())).show();
    //     tr.addClass('shown');
    //     tr.next('tr').css('background-color','#ffffff');
    //     tr.next('tr').find('td:eq(0)').css('padding-left','50px').css('padding-right','50px');
    //     $('[data-toggle="tooltip"]').tooltip();
    // }
});

/**
 * Set table detail
 * @param {array} d 
 * @returns 
 */
function setDetail(d){	
    console.log(d);
    
    var table = `<table class="table ml-auto w-[95%]">
                    <thead>
                        <tr>
                        <th>รายละเอียด</th>
                        <th>รายละเอียด (ภาษาอังกฤษ)</th>
                        <th>ประเภท</th>
                        <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>`;
    d.detail.forEach((data,index) => {
        table += `<tr class="">
                    <td>${data.DETAIL || ''}</td>
                    <td>${data.DETAILEN || ''}</td>
                    <td>${data.DETAIL_NAME || ''}</td>
                    <td>
                        <div class="flex items-center justify-center gap-3">
                            <label for="drawer-detail" class="drawer-button btn btn-sm btn-ghost btn-circle tooltip tooltip-left flex items-center edit" data-type="detail" data-index="${index}"  data-tip="แก้ไขรายละเอียด">
                                <i class="icofont-ui-edit"></i>
                            </label>
                            <button class="drawer-button btn btn-sm btn-ghost btn-circle tooltip tooltip-left confirm" data-type="detail" data-index="${index}"  data-tip="ลบรายละเอียด" onclick="modal_delete.showModal()">
                                <i class="icofont-ui-delete text-red-500"></i>
                            </button>
                        </div>
                        </td>
                </tr>`;
    });
    table += `</tbody></table>`;
    return table;
    // return detailForm(table, d);
}


/**
 * preview image
 */
$(document).on('click','.preview-image', function(){
    const base64 = $(this).attr('d-base64');
    const img = [{src: `<img src="${base64}" alt="" style="width:100%;">`, type: "html"}];
    new Fancybox(img);
});

/**
 * Required select2
 */
$(document).on('change', 'select.req', async function(){
    RequiredElement($(this));
});

/**
 * Create table
 * @param {array} data 
 * @returns 
 */
async function createTable(data ,id, col, category = null, formno = null) {
    // const id = "#tblMaster";
    const opt = { ...tableOption };
    // opt.ordering = false;
    opt.data = data;
    opt.order = [[0, "asc"]];
    opt.dom = domScroll;
    opt.columns = col

    opt.columnDefs = [
        { orderable: false, targets: '_all' } // ปิดการเรียงในคอลัมน์ที่กำหนด
    ];
    if(id == '#tblMaster'){
        opt.initComplete = function () {
            $(`${id}_wrapper .table-option`).append(`
                <label for="drawer-form" class="drawer-button btn btn-sm btn-primary add" data-type="form">
                    เพิ่มฟอร์ม
                </label>`);
            initJoin(id);
        };
        
        opt.drawCallback = function (settings) {
            tableGroup(6, this.api());
        }
    } else {
        opt.initComplete = function () {
            $(`${id}_wrapper .table-option`).append(`
                <label for="drawer-topic" class="drawer-button btn btn-sm btn-primary add" d-category="${category}" d-formno="${formno}" data-type="topic">
                    เพิ่มหัวข้อ
                </label>`);
            initJoin(id);
        };
        opt.createdRow = function (row, data, dataIndex) {
            // console.log(dataIndex % 2);
        
             if (dataIndex % 2 == 1) {
                $("td", row).eq(0).addClass("!bg-gray-100");
                $(row).addClass("!bg-gray-100");
            }else{
                $("td", row).eq(0).addClass("!bg-primary-content");
                $(row).addClass("!bg-primary-content");
            }
        };
    }
    return $(id).DataTable(opt);
}

/**
 * Toggle group
 */
$(document).on('click', 'tr.group-row', function () {
    clickTableGroup(table, 'CATEGORY_NAME', $(this));
});

/**
 * Open fancy image dialog
 */
$(document).on('click','.openImgDialog', async function (){
    const data = tblTopic.row($(this).closest('tr')).data()
    const btnAdd = $('#addFancyImage');
    btnAdd.attr('category',data.FRMT_CATEGORY);
    btnAdd.attr('formno',data.FRMT_RUNNO);
    btnAdd.attr('no',data.FRMT_NO);
    const res = await getData({
        ...ajaxOptions,
        url: `${host}form/getImage`,
        data : data,
    })
    console.log('open dialog : ', res);
    // res = setAttr(res);
    // console.log(await setAttr(res));
    
    await setFancyImage(await setAttr(res), data.FRMT_TOPIC);
    // await setFancyImage(res);
    await openImgDialog();

});

/**
 * Add Image
 */
$(document).on('click', '#addFancyImage', async function(){
    try {
        const e = $(this);
        const frm = $('#formAddFancyImage');
        const formData = new FormData(frm[0]);
        formData.set('FRMI_CATEGORY',e.attr('category'));
        formData.set('FRMI_RUNNO',e.attr('formno'));
        formData.set('FRMI_NO',e.attr('no'));
        formData.set('userno', userInfoData.sempno);
        const res = await getData({
            ...ajaxOptions,
            url: `${host}form/addImage`,
            data : formData,
            processData: false,
            contentType: false,
        });
        console.log(res);
        if(res.status == 1){
            showMessage('เพิ่มรูปภาพสำเร็จ', 'success');
            
            await setFancyImage(await setAttr(res.data));
            frm.trigger("reset");
            $('#cancelFancyImage').trigger('click');
        } else {
            showMessage('เพิ่มรูปภาพไม่สำเร็จ กรุณาลองใหม่ภายหลัง', 'warning')
        }
        // await addFancyImage('form/addImage', formData);
    } catch (e) {
        console.error('add image', e);
        showMessage(`เกิดข้อผิดพลาด: ${e.message} กรุณาลองใหม่อีกครั้งหรือติดต่อ Admin Tel:2038`);
        const mail = {...mailOpt};
        mail.BODY = [
            `เพิ่มรูปภาพฟอร์ม ${e}`
        ];
        sendMail(mail); 
        return true;
    }
});

async function setAttr(data){
    console.log('set attr:', data);
    return data.map((d)=>{
        d.attr =`data-frmi_category="${d.FRMI_CATEGORY}" 
                 data-frmi_runno="${d.FRMI_RUNNO}" 
                 data-frmi_no="${d.FRMI_NO}" 
                 data-frmi_id="${d.FRMI_ID}"`
        return d;
    });
}

$(document).on('click', '.deleteFancyImage', async function(e){
    try {
        const FRMI_CATEGORY = $(this).data("frmi_category");
        const FRMI_RUNNO    = $(this).data("frmi_runno");
        const FRMI_NO = $(this).data("frmi_no");
        const FRMI_ID = $(this).data("frmi_id");
        console.log(FRMI_CATEGORY, FRMI_RUNNO, FRMI_NO, FRMI_ID);
        
        // return;
        const res = await getData({
            ...ajaxOptions,
            url: `${host}form/deleteImage`,
            data : {
                FRMI_CATEGORY : FRMI_CATEGORY,
                FRMI_RUNNO    : FRMI_RUNNO,
                FRMI_NO : FRMI_NO,
                FRMI_ID : FRMI_ID
            },
        });
        if(res.status == 1){
            showMessage('ลบรูปภาพสำเร็จ', 'success');
            await setFancyImage(await setAttr(res.data));
        } else {
            showMessage('ลบรูปภาพไม่สำเร็จ กรุณาลองใหม่ภายหลัง', 'warning')
        }
    }catch (e) {
        console.error('delete image', e);
        showMessage(`เกิดข้อผิดพลาด: ${e.message} กรุณาลองใหม่อีกครั้งหรือติดต่อ Admin Tel:2038`);
        const mail = {...mailOpt};
        mail.BODY = [
            `ลบรูปภาพฟอร์ม ${e}`
        ];
        sendMail(mail); 
        return true;
    }
});




