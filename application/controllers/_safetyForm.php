<?php
defined('BASEPATH') or exit('No direct script access allowed');

trait _safetyForm{

    public function getForm(){
        $post = $this->input->post();
        $res  = [];
        if(!isset($post['areaNo'])){
            $form = $this->frm->getFormMaster(['CATEGORY' => $post['areaCategory'], 'TYPE' => $post['areaType']]);
            if(!empty($form)){
                $form = $form[0];
                $res  = $this->setForm($post['areaCategory'], $form->FORMNO);
            }
        }else{
            $res  = $this->setForm($post['areaCategory'], $post['areaNo']);
        }
        echo json_encode($res);
    }

    
    /**
     * Set form 
     * @param string $category e.g. 25
     * @param string $runno    e.g. 1
     * @return array
     */
    private function setForm($category, $runno){
        $topic  = $this->frm->getTopic(['FRMT_CATEGORY' => $category, 'FRMT_RUNNO' => $runno]);
        $detail = $this->frm->getFormDetail(['CATEGORY' => $category, 'RUNNO' => $runno]);
        $image  = $this->frm->getFormImage(['FRMI_CATEGORY' => $category, 'FRMI_RUNNO' => $runno]);
        $res = [];
        if(!empty($topic)){
            foreach($topic as $key => $t){
                $res[$key] = $t; 
                $res[$key]->image = [];
                $res[$key]->detail = [];
                if(!empty($detail)){
                    foreach ($detail as $d) {
                        if($d->TOPIC_NO == $t->FRMT_NO){
                            $res[$key]->detail[] = $d;
                        }
                    }
                }
                if(!empty($image)){
                    $index = 0;
                    foreach ($image as $k => $i) {
                        if($i->FRMI_NO == $t->FRMT_NO){
                            $res[$key]->image[$index] = $i;
                            $res[$key]->image[$index]->base64 = $this->conVBase64($i->FRMI_PATH.$i->FRMI_FNAME);
                            $index++;
                        }
                    }
                }
            }
        }
        return $res;
    }
}