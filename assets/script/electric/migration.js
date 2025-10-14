import $            from "jquery";
import select2      from "select2";
import { Fancybox } from "@fancyapps/ui";
import "select2/dist/css/select2.min.css";
import "@fancyapps/ui/dist/fancybox/fancybox.css";
import { colToNumber, readInOpt, readInput } from "../_excel";
import { ajaxOptions, getData, host, showMessage } from "../utils";



$(async function () {
    
});


$(document).on('change', '#uploadF', async function(){
    try {
        const file = this.files[0];
            // console.log(file);
        const sheetName = ['OFFICE', 'FACTORY'];
        for (const sheet of sheetName) {
            const data = await readInput(file,{
                ...readInOpt,
                sheetName: sheet
            });
            console.log(data);
            const res = await getData({
                ...ajaxOptions,
                url: `${host}electric/migration/save`,
                data: {
                    data: JSON.stringify(data),
                    sheet: sheet
                },
            });
            switch (res.status) {
                case 0:
                    showMessage(res.message);
                    break;
                case 1:
                    showMessage(res.message, 'success');
                    break;
            
                default:
                    showMessage(res.message, 'warning');
                    break;
            }
            console.log(res);   
        }
    } catch (error) {
        showMessage(`เกิดข้อผิดพลาดในการอ่านข้อมูล ${error}`, 'error');
    }
});

$(document).on('change', '#uploadA', async function(){
    try {
        const file = this.files[0];
        // console.log(file);
        const data = await readInput(file,
            {
                ...readInOpt,
                skipRow: 1,
                sheetName: 'AREA',
            }
        );
        console.log(data);
        // return
        const res = await getData({
            ...ajaxOptions,
            url: `${host}electric/migration/save`,
            data: {
                data: JSON.stringify(data),
                sheet: 'AREA'
            },
        });
        switch (res.status) {
            case 0:
                showMessage(res.message);
                break;
            case 1:
                showMessage(res.message, 'success');
                break;
        
            default:
                showMessage(res.message, 'warning');
                break;
        }
        console.log(res);
    } catch (error) {
        showMessage(`เกิดข้อผิดพลาดในการอ่านข้อมูล ${error}`, 'error');
    }
});



