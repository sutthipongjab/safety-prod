
import { jsPDF } from "jspdf";
import autoTable from "jspdf-autotable";

// ผูก method autoTable กลับเข้า jsPDF ให้เหมือนยุคเก่า
jsPDF.API.autoTable = function (options) {
  autoTable(this, options);
};

export { jsPDF, autoTable };