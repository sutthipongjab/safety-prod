import $       from "jquery";
import {host, showpreload, hidepreload, checkAuthen, generateRandomColors, getMonthtext} from "../utils.js";
import {getDepartment} from "../webservice.js";
import ApexCharts from 'apexcharts'


var fyear, chtDept, chtA, chtB, chtSec, chtYearSec, chtCatMon, chtCatA, chtCatB, monthSelected, monFY, xaxisYear;
const color1 = '#FCCF31';
const color2 = '#17ead9';

$(document).ready(async function () {
    fyear = parseInt($('#fyear').html());
    // console.log(fyear);
    const data = await getData(fyear);
    await setData(data);
});

$(document).on('click', '#previousFY',async function(){
    showpreload();
    const e = $('#fyear');
    const fyear = parseInt(e.html()) - 1;
    e.html(fyear)
    window.location.assign(`${host}patrol/report/index/${fyear}`)
});

$(document).on('click', '#nextFY',async function(){
    showpreload();
    const e = $('#fyear');
    const fyear = parseInt(e.html()) + 1;
    e.html(fyear)
    window.location.assign(`${host}patrol/report/index/${fyear}`)
});

/**
 * Set data and create chart
 * @param {*} data 
 */
async function setData(data){
    const fy =  parseInt(fyear.toString().slice(-2));
    xaxisYear = [`เม.ษ.${fy}`, `พ.ค.${fy}`, `มิ.ย.${fy}`, `ก.ค.${fy}`, `ส.ค.${fy}`, `ก.ย.${fy}`, `ต.ค.${fy}`, `พ.ย.${fy}`, `ธ.ค.${fy}`, `ม.ค.${fy+1}`, `ก.พ.${fy+1}`, `มี.ค.${fy+1}`];
    await chartCompair(data.compClass.classA, data.compClass.classB);
}

/**
 * Set data for create department chart
 * @param {string} monY 
 */
async function setDeptChart(monY){
    monthSelected = monthToNumber(monY.slice(0,-2));
    const dept  = await getDepartment();
    // console.log(month, dept);
    const dataDept = await getDataDept(monthSelected, dept);
    const classA   = await getClass(monthSelected, 'A');
    const classB   = await getClass(monthSelected, 'B');
    await chartDept(dataDept);
    await chartClass(classA, '#chartA');
    await chartClass(classB, '#chartB');
    $('.deptChart').removeClass('hidden');
    $('.monthSelected').removeClass('hidden');
    $('#monthSelected').html(monthSelected);
    $('.deptSelected').addClass('hidden');
    $('.secSelected').addClass('hidden');
    $('.secChart').addClass('hidden');
    $('.secClassDetail').addClass('hidden');
    $('.secDetail').addClass('hidden');
}

/**
 * Set data for create section chart
 * @param {string} dept 
 */
async function setSecChart(dept){
    // console.log('month : ', monthSelected,'fyear : ',fyear,'dept : ',dept);
    const sec = await getDataSec(dept);
    chartSec(sec);
    $('.dept').html(dept.toUpperCase());
    $('#deptSelected').html(dept.toUpperCase());
    $('.deptSelected').removeClass('hidden');
    // $('.secChart').addClass('w-full');
    $('.secChart').removeClass('hidden');
    $('.secClassDetail').addClass('hidden');
    $('.secDetail').addClass('hidden');
    $('.secSelected').addClass('hidden');
}

/**
 * Set data and create section detail chart
 * @param {string} sec 
 */
async function setSecDetail(sec){
    // console.log(sec, sec.toUpperCase());
    sec = sec.toUpperCase();
    const data = await getYearlySec(sec);
    // const category = await getCatByMonth(sec);
    yearlySecChart(data.A,data.B);
    catMonthChart(data.category);
    catClasschart(data.categoryA,'#chtCatClaA');
    catClasschart(data.categoryB,'#chtCatClaB');
    $('.secClass').html(`Class Detail ${sec.toUpperCase()}`);
    $('.sec').html(`${sec.toUpperCase()}`);
    $('#secSelected').html(`${sec.toUpperCase()}`);
    
    // $('.secChart').removeClass('w-full');
    $('.secSelected').removeClass('hidden');
    $('.secClassDetail').removeClass('hidden');
    $('.secDetail').removeClass('hidden');
}


/**
 * Chart compair class
 * @param {object} A
 * @param {object} B 
 */
async function chartCompair(A, B){
    var options = {
        series: [{
                name: `Class A`,
                data: [A.APR, A.MAY, A.JUN, A.JUL, A.AUG, A.SEP, A.OCT, A.NOV, A.DEC, A.JAN, A.FEB, A.MAR]
            }, 
            {
                name: `Class B`,
                data: [B.APR, B.MAY, B.JUN, B.JUL, B.AUG, B.SEP, B.OCT, B.NOV, B.DEC, B.JAN, B.FEB, B.MAR]
        }],
        chart: {
        type: 'bar',
        height: '100%',
        stacked: true,
        events: {
            dataPointSelection: function(event, chartContext, config) {
                // ดึงข้อมูลซีรีส์และตำแหน่งที่ถูกคลิก
                const seriesIndex = config.seriesIndex; // ดัชนีซีรีส์
                const dataPointIndex = config.dataPointIndex; // ดัชนีจุดข้อมูล
                const seriesName = config.w.globals.seriesNames[seriesIndex]; // ชื่อซีรีส์ Class A
                const category = config.w.globals.labels[dataPointIndex]; // แกน X ม.ค.24
                const value = config.w.globals.series[seriesIndex][dataPointIndex]; // ค่าของข้อมูลที่คลิก  5 10 15

                // console.log(`Clicked: ${seriesName}, Category: ${category}, Value: ${value}`);

                // เรียกฟังก์ชันสร้างกราฟใหม่
                setDeptChart(category);
                monFY = category;
            }
        }
      },
      responsive: [{
        breakpoint: 480,
        options: {
          legend: {
            position: 'bottom',
            offsetX: -10,
            offsetY: 0
          }
        }
      }],
      plotOptions: {
        bar: {
          horizontal: false,
          dataLabels: {
            total: {
              enabled: true,
              style: {
                fontSize: '13px',
                fontWeight: 900
              }
            }
          }
        },
      },
      colors: [color1, color2],
      xaxis: {
        categories: xaxisYear,
      },
      };

      new ApexCharts(document.querySelector("#chart"), options).render();
}

/**
 * create chart department
 * @param {object} data 
 */
async function chartDept(data){
    if (chtDept) {
        chtDept.destroy(); // ทำลายกราฟเดิม
    }
   
    const categories = Object.keys(data); // รายชื่อแผนก
    const classA = categories.map(dept => data[dept].classA); // ค่า classA
    const classB = categories.map(dept => data[dept].classB); // ค่า classB
    classA.push(classA.reduce((prev,cur) => parseInt(prev) + parseInt(cur),0));
    classB.push(classB.reduce((prev,cur) => parseInt(prev) + parseInt(cur),0));
    categories.push(monFY);
    const series = [
        { name: 'Class A', data: classA },
        { name: 'Class B', data: classB }
    ];
    
    // console.log(categories,classA,classB);
    // console.log(data,series);
    var options = {
        series: series,
        chart: {
        type: 'bar',
        height: '100%',
        stacked: true,
        events: {
            dataPointSelection: function(event, chartContext, config) {
                // ดึงข้อมูลซีรีส์และตำแหน่งที่ถูกคลิก
                const seriesIndex = config.seriesIndex; // ดัชนีซีรีส์
                const dataPointIndex = config.dataPointIndex; // ดัชนีจุดข้อมูล
                const seriesName = config.w.globals.seriesNames[seriesIndex]; // ชื่อซีรีส์ Class A
                const category = config.w.globals.labels[dataPointIndex]; // แกน X IS Dept.
                const value = config.w.globals.series[seriesIndex][dataPointIndex]; // ค่าของข้อมูลที่คลิก  5 10 15

                // console.log(`Clicked: ${seriesName}, Category: ${category}, Value: ${value}`);

                const LastIndex = config.w.globals.series[seriesIndex].length - 1;
                // console.log(dataPointIndex, LastIndex, config.w.globals.series[seriesIndex].length,seriesIndex);
                
                // เรียกฟังก์ชันสร้างกราฟใหม่
                if(dataPointIndex != LastIndex) setSecChart(category);
            }
        }
      },
        plotOptions: {
            bar: {
                horizontal: false,
                dataLabels: {
                    total: {
                    enabled: true,
                    style: {
                        fontSize: '13px',
                        fontWeight: 900
                    }
                    }
                }
            },
        },
        colors: [color1, color2],
        xaxis: {
            categories: categories,
        },
    };

    chtDept = new ApexCharts(document.querySelector("#deptChart"), options);
    chtDept.render();
}

/**
 * Create chart class a or b
 * @param {array} data 
 * @param {string} chartID 
 */
async function chartClass(data, chartID){
    if (chtA && chartID == '#chartA') {
        chtA.destroy(); // ทำลายกราฟเดิม
    }
    if (chtB && chartID == '#chartB') {
        chtB.destroy(); // ทำลายกราฟเดิม
    }
    const labels = data.map(categories => categories.ITEMS_NAME);
    const amounts = data.map(categories => parseInt(categories.AMOUNT));
    // console.log(labels, amounts);
    
    var options = {
        series: amounts,
        chart: {
            width: '100%',
            height: 400,
            type: 'pie',
        },
        labels: labels,
        responsive: [{
            breakpoint: 480,
            options: {
            chart: {
                width: 200
            },
            }
        }],
        legend: {
            position: 'bottom',
            horizontalAlign: 'left',
            
        },
        theme: {
            palette: 'palette2' // upto palette10
        },
        colors: generateRandomColors(amounts.length),
        responsive: [
            {
                breakpoint: 480, // ขนาดหน้าจอ (ต่ำกว่า 480px)
                options: {
                    legend: {
                        horizontalAlign: 'left', // จัดตำแหน่งแนวนอน
                        // position: 'right'
                    }
                }
            }
        ],
    };
        
    if(chartID == '#chartA'){
        chtA = new ApexCharts(document.querySelector(chartID), options);
        chtA.render();
    }else{
        chtB = new ApexCharts(document.querySelector(chartID), options);
        chtB.render();
    }
} 

/**
 * Create section chart
 * @param {object} data 
 */
async function chartSec(data){
    if (chtSec) {
        chtSec.destroy(); // ทำลายกราฟเดิม
    }
    
    const categories = Object.keys(data); // รายชื่อแผนก
    const classA = categories.map(sec => data[sec].A); // ค่า classA
    const classB = categories.map(sec => data[sec].B); // ค่า classB
    const series = [
        { name: 'Class A', data: classA },
        { name: 'Class B', data: classB }
    ];
    
    // console.log(typeof data);
    // console.log(categories,classA,classB);
    // console.log(data,series);
    var options = {
        series: series,
        chart: {
        type: 'bar',
        height: '100%',
        stacked: true,
        events: {
            dataPointSelection: function(event, chartContext, config) {
                // ดึงข้อมูลซีรีส์และตำแหน่งที่ถูกคลิก
                const seriesIndex = config.seriesIndex; // ดัชนีซีรีส์
                const dataPointIndex = config.dataPointIndex; // ดัชนีจุดข้อมูล
                const seriesName = config.w.globals.seriesNames[seriesIndex]; // ชื่อซีรีส์ Class A
                const category = config.w.globals.labels[dataPointIndex]; // แกน X IS Dept.
                const value = config.w.globals.series[seriesIndex][dataPointIndex]; // ค่าของข้อมูลที่คลิก  5 10 15

                // console.log(`Clicked: ${seriesName}, Category: ${category}, Value: ${value}`);

                // เรียกฟังก์ชันสร้างกราฟใหม่
                setSecDetail(category);
            }
        }
      },
        plotOptions: {
            bar: {
                horizontal: false,
                dataLabels: {
                    total: {
                    enabled: true,
                    style: {
                        fontSize: '13px',
                        fontWeight: 900
                    }
                    }
                }
            },
        },
        colors: [color1, color2],
        xaxis: {
            categories: categories,
        },
    };

    chtSec = new ApexCharts(document.querySelector("#secChart"), options);
    chtSec.render();
}

/**
 * Create yearly section chart
 * @param {array} A 
 * @param {array} B 
 */
async function yearlySecChart(A, B){
    if (chtYearSec) {
        chtYearSec.destroy(); // ทำลายกราฟเดิม
    }
    // console.log(typeof A, typeof B);
    
    const totalA = Object.values(A).reduce((sum, value) => parseInt(sum) + parseInt(value), 0);
    const totalB = Object.values(B).reduce((sum, value) => parseInt(sum) + parseInt(value), 0);
    const xaxisYearCopy = [...xaxisYear]; // สร้างสำเนาใหม่ของ xaxisYear
    xaxisYearCopy.push(`Total FY'${fyear.toString().slice(-2)}`); // เพิ่มค่าที่ต้องการ

    // console.log('Original:', xaxisYear); // xaxisYear เดิมไม่ถูกแก้ไข
    // console.log('Modified:', xaxisYearCopy); // สำเนาใหม่มีค่าเพิ่ม
    
    var options = {
        series: [{
            name: `Class A`,
            data: [A.APR, A.MAY, A.JUN, A.JUL, A.AUG, A.SEP, A.OCT, A.NOV, A.DEC, A.JAN, A.FEB, A.MAR, totalA]
        }, 
        {
            name: `Class B`,
            data: [B.APR, B.MAY, B.JUN, B.JUL, B.AUG, B.SEP, B.OCT, B.NOV, B.DEC, B.JAN, B.FEB, B.MAR, totalB]
        }],
        chart: {
        type: 'bar',
        height: '100%',
        stacked: true,
      },
        plotOptions: {
            bar: {
                horizontal: false,
                dataLabels: {
                    total: {
                    enabled: true,
                    style: {
                        fontSize: '13px',
                        fontWeight: 900
                    }
                    }
                }
            },
        },
        colors: [color1, color2],
        xaxis: {
            categories: xaxisYearCopy,
        },
    };
    // console.log('yearly: ',options);
    
    chtYearSec = new ApexCharts(document.querySelector("#yearSecChart"), options);
    chtYearSec.render();
}

/**
 * Create chart category by month 
 * @param {array} data 
 */
async function catMonthChart(data){
    if (chtCatMon) {
        chtCatMon.destroy(); // ทำลายกราฟเดิม
    }
    const xaxis  = data.map(categories => categories.ITEMS_NAME);
    const series = data.map(categories => categories.AMOUNT);
    // console.log('catMonthChart : ',xaxis, series);
    

    var options = {
        series: [{
            name : getMonthtext(monthSelected,'MMM'),
            data : series
        }],
        chart: {
        type: 'bar',
        height: '100%',
      },
        plotOptions: {
            bar: {
                horizontal: false,
                dataLabels: {
                    total: {
                    enabled: true,
                    style: {
                        fontSize: '13px',
                        fontWeight: 900
                    }
                    }
                }
            },
        },
        colors: [color1, color2],
        xaxis: {
            categories: xaxis,
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return `พบความเสี่ยง ${val} ครั้ง/เดือน` 
                }
            }
        }
    };
    chtCatMon = new ApexCharts(document.querySelector("#chtCatMon"), options);
    chtCatMon.render();
}

/**
 * Create class category chart
 * @param {array} data 
 * @param {string} chartID 
 */
async function catClasschart(data, chartID){
    if (chtCatA && chartID == '#chtCatClaA') {
        chtCatA.destroy(); // ทำลายกราฟเดิม
    }
    if (chtCatB && chartID == '#chtCatClaB') {
        chtCatB.destroy(); // ทำลายกราฟเดิม
    }
    const xaxis = data.map(categories => categories.ITEMS_NAME);
    const amounts = data.map(categories => parseInt(categories.AMOUNT));
    // console.log(xaxis, amounts);
    
    var options = {
        series: [{
            name : chartID == '#chtCatClaA' ? 'Class A' : 'Class B',
            data : amounts,
        }],
        chart: {
            width: '100%',
            height: '100%',
            type: 'bar',
        },
        // labels: labels,
        xaxis: {
            categories: xaxis,
        },
        responsive: [{
            breakpoint: 480,
            options: {
            chart: {
                width: 200
            },
            }
        }],
        legend: {
            position: 'bottom',
            horizontalAlign: 'left',
            
        },
        colors: generateRandomColors(amounts.length),
        responsive: [
            {
                breakpoint: 480, // ขนาดหน้าจอ (ต่ำกว่า 480px)
                options: {
                    legend: {
                        horizontalAlign: 'left', // จัดตำแหน่งแนวนอน
                        // position: 'right'
                    }
                }
            }
        ],
    };
        
    if(chartID == '#chtCatClaA'){
        chtCatA = new ApexCharts(document.querySelector(chartID), options);
        chtCatA.render();
    }else{
        chtCatB = new ApexCharts(document.querySelector(chartID), options);
        chtCatB.render();
    }
    
    
} 

/**
 * Convert month to number
 * @param {string} month 
 * @returns 
 */
function monthToNumber(month) {
    switch (month) {
        case 'ม.ค.': return '01';
        case 'ก.พ.': return '02';
        case 'มี.ค.': return '03';
        case 'เม.ย.': return '04';
        case 'พ.ค.': return '05';
        case 'มิ.ย.': return '06';
        case 'ก.ค.': return '07';
        case 'ส.ค.': return '08';
        case 'ก.ย.': return '09';
        case 'ต.ค.': return '10';
        case 'พ.ย.': return '11';
        case 'ธ.ค.': return '12';
        default: return null; 
    }
}


/**
 * get data class for compair fyear
 * @param {number} fyear 
 * @returns 
 */
async function getData(fyear){
    return new Promise((resolve) => {
        $.ajax({
            url: `${host}patrol/report/getData`,
            type: "post",
            dataType: "json",
            data:{fyear: fyear},
            beforeSend: function (){
                showpreload();
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
  
/**
 * get data department class
 * @param {string} month 
 * @param {array} dept 
 * @returns 
 */
function getDataDept(month, dept){
    return new Promise((resolve) => {
        $.ajax({
            url: `${host}patrol/report/getDataDept`,
            type: "post",
            dataType: "json",
            data:{
                month: month,
                dept : dept,
                fyear : fyear,
            },
            beforeSend: function (){
                showpreload();
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

/**
 * Get data class a or b
 * @param {string} month 
 * @param {string} type 
 * @returns 
 */
function getClass(month, type){
    return new Promise((resolve) => {
        $.ajax({
            url: `${host}patrol/report/getClass`,
            type: "post",
            dataType: "json",
            data:{
                type: type,
                month : month,
                fyear : fyear,
            },
            beforeSend: function (){
                showpreload();
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

/**
 * Get data class section for create chart
 * @param {string} sec 
 * @returns 
 */
function getDataSec(sec){
    return new Promise((resolve) => {
        $.ajax({
            url: `${host}patrol/report/getDataSec`,
            type: "post",
            dataType: "json",
            data:{
                month: monthSelected,
                sec : sec,
                fyear : fyear,
            },
            beforeSend: function (){
                showpreload();
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

/**
 * Get data yearly section
 * @param {string} sec 
 * @returns 
 */
function getYearlySec(sec){
    return new Promise((resolve) => {
        $.ajax({
            url: `${host}patrol/report/getYearlySec`,
            type: "post",
            dataType: "json",
            data:{
                month: monthSelected,
                sec : sec,
                fyear : fyear,
            },
            beforeSend: function (){
                showpreload();
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