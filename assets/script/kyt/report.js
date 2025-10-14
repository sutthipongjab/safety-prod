import $       from "jquery";
import * as my from "../utils.js";
import DataTable    from "datatables.net-dt";
import "datatables.net-responsive";
import "datatables.net-dt/css/dataTables.dataTables.min.css";
import "datatables.net-responsive-dt/css/responsive.dataTables.min.css";
import ApexCharts from 'apexcharts'
import {excelOptions, exportExcel, defaultExcel} from '../_excel.js';


var fyear, table_topten;
const color1 = '#FCCF31';
const color2 = '#17ead9';

$(document).ready(async function () {
    fyear = parseInt($('#fyear').html());
    console.log(fyear);
    const data = await getData(fyear);
    await setData(data);
});

$(document).on('click', '#previousFY',async function(){
    my.showpreload();
    const e = $('#fyear');
    const fyear = parseInt(e.html()) - 1;
    e.html(fyear)
    window.location.assign(`${my.host}kyt/report/index/${fyear}`)
});

$(document).on('click', '#nextFY',async function(){
    my.showpreload();
    const e = $('#fyear');
    const fyear = parseInt(e.html()) + 1;
    e.html(fyear)
    window.location.assign(`${my.host}kyt/report/index/${fyear}`)
});

$(document).on('click', '#exportExcel', async function(){
    const data = await getReport(fyear);
    if(data.length !=0){
        const opt = {...excelOptions};
        opt.sheetName = 'KYT Report';
        const columns = [
            {header : 'DEPARTMENT' , key : 'SDEPT'},
            {header : 'SECTION' , key : 'SSEC'},
            {header : 'LEADER'    , key : 'SNAME'},
            {header : 'APR'     , key : 'APR'},
            {header : 'MAY'     , key : 'MAY'},
            {header : 'JUN'     , key : 'JUN'},
            {header : 'JUL'     , key : 'JUL'},
            {header : 'AUG'     , key : 'AUG'},
            {header : 'SEP'     , key : 'SEP'},
            {header : 'OCT'     , key : 'OCT'},
            {header : 'NOV'     , key : 'NOV'},
            {header : 'DEC'     , key : 'DEC'},
            {header : 'JAN'     , key : 'JAN'},
            {header : 'FEB'     , key : 'FEB'},
            {header : 'MAR'     , key : 'MAR'},
            ];
        const workbook = defaultExcel(data, columns, opt);
        exportExcel(workbook, `KYT REPORT ${fyear}`);
    }else{
        my.showMessage('ไม่พบข้อมูลที่ผ่านการ Approve', 'warning');
    }
})

function getReport(fyear){
    return new Promise((resolve) => {
        $.ajax({
            url: `${my.host}kyt/report/getReport`,
            type: "post",
            dataType: "json",
            data:{fyear: fyear},
            beforeSend: function (){
                my.showpreload();
            },
            success: function (res) {
                resolve(res);
            },
            complete: function(xhr, status){
                my.checkAuthen(xhr, status);
                my.hidepreload();
            }
        });
    });
}
async function getData(fyear){
    return new Promise((resolve) => {
        $.ajax({
            url: `${my.host}kyt/report/getData`,
            type: "post",
            dataType: "json",
            data:{fyear: fyear},
            beforeSend: function (){
                my.showpreload();
            },
            success: function (res) {
                resolve(res);
            },
            complete: function(xhr, status){
                my.checkAuthen(xhr, status);
                my.hidepreload();
            }
        });
    });
}

async function setData(data){
    $('#ctotal').html(data.ctotal);
    $('#cpending').html(data.cpending);
    await createTable('#table_topten', data.topten);
    // const xaxisYear = ['เม.ษ.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.', 'ม.ค.', 'ก.พ.', 'มี.ค.'];
    const fy =  parseInt(fyear.toString().slice(-2));
    const xaxisYear = [`เม.ษ.${fy}`, `พ.ค.${fy}`, `มิ.ย.${fy}`, `ก.ค.${fy}`, `ส.ค.${fy}`, `ก.ย.${fy}`, `ต.ค.${fy}`, `พ.ย.${fy}`, `ธ.ค.${fy}`, `ม.ค.${fy+1}`, `ก.พ.${fy+1}`, `มี.ค.${fy+1}`];
    await chartCompair(data.curCompair, data.prevCompair, xaxisYear);

    const xaxisMon = Object.keys(data.monthly).map((key) => `${key} ${data.month}`);
    await monthlyChart(data.monthly, xaxisMon);
    await monSecChart(data.monSec, xaxisMon);
    await monCatChart(data.monCat, xaxisMon);
    await yearlyChart(data.curCompair, xaxisYear);
    await yearSecChart(data.yearSec, xaxisYear);
    await yearCatChart(data.yearCat, xaxisYear);
}

/**
 * Table topten
 * @param {string} tableID 
 * @param {object} data 
 * @returns 
 */
async function createTable(tableID, data){
    const opt = { ...my.tableOption };
    opt.ordering     = false;
    opt.searching    = false;
    opt.lengthChange = false;
    opt.paging = false;
    opt.info = false;
    opt.data = data;
    opt.columns = [
        { data: "ITEMS_NAME",   title: "ความเสี่ยงที่พบ",  className: 'align-top ', width: '20%'},
        { data: "AMOUNT",       title: "จำนวน",     className: 'align-top ', width: '20%'},
    ];
    return $(tableID).DataTable(opt);
}

/**
 * Chart compair fyear
 * @param {object} cur 
 * @param {object} prev 
 * @param {array} xaxis
 */
async function chartCompair(cur, prev, xaxis){
    var options = {
        series: [{
                name: `FY${fyear.toString().substr(2,2)}`,
                data: [cur.APR, cur.MAY, cur.JUN, cur.JUL, cur.AUG, cur.SEP, cur.OCT, cur.NOV, cur.DEC, cur.JAN, cur.FEB, cur.MAR]
            }, 
            {
                name: `FY${fyear.toString().substr(2,2)-1}`,
                data: [prev.APR, prev.MAY, prev.JUN, prev.JUL, prev.AUG, prev.SEP, prev.OCT, prev.NOV, prev.DEC, prev.JAN, prev.FEB, prev.MAR]
        }],
        chart: {
        type: 'bar',
        height: '100%'
      },
      plotOptions: {
        bar: {
            horizontal: true,
        },
      },
      dataLabels: {
        enabled: false
      },
      colors: [color1, color2],
      xaxis: {
        categories: xaxis,
        // categories: ['เม.ษ.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.', 'ม.ค.', 'ก.พ.', 'มี.ค.'],
      },
      tooltip: {
        y: {
          formatter: function (val) {
            return `พบความเสี่ยง ${val} ครั้ง/เดือน` 
          }
        }
      }
      };

      var chart = new ApexCharts(document.querySelector("#chart"), options);
      chart.render();
}

/**
 * Chart monthly
 * @param {object} data 
 * @param {array} xaxis 
 */
async function monthlyChart(data, xaxis){
    var options = {
        chart: {
            type: 'area',
            id:'monthly1',
            group: 'monthly',
            height: '200',
            sparkline: {
                enabled: true
            },
            zoom: {
                enabled: true // เปิดใช้งาน Zoom
            },
            toolbar: {
                show: true, // แสดง toolbar
                tools: {
                    zoom: true, // เปิดใช้งานการซูม
                    pan: false, // ปิดการลาก (Panning)
                    reset: true // แสดงปุ่มรีเซ็ตการซูม
                }
            }
        },
        xaxis: {
            categories: xaxis,
        },
        stroke: {
            width: 3,
            curve: 'smooth'
        },
        series: [{
            name: "พบความเสี่ยง",
            data: Object.keys(data).map((key) => data[key])
            }],
        tooltip: {
            y: {
                formatter: function (val) {
                    return `${val} ครั้ง/วัน` 
                }
            }
        }
    };
    new ApexCharts(document.querySelector("#monthlyChart"), options).render();
}
/**
 * Chart monthly section
 * @param {object} data 
 * @param {array} xaxis 
 */
async function monSecChart(data, xaxis){
    const series = Object.keys(data).map(key => {
        return {
            name: key, // ใช้ชื่อ key เป็น name
            data: Object.values(data[key]) // ดึงค่าทั้งหมดจาก object เป็น array
        };
    });
    const ranColor = my.generateRandomColors(series.length);
    var options = {
        series: series,
        chart: {
            type: 'line',
            id:'monthly2',
            group: 'monthly',
            height: '200',
            sparkline: {
                enabled: true
            },
            zoom: {
                enabled: true // เปิดใช้งาน Zoom
            },
            toolbar: {
                show: true, // แสดง toolbar
                tools: {
                    zoom: true, // เปิดใช้งานการซูม
                    pan: false, // ปิดการลาก (Panning)
                    reset: true // แสดงปุ่มรีเซ็ตการซูม
                }
            }
        },
        colors: ranColor,
        xaxis: {
            categories: xaxis,
        },
        stroke: {
            width: 3,
            curve: 'smooth'
        },
        tooltip: {
            custom: function ({ series, seriesIndex, dataPointIndex, w }) {
                // // ดึงค่าของ data point
                // const value      = series[seriesIndex][dataPointIndex];
                // const seriesName = w.globals.seriesNames[seriesIndex];
                // const category   = w.globals.categoryLabels[dataPointIndex];
                // const color      = w.globals.colors[seriesIndex];
                // console.log(w.globals);
                // console.log(seriesIndex);
                // console.log(dataPointIndex);
                
                // // ตรวจสอบว่าค่าคือ null หรือ 0
                // if (value === null || value === 0) {
                //     return ''; // ไม่แสดง Tooltip
                // }
    
                // return `
                //     <div class="apexcharts-tooltip-title" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;">
                //              <span>${category}</span>
                //          </div>
                //     <div class="apexcharts-tooltip-series-group apexcharts-tooltip-series-group-0 apexcharts-active" style="order: 1; display: flex;">
                //         <span class="apexcharts-tooltip-marker" style="background-color: ${color};"></span>
                //         <div class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;">
                //             <div class="apexcharts-tooltip-y-group">
                //                 <span class="apexcharts-tooltip-text-y-label">${seriesName}</span>
                //                 <span class="apexcharts-tooltip-text-y-value">: ${value} ครั้ง/วัน</span>
                //             </div>
                //         </div>
                //     </div>
                // `;
                 // ดึงข้อมูลของจุด (Category และ Title)
                const category = w.globals.categoryLabels[dataPointIndex];
                let tooltipContent = `
                    <div class="apexcharts-tooltip-title" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;">
                        <span>${category}</span>
                    </div>
                `;
                let c = 0;
                // วนลูปเพื่อแสดงข้อมูลของทุกซีรีส์
                for (let i = 0; i < series.length; i++) {
                    const value = series[i][dataPointIndex];
                    const seriesName = w.globals.seriesNames[i];
                    const color = w.globals.colors[i];

                    // ข้ามค่าที่เป็น null หรือ 0
                    if (value === null || value === 0) continue;
                    c++;
                    // เพิ่มข้อมูลของแต่ละซีรีส์ลงใน Tooltip
                    tooltipContent += `
                        <div class="apexcharts-tooltip-series-group" style="display: flex; align-items: center; margin-top: 5px;">
                            <span class="apexcharts-tooltip-marker" style="background-color: ${color}; width: 10px; height: 10px; border-radius: 50%; margin-right: 8px;"></span>
                            <span class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;">
                                <span class="apexcharts-tooltip-y-label">${seriesName}:</span>
                                <span class="apexcharts-tooltip-y-value">${value} ครั้ง/วัน</span>
                            </span>
                        </div>
                    `;
                }
                tooltipContent = checkRisk(tooltipContent,c);
                return tooltipContent;
            }
        }
        
    };
    new ApexCharts(document.querySelector("#monSecChart"), options).render();
}


/**
 * 
 * @param {object} data 
 * @param {array} xaxis 
 */
async function monCatChart(data, xaxis){
    const series = Object.keys(data).map(key => {
        return {
            name: key, // ใช้ชื่อ key เป็น name
            data: Object.values(data[key]) // ดึงค่าทั้งหมดจาก object เป็น array
        };
    });
    const ranColor = my.generateRandomColors(series.length);
    var options = {
        series: series,
        chart: {
            type: 'line',
            id:'monthly3',
            group: 'monthly',
            height: '200',
            sparkline: {
                enabled: true
            },
            zoom: {
                enabled: true // เปิดใช้งาน Zoom
            },
            toolbar: {
                show: true, // แสดง toolbar
                tools: {
                    zoom: true, // เปิดใช้งานการซูม
                    pan: false, // ปิดการลาก (Panning)
                    reset: true // แสดงปุ่มรีเซ็ตการซูม
                }
            }
        },
        colors: ranColor,
        xaxis: {
            categories: xaxis,
        },
        stroke: {
            width: 3,
            curve: 'smooth'
        },
        tooltip: {
            custom: function ({ series, seriesIndex, dataPointIndex, w }) {
                // ดึงข้อมูลของจุด (Category และ Title)
                const category = w.globals.categoryLabels[dataPointIndex];
                let tooltipContent = `
                    <div class="apexcharts-tooltip-title" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;">
                        <span>${category}</span>
                    </div>
                `;
                let c = 0;
                // วนลูปเพื่อแสดงข้อมูลของทุกซีรีส์
                for (let i = 0; i < series.length; i++) {
                    const value = series[i][dataPointIndex];
                    const seriesName = w.globals.seriesNames[i];
                    const color = w.globals.colors[i];

                    // ข้ามค่าที่เป็น null หรือ 0
                    if (value === null || value === 0) continue;
                    c++;
                    // เพิ่มข้อมูลของแต่ละซีรีส์ลงใน Tooltip
                    tooltipContent += `
                        <div class="apexcharts-tooltip-series-group" style="display: flex; align-items: center; margin-top: 5px;">
                            <span class="apexcharts-tooltip-marker" style="background-color: ${color}; width: 10px; height: 10px; border-radius: 50%; margin-right: 8px;"></span>
                            <span class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;">
                                <span class="apexcharts-tooltip-y-label">${seriesName}:</span>
                                <span class="apexcharts-tooltip-y-value">${value} ครั้ง/วัน</span>
                            </span>
                        </div>
                    `;
                }
                tooltipContent = checkRisk(tooltipContent,c);
                return tooltipContent;
            }
        }
        
    };
    new ApexCharts(document.querySelector("#monCatChart"), options).render();
}


/**
 * Chart yearly
 * @param {object} data 
 * @param {array} xaxis 
 */
async function yearlyChart(data, xaxis){
    var options = {
        chart: {
            type: 'area',
            id:'yearly1',
            group: 'yearly',
            height: '200',
            sparkline: {
                enabled: true
            },
            zoom: {
                enabled: true // เปิดใช้งาน Zoom
            },
            stroke: {
                width: 3,
                curve: 'smooth'
            },
            toolbar: {
                show: true, // แสดง toolbar
                tools: {
                    zoom: true, // เปิดใช้งานการซูม
                    pan: false, // ปิดการลาก (Panning)
                    reset: true // แสดงปุ่มรีเซ็ตการซูม
                }
            }
        },
        xaxis: {
            categories: xaxis,
        },
        series: [{
            name: "พบความเสี่ยง",
            data: Object.keys(data).map((key) => data[key])
            }],
        tooltip: {
            y: {
                formatter: function (val) {
                    return `${val} ครั้ง/เดือน` 
                }
            }
        }
    };
    new ApexCharts(document.querySelector("#yearlyChart"), options).render();
}


/**
 * 
 * @param {object} data 
 * @param {array} xaxis 
 */
async function yearSecChart(data, xaxis){
    const series = Object.keys(data).map(key => {
        return {
            name: key, // ใช้ชื่อ key เป็น name
            data: Object.values(data[key]) // ดึงค่าทั้งหมดจาก object เป็น array
        };
    });
    console.log(series);
    const ranColor = my.generateRandomColors(series.length);
    var options = {
        series: series,
        chart: {
            type: 'line',
            id:'yearly2',
            group: 'yearly',
            height: '200',
            sparkline: {
                enabled: true
            },
            zoom: {
                enabled: true // เปิดใช้งาน Zoom
            },
            toolbar: {
                show: true, // แสดง toolbar
                tools: {
                    zoom: true, // เปิดใช้งานการซูม
                    pan: false, // ปิดการลาก (Panning)
                    reset: true // แสดงปุ่มรีเซ็ตการซูม
                }
            }
        },
        colors: ranColor,
        xaxis: {
            categories: xaxis,
        },
        stroke: {
            width: 3,
            curve: 'smooth'
        },
        tooltip: {
            custom: function ({ series, seriesIndex, dataPointIndex, w }) {
                // ดึงข้อมูลของจุด (Category และ Title)
                const category = w.globals.categoryLabels[dataPointIndex];
                let tooltipContent = `
                    <div class="apexcharts-tooltip-title" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;">
                        <span>${category}</span>
                    </div>
                `;
                let c = 0;
                // วนลูปเพื่อแสดงข้อมูลของทุกซีรีส์
                for (let i = 0; i < series.length; i++) {
                    const value = series[i][dataPointIndex];
                    const seriesName = w.globals.seriesNames[i];
                    const color = w.globals.colors[i];

                    // ข้ามค่าที่เป็น null หรือ 0
                    if (value === null || value === 0) continue;
                    c++;
                    // เพิ่มข้อมูลของแต่ละซีรีส์ลงใน Tooltip
                    tooltipContent += `
                        <div class="apexcharts-tooltip-series-group" style="display: flex; align-items: center; margin-top: 5px;">
                            <span class="apexcharts-tooltip-marker" style="background-color: ${color}; width: 10px; height: 10px; border-radius: 50%; margin-right: 8px;"></span>
                            <span class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;">
                                <span class="apexcharts-tooltip-y-label">${seriesName}:</span>
                                <span class="apexcharts-tooltip-y-value">${value} ครั้ง/เดือน</span>
                            </span>
                        </div>
                    `;
                }
                tooltipContent = checkRisk(tooltipContent,c);
                return tooltipContent;
            }
        }
        
    };
    new ApexCharts(document.querySelector("#yearSecChart"), options).render();
}

/**
 * 
 * @param {object} data 
 * @param {array} xaxis 
 */
async function yearCatChart(data, xaxis){
    const series = Object.keys(data).map(key => {
        return {
            name: key, // ใช้ชื่อ key เป็น name
            data: Object.values(data[key]) // ดึงค่าทั้งหมดจาก object เป็น array
        };
    });
    // console.log(series);
    const ranColor = my.generateRandomColors(series.length);
    
    var options = {
        series: series,
        chart: {
            type: 'line',
            id:'yearly3',
            group: 'yearly',
            height: '200',
            sparkline: {
                enabled: true
            },
            zoom: {
                enabled: true // เปิดใช้งาน Zoom
            },
            toolbar: {
                show: true, // แสดง toolbar
                tools: {
                    zoom: true, // เปิดใช้งานการซูม
                    pan: false, // ปิดการลาก (Panning)
                    reset: true // แสดงปุ่มรีเซ็ตการซูม
                }
            }
        },
        colors: ranColor,
        xaxis: {
            categories: xaxis,
        },
        stroke: {
            width: 3,
            curve: 'smooth'
        },
        tooltip: {
            custom: function ({ series, seriesIndex, dataPointIndex, w }) {
                // ดึงข้อมูลของจุด (Category และ Title)
                const category = w.globals.categoryLabels[dataPointIndex];
                let tooltipContent = `
                    <div class="apexcharts-tooltip-title" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;">
                        <span>${category}</span>
                    </div>
                `;
                let c = 0;
                // วนลูปเพื่อแสดงข้อมูลของทุกซีรีส์
                for (let i = 0; i < series.length; i++) {
                    const value = series[i][dataPointIndex];
                    const seriesName = w.globals.seriesNames[i];
                    const color = w.globals.colors[i];

                    // ข้ามค่าที่เป็น null หรือ 0
                    if (value === null || value === 0) continue;
                    c++;
                    // เพิ่มข้อมูลของแต่ละซีรีส์ลงใน Tooltip
                    tooltipContent += `
                        <div class="apexcharts-tooltip-series-group" style="display: flex; align-items: center; margin-top: 5px;">
                            <span class="apexcharts-tooltip-marker" style="background-color: ${color}; width: 10px; height: 10px; border-radius: 50%; margin-right: 8px;"></span>
                            <span class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;">
                                <span class="apexcharts-tooltip-y-label">${seriesName}:</span>
                                <span class="apexcharts-tooltip-y-value">${value} ครั้ง/เดือน</span>
                            </span>
                        </div>
                    `;
                }
                tooltipContent = checkRisk(tooltipContent,c);
                return tooltipContent;
            }
        }
        
    };
    new ApexCharts(document.querySelector("#yearCatChart"), options).render();
}


/**
 * Check risk
 * @param {string} text 
 * @param {number} i 
 * @returns 
 */
function checkRisk(text, i){
    return i > 0 ? text : text += `
    <div class="apexcharts-tooltip-series-group" style="display: flex; align-items: center; margin-top: 5px;">
        <span class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;">
            <span class="apexcharts-tooltip-y-label">ไม่พบความเสี่ยง</span>
        </span>
    </div>
    `;
}




  
  

