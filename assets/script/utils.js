
import "flatpickr/dist/flatpickr.min.css";

//JS Loader
import $            from "jquery";
import moment       from "moment";
import flatpickr    from "flatpickr";
import dayjs        from 'dayjs';

/**
 * https://amecwebtest.mitsubishielevatorasia.co.th/safety/
 */
export const host = $("meta[name=base_url]").attr("content");

/**
 * https://amecwebtest.mitsubishielevatorasia.co.th
 */
export const uri = $("meta[name=base_uri]").attr("content");

/**
 * https || http
 */
export const scheme = $("meta[name=base_scheme]").attr("content");

/**
 * Default select2
 */
export const select2Option = {
    allowClear: true,
    placeholder : 'กรุณาเลือก'
}

export const domScroll = '<"top flex flex-col gap-2"<"#filterBtnDt.flex flex-wrap gap-2"><"top-menu flex flex-wrap gap-3 mb-2"<"lf-opt flex-1 join gap-3 "lf><"table-option join  items-center">>><"bg-white border border-slate-300 rounded-lg overflow-scroll max-h-[60vh]"rt><"bottom flex justify-between mt-5"pi>';
// export const domScroll = '<"top flex flex-wrap gap-3 mb-2"<"lf-opt flex-1 join gap-3 "lf><"table-option join gap-[2px] items-center">><"overflow-scroll max-h-[60vh]"rt><"bottom flex justify-between mt-5"pi>';

export const initJoin = (id) => {
    $(id).find('thead').addClass('sticky top-0 bg-white z-10');
    $(".dt-length").find('select').addClass("join-item");
    $(".dt-search").find('input').addClass("join-item");
    $('.lf-opt').removeClass('gap-3');
}

/**
 * Default datatable
 */
export const tableOption = {
  dom: '<"flex mb-3"<"flex-1"f><"flex items-center table-option"l>><"bg-white border border-slate-300 rounded-lg overflow-scroll my-5"t><"flex mt-5"<"flex-1"p><"flex-none"i>>',
  pageLength: 10,
  autoWidth: false,
  destroy: true,
  responsive: true,
  language: {
    info: "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
    infoEmpty: "",
    paginate: {
      previous: '<i class="icofont-circled-left"></i>',
      next: '<i class="icofont-circled-right"></i>',
      first: '<i class="icofont-double-left"></i>',
      last: '<i class="icofont-double-right"></i>',
    },
    search: "",
    searchPlaceholder: "ค้นหา",
    loadingRecords: "กำลังโหลดข้อมูล...",
    emptyTable: "ไม่มีข้อมูลในตาราง",
    zeroRecords: "ไม่พบข้อมูลที่ต้องการ",
    lengthMenu: "_MENU_",

  },
  lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
  columnDefs: [
    {
      targets: "action",
      searchable: false,
      orderable: false,
    },
  ],
//   headerCallback: function(thead, data, start, end, display) {
//     $(thead).find('th').css({
//         'background-color': '#000', /* สีพื้นหลัง */
//         'color': 'white'              /* สีตัวอักษร */
//     });
// }
};

/**
 * Click toggle tr group 
 * @param {object} table is dataTable
 * @param {string} row e.g. 'TYPE_NAME'
 * @param {object} e e.g. $(this)
 */
export function clickTableGroup(table, row, e){
    var group = e.data('group'); // รับค่าหมวดหมู่
    console.log(group); 

    var rows = table.rows({ page: 'current' }).data(); // ดึงข้อมูลปัจจุบันทั้งหมด
    console.log(rows);

    var $icon = e.find('i'); // หาไอคอนใน row นี้

    // Toggle ไอคอน
    if ($icon.hasClass('icofont-rounded-right')) {
        $icon.removeClass('icofont-rounded-right').addClass('icofont-rounded-down');
    } else {
        $icon.removeClass('icofont-rounded-down').addClass('icofont-rounded-right');
    }

    table.rows({ page: 'current' }).every(function () {
        var rowData = this.data(); // ดึงข้อมูลแถวปัจจุบัน
        if (rowData[row] === group) { // ตรวจสอบค่า Category
            var rowNode = this.node(); // ดึง DOM node ของแถว
            $(rowNode).toggle('-translate-y-6'); // ซ่อนหรือแสดงแถว
        }
    });
}

/**
 * Create group table
 * @param {number} colspan 
 * @param {object} api 
 * @param {number} groupCol
 */
export function tableGroup(colspan, api, groupCol = 0){
    var rows = api.rows({ page: 'current' }).nodes();
    
    var lastGroup = null;
    // ลบกลุ่มที่มีอยู่เดิม
    $(rows).removeClass('group-row');
    // วนลูปเพื่อสร้างกลุ่ม
    api.column(groupCol, { page: 'current' }).data().each(function (group, i) {
        if (lastGroup !== group) {
            $(rows).eq(i).before(`
                <tr class="group-row font-bold" data-group="${group}">
                    <td colspan="${colspan}"><i class="icofont-rounded-down"></i> ${group}</td>
                </tr>
            `);
            lastGroup = group;
        }
    });
}

/**
 * Set filter button
 * @param {object} table 
 * @param {number} col e.g. 0
 */
export function setBtnFilter(table, col){

    // ดึงค่าหมวดหมู่จากคอลัมน์ที่กำหนด
    var uniqueCategories = [];

    table.column(col).data().each(function (value) {
        // console.log(uniqueCategories.indexOf(value),value);
        if (uniqueCategories.indexOf(value) === -1) {
            uniqueCategories.push(value);
        }
    });
    console.log(uniqueCategories);
    
    if(uniqueCategories.length > 0){
        // เพิ่มปุ่ม "ทั้งหมด" ไว้ที่จุดเริ่มต้น
        var buttonContainer = $('#filterBtnDt');
        buttonContainer.append('<button class="filter-btn-dt btn btn-primary btn-sm w-fit" data-filter="">ทั้งหมด</button>');
    
        // สร้างปุ่มสำหรับแต่ละหมวดหมู่
        $.each(uniqueCategories, function (index, category) {
            buttonContainer.append('<button class="filter-btn-dt btn btn-sm w-fit" data-filter="' + category + '">' + category + '</button>');
        });
    }

    // เมื่อกดปุ่มให้กรองข้อมูลตามหมวดหมู่
    $('#filterBtnDt').on('click', '.filter-btn-dt', function () {
        var filterValue = $(this).attr('data-filter');
        console.log(`^${$.fn.dataTable.util.escapeRegex(filterValue)}$`);
        
        filterValue == '' ? table.column(col).search(filterValue).draw() :
                            table.column(col).search(`^${$.fn.dataTable.util.escapeRegex(filterValue)}$`, true, false).draw();
    });
}

/**
 * Show message popup
 * @param {string} msg 
 * @param {string} type 
 * @param {string} position toast-end | toast-top | toast-center | toast-bottom | toast-start | toast-middle
 */
export function showMessage(msg, type = "error", position = 'toast-end') {

  const prop = [
    {
      id: "error",
      bg: "bg-red-800",
      text: "text-white",
      title: "Processing Fail!",
    },
    { id: "success", bg: "bg-green-800", text: "text-white", title: 'Success' },
    { id: "info", bg: "bg-blue-500", text: "text-white", title: 'Info' },
    { id: "warning", bg: "bg-yellow-500", text: "text-white", title: 'Warning!' },
  ];

  const dt = prop.find((x) => x.id == type);
//   const toast = document.createElement('div');  // สร้าง DOM element

  const toast = $(`
        <dialog class="msg-notify toast ${position} ${dt.bg} z-[9999] !p-0 rounded-2xl m-5 alert-message w-80 max-w-80 transition-all duration-1000">
            <div class="alert flex flex-col gap-2 overflow-hidden relative ${dt.bg}">
                <div class="msg-title text-xl font-semibold block w-full text-left ${dt.text}">${dt.title}</div>
                <div class="msg-txt block w-full text-left max-w-80 text-wrap ${dt.text}">${msg}</div>
                <div class="msg-close absolute top-2 right-5 z-[102]">
                    <i class="icofont-ui-close"></i>
                </div>
                <div class="absolute right-[-30px] top-[-10px] text-[120px] z-0 opacity-20">
                    <i class="icofont-exclamation-circle"></i>
                </div>
            </div>
        </dialog>
        `);
  $('.msg-notify').remove();
//   document.body.appendChild(toast);
  toast.appendTo('body');
//   $(document.body).append(toast);
//   setTimeout(() => {
//     console.log('msg-close');
    
//     // $(".msg-close").trigger('click');
//     toast.remove();
//   }, 10000);
  setTimeout(() => {
    console.log(toast, $(toast));
    $(toast).find('.msg-close').trigger('click');  // ลบ toast เมื่อหายไปแล้ว
  }, 5000); 
}

/**
 * Set day off in .fdate
 * @param {object} options 
 * @param {string or object} e string id e.g. #date or element e.g. $('#date')
 * @returns 
 */
export const setDatePicker = (options = {}, e = '') => {
    const element = e == '' ? '.fdate' : e;
    //Date Picker
    const storedDayOffs = JSON.parse(localStorage.getItem("dayoff")) || [];
    const instance = flatpickr(element, {
        dateFormat: "Y-m-d",
        ...options,
        // allowInput: true,
        // disableMobile: true,
        // disable: storedDayOffs.value,  // disble วันหยุด
        onDayCreate: function (dObj, dStr, fp, dayElem) {
            try {
                const dateStr = dayElem.dateObj.toLocaleDateString().split("T")[0]; // แปลงวันที่เป้นสตริง
                
                // const dd = moment(dateStr).format("yyyy-M-D"); // แปลงสตริงเป็น fomat วันที่ ที่ต้องการ
                const dd = dayjs(dateStr).format("YYYY-M-D"); // แปลงสตริงเป็น fomat วันที่ ที่ต้องการ\

                if (storedDayOffs.value.includes(dd)) {
                dayElem.classList.add("day-off"); // เพิ่มคลาส
                //   dayElem.style.pointerEvents = "none"; 
                //   dayElem.style.opacity = "0.5";
                }
            } catch (error) {
                console.error("Error in onDayCreate:", error);
            }
        },
    });
    return instance;
  };

/**
 * Check Authen
 * @param {*} xhr 
 * @param {*} status 
 */
// export function checkAuthen(url, status){
export function checkAuthen(xhr, status=''){
    // console.log(xhr);
    // console.log(status);

    // const response = JSON.parse(xhr.responseText);
    // console.log(response);
    // // ตรวจสอบสถานะ
    // if (response.status === "403") {
    //     window.location.href = host;
    // }
    
    
    try{
        console.log(xhr);
        
        if (!xhr.responseJSON) {
            throw new Error('Response is not JSON');
        }else{
            // datatype only json
            const statusCode  = xhr.responseJSON.status;
            const urlRedirect = xhr.responseJSON.url;
            // console.log('statusCode', statusCode);
            // console.log('urlRedirect', urlRedirect);
            if (statusCode == '403' && urlRedirect) {
                window.location.href = urlRedirect;
            }
        }
    } catch (error) {
        // console.error("Error in checkAuthen: ", error);
        return;
    }
    

    // const redirect = xhr.responseText.substring(0, 9).toUpperCase()=='<!DOCTYPE' ? true : false;
    //    if((status == 'parsererror') && (redirect)){
    //        window.location.href = host;
    //    }
}

export function showpreload(){
    // console.log(3);
    
    $('.preload').removeClass('hidden');
}

export function hidepreload(){
    // console.log(4);
    
    $('.preload').addClass('hidden');
}

export async function showpreloader(){
    // console.log(1);
    
    $('#preload').removeClass('hidden');
    $('#preload').find('div').removeClass('bg-opacity-50');
}

export async function hidepreloader(){
    // console.log(2);
    
    $('#preload').addClass('hidden');
    $('#preload').find('div').addClass('bg-opacity-50');
}

export function openModal(id){
    $(id).addClass('modal-open');
}
export function closeModal(id){
    $(id).removeClass('modal-open');
}

/**
 * Format date
 * @param {string} date 
 * @returns 
 */
export function dateFormat(date){
    moment.locale("en");
    const fulldate = moment(date).format("D MMM. YYYY");
    const dateEn = moment(date).format("MMMM YYYY");
    moment.locale("th");
    const dateTh = moment(date).format("MMMM YYYY");
    return {fulldate: fulldate, dateEn: dateEn, dateTh:dateTh};
}

/**
 * reset form and remove class error in .req
 * @param {string} form id or class Form e.g. #chemical-master , .inspection-form
 */
export function resetForm(form){
    $(form)[0].reset();
    formRemoveError(form)
}

/**
 * Remove error class in form
 * @param {string} form id or class Form e.g. #chemical-master , .inspection-form
 */
export function formRemoveError(form){
    $(form).find(".req").map(function (i, el) {
        removeClassError($(el));
    });
}

/**
 * Check required form
 * @param {object} form element form class or id e.g. #chemical-master , .inspection-form
 * @param {object} fields e.g. [{element: element, message: message}]
 * @param {string} position e.g. toast-end | toast-top | toast-center | toast-bottom | toast-start | toast-middle
 * @returns 
 */
export async function requiredForm(form, fields=[], position = ''){
    let check = false;
    $(form).find('input, select, textarea').each(function() {
        const target = $(this);
        
        if(RequiredElement(target)){
            // console.log(target);
            check = true;
            // console.log('check',check);
        }
    });
    // console.log('check',check);
    
    if (fields.length == 0 && check) {
        showMessage('กรุณากรอกข้อมูลให้ครบถ้วน', 'warning', position);
        return false;
    }
    for (const field of fields) {
        if (!field.element.val() || field.element.val().length === 0) {
            // console.log(field.element);
            
            showMessage(field.message, 'warning', position);
            return false;
        }
    }
    return true;

}

// /**
//  * Check required form
//  * @param {string} className e.g. .list-inspection
//  */
// export function requiredForm(className){
//     $(className).find('input, select, textarea').each(function() {
//         const target = $(this);
//         RequiredElement(target);
//     });
// }

/**
 * Check Required element
 * @param {object} e 
 */
export function RequiredElement(e){
    // console.log(e);
    const groupName = e.attr('name');
    // const isEmptyRadioWithReq = (e.prop('checked') === false && (e.prop('type') === 'radio' || e.prop('type') == 'checkbox') && e.hasClass('req'));
    const isEmptyRadioWithReq = ($(`input[name="${groupName}"].req:checked`).length === 0 && (e.prop('type') === 'radio' || e.prop('type') == 'checkbox') && e.hasClass('req'));
    const isEmptyWithReq      = ((e.val() === '' || e.val().length === 0) && e.hasClass('req')); // Fixed '=' to '===' and checked length
    // console.log(isEmptyRadioWithReq, isEmptyWithReq, e);
    
    if (isEmptyRadioWithReq || isEmptyWithReq) {
        // console.log('addClassError', e);
        addClassError(e);
        return true;
    }else{
        // console.log('removeClassError', e);
        
        removeClassError(e);
        return false;
    }
}

/**
 * Add css error class
 * @param {object} e 
 */
export function addClassError(e){
    // console.log(e.prop('type'), e);
    if(e.is('input')){
        const groupName = e.attr('name'); 
        if($(`input[name="${groupName}"].req:checked`).length === 0){
            if (e.prop('type') == 'radio' ) {
                $(`input[name="${groupName}"]`).addClass('radio-error');
            } else if (e.prop('type') == 'checkbox') {
                $(`input[name="${groupName}"]`).addClass('checkbox-error');
            } else {
                e.addClass('input-error');
            }
        }
        // if(e.prop('type') == 'radio' ){
        //     const groupName = e.attr('name'); 
        //     if($(`input[name="${groupName}"].req:checked`).length === 0){
        //         $(`input[name="${groupName}"]`).addClass('radio-error');
        //     }
        // }else if (e.prop('type') == 'checkbox') {
        //     const groupName = e.attr('name'); 
        //     // console.log(groupName);
            
        //     if($(`input[name="${groupName}"].req:checked`).length === 0){
        //         $(`input[name="${groupName}"]`).addClass('checkbox-error');
        //     }
        // }else{
        //     e.addClass('input-error');
        // }
    }else if(e.is('select')){
        e.next('.select2-container').addClass('select-error');
    }else if(e.is('textarea')){
        e.addClass('textarea-error');
    }
}

/**
 * Remove css error class
 * @param {object} e 
 */
export function removeClassError(e){
    if(e.is('input')){
        const groupName = e.attr('name'); 
        if(e.prop('type') == 'radio'){
            $(`input[name="${groupName}"]`).removeClass('radio-error');
        }else if (e.prop('type') == 'checkbox') {
            $(`input[name="${groupName}"]`).removeClass('checkbox-error');
        }else{
            e.removeClass('input-error');
        }
    }else if(e.is('select')){
        e.next('.select2-container').removeClass('select-error');
    }else if(e.is('textarea')){
        e.removeClass('textarea-error');
    }
}

/**
 * Thaionly
 * @param {object} obj 
 */
export function thaionly(obj){
    const regex = /^[ก-๙0-9\s\[\]\{\}@#$&!%*()?+=.,_\/\\-]*$/;
    const currentValue = obj.val();
    // ตรวจสอบและลบอักขระที่ไม่ใช่ภาษาไทย
    if (!regex.test(currentValue)) {
      $(".warn-th").removeClass("hidden"); // แสดงข้อความแจ้งเตือน
      obj.addClass('input-error');
      obj.val(currentValue.replace(/[^ก-๙0-9\s\[\]\{\}@#$&!%*()?+=.,_\/\\-]/g, ""));
    } else {
        $(".warn-th").addClass("hidden"); // ซ่อนข้อความแจ้งเตือน
        obj.removeClass('input-error');
    }
}

/**
 * Engonly
 * @param {object} obj 
 */
export function  engonly(obj){
    const Regex = /^[A-Za-z0-9@#$&!%*()?+=.,_\\\/\-\[\]\{\}\s]*$/; 
    const currentValue = obj.val();
    // ตรวจสอบและลบอักขระที่ไม่ใช่ภาษาอังกฤษ
    if (!Regex.test(currentValue)) {
      $(".warn-en").removeClass("hidden"); // แสดงข้อความแจ้งเตือน
      obj.addClass('input-error');
      obj.val(currentValue.replace(/[^A-Za-z0-9\s\[\]\{\}@#$&!%*()?+=.,_\/\\-]/g, ""));
    } else {
        $(".warn-en").addClass("hidden"); // ซ่อนข้อความแจ้งเตือน
        obj.removeClass('input-error');
    }
}

/**
 * Random color
 * @returns 
 */
function getRandomColor() {
    const letters = '0123456789ABCDEF';
    let color = '#';
    for (let i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}

/**
 * Random color from count
 * @param {number} seriesCount 
 * @returns 
 */
export function generateRandomColors(seriesCount) {
    let colors = [];
    for (let i = 0; i < seriesCount; i++) {
        colors.push(getRandomColor());
    }
    return colors;
}

/**
 * Get month
 * @param {number} monthNumber 
 * @param {string} format 
 * @returns 
 */
export function getMonthtext(monthNumber, format='MMMM') {
    return moment(monthNumber, 'MM').format(format);
}

export const userInfoData = Array.from($('.user-info-data').get(0).attributes).reduce((obj, a) => {
    obj[a.name] = a.value; // ใช้ชื่อ attribute เป็น key และค่าของมันเป็น value
    return obj;
}, {});


export const ajaxOptions = {
    // url: '',
    type: "post",
    dataType: "json",
    // data: '',
    beforeSend: function () {
        showpreload();
    },
    complete: function (xhr) {
        checkAuthen(xhr);
        hidepreload();
    }
};
export function getData(ajaxOptions){
    return new Promise((resolve, reject) => {
        const options = {
            ...ajaxOptions,
            success: function (res) {
                // console.log(res);
                // console.log(typeof res);
                resolve(res); 
            },
            // error: function (xhr, err) {
            //     console.log(xhr, err);
            //     // sendmail(`Ajax error ${}`)
            //     reject(err); 
            // },
            error: function (xhr, textStatus, errorThrown) {
                // console.log(xhr, textStatus, errorThrown);
                let error = new Error(errorThrown || "Unknown AJAX error");
                error.status = xhr.status;
                error.responseText = xhr.responseText; 
                reject(error);
            },
        };
        // console.log(options);
        
        $.ajax(options);
    });
}

const date    = new Date();
const seccond = date.getSeconds();
const minute  = date.getMinutes();
const hour    = date.getHours();
const day     = date.getDay();
const month   = date.getMonth();
const year    = date.getFullYear();



export const mailFrom    = 'noreply@MitsubishiElevatorAsia.co.th';
// export const mailsubject = 'Safery System ERROR : '+ day + '-' +month + '-' + year + '::' + hour + ':' +minute + ':' + seccond  ;
export const mailAdmin   = 'sutthipongt@MitsubishiElevatorAsia.co.th';
export function sendmail(body, to = mailAdmin, subject = mailsubject(),  from = mailFrom,  cc = null, pathfile = null){
    return new Promise((resolve) => {
        $.ajax({
            url: `${uri}/api-auth/api-dev/Amecmail/sendmail`,
            type: "post",
            dataType: "json",
            data: { 
                from     : from, 
                to       : to, 
                cc       : cc,
                subject  : subject,
                body     : body,
                pathfile : pathfile
            },
            beforeSend: function (){
                showpreload()
            },
            success: function (res) {
                resolve(res);
            },
            complete: function(xhr, status){
                checkAuthen(xhr, status);
                hidepreload();
            }
        });
    });
    
}



export const mailsubject = (subject = 'Safery System JS ERROR 😭 : ') =>{
    return `${subject}${day}/${month}/${year} :: ${hour}:${minute}:${seccond}`;
} ;
export const mailOpt = {
    VIEW: 'layout/mail/mailAlert',
    SUBJECT: mailsubject(),
    TO: mailAdmin,
    CC: [],
    BCC: [],
    BODY: [],
    ENFILE: []
}

export const mailForm = (NFRMNO, VORGNO, CYEAR, CYEAR2, NRUNNO, header = '') =>{
    return `<div style="font-size:20px; font-weight:bold;">${header}</div>
        <table style="border: 1px solid #333; width: 100%;"> 
            <thead style="background-color: #e38300; color: #fff;">
                <th>Form No</th>
                <th>ORGNO</th>
                <th>CYear</th>
                <th>CYear2</th>
                <th>Run No</th>
            </thead>
            <tbody>
                <tr  style="padding: 2px 3px;">
                    <td>${NFRMNO}</td>
                    <td>${VORGNO}</td>
                    <td>${CYEAR}</td>
                    <td>${CYEAR2}</td>
                    <td>${NRUNNO}</td>
                </tr>
            </tbody>
        </table>
        `;
}
/**
 * Sends an email using the specified data.
 * @param {*} data - The data for the email.
 * @returns {Promise} - A Promise that resolves with the server response.
 */
export function sendMail(data = mailOpt) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: `${host}chemical/webflow/sendMail/`,
            // url: `${uri}/api-auth/api-dev/Amecmail/sendmail`,
            type: "post",
            dataType: "json",
            data: { 
                data : data
            },
            beforeSend: function () {
                showpreload();  
            },
            success: function (res) {
                resolve(res);
            },
            error: function (xhr, err) {
                console.error(xhr, err);
                reject(err); 
            },
            complete: function(xhr, status) {
                console.log(xhr, status);
                
                // checkAuthen(xhr, status);
                hidepreload();
            }
        });
    });
    
}


