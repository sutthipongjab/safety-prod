import $ from "jquery";
import { createForm, deleteForm, showFlow } from "../_form.js";
import { host, tableOption, showMessage } from "../utils.js";
import DataTable from "datatables.net-dt";
import { Table } from "jspdf-autotable";
import ExcelJS from "exceljs";
import { getfileInPath, getArrayBufferFile } from "../_file.js";
// var host = window.location.origin + "/safety/";
console.log(host);

$(document).ready(function () {
  $(document).on("click", "#Search-date", function () {
    const date_s = $("#datepicker-range-start").val();
    const date_e = $("#datepicker-range-end").val();

    if (!date_s || !date_e) {
      // Check if the values are empty or undefined
      showMessage("กรุณาเลือกวันที่", "error");
      // alert("กรุณาเลือกวันที่");
      return;
    }
    $("#table_history").DataTable().ajax.reload();
  });

  $(document).on("click", "#reset-date", function () {
    $("#datepicker-range-end").val("");
    $("#datepicker-range-start").val("");
    $("#table_history").DataTable().ajax.reload();
  });
  // const date_select = $("#date_select").val();

  // $(document).on("change", "#date_select", function () {
  //   $("#table_history").DataTable().ajax.reload();
  // });
  new DataTable("#table", {
    ...tableOption,
    ajax: {
      url: `${host}ppe/admin/requisition/get_data_request`,
      dataSrc: function (res) {
        console.log(res);
        return res;
      },
    },
    columns: [
      {
        data: null,
        title: "No.",
        render: function (data, type, row, meta) {
          return meta.row + 1;
        },
      },
      {
        data: "FORMNUMBER",
        title: "Req No.",
        className: "text-center",
      },
      { data: "CREATE_DATE", title: "Req Date" },
      {
        data: "EMP_REQUEST",
        title: "EmpCode",
      },
      { data: "STNAME", title: "Requester" },
      {
        data: null,
        title: "Category",
        render: function (data) {
          return data.CATNAME + "(" + data.PROD_SIZES + ")";
        },
      },
      { data: "CATDESC", title: "Model" },
      { data: "RT_DETAIL", title: "Req Type" },
      { data: "QTY", title: "Qty Req" },
      { data: "PROD_REMAIN", title: "Stock" },
      {
        data: null,
        title: "Status",
        width: "10%",
        className: "text-center",
        render: function (data) {
          if (data.CSTEPST == 2 || data.CSTEPST == 1) {
            return `
                  <div class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold">
                    <span class="mr-1.5 h-2 w-2 rounded-full bg-yellow-500"></span>
                    <span class="text-yellow-700">กำลังดำเนินการ</span>
                  </div>`;
          } else {
            return `<div class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold">
                      <span class="mr-1.5 h-2 w-2 rounded-full bg-green-500"></span>
                      <span class="text-green-700">รออนุมัติ</span>
                    </div>`;
          }
          // return data.CONFIRMED == 1 ? "Confirmed" : "Pending";
        },
      },
      {
        data: null,
        title: "Action",
        sortable: false,
        className: "text-center",
        width: "5%",
        render: function (data) {
          const edit = data.CSTEPST != 2 ? "&edit=1" : "";
          return `<a class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg"  href="${host}ppe/form/webflow?no=${data.NFRMNO}&orgNo=${data.VORGNO}&y=${data.CYEAR}&y2=${data.CYEAR2}&runNo=${data.NRUNNO}&empno=${$(
            "#emp_login"
          ).val()}&bp=%2Fform%2Fworkflow%2FwaitApv%2Easp&menu=2&closing=1${edit}" target="_blank" onclick="window.open(this.href, 'popup', 'width=1200,height=760'); return false;">FORM</a>`;
        },
      },
    ],
  });

  $("#table_history").DataTable({
    ...tableOption,
    ajax: {
      url: `${host}ppe/admin/requisition/get_data_history`,
      type: "POST",
      data: function (d) {
        const date_s = $("#datepicker-range-start").val();
        const date_e = $("#datepicker-range-end").val();
        const formattedDateStart = date_s.split("/").reverse().join("-");
        const formattedDateEnd = date_e.split("/").reverse().join("-");

        d.date_s = formattedDateStart;
        d.date_e = formattedDateEnd;
        // d.date = $("#date_select").val(); // Send selected date to API
      },
      beforeSend: function (xhr) {
        // โค้ดที่ต้องการให้ทำก่อนส่งคำขอ
        console.log("กำลังส่งคำขอไปยังเซิร์ฟเวอร์...");
        // ตัวอย่าง: แสดง loading indicator
        // $("#loading-indicator").show();
      },
      dataSrc: function (res) {
        console.log(res);
        return res;
      },
    },
    columns: [
      {
        data: "TRS_SHOWDATE",
        title: "วันที่ ",
      },
      {
        data: "TRS_TYPE",
        title: "ประเภท",
        render: function (data) {
          if (data == "1") {
            return "รับ";
          } else {
            return "จ่าย";
          }
        },
      },
      {
        data: null,
        title: "รายการ",
        render: function (data) {
          return data.CATNAME + " (" + data.PROD_SIZES + ")";
        },
      },
      {
        data: "TRS_DETAIL",
        title: "รายละเอียด",
      },
      {
        data: "TRS_QTY",
        title: "จำนวน",
      },
      {
        data: "TRS_USER",
        title: "รหัสพนักงาน",
      },
      {
        data: "STNAME",
        title: "ผู้ทำรายการ",
      },
      {
        data: "REMAINLOG",
        title: "จำนวนก่อนทำรายการ",
      },
      {
        data: "TRS_QTY",
        title: "จำนวนที่ทำรายการ",
      },
      {
        data: null,
        title: "จำนวนหลังทำรายการ",
        render: function (data) {
          if (data.TRS_TYPE == 1) {
            return Number(data.REMAINLOG) + Number(data.TRS_QTY);
          } else {
            return Number(data.REMAINLOG) - Number(data.TRS_QTY);
          }
        },
      },
    ],
  });

  $(document).on("click", "#export_excel", function () {
    let date = $("#export-month").val();
    if (!date) {
      showMessage("กรุณาเลือกเดือนที่ต้องการ Export", "error");
      return;
    }
    $.ajax({
      type: "POST",
      url: `${host}ppe/admin/requisition/ppe_list`,
      data: { date: date },
      dataType: "json",
      success: function (response) {
        generateExcel(response,date);
      },
    });
  });
});

async function generateExcel(data,date) {
  const workbook = new ExcelJS.Workbook();
  const template = await getfileInPath("assets/file/template/", "ppe_template.xlsx");
  await workbook.xlsx.load(template[0].content, { base64: true });
  const worksheet = workbook.getWorksheet(1);
  workbook.calcProperties.fullCalcOnLoad = true;

  const currentDate = new Date();
  const month = currentDate.toLocaleString("en-US", { month: "long" });
  const year = currentDate.getFullYear();
  const previousMonthDate = new Date(currentDate);
  previousMonthDate.setMonth(currentDate.getMonth() - 1);
  const prevMonth = previousMonthDate.toLocaleString("en-US", { month: "long" });
  const prevYear = previousMonthDate.getFullYear();

  worksheet.getRow(4).getCell("B").value = `Keep record from 1 - 31 ${month} ${year}`;
  worksheet.getRow(7).getCell("F").value = `ON HAND QTY (STOCK)  30 ${prevMonth}.${prevYear}`;
  worksheet.getColumn("E").width = 12;

  let row = 10;
  let groupStartRow = row;
  let catNames = [];

  const applyCellStyle = (cell, colNumber) => {
    const borders = {
      top: { style: "thin" },
      left: { style: "thin" },
      bottom: { style: "thin" },
      right: { style: "thin" },
    };
    const mediumCols = [6, 9, 12, 15, 18, 19, 20, 21, 22];
    if (mediumCols.includes(colNumber)) borders.left = { style: "medium" };

    cell.border = borders;

    const fillColors = {
      "6-8": "CCCCFF",
      "9-11": "CCFFCC",
      "12-14": "FFFF99",
      "15-17": "CCFFFF",
    };
    for (let range in fillColors) {
      const [start, end] = range.split("-").map(Number);
      if (colNumber >= start && colNumber <= end) {
        cell.fill = {
          type: "pattern",
          pattern: "solid",
          fgColor: { argb: fillColors[range] },
        };
        break;
      }
    }

    const formats = {
      "#,##0": [2, 6, 7, 9, 12],
      "#,##0.00": [10, 13, 15, 16],
    };
    for (const [fmt, cols] of Object.entries(formats)) {
      if (cols.includes(colNumber)) {
        cell.numFmt = fmt;
        break;
      }
    }
  };

  data.forEach((group) => {
    if (!group.length) return;
    group.forEach((item, idx) => {
      const dataRow = [
        "",
        idx + 1,
        item.CATDESC,
        item.CATNAME,
        item.PROD_SIZES,
        { formula: `O${row}-I${row}+L${row}` },
        item.PROD_PRICE,
        { formula: `F${row}*G${row}` },
        item.RECEIVE,
        item.PROD_PRICE,
        { formula: `I${row}*J${row}` },
        item.ISSUE,
        item.PROD_PRICE,
        { formula: `L${row}*M${row}` },
        item.PROD_REMAIN,
        item.PROD_PRICE,
        { formula: `O${row}*P${row}` },
        item.Lastpurchase || '-',
        item.Lastrequest || '-',
        { formula: `Q${row}` },
        "",
        "",
      ];
      worksheet.insertRow(row, dataRow);

      worksheet.getRow(row).eachCell((cell, colNumber) => {
        applyCellStyle(cell, colNumber);
      });

      worksheet.getCell(`E${row}`).alignment = { vertical: "middle", horizontal: "center" };
      catNames.push(item.CATNAME);
      row++;
    });

    const totalRow = [
      "",
      "TOTAL",
      "",
      "",
      "",
      { formula: `SUM(F${groupStartRow}:F${row - 1})` },
      "",
      { formula: `SUM(H${groupStartRow}:H${row - 1})` },
      { formula: `SUM(I${groupStartRow}:I${row - 1})` },
      "",
      { formula: `SUM(K${groupStartRow}:K${row - 1})` },
      { formula: `SUM(L${groupStartRow}:L${row - 1})` },
      "",
      { formula: `SUM(N${groupStartRow}:N${row - 1})` },
      { formula: `SUM(O${groupStartRow}:O${row - 1})` },
      "",
      { formula: `SUM(Q${groupStartRow}:Q${row - 1})` },
      "",
      "",
      { formula: `SUM(T${groupStartRow}:T${row - 1})` },
      { formula: `SUM(U${groupStartRow}:U${row - 1})` },
      { formula: `SUM(V${groupStartRow}:V${row - 1})` },
    ];
    worksheet.insertRow(row, totalRow);
    worksheet.mergeCells(`B${row}:C${row}`);
    worksheet.getRow(row).eachCell((cell) => {
      cell.fill = { type: "pattern", pattern: "solid", fgColor: { argb: "FFFF00" } };
      cell.border = {
        top: { style: "medium" },
        left: { style: "medium" },
        bottom: { style: "medium" },
        right: { style: "medium" },
      };
      cell.font = { ...cell.font, bold: true };
    });

    catNames.push("TOTAL");
    row++;
    groupStartRow = row;
  });

  let mergeStartRow = 10;
  let currentCatName = catNames[0];
  catNames.forEach((name, i) => {
    const actualRow = i + 10;
    if (name !== currentCatName) {
      if (currentCatName !== "TOTAL") {
        worksheet.mergeCells(`D${mergeStartRow}:D${actualRow - 1}`);
        worksheet.getCell(`D${mergeStartRow}`).alignment = { vertical: "middle", horizontal: "center", wrapText: true, textRotation: 90 };
      }
      currentCatName = name;
      mergeStartRow = actualRow;
    }
  });

  if (mergeStartRow <= catNames.length + 9 && currentCatName !== "TOTAL") {
    worksheet.mergeCells(`D${mergeStartRow}:D${catNames.length + 9}`);
    worksheet.getCell(`D${mergeStartRow}`).alignment = { vertical: "middle", horizontal: "center", wrapText: true, textRotation: 90 };
  }

  const buffer = await workbook.xlsx.writeBuffer();
  const blob = new Blob([buffer], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });
  const link = document.createElement("a");
  link.href = URL.createObjectURL(blob);
  link.download = `template_${date}.xlsx`;
  link.click();
}
