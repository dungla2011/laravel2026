<?php

namespace App\Components;

class Recusive
{
    private $data;

    private $htmlSelect = '';

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function categoryRecusive($pid = null, $id = 0, $text = '')
    {
        //        if(!$this->data)
        //            return;
        foreach ($this->data as $value) {
            if ($value['parent_id'] == $id) {
                //                  echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
                //            print_r($value);
                //            echo "</pre>";
                $padSelect = null;
                if ($pid && $pid == $value['id']) {
                    $padSelect = 'selected';
                }

                $this->htmlSelect .= "<option $padSelect value='".$value['id']."'>".$text.$value['name'].'</option>';
                $this->categoryRecusive($pid, $value['id'], $text.'--');
            }
        }

        return $this->htmlSelect;
    }

    public function MenuRecusive($pid = null, $id = 0, $text = '')
    {
        //        if(!$this->data)
        //            return;
        foreach ($this->data as $value) {
            if ($value['parent_id'] == $id) {
                //                  echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
                //            print_r($value);
                //            echo "</pre>";
                $padSelect = null;
                if ($pid && $pid == $value['id']) {
                    $padSelect = 'selected';
                }

                $this->htmlSelect .= "<option $padSelect value='".$value['id']."'>".$text.$value['name'].'</option>';
                $this->MenuRecusive($pid, $value['id'], $text.'--');
            }
        }

        return $this->htmlSelect;
    }
}
