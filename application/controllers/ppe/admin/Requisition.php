<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'controllers/_form.php';
require_once APPPATH . "/third_party/PHPExcel.php";

function pre_array($data)
{
    echo "<pre>" . print_r($data, true) . "</pre>";
}
class Requisition extends MY_Controller
{

    use _Form;
    private $data;
    public function __construct()
    {
        parent::__construct();
        $this->session_expire();
        $this->load->model('ppe_model', 'ppe');
        $this->load->model('webform_model', 'wf');
        $this->data['title'] = 'PPE-Request';
    }


    public function index()
    {
        $fmst  = $this->wf->getFormMaster('ST-PPE');
        $no    = $fmst[0]->NNO;
        $orgNo = $fmst[0]->VORGNO;
        $y     = $fmst[0]->CYEAR;

        // $this->data['Fmno'] = $this->toFormNumber($no, $orgNo, $y, $y2, $runNo);
        $this->views('ppe/requisition/admin_request', $this->data);
    }

    public function history()
    {
        $this->views('ppe/requisition/admin_history', ['title' => 'PPE-History']);
    }

    public function get_data_request()
    {
        $fmst   = $this->wf->getFormMaster('ST-PPE');
        $nfrmno = $fmst[0]->NNO;
        $vorgno = $fmst[0]->VORGNO;
        $cyear  = $fmst[0]->CYEAR;
        $data   = $this->ppe->get_all_request($nfrmno, $vorgno, $cyear);

        foreach ($data as $key => $value) {
            $value->FORMNUMBER = $this->toFormNumber($nfrmno, $vorgno, $cyear, $value->CYEAR2, $value->NRUNNO);
        }
        echo json_encode($data);
        // echo "<pre>" . print_r($data, true) . "</pre>";

    }

    public function get_data_history()
    {
        $fmst   = $this->wf->getFormMaster('ST-PPE');
        $nfrmno = $fmst[0]->NNO;
        $vorgno = $fmst[0]->VORGNO;
        $cyear  = $fmst[0]->CYEAR;
        $date_s = $this->input->post('date_s');
        $date_e = $this->input->post('date_e');
        if ($date_s != '' && $date_e != '') {
            $q = array(
                'TO_CHAR(TRS_DATE, \'YYYY-MM-DD\') >= \'' . $date_s . '\'' => null,
                'TO_CHAR(TRS_DATE, \'YYYY-MM-DD\') <= \'' . $date_e . '\'' => null,
            );
        } else {
            $q = '';
        }
        $data = $this->ppe->get_history($q);

        // foreach ($data as $key => $value) {
        //     $value->FORMNUMBER = $this->toFormNumber($nfrmno, $vorgno, $cyear, $value->CYEAR2, $value->NRUNNO);
        // }
        echo json_encode($data);
    }

    public function test_excel()
    {
        $data = $this->ppe->get_history();

        $summary = [];
        foreach ($data as $item) {
            $type    = $item->TRS_TYPE;
            $product = $item->TRS_PRODUCT;
            $qty     = $item->TRS_QTY;

            if (!isset($summary[$type])) {
                $summary[$type] = [];
            }

            if (!isset($summary[$type][$product])) {
                $summary[$type][$product] = 0;
            }

            $summary[$type][$product] += $qty;
        }
        pre_array($summary);
        // pre_array($data);
    }

    // public function ppe_list()
    // {
    //     // $date       = $this->input->post('date');
    //     // $date       = date("Y-m");
    //     $date       = "2025-03";
    //     $categories = $this->ppe->get_category();
    //     $result     = [];
    //     $q          = array(
    //         'TO_CHAR(TRS_DATE, \'YYYY-MM\') = \'' . $date . '\'' => null,
    //     );
    //     $history    = $this->ppe->get_history($q);
    //     $summary    = [];
    //     $lastDates  = [];

    //     // print_r($history);
    //     echo "<pre>" . print_r($history, TRUE) . "</pre>";
    //     foreach ($history as $item) {
    //         $type    = $item->TRS_TYPE;
    //         $product = $item->TRS_PRODUCT;
    //         $qty     = $item->TRS_QTY;
    //         $date    = $item->TRS_DATE;

    //         if (!isset($summary[$type])) {
    //             $summary[$type] = [];
    //         }

    //         if (!isset($summary[$type][$product])) {
    //             $summary[$type][$product] = 0;
    //         }

    //         $summary[$type][$product] += $qty;
    //     }

    //     foreach ($categories as $category) {
    //         $ppeList = $this->ppe->get_ppe_list_by_id($category->CATID);

    //         foreach ($ppeList as $ppeItem) {
    //             $prodId = $ppeItem->PROD_ID;

    //             $ppeItem->RECEIVE = isset($summary['1'][$prodId]) ? $summary['1'][$prodId] : 0;
    //             $ppeItem->ISSUE   = isset($summary['4'][$prodId]) ? $summary['4'][$prodId] : 0;

    //             $lastRequest          = $this->ppe->get_last_request_date($prodId);
    //             $ppeItem->Lastrequest = $lastRequest ? date("Y-m-d", strtotime($lastRequest)) : null;
    //         }

    //         if (!empty($ppeList)) {
    //             $result[] = $ppeList;
    //         }
    //     }

    //     echo "<pre>" . print_r($result, TRUE) . "</pre>";
    //     // echo json_encode($result);
    // }

    public function ppe_list()
    {
        // ตัวอย่างกำหนดเดือน (เปลี่ยนตามต้องการ)
        $date = $this->input->post('date');

        $categories = $this->ppe->get_category();
        $result     = [];

        // ดึงข้อมูลการเคลื่อนไหวภายในเดือน
        $q       = array(
            'TO_CHAR(TRS_DATE, \'YYYY-MM\') = \'' . $date . '\'' => null,
        );
        $history = $this->ppe->get_history($q);

        // เตรียมข้อมูลสรุปและวันที่ล่าสุด
        $summary   = [];
        $lastDates = [];

        foreach ($history as $item) {
            $type    = $item->TRS_TYPE;
            $product = $item->TRS_PRODUCT;
            $qty     = $item->TRS_QTY;
            $trsDate = $item->TRS_DATE;

            // สรุปยอด
            if (!isset($summary[$type])) {
                $summary[$type] = [];
            }

            if (!isset($summary[$type][$product])) {
                $summary[$type][$product] = 0;
            }

            $summary[$type][$product] += $qty;

            // หาวันที่ล่าสุดตามประเภท
            if (in_array($type, ['1', '4'])) {
                if (!isset($lastDates[$product][$type]) || strtotime($trsDate) > strtotime($lastDates[$product][$type])) {
                    $lastDates[$product][$type] = $trsDate;
                }
            }
        }

        // วนแต่ละหมวด
        foreach ($categories as $category) {
            $ppeList = $this->ppe->get_ppe_list_by_id($category->CATID);

            foreach ($ppeList as $ppeItem) {
                $prodId = $ppeItem->PROD_ID;

                $ppeItem->RECEIVE = isset($summary['1'][$prodId]) ? $summary['1'][$prodId] : 0;
                $ppeItem->ISSUE   = isset($summary['4'][$prodId]) ? $summary['4'][$prodId] : 0;

                $ppeItem->Lastpurchase = isset($lastDates[$prodId]['1']) ? date("Y-m-d", strtotime($lastDates[$prodId]['1'])) : null;
                $ppeItem->Lastrequest  = isset($lastDates[$prodId]['4']) ? date("Y-m-d", strtotime($lastDates[$prodId]['4'])) : null;
            }

            if (!empty($ppeList)) {
                $result[] = $ppeList;
            }
        }

        // echo "<pre>" . print_r($result, TRUE) . "</pre>";
        echo json_encode($result); // ถ้าอยากคืนเป็น JSON
    }


    public function excel()
    {
        $objPHPExcel = PHPExcel_IOFactory::load('ppe_template.xlsx');
        $sheet       = $objPHPExcel->getActiveSheet();

        $data = $this->ppe->get_ppe_list();

        $row = 10;
        $i   = 1;

        $catNames = []; // เก็บ CATNAME เพื่อใช้ในการ merge

        foreach ($data as $item) {
            $sheet->insertNewRowBefore($row, 1);
            $sheet->setCellValue('B' . $row, $i);
            $sheet->setCellValue('C' . $row, $item->CATDESC);
            $sheet->setCellValue('D' . $row, $item->CATNAME);

            $catNames[$i] = $item->CATNAME; // เก็บ CATNAME พร้อม index

            $row++;
            $i++;
        }

        // ทำการ merge เซลล์ในคอลัมน์ D
        $startRow       = 10; // แถวเริ่มต้น
        $currentCatName = null;
        $mergeStartRow  = null;
        $currentRow     = $startRow;

        for ($j = 1; $j <= count($catNames); $j++) {
            $catName = $catNames[$j];

            if ($currentCatName === null) {
                $currentCatName = $catName;
                $mergeStartRow  = $currentRow;
            } elseif ($currentCatName !== $catName) {
                if ($currentRow - $mergeStartRow > 1) {
                    $sheet->mergeCells('D' . $mergeStartRow . ':D' . ($currentRow - 1));
                }
                $currentCatName = $catName;
                $mergeStartRow  = $currentRow;
            }
            $currentRow++;
        }

        // ทำการ merge เซลล์สำหรับกลุ่มสุดท้าย
        if ($mergeStartRow !== null && $currentRow - $mergeStartRow > 1) {
            $sheet->mergeCells('D' . $mergeStartRow . ':D' . ($currentRow - 1));
        }

        // กำหนดสไตล์การเอียงข้อความในคอลัมน์ D
        $styleArray = array(
            'alignment' => array(
                'rotation' => 90, // เอียง 90 องศา (หันไปทางขวา)
            ),
        );

        // ใช้ applyFromArray เพื่อกำหนดสไตล์
        $lastRow = $row - 1; // แถวสุดท้าย
        $sheet->getStyle('D' . $startRow . ':D' . $lastRow)->applyFromArray($styleArray);

        // กำหนดความสูงของแถว
        for ($r = $startRow; $r < $row; $r++) {
            $sheet->getRowDimension($r)->setRowHeight(13.5);
        }

        // กำหนดความกว้างของคอลัมน์อัตโนมัติ
        foreach (range('B', 'D') as $col) { // แก้ไขเป็น B ถึง D
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        // กำหนดความกว้างคอลัมน์ D เท่ากับ 4
        $sheet->getColumnDimension('D')->setWidth(4);

        // กำหนดพื้นหลังสีขาวให้กับคอลัมน์ B ถึง D
        $styleArrayWhite = array(
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'FFFFFF') // FFFFFF คือสีขาว
            ),
        );
        $sheet->getStyle('B' . $startRow . ':E' . $lastRow)->applyFromArray($styleArrayWhite);

        $filename = 'PPE_History_' . date('Ymd') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }
}