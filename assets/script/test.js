import $ from "jquery";

import {host, showpreload, hidepreload, checkAuthen, tableOption, showMessage, userInfoData ,ajaxOptions, getData ,setDatePicker, dateFormat, select2Option, domScroll, initJoin, RequiredElement, requiredForm, removeClassError} from "./utils.js";
// import {writeExcelTemp, writeOpt, exportExcel, colToNumber, numberToCol, fill, border, alignment} from '../_excel.js';
import {getfileInPath, getArrayBufferFile} from './_file.js';
import {createColumnFilters} from './filter.js';
import {createStamp, loadFont, optAutoTable} from './_jsPDF.js';

import ExcelJS from 'exceljs';

$(document).on('click', '#exceljs',async function(){
    const data = [];
    const fileName = 'test'
    const template = await getfileInPath('assets/file/template/old/','test.xlsx')
    const file = template[0].buffer;
    const startCol = 2;
    const startRow = 8;
    var workbook = new ExcelJS.Workbook();
        await workbook.xlsx.load(template[0].content, {base64:true}).then( async (workbook) => {
        // await workbook.xlsx.load(file).then( async (workbook) => {
            const sheet = workbook.worksheets[0];
            sheet.getCell(1, 1).value = `LIST OF ${fileName} CHEMICAL SDS REGISTRATION RECORDS`;
            
            console.log(data.length);
            data.forEach( (d, index) => {
                const rowIndex = startRow + index; 
                let colIndex   = startCol;
                    
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
            // สร้างไฟล์ Excel เป็น Blob
            workbook.xlsx.writeBuffer().then(function (buffer) {
                const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });

                // สร้างลิงก์ดาวน์โหลด
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = `${fileName}.xlsx`;
                link.click();
            });
        })


});



// import ExcelJS from 'exceljs';
// const workbook = new ExcelJS.Workbook();
// const sheet = workbook.addWorksheet('sheet1'); // เพื่มชีท และตั้งชื่อชีท

// const columns = [
//     {header : 'ชื่อหมวดหมู่' , key : 'ITEMS_NAME'},
//     {header : 'เจ้าของพื้นที่' , key : 'OWNER_SECTION'},
//     {header : 'ประเภท'    , key : 'CLASS'},
//     {header : 'จำนวน'     , key : 'AMOUNT'},
// ];
// // ตั้งชื่อ cloumn และ key เพื่อให้สอดคล้องกับข้อมูล
// sheet.columns = columns;  
// // เพิ่มข้อมูลใน Sheet
// sheet.addRows(data);
// // สร้างไฟล์ Excel เป็น Blob
// workbook.xlsx.writeBuffer().then(function (buffer) {
//     const blob = new Blob([buffer], 
//     { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });

//     // สร้างลิงก์ดาวน์โหลด
//     const link = document.createElement('a');
//     link.href = URL.createObjectURL(blob);
//     link.download = `${fileName}.xlsx`;
//     link.click();
// });



// import ExcelJS from 'exceljs';
// const workbook = new ExcelJS.Workbook();
// await workbook.xlsx.load(file);
// await workbook.xlsx.load(file, {base64:true});

// const sheet = workbook.worksheets[0] 
// const sheet1 = workbook.getWorksheet('sheet1');
// const sheet2 = workbook.getWorksheet(2);

// sheet.eachRow((row) => {
//     // คอลัมน์แรก
//     data = row.values[1];
// });
// data1 = sheet1.getRow(1).values
