require("dotenv").config();
const path = require("path");
const webpack = require("webpack");
const Dotenv = require("dotenv-webpack");
const CompressionPlugin = require("compression-webpack-plugin");
const TerserPlugin = require("terser-webpack-plugin");
const CopyWebpackPlugin = require("copy-webpack-plugin");
module.exports = {
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
        form: "./assets/script/form.js",
    },
    output: {
        filename: "[name].bundle.js",
        path: path.resolve(__dirname, "assets/dist/js"),
    },
    mode: process.env.STATE,
    //   devtool: "inline-source-map",
    resolve: {
        //prettier-ignore
        alias: {
            "@public": path.resolve(__dirname,"../form/assets/script/public/v1.0.3"),
            jquery$: path.resolve(__dirname, "node_modules/jquery/src/jquery"),
            "@indexDB": path.resolve(__dirname, "../form/assets/script/indexDB"),
            "@styles": path.resolve(__dirname, "../form/assets/dist/css"),
            "@api": path.resolve(__dirname, "../form/assets/script/api"),
            '@root': path.resolve(__dirname, '../form/assets/script'),
        },
    },
    module: {
        rules: [
            {
                test: /\.css$/,
                use: ["style-loader", "css-loader"],
            },
        ],
    },
    optimization: {
        concatenateModules: true,
        minimize: true,
        // minimizer: [new TerserPlugin()],
        minimizer: [
            new TerserPlugin({
                parallel: true, // ✅ เปิด multi-core minify
                terserOptions: {
                    format: {
                        comments: false, // ลบคอมเมนต์ทิ้ง
                    },
                },
                extractComments: false, // ไม่แยก LICENSE ออกมาเป็นไฟล์ .txt
                exclude: /public/, // <<< อย่ามาย่อไฟล์ที่ copy มา
            }),
        ],
        // sideEffects: true,
    },
    plugins: [
        new Dotenv({
            path: path.resolve(__dirname, "./.env"),
        }),
        new CompressionPlugin({
            algorithm: "gzip", // หรือใช้ "brotliCompress" ก็ได้
            test: /\.(js|css|html|svg)$/,
            threshold: 10240,
            minRatio: 0.8,
            exclude: /public/, // <<< อย่ามาบีบอัดไฟล์ที่ copy มา
        }),
        new webpack.ProvidePlugin({
            $: "jquery",
            jQuery: "jquery",
        }),
        new CopyWebpackPlugin({
            patterns: [
                {
                    from: path.resolve(
                        __dirname,
                        "../form/assets/script/public/v1.0.3"
                    ),
                    to: path.resolve(__dirname, "assets/script/public"),
                    noErrorOnMissing: true,
                    globOptions: {
                        // เอาเฉพาะ js ก็พอ
                        ignore: ["**/*.map", "**/*.json"], // จะไม่ก็อปไฟล์ขยะ
                    },
                },
            ],
        }),
    ],
    cache:
        process.env.STATE === "production"
            ? false
            : {
                  type: "filesystem",
                  //   cacheDirectory: path.resolve(__dirname, '.cache/webpack'),
                  buildDependencies: {
                      config: [__filename],
                  },
              },
};
