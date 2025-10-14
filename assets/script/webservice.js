import $ from "jquery";
import { host, uri, showMessage } from "./utils";

export function getDivision(q = {}) {
  // get local storage
  const division = JSON.parse(localStorage.getItem("amecdivision"));
  if (division) {
    return new Promise((resolve) => {
      resolve(division);
    });
  }

  return new Promise((resolve) => {
    $.ajax({
      type: "post",
      dataType: "json",
      url: `${uri}/webservice/webflow/organization/division/`,
      data: q,
      success: function (data) {
        // set local storage
        localStorage.setItem("amecdivision", JSON.stringify(data));
        resolve(data);
      },
    });
  });
}

export function getDepartment(q = {}) {
  // get local storage
  const department = JSON.parse(localStorage.getItem("amecdepartment"));
  if (department) {
    return new Promise((resolve) => {
      resolve(department);
    });
  }
  return new Promise((resolve) => {
    $.ajax({
      type: "post",
      dataType: "json",
      url: `${uri}/webservice/webflow/organization/department/`,
      data: q,
      success: function (data) {
        console.log(data);
        
        localStorage.setItem("amecdepartment", JSON.stringify(data));
        resolve(data);
      },
    });
  });
}

export function getSection(q = {}) {
  const section = JSON.parse(localStorage.getItem("amecsection"));
  if (section) {
    return new Promise((resolve) => {
      resolve(section);
    });
  }
  return new Promise((resolve) => {
    $.ajax({
      type: "post",
      dataType: "json",
      url: `${uri}/webservice/webflow/organization/section/`,
      data: q,
      success: function (data) {
        const d = data.sort((a, b) => a.SSEC.localeCompare(b.SSEC));
        localStorage.setItem("amecsection", JSON.stringify(data));
        resolve(d);
      },
    });
  });
}

export function getEmployee(q = {}) {
    return new Promise((resolve) => {
      $.ajax({
        type: "post",
        dataType: "json",
        url: `${uri}/webservice/webflow/amecusers/users/`,
        data: q,
        success: function (data) {
          resolve(data);
        },
      });
    });
  }

export async function getUser(id) {
  const findUser = (id) => {
    return new Promise((resolve) => {
      $.ajax({
        type: "post",
        dataType: "json",
        url: `${uri}/webservice/webflow/amecusers/users/`,
        data: { id, mode: 1 },
        success: async function (data) {
          if (data.status === false) {
            return;
          }

          const localUsers = JSON.parse(localStorage.getItem("amecuser")) || {};
          const users = localUsers.value || [];
          users.push(data[0]);
          localStorage.setItem("amecuser", JSON.stringify({ value: users }));
          resolve(data[0]);
        },
      });
    });
  };

  const localUsers = JSON.parse(localStorage.getItem("amecuser")) || {};
  const users = localUsers.value || [];
  let user = users.find((el) => el.SEMPNO === id);
  if (user == undefined) {
    user = await findUser(id);
  }
  return new Promise((resolve) => resolve(user));
}

export async function extractUser(data, key) {
    return new Promise((resolve) => {
      data.map(async (item) => {
        await getUser(item[key]);
      });
      const localUsers = JSON.parse(localStorage.getItem("amecuser")) || {};
      const users = localUsers.value || [];
      const uniqueUsers = users.filter(
        (user, index, self) =>
          index === self.findIndex((u) => u.SEMPNO === user.SEMPNO)
      );
      localStorage.setItem("amecuser", JSON.stringify({ value: uniqueUsers }));
      resolve(uniqueUsers);
    });
  }
