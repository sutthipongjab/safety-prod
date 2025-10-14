import $ from "jquery";
import DataTable from "datatables.net-dt";
import { tableOption, showMessage } from "../utils.js";
import { getProducts } from "./data.js";

const host = `${window.location.origin}/safety/`;
let table;

$(document).ready(async function () {
  $(document).on("click", "#btn_add", () => $("#my_modal_1")[0].showModal());

  const data = await getProducts();
  table = await createTable();

  fetchCategories();

  $("#add_uniform").submit(handleAddUniform);
  $(document).on("click", "#filterCategory", handleFilterCategory);
  $(document).on("click", ".btn-edit", handleEdit);
  $(document).on("click", "#btn-save-edit", handleSaveEdit);
  $(document).on("click", ".btn-delete", handleRemove);
  $(document).on("click", ".btn-revcieve", handleReceive);
  $(document).on("submit", "#remove_stock_form", handleSaveRemove);
  // $(document).on("click","#btn-save-add-stock",function(){
  //   $("#add_stock_form").submit();
  // });
  $(document).on("submit", "#add_stock_form", function (e) {
    e.preventDefault();
    const id = $("#id_add_stock").val();
    const qty = $("#quantity_add_stock").val();
    const remain = $("#remain_add_stock").val();
    const remark = $("#remark_add_stock").val();
    const table = $("#table").DataTable();
    const currentPage = table.page.info().page;

    $.post(`${host}ppe/admin/products/updateTransaction`, { PROD_ID: id, PROD_REMAIN: remain, REMARK: remark, QTY: qty, TYPE: "1" }, () => {
      $("#stock_modal")[0].close();
      table.ajax.reload(() => table.page(currentPage).draw(false));
    }).fail(console.error);
  });

  $(document).on("blur", ".change-size", function () {
    const id = $(this).data("id");
    const size = $(this).val();

    // console.log(size);
    // const table = $("#table").DataTable();
    // const currentPage = table.page.info().page;

    $.post(`${host}ppe/admin/products/updateSize`, { PROD_ID: id, PROD_SIZES: size }, () => {
      // table.ajax.reload(() => table.page(currentPage).draw(false));
    }).fail(console.error);
  });

  $(document).on("blur", ".change-price", function () {
    const id = $(this).data("id");
    const price = $(this).val();

    $.post(`${host}ppe/admin/products/updatePrice`, { PROD_ID: id, PROD_PRICE: price }, () => {}).fail(console.error);
  });

  $(document).on("change", ".status-checkbox", function () {
    const prodId = $(this).data("id");
    const isChecked = this.checked ? 1 : 0;
    handleDelete(prodId, isChecked);
  });
});

function fetchCategories() {
  $.getJSON(`${host}ppe/admin/products/getType`, (response) => {
    $("#btn-group").append(`<button class="btn btn-sm btn-info" data-catid="" id="filterCategory">ทั้งหมด</button>`);
    $("#category").append(`<option value="">เลือกหมวดหมู่</option>`);
    response.forEach(({ CATID, CATNAME }) => {
      $("#category").append(`<option value="${CATID}">${CATNAME}</option>`);
      $("#btn-group").append(`<button class="btn btn-sm" data-catid="${CATNAME}" id="filterCategory">${CATNAME}</button>`);
    });
  });
}

async function handleAddUniform(e) {
  e.preventDefault();
  const category = $("#category").val();
  const size = $("#size").val();
  const table = $("#table").DataTable();
  const currentPage = table.page.info().page;

  console.log("submit_adduniform");
  // try {
  const chk_product = await getProduct(category, size);
  console.log(chk_product);
  if (chk_product.length > 0) {
    console.log("chk_product.length > 0");
    showTemporaryAlert();
    return;
  }
  $.post(`${host}ppe/admin/products/insertProduct`, $(this).serialize(), () => {
    $("#close_btn").trigger("click");
    table.ajax.reload(() => table.page(currentPage).draw(false));
  });

  console.log("submit_adduniform");
  // } catch (err) {
  //   showError(err);
  // }
}

function handleFilterCategory() {
  const id = $(this).data("catid");
  table.column(1).search(id).draw();
  $(this).addClass("btn-info").siblings().removeClass("btn-info");
}

async function handleEdit() {
  const id = $(this).data("id");
  try {
    const res = await $.post(`${host}ppe/admin/products/getProducts`, { PROD_ID: id });
    const product = res[0];
    $("#category_edit").val(product.CATNAME);
    $("#model_edit").val(product.CATDESC);
    $("#size_edit").val(product.PROD_SIZES);
    $("#quantity_edit").val(product.PROD_REMAIN);
    $("#btn-save-edit").data("id", product.PROD_ID);
    $("#edit_qty_modal")[0].showModal();
  } catch (err) {
    console.error(err);
  }
}

function handleSaveEdit(e) {
  e.preventDefault();
  const id = $(this).data("id");
  const qty = $("#quantity_edit").val();
  const table = $("#table").DataTable();
  const currentPage = table.page.info().page;

  $.post(`${host}ppe/admin/products/updateProduct`, { PROD_ID: id, PROD_REMAIN: qty }, () => {
    $("#edit_qty_modal")[0].close();
    table.ajax.reload(() => table.page(currentPage).draw(false));
  }).fail(console.error);
}

function handleDelete(prodId, isChecked) {
  const table = $("#table").DataTable();
  const currentPage = table.page.info().page;

  $.post(`${host}ppe/admin/products/updateStatus`, { id: prodId, status: isChecked }, () => {
    showMessage("Status updated successfully!", "success");
    table.ajax.reload(() => table.page(currentPage).draw(false));
  }).fail(console.error);
}

function handleRemove() {
  // เพิ่มฟังก์ชันนี้
  const id = $(this).data("id");
  $.ajax({
    url: `${host}ppe/admin/products/getProducts`,
    method: "POST",
    data: { PROD_ID: id },
    dataType: "json",
    beforeSend: () => {
      $("#loading-screen").removeClass("hidden");
    },
    success: (response) => {
      const product = response[0];
      $("#loading-screen").addClass("hidden");
      $("#stock_modal_out")[0].showModal();
      $("#category_remove_stock").val(product.CATNAME);
      $("#model_remove_stock").val(product.CATDESC);
      $("#size_remove_stock").val(product.PROD_SIZES);
      $("#id_remove_stock").val(product.PROD_ID);
      $("#remain_remove_stock").val(product.PROD_REMAIN);
    },
    error: (err) => {
      console.error("Error:", err);
      showMessage("เกิดข้อผิดพลาด", "error");
    },
  });
}

function handleSaveRemove(e) {
  // เพิ่มฟังก์ชันนี้
  e.preventDefault();
  const id = $("#id_remove_stock").val();
  const qty = $("#quantity_remove_stock").val();
  const remain = $("#remain_remove_stock").val();
  const remark = $("#remark_remove_stock").val();
  const table = $("#table").DataTable();
  const currentPage = table.page.info().page;

  $.post(`${host}ppe/admin/products/updateTransaction`, { PROD_ID: id, PROD_REMAIN: remain, REMARK: remark, QTY: qty, TYPE: "4" }, () => {
    $("#stock_modal_out")[0].close();
    table.ajax.reload(() => table.page(currentPage).draw(false));
  }).fail(console.error);
}

function handleReceive() {
  const id = $(this).data("id");
  $.ajax({
    url: `${host}ppe/admin/products/getProducts`,
    method: "POST",
    data: { PROD_ID: id },
    dataType: "json",
    beforeSend: () => {
      $("#loading-screen").removeClass("hidden");
    },
    success: (response) => {
      const product = response[0];
      $("#loading-screen").addClass("hidden");
      $("#stock_modal")[0].showModal();
      $("#category_add_stock").val(product.CATNAME);
      $("#model_add_stock").val(product.CATDESC);
      $("#size_add_stock").val(product.PROD_SIZES);
      $("#id_add_stock").val(product.PROD_ID);
      $("#remain_add_stock").val(product.PROD_REMAIN);
    },
    error: (err) => {
      console.error("Error:", err);
      showMessage("เกิดข้อผิดพลาด", "error");
    },
  });
}

function getProduct(category, size) {
  return $.ajax({
    url: `${host}ppe/admin/products/getProducts`,
    method: "POST",
    data: { category, size },
    dataType: "json",
  });
}

function createTable() {
  const opt = { ...tableOption, ajax: { url: `${host}ppe/admin/products/getProducts`, dataSrc: "" }, order: [[0, "asc"]], columns: getTableColumns(), initComplete: setupTableOptions };
  return new DataTable("#table", opt);
}

function getTableColumns() {
  return [
    { data: null, title: "รายการ", render: (data, type, row, meta) => meta.row + 1, className: "text-center" },
    { data: "CATNAME", title: "หมวดหมู่", className: "text-center" },
    { data: "CATDESC", title: "รุ่น", className: "text-center" },
    {
      data: null,
      title: "ขนาด",
      render: (data) => `<input value="${data.PROD_SIZES}" class="input change-size" data-id="${data.PROD_ID}" style="width: 100%; text-align: center;" />`,
    },
    {
      data: null,
      title: "ราคา",
      className: "text-center",
      render: (data) => `<input value="${data.PROD_PRICE}" class="input change-price" data-id="${data.PROD_ID}" style="width: 100%; text-align: center;" />`,
    },
    { data: "PROD_REMAIN", title: "จำนวนคงเหลือ", sortable: false, render: formatRemainQuantity },
    { data: "PROD_ALOC", title: "จำนวนขอเบิก", sortable: false },
    {
      data: "PROD_STATUS",
      title: "Status",
      className: "text-center",
      render: (data, type, row) => {
        const checked = data == 1 ? "checked" : "";
        return `
          <label class="inline-flex items-center me-5 cursor-pointer">
            <input type="checkbox" value="" class="sr-only peer status-checkbox" data-id="${row.PROD_ID}" ${checked}>
            <div class="relative w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-focus:ring-4 peer-focus:ring-green-300 dark:peer-focus:ring-green-800 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600 dark:peer-checked:bg-green-600"></div>
          </label>
        `;
      },
    },
    { data: null, title: "Action", sortable: false, width: "15%", render: renderActions },
  ];
}

function formatRemainQuantity(data) {
  const badgeClass = data == "0" ? "badge-error" : data <= 5 ? "badge-warning" : "badge-success";
  return `<span class="badge ${badgeClass}" style="width: 40px; text-align: center;">${data}</span>`;
}

function renderActions(data) {
  return `
    <button class="btn btn-revcieve btn-success" data-id="${data.PROD_ID}">รับเข้า</button>
    <button class="btn btn-delete btn-error" data-id="${data.PROD_ID}">จ่ายออก</button>
  `;
}

function setupTableOptions() {
  $(".table-option").append('<button class="drawer-button btn btn-sm btn-primary" id="btn_add">เพิ่มรายการ</button>');
  $("#seq").val($("#table").DataTable().rows().count() + 1);
  $(".dt-length").addClass("hidden");
}

function showTemporaryAlert() {
  $(".alert")
    .removeClass("hidden")
    .fadeIn(300)
    .delay(3000)
    .fadeOut(() => $(".alert").addClass("hidden"));
}

function showError(err) {
  console.error("Error:", err);
  showMessage("เกิดข้อผิดพลาด", "error");
}
