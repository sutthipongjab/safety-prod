
import $ from "jquery";
import {host, showpreload, hidepreload, checkAuthen, showMessage} from "./utils.js";

export const fileImgFormat = ['.JPG','.jpg', '.jpeg', '.png', '.gif'];
/**
 * Check file format
 * @param {object} e $(this)
 * @param {string} format .pdf  .xlsx or ['.jpg', '.jpeg', '.png', '.gif']
 * @param {string} msg ไฟล์ไม่ถูกต้อง กรุณาแนบไฟล์ PDF เท่านั้น
 */
export function checkFileFormat(e, format, msg, layoutMsg = 'toast-end'){
    // const file = e.val();
    // if (file && !file.toLowerCase().endsWith(format)) {
    //     e.val('');
    //     showMessage(msg);
    // } else if (/[ก-๙]/.test(file)) {
    //     e.val('');
    //     showMessage('ชื่อไฟล์ต้องเป็นภาษาอังกฤษเท่านั้น', 'warning');
    // } 
    const file = e.val();
    console.log(file, format);
    
    if (!file) return;
    console.log('file');
    
    const isValidFormat = Array.isArray(format)
        ? format.some(ext => file.toLowerCase().endsWith(ext))
        : file.toLowerCase().endsWith(format); // รองรับกรณี format เป็น string แบบเดิม
    
    console.log(isValidFormat);
    
    if (!isValidFormat) {
        e.val('');
        showMessage(msg, 'warning', layoutMsg);
    }
    if (/[ก-๙]/.test(file) && (format === '.pdf' || (Array.isArray(format) && format.includes('.pdf')))) {
        e.val('');
        showMessage('ชื่อไฟล์ต้องเป็นภาษาอังกฤษเท่านั้น', 'warning', layoutMsg);
    }
}

/**
 * Download all file in path
 * @param {string} pathFile e.g. 'assets/file/template/'
 * @param {string} filename e.g. 'chemical Template.xlsx'
 */
export async function downloadInPath(pathFile, filename = ''){
    const File = await getfileInPath(pathFile, filename);
    File.forEach(async file => {
        console.log(`Processing file: ${file.filename}`);
        const binaryData = atob(file.content); // แปลง Base64 เป็น Binary
        const buffer = new Uint8Array(binaryData.length);

        for (let i = 0; i < binaryData.length; i++) {
            buffer[i] = binaryData.charCodeAt(i);
        }
        downloadExcelFile(buffer, file.filename);
    });
}

/**
 * Download one file
 * @param {string} filePath e.g. 'assets/file/template/'
 * @param {string} fileName e.g. 'chemical Template.xlsx'
 */
export async function downloadExcel(filePath, fileName){
    const file = await getArrayBufferFile(filePath, fileName);
    downloadExcelFile(file, fileName);
}

/**
 * Download file
 * @param {object} fileBuffer 
 * @param {string} fileName e.g. Template.xlxs
 */
export function downloadExcelFile(fileBuffer, fileName = 'Template.xlxs'){
    const blob = new Blob([fileBuffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = fileName;
    link.click();
}

/**
 * Get file as array buffer
 * @param {string} filePath e.g. 'assets/file/template/'
 * @param {string} fileName e.g. 'chemical Template.xlsx'
 * @returns 
 */
export async function getArrayBufferFile(filePath, fileName){
    return new Promise((resolve, reject) => {
        $.ajax({
            url: `${host}home/getArrayBufferFile/`,
            type: "post",
            data: {
                filePath: filePath,
                filename: fileName
            },
            xhrFields: {
                responseType: 'arraybuffer' // ดึงข้อมูลเป็น binary เพื่อให้ ExcelJS สามารถอ่านได้
            },
            // dataType: 'json',
            beforeSend: function () {
                showpreload();
            },
            success: function (res) {     
                console.log("Type of res:", typeof res); // ควรเป็น object ถ้าเป็น ArrayBuffer
                console.log("Instance of ArrayBuffer:", res instanceof ArrayBuffer); // ตรวจสอบว่าเป็น ArrayBuffer หรือไม่
                console.log("Object.prototype.toString.call(res):", Object.prototype.toString.call(res)); // แสดงชนิดที่แท้จริง
                console.log("Raw res:", res); // แสดงข้อมูลดิบที่ได้รับ
                // if(res instanceof ArrayBuffer){
                //     resolve(res);
                // }else{
                //     const json = JSON.parse(new TextDecoder().decode(new Uint8Array(res)));
                //     console.log("JSON Data:", json);
                //     checkAuthen(json);
                // }
                resolve(res);
            },
            error:function(xhr, err){
                console.log(xhr,err);
                
            },
            complete: function(xhr){
                // checkAuthen(xhr);
                // console.log(xhr);
                hidepreload();
            }
        });
    });
}

/**
 * Get all file in path
 * @param {string} path     e.g. 'assets/file/master/chemical
 * @param {string} fileName e.g. '03_QES_Rev.B_11.12.2024.xlsx'
 * @returns 
 * await getfileInPath('assets/file/master/chemical'); แบบทุกไฟล์ใน path
 * await getfileInPath('assets/file/master/chemical','03_QES_Rev.B_11.12.2024.xlsx'); แบบเฉพาะไฟล์นั้นๆ
 */
export function getfileInPath(path, fileName = ''){
    return new Promise((resolve, reject) => {
        $.ajax({
            url: `${host}home/getfileInPath/`,
            type: "post",
            dataType: 'json',
            data:{
                path:path,
                fileName:fileName
            },
            beforeSend: function () {
                showpreload();
            },
            success: function (res) {
                // console.log(res);
                // console.log(typeof res); 
                if(Array.isArray(res)){
                    res.forEach(async file => {
                        console.log(`Processing file: ${file.filename}`);

                        const binaryData = atob(file.content); // แปลง Base64 เป็น Binary
                        const buffer = new Uint8Array(binaryData.length);
                
                        for (let i = 0; i < binaryData.length; i++) {
                            buffer[i] = binaryData.charCodeAt(i);
                        }
                        file.buffer = buffer; // เอา buffer ไปใช้งานต่อ เขียนหรืออ่าน
                    });    
                }  
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
