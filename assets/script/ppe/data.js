import $ from "jquery";
import { host, showMessage } from "../utils.js";

export const getProducts = () => {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: `${host}ppe/admin/products/getProducts`,
      type: "post",
      dataType:'json',
      success: function (data) {
        resolve(data);
      },
      error: function (error) {
        reject(error);
      },
    });
  });
};
