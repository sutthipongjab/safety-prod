import $ from "jquery";
import "select2";
import "select2/dist/css/select2.min.css";
import 'datatables.net-dt';
import "datatables.net-dt/css/dataTables.dataTables.min.css";
import "datatables.net-responsive";
import "datatables.net-responsive-dt";
// import "datatables.net-dt/css/dataTables.dataTables.min.css";
import "datatables.net-responsive-dt/css/responsive.dataTables.min.css";
import { jsPDF } from "../lib/pdf.js";
import html2canvas from 'html2canvas-pro';
import {host, tableOption, showMessage, userInfoData ,ajaxOptions, getData , select2Option, domScroll, initJoin} from "../utils.js";
import {writeExcelTemp, writeOpt, exportExcel, colToNumber, numberToCol} from '../_excel.js';
import {getfileInPath} from '../_file.js';
import {createColumnFilters} from '../filter.js';
import {createStamp, loadFont, optAutoTable} from '../_jsPDF.js';

var sectionList, revisionList, table, freesiaUPC, freesiaUPC_BOLD, userControl, ownerCode;
var chmDetail = [];


$(document).ready(async function () {
    freesiaUPC = await loadFont(host, 'freesiaUPC/upcfl.ttf');
    freesiaUPC_BOLD = await loadFont(host, 'freesiaUPC/upcfb.ttf');
    $('#owner').select2({...select2Option, placeholder: 'เลือกแผนก'});
});

$(document).on('change', '#owner', async function(){
    ownerCode = $(this).val();
    const data = await getData({ 
        ...ajaxOptions ,
        url: `${host}chemical/chemicalList/getChmSec`,
        data:{OWNERCODE: $(this).val(), OWNER: $(this).find('option:selected').attr('owner')}
    });
    if(data.status == 1){
        $('.divider').removeClass('hidden');
        sectionList  = data.sec;
        revisionList = data.rev;
        userControl  = data.userControl;
        setTable(data.data,data.sec);
    }else{
        showMessage('ไม่พบข้อมูล กรุณาเลือกใหม่');
    }
});





// $(document).on('click', '#cancleRev', function(){
//     $('#modal_rev').prop('checked', false);
// });

// $(document).on('click', '#modal_rev', function(){
//     console.log('modal_rev');
//     $('#current-revision').val(revisionList.MASTER).focus();
// });

// $(document).on('click', '#saveRev', async function(){
//     const rev = $('#current-revision').val();
//     if(rev == ''){
//         showMessage('กรุณากรอก Revision','warning');
//         return;
//     }
//     const ajax = {...ajaxOptions};
//     ajax.url = `${host}Chemical/chemicalList/updateRev`;
//     ajax.data = {rev:rev, own:'MASTER', USER_UPDATE: userInfoData.sempno};
//     await getData(ajax).then(async (res) => {
//         if(res.status == true){
//             revisionList = res.rev;
//             showMessage('บันทึกข้อมูลสำเร็จ','success');
//             $('.revision-master').html(`Rev. No. ${rev.toUpperCase()}`);
//             $('#cancleRev').trigger('click');
//             // getMaster();
//         }else if(res.status == 3){
//             showMessage('กรุณากรอก Revision ใหม่','warning');

//         }else{
//             showMessage('บันทึกข้อมูลไม่สำเร็จ กรุณาลองใหม่ภายหลัง');
//         }
//     });
// });



/**
 * Set table
 * @param {object} data 
 * @param {object} sec 
 */
async function setTable(data, sec){
    console.log(data, sec);
    
    let html =``;
    const columns = [
        
        {
            data:'AMEC_SDS_ID',        
            title: 'Detail',
            render: function(data, type, row, meta){
                return `<div class="">
                            <label for="modal_detail" class="drawer-button btn btn-xs btn-neutral flex items-center tooltip tooltip-right" data-tip="รายละเอียด " id="view-detail">
                                <i class="icofont-eye-alt"></i>
                            </label>    
                        </div>
                        `
            }

        }, 
        {data:'AMEC_SDS_ID',        title: 'ID'}, 
        {data:'CHEMICAL_NAME',      title: 'ชื่อสารเคมี'}, 
        {data:'REV',                title: 'REV'},
        {data:'EFFECTIVE_DATE',     title: 'Effective Date'}, 
        {data:'RECEIVED_SDS_DATE',  title: 'Received SDS Date'}, 
        {data:'USED_FOR',           title: 'การใช้ประโยชน์', className: "min-w-[250px]"}, 
        {
            data:'USED_AREA',          
            title: 'จุดใช้งาน',
            render: function(data, type, row, meta){
                return data == null ? '' : data.replace(/\|/g, ', ');
            }
        },
        {
            data:'KEEPING_POINT',      
            title: 'จุดจัดเก็บ',
            render: function(data, type, row, meta){
                return data == null ? '' : data.replace(/\|/g, ', ');
            }
        },
        {data:'QTY',                title: 'จำนวน'},
        {
            data:'REC4052',            
            title: 'REC4052',
            render: function(data, type, row, meta){
                return data == 1 ? 'OK' : 'N/A';
            }
        },
        {
            data:'REC4054',            
            title: 'REC4054',
            render: function(data, type, row, meta){
                return data == 1 ? 'OK' : 'N/A';    
            }
        },
        {data:'CLASS',              title: 'Class'},
    ];
        
        for (const [key, value] of Object.entries(data)) {
            console.log(data);
            
            const index = Object.keys(data).indexOf(key);
            const check = index == 0 ? 'checked' : ''; 
            const org = key.slice(0, -1).replace(/\s+/g, '_');
            console.log(org);
            
            html += `
                    <div  class="w-full">
                        <div class="font-bold">
                            <span class="revision-master">Rev. No. ${revisionList[key]}</span>
                        </div>
                        <div class="overflow-x-auto w-full">
                            <table class="table" id="${org}"></table>
                        </div>
                    </div>`;
        }
        html += `</div>`;
        $('.data-table').html(html);
        Object.entries(data).forEach(async ([key, value], index) => {
            const org = key.slice(0, -1).replace(/\s+/g, '_');
            console.log(org);
            
            table = await createTable(`#${org}`, value, columns);
            createColumnFilters(table, '1-12');
        });
}



/**
 * Table upload
 * @param {string} tableID 
 * @param {object} data 
 * @returns 
 */
async function createTable(tableID, data, columns, maxH = '60vh'){
    console.log('create table',tableID, data, columns);
    
    const opt = { ...tableOption };
    opt.lengthMenu = [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']]
    opt.order = [[0, 'asc']];
    opt.data = data;
    opt.dom  = domScroll.replace('max-h-[60vh]', `max-h-[${maxH}]`);
    opt.columns = columns;
    opt.initComplete = function () {
        $(`${tableID}_wrapper .table-option`).append(`
            <label for="" class="btn btn-sm join-item btn-success flex items-center max-w-xs tooltip tooltip-left" data-tip="ดาว์นโหลดไฟล์ Excel" id="exportExcel" tableID="${tableID}">
                <i class="icofont-file-excel text-xl "></i>
            </label>
            <label for="" class="btn btn-sm join-item btn-error flex items-center  max-w-xs tooltip tooltip-left" data-tip="ดาว์นโหลดไฟล์ PDF" id="exportPDF" tableID="${tableID}">
                <i class="icofont-file-pdf text-xl" ></i>
            </label>`);
        initJoin(tableID);
    };
    opt.columnDefs = [
        { orderable: false, targets: '_all' } // ปิดการเรียงในคอลัมน์ที่กำหนด
    ];
    
    return $(tableID).DataTable(opt);
}

    
/**
 * Export excel
 */
$(document).on('click', '#exportExcel',async function(){
    const tableID = $(this).attr('tableID');
    const table = $(tableID).DataTable();
    var data = table.rows().data().toArray();
    if(data.length > 0){
        data = data.map(item => {
            delete item.STATUS;
            return item;
        });
        exportMasterSec(data, tableID.replace('#','').replace('_',' '));
    }else{
        showMessage('ไม่พบข้อมูล','warning');
    }
});


/**
 * Set data to export excel safety chemical master section
 * @param {object} data 
 * @param {string} fileName e.g. QC1 SEC
 */
async function exportMasterSec(data, fileName){
    console.log(data);
    const template = await getfileInPath('assets/file/Template','Chemical list section.xlsx')
    console.log('template',template);
    if(template.length > 0){
        const file = template[0].buffer;
        const opt = {...writeOpt};
        let isProcessed = false;
        opt.startCol = colToNumber('B');
        opt.startRow = 8;
        opt.customSheet = async (workbook) => {
            if (isProcessed) return; // ถ้าเคยทำงานแล้ว ให้หยุด
            isProcessed = true;
            const sheet = workbook.worksheets[0];
            sheet.headerFooter = {
                oddHeader: `&L Rev. No. ${revisionList[fileName+'.']}`, // ตรงซ้ายของ Header
            };
            sheet.getCell(1, 1).value = `LIST OF ${fileName} CHEMICAL SDS REGISTRATION RECORDS`;

            sheet.autoFilter = {
                from: `A${opt.startRow-1}`, 
                to:   `${numberToCol(Object.keys(data[0]).length+1)}${opt.startRow-1}`, 
            };
            
            console.log(data.length);
            
            sheet.getCell(opt.startRow+1, colToNumber('J')).value = { formula: `=SUM(J${opt.startRow}:J${opt.startRow+data.length-1})` }; 

            await sheet.duplicateRow(opt.startRow, data.length, true);

            let rowIndex = opt.startRow;
            data.forEach( (d, index) => {
                rowIndex = opt.startRow + index; 
                let colIndex   = opt.startCol;
                    
                sheet.getCell(rowIndex, 1).value = index+1; // No.

                Object.entries(d).forEach(([key, value]) => {
                    if(['QTY', 'AMEC_SDS_ID', 'CLASS'].includes(key)){
                        sheet.getCell(rowIndex, colIndex).value = parseInt(value); 
                    }else if(['REC4052', 'REC4054'].includes(key)){
                        sheet.getCell(rowIndex, colIndex).value = parseInt(value) == 1 ? 'OK' : 'N/A';
                    }else{
                        sheet.getCell(rowIndex, colIndex).value = value; 
                    }
                    colIndex++;
                });
            });
            // console.log(fileName,userControl,userControl[`${fileName}.`] );
            rowIndex+=3;
            const userCon = userControl[`${fileName}.`] || '';
            if(userCon != ''){
                if(userCon.includes('|')){
                    console.log(rowIndex,userCon.split('|').length+1);
                    
                    // await sheet.duplicateRow(rowIndex, userCon.split('|').length+1, true);
                    sheet.insertRow(rowIndex, []);
                    sheet.getCell(rowIndex, 3).value = 'CONTROLLER'; 
                    rowIndex++;
                    userCon.split('|').forEach((name, index) => {
                        sheet.insertRow(rowIndex, []);
                        sheet.getCell(rowIndex, 3).value = name; 
                        rowIndex++;
                    });
                }else{
                    sheet.getCell(rowIndex, 3).value = userCon; 
                }
            }else{
                // rowValues[3] = 'CONTROLLER';
                // await sheet.insertRow(rowIndex, rowValues);
                // rowValues[3] = '-';
                // await sheet.insertRow(rowIndex+1, rowValues);
                // console.log(rowIndex);
                sheet.insertRow(rowIndex, []);
                sheet.getCell(rowIndex, 3).value = 'CONTROLLER'; 
                sheet.insertRow(rowIndex+1, []);
                sheet.getCell(rowIndex+1, 3).value = '-'; 
                // sheet.insertRow(rowIndex, [ , , , 'CONTROLLER']);
                // sheet.insertRow(rowIndex+1, [ , , , '-']);


                
                // sheet.getRow(19).values = []
                // console.log(sheet.rowCount, sheet.getRow(19))
                // sheet.getCell(rowIndex, 3).value = 'CONTROLLER'; 
                // await sheet.insertRow(rowIndex, [])
                // await sheet.duplicateRow(rowIndex, 2);
                // sheet.getCell(rowIndex, 3).value = 'CONTROLLER'; 
                // sheet.getCell(rowIndex+1, 3).value = '-'; 
            }
            

        };
        const wb = await writeExcelTemp(file.buffer,opt);
        console.log('wb',wb);
        exportExcel(wb, `Chemical list ${fileName}`)
    }else{
        showMessage('ไม่พบไฟล์ Template ติดต่อ admin 2038');
    }
}



$(document).on('click', '#exportPDF',async function(){

    const ajax = {...ajaxOptions};
    ajax.url = `${host}Chemical/chemicalList/getDataForPDF`;
    ajax.data = {owner: $(this).attr('tableID').replace('#','').replace('_',' ')};
    await getData(ajax).then(async (res) => {
        console.log(res);
        const currentYear = new Date().getFullYear();
        const currentSMon = new Date().toLocaleString('default', { month: 'short' });
        const currentDay  = new Date().getDate();
        let fileName = `List of chemical Rev`;

        const x = 282.5;
        const y = 20;
        const headerHeight = 18;
        
        const classData = res.class.map((item) => {
            return {Class: `${item.TYPE_NAME} ${item.TYPE_DETAIL}`}
        });
        const columnClass = [{ header: "Remark: UN Classification of Hazardous Substances", dataKey: "Class" }];

        const colUsrCon = [{header: "CONTROLLER", dataKey: 'Controller'}]
        const controllers = res.userControl[res.owner+'.'] || [];
        console.log(classData);
        console.log(controllers);
        
        let usrCon = [];
        if(controllers.length > 0){
            if(controllers.includes('|')){
                console.log(controllers.split('|'));
                
                controllers.split('|').forEach((name, index) => {
                    console.log(name);
                    
                    usrCon.push({Controller: name});
                    // return {Controller: name}
                });
            }else{
                usrCon.push({Controller: controllers});
            }
        }else{
            usrCon.push({Controller: '-'});
        }
        console.log(usrCon);
        

       
        const doc = new jsPDF({
            orientation: 'landscape',
        })

        doc.addFileToVFS("upcfl.ttf", freesiaUPC);
        doc.addFont("upcfl.ttf", "freesiaUPC", "normal");
        doc.addFileToVFS("upcfb.ttf", freesiaUPC_BOLD);
        doc.addFont("upcfb.ttf", "freesiaUPC", "bold");
        doc.setFont("freesiaUPC");
        
        doc.setFontSize(20)
        doc.text(`LIST OF SDS_CHEMICAL USER OR HANDLING in ${currentYear}`, 6, 10)

        //สร้างรูปทรงสี่เหลี่ยม x y w h
        doc.rect(5, 6, 120, 6);
        doc.rect(273, 6, 19, 22);
        doc.rect(273, 6, 19, 5);

        
    
        const tableID = $(this).attr('tableID');
        const table = $(tableID).DataTable();
        const own = tableID.replace('#','').replace('_',' ');
        var data = JSON.parse(JSON.stringify(table.rows().data().toArray()));
        data.forEach((item, index) => {
            item.No = index + 1;
            item.REC4052 = item.REC4052 == '1' ? 'OK' : 'N/A';
            item.REC4054 = item.REC4054 == '1' ? 'OK' : 'N/A';
        });
        console.log(data);
        
        const columnsData = [
            { header: 'No.',                   dataKey: 'No' },
            { header: 'ID',                    dataKey: 'AMEC_SDS_ID' },
            { header: 'ชื่อสารเคมี / ชื่อทางการค้า', dataKey: 'CHEMICAL_NAME' },
            { header: 'REV',                   dataKey: 'REV' },
            { header: 'EFFECTIVE DATE',        dataKey: 'EFFECTIVE_DATE' },
            { header: 'RECEIVED SDS DATE',     dataKey: 'RECEIVED_SDS_DATE' },
            { header: 'การใช้ประโยชน์',          dataKey: 'USED_FOR' },
            { header: 'จุดใช้งาน ',              dataKey: 'USED_AREA' },
            { header: 'จุดจัดเก็บ',               dataKey: 'KEEPING_POINT' },
            { header: 'จำนวน',                 dataKey: 'QTY' },
            { header: 'REC 4052',              dataKey: 'REC4052' },
            { header: 'REC 4054',              dataKey: 'REC4054' },
            { header: 'CLASS',                 dataKey: 'CLASS' },
        ];
        const columnStyles = {
            No:                { cellWidth: 7 },
            AMEC_SDS_ID:       { cellWidth: 10 },
            CHEMICAL_NAME:     { cellWidth: 'auto' },
            REV:               { cellWidth: 10 },
            EFFECTIVE_DATE:    { cellWidth: 18 },
            RECEIVED_SDS_DATE: { cellWidth: 18 },
            USED_FOR:          { cellWidth: 'auto' },
            USED_AREA:         { cellWidth: 'auto' },
            KEEPING_POINT:     { cellWidth: 'auto' },
            QTY:               { cellWidth: 10 },
            REC4052:           { cellWidth: 10 },
            REC4054:           { cellWidth: 10 },
            CLASS:             { cellWidth: 10 },
        };

        createStamp(doc, x, y, res.manager[0].SPOSNAME, res.manager[0].aprDate, res.manager[0].SNAME.split(' ')[0])
        const opt = {...optAutoTable}
        opt.headStyles.minCellHeight = headerHeight,
        opt.headStyles.fillColor = [173, 216, 230]
        opt.startY = 30;
        opt.columns = columnsData,
        opt.columnStyles = columnStyles, 
        opt.body = data,
        opt.didParseCell = (data) => {
            if (data.section === 'body') {
                if([0, 1, 3, 9, 10, 11, 12].includes(data.column.index)){
                    data.cell.styles.halign = 'center'; // จัดตำแหน่งข้อความตรงกลาง
                }
            }
        },
        opt.didDrawPage = (data) => {
            doc.setFontSize(10)
            doc.text(`REV. NO. ${revisionList[own+'.']}`, 5, 5);
        }
        doc.autoTable(opt);
        fileName = `${own} List of chemical Rev ${revisionList[own+'.']}.pdf`;
       
        console.log(optAutoTable);

        doc.autoTable({
            ...optAutoTable,
            headStyles:{font: "freesiaUPC", fontSize: 14, fillColor: [220, 220, 220]},
            columns: colUsrCon,
            body: usrCon,    
            startY: doc.lastAutoTable.finalY
        });
        
        const optCls = {...optAutoTable};
        optCls.headStyles = { font: "freesiaUPC", fontSize: 14, fillColor: [220, 220, 220]  };
        optCls.columns =  columnClass;
        optCls.body = classData;
        optCls.startY = doc.lastAutoTable.finalY; 

        doc.autoTable(optCls);
        doc.save(fileName)
    });

});

$(document).on('click', '#exportPDFDetail', function(){
    console.log(chmDetail);
    html2canvas(document.getElementById("print-preview")).then(function(canvas) {
        console.log(canvas);
         const imgData = canvas.toDataURL('image/png');

        // สร้าง PDF (A4, หน่วย mm)
        const doc = new jsPDF({
            orientation: 'portrait',
            unit: 'mm',
            format: 'a4'
        });

        // ขนาดหน้ากระดาษ A4 (mm)
        const pdfWidth = doc.internal.pageSize.getWidth();    // 210 mm
        const pdfHeight = doc.internal.pageSize.getHeight();  // 297 mm

        // กำหนด margin (mm)
        const margin = 10; 

        // ขนาด canvas (px) 
        const canvasWidth = canvas.width;
        const canvasHeight = canvas.height;

        // พื้นที่ว่างหลังหัก margin 2 ข้าง
        const availableWidth = pdfWidth - margin * 2;
        const availableHeight = pdfHeight - margin * 2;

        // คำนวณอัตราส่วน (ratio) เพื่อย่อ/ขยายภาพให้พอดี
        const ratio = Math.min(availableWidth / canvasWidth, availableHeight / canvasHeight);

        // ขนาดภาพหลังย่อ/ขยาย
        const imgW = canvasWidth * ratio;
        const imgH = canvasHeight * ratio;

        // เพิ่มภาพโดยเลื่อนจากขอบกระดาษ margin mm
        doc.addImage(imgData, 'PNG', margin, margin, imgW, imgH);

        // บันทึก PDF
        doc.save(`${chmDetail[0].OWNER}_${chmDetail[0].AMEC_SDS_ID}_${chmDetail[0].CHEMICAL_NAME}.pdf`);
    });
    return
    
});


/**
 * Chemical detail
 */
$(document).on('click', '#view-detail',async function(){
    $('.detail-Loading').removeClass('hidden');
    $('.detail').addClass('hidden');

    const data   = table.row($(this).parents("tr")).data();
    const chmID  = data.AMEC_SDS_ID;
    const res = await getData({
        ...ajaxOptions,
        url: `${host}chemical/chemicalList/getDetail`,
        data: { OWNERCODE: ownerCode, id: chmID }
    });
    if(res.detail.length > 0){
        chmDetail = res.data;
        $('.information').find('input').prop('checked',true);
        $('#detail-chemical').prop('checked',true);

        const d = res.detail[0];
        console.log( res, d);
            
        const genaralCol = ` 
            <span class="font-bold">ชื่อสารเคมี</span>
            <span class="font-bold">ปริมาณที่ใช้</span>
            <span class="font-bold">ใช้สำหรับ</span>
            <span class="font-bold">จุดใช้งาน</span>
            <span class="font-bold">จุดจัดเก็บ</span>
            <span class="font-bold layout-file">Layout File</span>
        `;
        const generalData = `
            <span class="text-gray-500">${d.CHEMICAL_NAME}</span>
            <span class="text-gray-500">${d.QUANTITY_KG} ${d.QUANTITY_TYPE}</span>
            <span class="text-gray-500">${d.USED_FOR}</span>
           
            <span class="text-gray-500">${d.USED_AREA ? (d.USED_AREA.includes('|') ? d.USED_AREA.replace(/\|/g, ', ') : d.USED_AREA ): '-'}</span> 
            <span class="text-gray-500">${d.KEEPING_POINT ? (d.KEEPING_POINT.includes('|') ? d.KEEPING_POINT.replace(/\|/g, ', ') : d.KEEPING_POINT) : '-'}</span>
        `;

        const layout = ` 
            <span>
                ${d.LONAME && d.LFNAME ? `
                <a href="${host}chemical/webflow/downloadFile/${d.LONAME}/${d.LFNAME}" target="_blank">
                    <i class="icofont-download text-blue-500"></i>
                    <span class="text-gray-500">${d.LONAME}</span>
                </a>
                ` : '-'}
            </span>
        `;
        const safety1Col = `
            <span class="font-bold">กฎหมายที่เกี่ยวข้อง</span>
            <span class="font-bold">ตรวจสุขภาพเพิ่มเติม</span>
            <span class="font-bold">ตรวจสอบสิ่งแวดล้อมเพิ่มเติม</span>
            <span class="font-bold">อุปกรณ์ PPE ที่ต้องใช้</span>
            <span class="font-bold">ผู้ควบคุมพิเศษ</span>
            <span class="font-bold">ระบบป้องกันและระงับอัคคีภัย</span>
        `;
        const safety1Data = `
            <span class="text-gray-500 ">${d.LAW}</span> 
            <span class="text-gray-500">${d.BEI}</span> 
            <span class="text-gray-500">${d.EVM_PARAMETER}</span>
            <span class="text-gray-500">${d.PPE_EQUIPMENT}</span>
            <span class="text-gray-500">${d.SPECIAL_CONTROLLER}</span>
            <span class="text-gray-500"> ${d.FIRE_EQUIPMENT}</span>
        `;

        const efcCol = `
            <span class="font-bold">การควบคุมน้ำเสีย , ของเสีย</span>
            <span class="font-bold">ผลการตรวจสอบ</span>
            <span class="font-bold">เหตุผลที่ไม่ผ่าน</span>
        `;

        const efcData = `
            <span class="text-gray-500">${d.EFC_WASTE}</span>
            <span class="text-gray-500">${d.EFC_RESULT}</span>
            <span class="text-gray-500">${d.EFC_REASON}</span>
        `;

        const bpCol = `
            <span class="font-bold">เจ้าหน้าที่จัดซื้อสารเคมี</span>
            <span class="font-bold">Product Code หรือ Item No.</span>
            <span class="font-bold">บริษัทผู้ขาย  </span>
            <span class="font-bold">เลขประจำตัวผู้เสียภาษี </span>
            <span class="font-bold">ที่อยู่ผู้ขาย  </span>
            <span class="font-bold sds-file">SDS ต้นฉบับ </span>
        `;

        const bpData = `
            <span class="text-gray-500">${d.PUR_CODE} ${d.PUR_INCHARGE }</span>
            <span class="text-gray-500">${d.PRODUCT_CODE}</span>
            <span class="text-gray-500">${d.VENDOR}</span>
            <span class="text-gray-500">${d.VENDOR_TAX_NO}</span>
            <span class="text-gray-500">${d.VENDOR_ADDRESS}</span>
        `;

        const sds = `
            <span>
                ${d.SONAME && d.SFNAME ? `
                    <a href="${host}chemical/webflow/downloadFile/${d.SONAME}/${d.SFNAME}" target="_blank">
                        <i class="icofont-download text-blue-500"></i>
                        <span class="text-gray-500">${d.SONAME}</span>
                    </a>
                    ` : '-'}
            </span>
        `;

        const safety2Col = `
            <span class="font-bold">รายการวัตถุอันตราย</span>
            <span class="font-bold">CAS No.</span>
            <span class="font-bold">ชื่อสาร </span>
            <span class="font-bold">น้ำหนัก (%)  </span>
            <span class="font-bold">ประเภทวัตถุอันตราย </span>
            <span class="font-bold">สารที่สามารถก่อมะเร็ง  </span>
            <span class="font-bold">ข้อมูลเพิ่มเติม</span>
            <span class="font-bold">กลุ่มการเกิดมะเร็ง  </span>
            <span class="font-bold">CLASS  </span>
        `;

        const safety2Data = `
            <span class="text-gray-500">${d.HAZARDOUS}</span>
            <span class="text-gray-500">${d.CAS_NO}</span>
            <span class="text-gray-500">${d.SUBSTANCE_NAME}</span>
            <span class="text-gray-500">${d.SUBSTANCE_WEIGHT}</span>
            <span class="text-gray-500">${d.SUBSTANCE_TYPE}</span>
            <span class="text-gray-500">${d.CARCINOGENS}</span>
            <span class="text-gray-500">${d.CARCINOGENS_DETAIL}</span>
            <span class="text-gray-500">${d.CARCINOGENS_TYPE}</span>
            <span class="text-gray-500">${d.CLASS_DETAIL}</span>
        `;
        $('.general-detail').html(genaralCol+generalData+layout);
        $('.safety1-detail').html(safety1Col+safety1Data);
        $('.efc-detail').html(efcCol+efcData);
        $('.bp-detail').html(bpCol+bpData+sds);
        $('.safety2-detail').html(safety2Col+safety2Data);

        console.log($('.preview-detail').find('.layout-file'));
        
        $('.preview-chmName').html(`ข้อมูลสารเคมี ${d.CHEMICAL_NAME}`);
        $('.preview-detail').html(genaralCol+safety1Col+efcCol+bpCol+safety2Col+generalData+safety1Data+efcData+bpData+safety2Data);
        $('.preview-detail').find('.layout-file, .sds-file').addClass('hidden');
        $('.preview-detail').find('span').addClass('p-3 border');
        $('.detail').removeClass('hidden');
        $('.detail-Loading').addClass('hidden');
    }else{
        showMessage('ไม่พบข้อมูล','warning');
        $('#modal_detail').prop('checked',false);
    }
    console.log(data);
    console.log(res);
});

// function checkroll(val, t = 'จำเป็น', f = 'ไม่จำเป็น'){
//     if(!val) return '-';
//     return val == 1 ? `<span class="text-green-500 ">${t}</span>` : `<span class="text-red-500 ">${f}</span>`;
// }




 
