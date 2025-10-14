const path = require("path");
const webpack = require('webpack');
module.exports = {
    plugins: [
        new webpack.ProvidePlugin({
          $: 'jquery',
          jQuery: 'jquery'
        })
      ],
    entry: {
        app: "./assets/script/apps.js",
        area: "./assets/script/area.js",
        items: "./assets/script/items.js",
        type: "./assets/script/type.js",
        status: "./assets/script/status.js",
        // patrol
        inspection: "./assets/script/patrol/inspection.js",
        patrol: "./assets/script/patrol/patrol.js",
        patrolForm: "./assets/script/patrol/patrolForm.js",
        patrolFormWebflow: "./assets/script/patrol/patrolFormWebflow.js",
        patrolReport: "./assets/script/patrol/report.js",
        patrolExport: "./assets/script/patrol/export.js",
        //KYT Recode
        kyt: "./assets/script/kyt/index.js",
        kytview: "./assets/script/kyt/view.js",
        kytfollow: "./assets/script/kyt/follow.js",
        kytReport: "./assets/script/kyt/report.js",

        //PPE
        ppeproduct: "./assets/script/ppe/product.js",
        ppenewproduct: "./assets/script/ppe/newproduct.js",
        ppetype: "./assets/script/ppe/type.js",
        ppecategory: "./assets/script/ppe/category.js",
        pperequest: "./assets/script/ppe/request.js",
        ppewebform: "./assets/script/ppe/webform.js",
        ppeadmin: "./assets/script/ppe/admin.js",

        //Chemical
        importData: "./assets/script/chemical/importData.js",
        ChemicalList: "./assets/script/chemical/list.js",
        ChemicalSectionList: "./assets/script/chemical/sectionList.js",
        ChemicalRequest: "./assets/script/chemical/request.js",
        chemicalWebform: "./assets/script/chemical/webform.js",
        chemicalModify: "./assets/script/chemical/modify.js",
        chemicalFollow: "./assets/script/chemical/follow.js",

        //electrical
        electricArea: "./assets/script/electric/area.js",
        electricInspection: "./assets/script/electric/inspection.js",
        migrationElec: "./assets/script/electric/migration.js",
        electricWebForm: "./assets/script/electric/webform.js",
        electricFollow: "./assets/script/electric/follow.js",

        test: "./assets/script/test.js",

        // Form
        form : "./assets/script/form.js",

    },
    output: {
        filename: "[name].bundle.js",
        path: path.resolve(__dirname, "assets/dist/js"),
    },
    mode: "development", // ใช้โหมด development หรือ production
    //   devtool: "inline-source-map",
    module: {
        rules: [
        {
            test: /\.css$/,
            use: ["style-loader", "css-loader"],
        },
        ],
    },
    //   optimization: {
    //     splitChunks: {
    //         chunks: "all",
    //     },
    // },
};
