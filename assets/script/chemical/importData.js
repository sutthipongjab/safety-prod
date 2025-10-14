import $ from "jquery";
import DataTable    from "datatables.net-dt";
import {host, showpreload, hidepreload, checkAuthen, tableOption, showMessage} from "../utils.js";
import {downloadExcel, getfileInPath, downloadInPath} from "../_file.js";
// import ExcelJS from 'exceljs';
import {readInput, readInOpt, colToNumber} from '../_excel.js';
import "datatables.net-responsive";
import "datatables.net-dt/css/dataTables.dataTables.min.css";
import "datatables.net-responsive-dt/css/responsive.dataTables.min.css";


const cheTempName = 'chemical Template.xlsx';
var dataUpload;
$(document).ready(async function () {

});

$(document).on('click', '#template', async function(){
    // downloadInPath('assets/file/template/old/','test.xlsx');
    downloadExcel('assets/file/template/','Chemical list.xlsx');
    // downloadInPath('assets/file/template/','Chemical list.xlsx');
    // downloadExcel('assets/file/template/',cheTempName);
});

$(document).on('click', '#blogMaster', function(){
    $('.blogMasterSec').addClass('hidden');
    $('.blogMaster').removeClass('hidden');
    $(this).addClass('tab-active');
    $('#blogMasterSec').removeClass('tab-active');

});
$(document).on('click', '#blogMasterSec', function(){
    $('.blogMaster').addClass('hidden');
    $('.blogMasterSec').removeClass('hidden');
    $(this).addClass('tab-active');
    $('#blogMaster').removeClass('tab-active');


});

$(document).on('change','#upload',async function(){
    const file = this.files[0];
    // console.log(file);
    showpreload();
    dataUpload = await read(file);
    const columns = [
        // {data:'No',                         title:'No.'}, 
        {data:'AMEC_SDS_ID',                title:'AMEC SDS ID'}, 
        {data:'RECEIVED_SDS_DATE',          title:'RECEIVED SDS DATE'}, 
        {data:'EFFECTIVE_DATE',             title:'EFFECTIVE DATE'}, 
        {data:'PRODUCT_CODE',     title:'Product Code / Item No.'}, 
        {data:'CHEMICAL_NAME',   title:'CHEMICAL NAME/TRADE NAME'}, 
        {data:'VENDOR',      title:'MANUFACTURER / VENDOR'},
        {data:'PUR_INCHARGE',               title:'PUR. INCHARGE'},
        {data:'UN_CLASS',                   title:'UN CLASS'},
        {data:'REV',                        title:'Rev.'},
        // {data:'GA Dept',},
        // {data:'TMA',},
        // {data:'MMA',},
        // {data:'MTF',},
        // {data:'ELA Dept',},
        // {data:'STF',},
        // {data:'STA',},
        // {data:'ESA',},
        // {data:'ESP',},
        // {data:'M/P',},
        // {data:'CEC',},
        // {data:'WHI',},
        // {data:'EFC',},
        // {data:'MAT',},
        // {data:'QC3',},
        // {data:'QEE',},
        // {data:'QEM',},
        // {data:'QC1',},
        // {data:'PKC',},
        // {data:'QIC',},
        // {data:'QC2',},
        // {data:'สารเคมีอันตราย',},
        // {data:'วัตถุอันตราย',},
    ];
    await createTable('#table_upload', dataUpload, columns);
    $('#submit').removeClass('hidden');
    hidepreload();
});

$(document).on('click', '#submit', async function(){
    if(dataUpload){
        const save = await saveMaster();
        save.status == 1 ? showMessage('บันทึกข้อมูลเรียบร้อย', 'success') : showMessage('บันทึกข้อมูลไม่สำเร็จ');
    }else{
        showMessage('ไม่พบข้อมูล', 'warning');
    }
});

function saveMaster(){
    return new Promise((resolve, reject) => {
        $.ajax({
            url: `${host}chemical/migration/saveMaster/`,
            type: "post",
            dataType: 'json',
            // data: {data:dataUpload},
            data: {data:JSON.stringify(dataUpload)},
            beforeSend: function () {
                showpreload();
            },
            success: function (res) {
                console.log(res);
                console.log(typeof res);       
                resolve(res);
            },
            error:function(xhr, err){
                console.log(xhr,err);
            },
            complete: function(xhr){
                checkAuthen(xhr);
                hidepreload();
            }
        });
    });
}

async function read (file){
    const opt = {...readInOpt};
    opt.headerName = [
        '',
        'No', 
        'RECEIVED_SDS_DATE', 
        'EFFECTIVE_DATE', 
        'AMEC_SDS_ID', 
        'PRODUCT_CODE', 
        'CHEMICAL_NAME', 
        'VENDOR',
        'PUR_INCHARGE',
        'UN_CLASS',
        'REV',
        'GA Dept',
        'TMA',
        'MMA',
        'MTF',
        'ELA Dept',
        'STF',
        'STA',
        'ESA',
        'ESP',
        'M/P',
        'CEC',
        'WHI',
        'EFC',
        'MAT',
        'QC3',
        'QEE',
        'QEM',
        'QC1',
        'PKC',
        'QIC',
        'QC2',
        'สารเคมีอันตราย',
        'วัตถุอันตราย',
    ]
    opt.maxReadRow = 500;
    opt.startRow = 8;
    opt.startCol = colToNumber('A');
    opt.endCol   = colToNumber('AG'); 
    opt.sheetName = 'ฉบับตัด';
    // opt.customSheet = (workbook) => {
    //     const data = [];
    //     const worksheet = workbook.getWorksheet('ฉบับตัด');
    //     const headerName = [
    //         '',
    //         'No', 
    //         'RECEIVED_SDS_DATE', 
    //         'EFFECTIVE_DATE', 
    //         'AMEC_SDS_ID', 
    //         'PRODUCT_CODE', 
    //         'CHEMICAL_NAME', 
    //         'VENDOR',
    //         'PUR_INCHARGE',
    //         'UN_CLASS',
    //         'REV',
    //         'GA Dept',
    //         'TMA',
    //         'MMA',
    //         'MTF',
    //         'ELA Dept',
    //         'STF',
    //         'STA',
    //         'ESA',
    //         'ESP',
    //         'M/P',
    //         'CEC',
    //         'WHI',
    //         'EFC',
    //         'MAT',
    //         'QC3',
    //         'QEE',
    //         'QEM',
    //         'QC1',
    //         'PKC',
    //         'QIC',
    //         'QC2',
    //         'สารเคมีอันตราย',
    //         'วัตถุอันตราย',
    //     ]
    //     const maxReadRow = 500;
    //     const startRow = 8;
    //     const startCol = colToNumber('A'); // A
    //     const endCol   = colToNumber('AG'); // AG
    //     worksheet.eachRow((row, rowNumber) => {
    //         if (rowNumber < startRow) return;
    //         if (rowNumber > maxReadRow) return;
    //         for (let colIndex = startCol; colIndex <= endCol; colIndex++) {
    //             if(row.values[1] != '' && typeof row.values[1] == 'number'){
    //                 const index = row.values[1]-1;
    //                 if (!data[index]) {
    //                     data[index] = {};
    //                 }
    //                 data[index][headerName[colIndex]] = row.values[colIndex] || '';
    //             }
    //         }
    //     });
    //     return data;
    // }
    const data = await readInput(file,opt);
    return data;
}

/**
 * Table upload
 * @param {string} tableID 
 * @param {object} data 
 * @returns 
 */
async function createTable(tableID, data, columns){
    console.log(data,data[0]);
    
    const opt = { ...tableOption };
    // opt.ordering     = false;
    opt.searching    = false;
    opt.lengthChange = false;
    opt.order = [[0, 'asc']];
    opt.data = data;
    
    opt.columns = columns
    console.log(tableID,opt);
    
    return $(tableID).DataTable(opt);
}



var dataimport;
$(document).on('click','#importP', async function(){
    dataimport = []
    // const pathFile = await getfileInPath('assets/file/template');
    const pathFile = await getfileInPath('assets/file/master/chemical');
    console.log(pathFile);
    
    // await pathFile.forEach(async file => {
    // });
    for (const file of pathFile) {
        showpreload();
        const data = await readImport(file.buffer);
        dataimport.push({ filename: file.filename, data: data });
    }
    hidepreload();
    console.log(dataimport);
    if(dataimport.length > 0){
        showMessage('อ่านข้อมูลเรียบร้อย', 'success');
        const dtable = dataimport.map(e => e.data).flat();
        console.log(dtable);
        const columns = [
            // {data:'No',                 title: 'No.'}, 
            {data:'AMEC_SDS_ID',        title: 'AMEC SDS ID'}, 
            {data:'CHEMICAL_NAME',      title: 'Chemical Name'}, 
            {data:'REV',                title: 'Revision'},
            {data:'EFFECTIVE_DATE',     title: 'Effective Date'}, 
            {data:'RECEIVED_SDS_DATE',  title: 'Received SDS Date'}, 
            {data:'USED_FOR',           title: 'Used For'}, 
            {data:'USED_AREA',          title: 'Used Area'},
            {data:'KEEPING_POINT',      title: 'Keeping Point'},
            {data:'QTY',                title: 'Quantity'},
            {data:'REC4052',            title: 'REC4052'},
            {data:'REC4054',            title: 'REC4054'},
            {data:'CLASS',              title: 'Class'},
        ];
        await createTable('#table_import',dtable, columns);
        $('#submitP').removeClass('hidden');
    }else{
        showMessage('ไม่พบข้อมูล', 'warning'); 
    }  
    // // ไม่สามารถใช้กับ .xls
    // const workbook = new ExcelJS.Workbook();
    // // pathFile.filename.forEach(e => {
    // //     console.log(pathFile.path + e);
        
    // //     await workbook.xlsx.readFile(filename);
    // // });
    // pathFile.filename.forEach(async (filename) => {
    //     const path = './file/master/chemical';
    //     // const filePath = `${pathFile.path}/${filename}`;
    //     const filePath = `${path}/${filename}`;
    //     console.log(filePath.replace(/\//g, '\\'));
        
    //     // await workbook.xlsx.readFile(filePath.replace(/\//g, '\\'));
    //     await workbook.xlsx.readFile(filePath);
    //     console.log(workbook);
        
        
        
    // });
    // // await createTable('#table_upload', dataUpload);
});

$(document).on('click', '#submitP',async function(){
    console.log(dataimport);
    if(dataimport){
        const save = await saveMasterSec();
        save.status == 1 ? showMessage('บันทึกข้อมูลเรียบร้อย', 'success') : showMessage('บันทึกข้อมูลไม่สำเร็จ');
    }else{
        showMessage('ไม่พบข้อมูล', 'warning');
    }
    // dataimport = []
    // const pathFile = await getfileInPath('assets/file/master/chemical','03_QES_Rev.B_11.12.2024.xlsx');
    // pathFile.forEach(async file => {
    //     const data = await readImport(file.buffer);
    //     dataimport.push({ filename: file.filename, data: data });
    // });
});

/**
 * Read import data file in path
 * @param {object} file 
 * @returns 
 */
async function readImport(file){
    const opt = {...readInOpt};
    opt.headerName = [
        '',
        'No', 
        'AMEC_SDS_ID', 
        'CHEMICAL_NAME', 
        'REV',
        'EFFECTIVE_DATE', 
        'RECEIVED_SDS_DATE', 
        'USED_FOR', 
        'USED_AREA',
        'KEEPING_POINT',
        'QTY',
        'REC4052',
        'REC4054',
        'CLASS',
    ]
    opt.maxReadRow = 300;
    opt.startRow = 4;
    opt.startCol = colToNumber('A');
    opt.endCol   = colToNumber('M'); 
    const data = await readInput(file,opt);
    // console.log(d);
    return data;
}

function saveMasterSec(){
    return new Promise((resolve, reject) => {
        $.ajax({
            url: `${host}chemical/migration/saveMasterSec/`,
            type: "post",
            dataType: 'json',
            data: {data:JSON.stringify(dataimport)},
            beforeSend: function () {
                showpreload();
            },
            success: function (res) {
                console.log(res);
                console.log(typeof res);       
                resolve(res);
            },
            error:function(xhr, err){
                console.log(xhr,err);
            },
            complete: function(xhr){
                checkAuthen(xhr);
                hidepreload();
            }
        });
    });
}