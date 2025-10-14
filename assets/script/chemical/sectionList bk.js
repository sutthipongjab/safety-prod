import $ from "jquery";
import "select2";
import "select2/dist/css/select2.min.css";
import 'datatables.net-dt';
import "datatables.net-dt/css/dataTables.dataTables.min.css";
import jsPDF from 'jspdf'
import autoTable from 'jspdf-autotable'
import {host, showpreload, hidepreload, checkAuthen, tableOption, showMessage, userInfoData ,ajaxOptions, getData ,setDatePicker, dateFormat, select2Option, domScroll, initJoin, RequiredElement, requiredForm, removeClassError} from "../utils.js";
import {writeExcelTemp, writeOpt, exportExcel, colToNumber, numberToCol} from '../_excel.js';
import {getfileInPath} from '../_file.js';
import {createColumnFilters} from '../filter.js';
import {createStamp, loadFont, optAutoTable} from '../_jsPDF.js';

var sectionList, revisionList, table, freesiaUPC;


$(document).ready(async function () {
    freesiaUPC = await loadFont(host, 'freesiaUPC/upcfl.ttf')
    $('#owner').select2({...select2Option, placeholder: 'เลือกแผนก'});
    const data = await getData({ 
        ...ajaxOptions ,
        url: `${host}chemical/chemicalList/getChmAllSec`
    });
    if(data.status == 1){
        sectionList  = data.sec;
        revisionList = data.rev;
        setTable(data.data,data.sec);
    }else{
        // showMessage('ไม่พบข้อมูล ระบบกำลังนำทางสู่หน้าขอใช้สารเคมี');
        // showpreload();
        // setTimeout(() => {
        //     window.location.href = `${host}chemical/request`;
            
        // }, 5000);
    }
});


$(document).on('click', '#cancleRev', function(){
    $('#modal_rev').prop('checked', false);
});

$(document).on('click', '#modal_rev', function(){
    console.log('modal_rev');
    $('#current-revision').val(revisionList.MASTER).focus();
});

$(document).on('click', '#saveRev', async function(){
    const rev = $('#current-revision').val();
    if(rev == ''){
        showMessage('กรุณากรอก Revision','warning');
        return;
    }
    const ajax = {...ajaxOptions};
    ajax.url = `${host}Chemical/chemicalList/updateRev`;
    ajax.data = {rev:rev, own:'MASTER', USER_UPDATE: userInfoData.sempno};
    await getData(ajax).then(async (res) => {
        if(res.status == true){
            revisionList = res.rev;
            showMessage('บันทึกข้อมูลสำเร็จ','success');
            $('.revision-master').html(`Rev. No. ${rev.toUpperCase()}`);
            $('#cancleRev').trigger('click');
            // getMaster();
        }else if(res.status == 3){
            showMessage('กรุณากรอก Revision ใหม่','warning');

        }else{
            showMessage('บันทึกข้อมูลไม่สำเร็จ กรุณาลองใหม่ภายหลัง');
        }
    });
});




/**
 * Set table
 * @param {object} data 
 * @param {object} sec 
 */
async function setTable(data, sec){
    console.log(data, sec);
    
    let html =`<div role="tablist" class="tabs tabs-lifted w-full grid-cols-[0fr]">`;
    const columns = [
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
            
            html += `<input type="radio" name="my_tabs_2" role="tab" class="tab whitespace-nowrap" aria-label="${key}" ${check} />
                    <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-box p-6">
                        <div class="font-bold">
                            <span class="revision-master">Rev. No. ${revisionList[key]}</span>
                        </div>
                        <div class="overflow-x-auto w-full">
                            <table class="table" id="${org}"></table>
                        </div>
                    </div>`;
        }
        html += `</div>`;
        $('.card-body').append(html);
        Object.entries(data).forEach(async ([key, value], index) => {
            const org = key.slice(0, -1).replace(/\s+/g, '_');
            console.log(org);
            
            table = await createTable(`#${org}`, value, columns);
            createColumnFilters(table, '0-11');
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
        const addChe = tableID == '#table_master' ? 
            `<label for="drawer-master" class="drawer-button btn btn-sm join-item btn-primary flex items-center tooltip tooltip-left " data-tip="เพิ่มสารเคมี" id="add-chemical">
                
                <i class="icofont-plus-circle text-xl"></i>
            </label>
            <label for="modal_rebuild" class="drawer-button btn btn-sm join-item btn-neutral flex items-center tooltip tooltip-left" data-tip="เปิดใช้งานสารเคมีเก่า" id="re-chemical">
                <i class="icofont-duotone icofont-rebuild text-xl"></i>
            </label> 
            ` :'';
            console.log(tableID);
            
        if(tableID != '#table_rebuild' && tableID != '#table_rebuild_sec' && tableID != '#table_submit'){
            
            $(`${tableID}_wrapper .table-option`).append(`
                <label for="" class="btn btn-sm join-item btn-success flex items-center max-w-xs tooltip tooltip-left" data-tip="ดาว์นโหลดไฟล์ Excel" id="exportExcel" tableID="${tableID}">
                    <i class="icofont-file-excel text-xl "></i>
                </label>
                <label for="" class="btn btn-sm join-item btn-error flex items-center  max-w-xs tooltip tooltip-left" data-tip="ดาว์นโหลดไฟล์ PDF" id="exportPDF" tableID="${tableID}">
                    <i class="icofont-file-pdf text-xl" ></i>
                </label>
                ${addChe}`);
        }
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

            data.forEach( (d, index) => {
                const rowIndex = opt.startRow + index; 
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
       
        const doc = new jsPDF({
            orientation: 'landscape',
        })

        doc.addFileToVFS("upcfl.ttf", freesiaUPC);
        doc.addFont("upcfl.ttf", "freesiaUPC", "normal");
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
        
        const optCls = {...optAutoTable};
        optCls.headStyles = { font: "freesiaUPC", fontSize: 14, fillColor: [220, 220, 220]  };
        optCls.columns =  columnClass;
        optCls.body = classData;
        optCls.startY = doc.lastAutoTable.finalY; 

        doc.autoTable(optCls);
        doc.save(fileName)
    });

});





 
