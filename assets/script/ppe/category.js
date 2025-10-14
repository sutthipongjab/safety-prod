import DataTable from "datatables.net-dt";
import $ from "jquery";
import { host, tableOption, showMessage } from "../utils.js";
import { Fancybox } from "@fancyapps/ui";
import "@fancyapps/ui/dist/fancybox/fancybox.css";

$(document).ready(function () {
  const table = setupDataTable();
  setupAddCategoryForm(table);
  setupEditCategoryHandler(table);
  setupImageClickHandler();
});

function setupDataTable() {
  const dataTable = new DataTable("#table", {
    ...tableOption,
    ajax: {
      url: `${host}ppe/admin/category/get_categories`,
      dataSrc: (res) => res,
    },
    columns: [
      {
        data: null,
        title: "No.",
        render: (data, type, row, meta) => meta.row + 1,
      },
      {
        data: "CATIMAGE",
        title: "Picture",
        render: (imageUrl) => {
          if (!imageUrl) {
            return `<div class="w-16 rounded-xl"><img src="${host}assets/images/no-image.png"/></div>`;
          }
          return `<img src="${imageUrl}" alt="Picture" class="img" d-base64="${imageUrl}" style="width:50px;height:50px;"/>`;
        },
      },
      { data: "CATNAME", title: "Name" },
      { data: "CATDESC", title: "Description" },
      { data: "CATUNIT", title: "Unit" },
      { data: "CATMETER", title: "Meter" },
      {
        data: "CATANNUAL",
        title: "Status",
        render: (data, type, row) => {
          const checked = data == 1 ? "checked" : "";
          return `
            <label class="inline-flex items-center me-5 cursor-pointer">
              <input type="checkbox" value="" class="sr-only peer status-checkbox" data-id="${row.CATID}" ${checked}>
              <div class="relative w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-focus:ring-4 peer-focus:ring-green-300 dark:peer-focus:ring-green-800 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600 dark:peer-checked:bg-green-600"></div>
            </label>
          `;
        },
      },
      {
        data: "CATID",
        title: "Action",
        sortable: false,
        width: "15%",
        render: (catId) => `<button class="btn btn-edit" data-id="${catId}">Edit</button>`,
      },
    ],
    initComplete: function () {
      $(".table-option").append(`<button class="btn drawer-button btn-sm btn-primary modal-button" onclick="my_modal.showModal()">เพิ่มรายการ</button>`);
      $("#catseq").val(this.api().rows().count() + 1);
      $(".dt-length").addClass("hidden");
    },
  });

  $(document).on("change", ".status-checkbox", function () {
    const catId = $(this).data("id");
    const isChecked = this.checked ? 1 : 0;
    updateCategoryStatus(catId, isChecked, dataTable);
  });
  return dataTable;
}

function updateCategoryStatus(catId, status, table) {
  $.ajax({
    url: `${host}ppe/admin/category/update_category_status`, // ต้องมี endpoint สำหรับอัปเดตสถานะ
    type: "POST",
    data: { id: catId, status: status },
    success: () => {
      showMessage("Status updated successfully!", "success");
      table.ajax.reload();
    },
    error: (error) => {
      console.error("Error:", error);
      alert("Failed to update status.");
    },
  });
}

function setupAddCategoryForm(table) {
  $("#add_category_form").on("submit", function (event) {
    event.preventDefault();
    const formData = new FormData(this);
    $.ajax({
      url: $(this).attr("action"),
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: () => {
        showMessage("Insert Data Successfully!!", "success");
        $("#my-modal").prop("checked", false);
        table.ajax.reload();
        $("form")[0].reset();
        $("#close_btn").trigger("click");
      },
      error: (error) => {
        console.error("Error:", error);
        alert("An error occurred");
      },
    });
  });
}

function setupEditCategoryHandler(table) {
  $(document).on("click", ".btn-edit", function () {
    const id = $(this).data("id");
    $.ajax({
      url: host + "ppe/admin/category/get_categoly_id",
      type: "POST",
      data: { id: id },
      dataType: "json",
      success: (data) => {
        $("#input_name_edit").val(data.CATNAME);
        $("#input_unit_edit").val(data.CATUNIT);
        $("#input_description_edit").val(data.CATDESC);
        $("#input_id_edit").val(data.CATID);
        $("#remark").val(data.CATREMARK);
        if (data.CATIMAGE) {
          $("#div_preview").removeClass("hidden");
          $("#preview_image").attr("src", data.CATIMAGE);
        } else {
          $("#div_preview").addClass("hidden");
        }
        $("#edit-modal").prop("checked", true);
      },
      error: (error) => {
        console.error("Error:", error);
        alert("An error occurred while fetching data");
      },
    });
  });

  $("#edit_category_form").on("submit", function (event) {
    event.preventDefault();
    const formData = new FormData(this);
    $.ajax({
      url: $(this).attr("action"),
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: () => {
        showMessage("Update Data Successfully!!", "success");
        table.ajax.reload();
        $("#edit-modal").prop("checked", false);
      },
      error: (error) => {
        console.error("Error:", error);
        alert("An error occurred while updating the category");
      },
    });
  });
}

function setupImageClickHandler() {
  $(document).on("click", ".img", function () {
    const base64 = $(this).attr("d-base64");
    const img = [{ src: `<img src="${base64}" alt="" style="width:100%;">`, type: "html" }];
    new Fancybox(img);
  });
}
