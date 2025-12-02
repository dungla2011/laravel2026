<?php

namespace Tests\Browser;

use App\Models\DemoFolderTbl;
use App\Models\DemoTbl;
use App\Models\Tag;
use App\Models\TagDemo;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Support\Facades\DB;
use LadLib\Common\Tester\clsTestBase2;
use Tests\Browser\DuskTestCaseBase;

/**
 *  Filter autocomplete top filter
 *  Edit, Add item
 */
class DemoMultiSetValueTest extends DuskTestCaseBase
{
    protected function hasHeadlessDisabled(): bool
    {
        return true;

        return parent::hasHeadlessDisabled();
    }

    /**
     * Test multi field autocomplete: đưa tag name vào , thay đổi, save, kiểm tra KQ
     */
    public function testClickSetFieldMultiValueTagList()
    {
        $field = 'tag_list_id';

        //Chắc chắn taglist có 2 phần tử
        $mm = TagDemo::where(DB::raw('LENGTH(name)'), '>', '1')->get();

        $mTag = [];
        foreach ($mm as $tag) {
            $mTag[$tag->id] = $tag->name;
        }

        self::assertTrue(count($mTag) > 1);

        $obj = DemoTbl::first();
        $this->testLoginTrueAcc();
        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();
        clsTestBase2::$driver = $drv;
        $objMeta = DemoTbl::getMetaObj();
        $browser->visit('/admin/demo-api');

        //        clsTestBase2::findOneByXPath("//input[contains(@class,'select_all_check')]", 0)->click();

        //        clsTestBase2::findOneByXPath("//input[contains(@class,'select_one_check')]", 1)->click();
        //        clsTestBase2::findOneByXPath("//input[contains(@class,'select_one_check')]", 4)->click();
        //        clsTestBase2::findOneByXPath("//input[contains(@class,'select_one_check')]", 3)->click();

        //Tìm field đầu tiên:
        //        $obj = clsTestBase2::findOneByXPath("//i[contains(@class,'icon_tool_for_field')][@data-field='$field']");
        //        $obj->click();
        //
        //        dump(" FIELD = $field");
        //
        //        clsTestBase2::clickOnFirstMenuContext("items");
        //        self::assertTrue(clsTestBase2::findOneById('common_dialog')->isDisplayed());
        //
        //        //Set Zero
        //        clsTestBase2::findOneById("btn_set_value_all_item_selecting")->click();
        //
        //        sss(1);
        //        clsTestBase2::clickAlertDialogOK();
        //        sss(1);
        //        clsTestBase2::findOneById("save-all-data")->click();
        //        $browser->refresh();
        //        sss(2);
        //
        //        $mm = $drv->findElements(WebDriverBy::xpath("input[@data-field='$field'][contains(@class, 'input_value_to_post')]"));
        //        foreach ($mm AS $obj){
        //            self::assertTrue(empty($obj->getAttribute('value')));
        //        }

        
        //Đợi cho đến khi xuất hiện "//input[contains(@class,'select_one_check')]
        $this->waitForXPath("//input[contains(@class,'select_one_check')]");

        
        $this->deleteAllValueOfAColumnField($field);

        self::assertTrue(clsTestBase2::findOneByXPath("//input[contains(@class,'select_one_check')]", 1) != null);


        clsTestBase2::findOneByXPath("//input[contains(@class,'select_one_check')]", 1)->click();
        clsTestBase2::findOneByXPath("//input[contains(@class,'select_one_check')]", 4)->click();
        clsTestBase2::findOneByXPath("//input[contains(@class,'select_one_check')]", 3)->click();

        /////
        //        $obj = clsTestBase2::findOneByXPath("//i[contains(@class,'icon_tool_for_field')]");
        $obj = clsTestBase2::findOneByXPath("//i[contains(@class,'icon_tool_for_field')][@data-field='tag_list_id']");

        // Scroll into view and wait for element to be clickable
        try {
            // Wait for element to be visible and enabled
            $this->waitForXPath("//i[contains(@class,'icon_tool_for_field')][@data-field='tag_list_id']");
            
            // $this->getDrv()->executeScript("arguments[0].scrollIntoView(true);", [$obj]);

            sleep(1);
            
            // Try to click with multiple attempts
            $maxAttempts = 3;
            $clicked = false;
            
            for ($i = 0; $i < $maxAttempts; $i++) {
                try {
                    $obj->click();
                    $clicked = true;
                    break;
                } catch (\Exception $e) {
                    if ($i < $maxAttempts - 1) {
                        echo "Click attempt " . ($i + 1) . " failed, retrying...\n";
                        sleep(1);
                        $obj = clsTestBase2::findOneByXPath("//i[contains(@class,'icon_tool_for_field')][@data-field='tag_list_id']");
                    }
                }
            }
            
            if (!$clicked) {
                throw new \Exception("Could not click on icon_tool_for_field element after $maxAttempts attempts");
            }
        } catch (\Exception $e) {
            // Log full stack trace
            echo "\n\n=== FULL ERROR TRACE ===\n";
            echo "Error: " . $e->getMessage() . "\n";
            echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
            echo "\nStack Trace:\n" . $e->getTraceAsString() . "\n";
            echo "=== END TRACE ===\n\n";
            throw $e;
        }
        $field = $obj->getAttribute('data-field');

        dump(" FIELD1 = $field");

        clsTestBase2::clickOnFirstMenuContext('items');
        self::assertTrue(clsTestBase2::findOneById('common_dialog')->isDisplayed());

        clsTestBase2::findOneById('search_autocomplete_this_value_to_all_item_field')->clear()->sendKeys(array_values($mTag)[0]);
        sss(2);
        clsTestBase2::clickFirstAutoCompleteDown();
        sss(1);

        clsTestBase2::findOneById('search_autocomplete_this_value_to_all_item_field')->clear()->sendKeys(array_values($mTag)[1]);
        sss(2);
        clsTestBase2::clickFirstAutoCompleteDown();
        sss(1);

        clsTestBase2::findOneById('btn_set_value_all_item_selecting')->click();
        sss(2);
        //        clsTestBase2::clickAlertDialogOK();
        //        sss(1);

        //Truoc khi save all, cac value phai ok
        $val1 = clsTestBase2::findOneByXPath("//input[contains(@class,'input_value_to_post')][@data-field='$field']", 0)->getAttribute('value');
        $val2 = clsTestBase2::findOneByXPath("//input[contains(@class,'input_value_to_post')][@data-field='$field']", 2)->getAttribute('value');
        $val3 = clsTestBase2::findOneByXPath("//input[contains(@class,'input_value_to_post')][@data-field='$field']", 3)->getAttribute('value');

        dump(" VAL = $val1, $val2, $val3");

        self::assertTrue($val1 == array_keys($mTag)[0].','.array_keys($mTag)[1]);
        self::assertTrue($val2 == array_keys($mTag)[0].','.array_keys($mTag)[1]);
        self::assertTrue($val3 == array_keys($mTag)[0].','.array_keys($mTag)[1]);

        clsTestBase2::findOneById('save-all-data')->click();
        sss(2);
        $browser->refresh();
        sss(1);

        //Sau khi refresh, check lại ok
        $val1 = clsTestBase2::findOneByXPath("//input[contains(@class,'input_value_to_post')][@data-field='$field']", 0)->getAttribute('value');
        $val2 = clsTestBase2::findOneByXPath("//input[contains(@class,'input_value_to_post')][@data-field='$field']", 2)->getAttribute('value');
        $val3 = clsTestBase2::findOneByXPath("//input[contains(@class,'input_value_to_post')][@data-field='$field']", 3)->getAttribute('value');

        dump(" VAL = $val1, $val2, $val3");

        self::assertTrue($val1 == array_keys($mTag)[0].','.array_keys($mTag)[1]);
        self::assertTrue($val2 == array_keys($mTag)[0].','.array_keys($mTag)[1]);
        self::assertTrue($val3 == array_keys($mTag)[0].','.array_keys($mTag)[1]);

    }

    public function testClickSetFieldValueText($field = 'status')
    {

        $obj = DemoTbl::first();
        $this->testLoginTrueAcc();
        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();
        clsTestBase2::$driver = $drv;
        $objMeta = DemoTbl::getMetaObj();
        $browser->visit('/admin/demo-api');

        //        clsTestBase2::findOneByXPath("//input[contains(@class,'select_all_check')]", 0)->click();
        //        clsTestBase2::findOneByXPath("//input[contains(@class,'select_one_check')]", 1)->click();
        //        clsTestBase2::findOneByXPath("//input[contains(@class,'select_one_check')]", 4)->click();
        //        clsTestBase2::findOneByXPath("//input[contains(@class,'select_one_check')]", 3)->click();

        $this->deleteAllValueOfAColumnField($field);

        //Tìm field đầu tiên:
        //        $obj = clsTestBase2::findOneByXPath("//i[contains(@class,'icon_tool_for_field')][@data-field='$field']");
        //        $obj->click();
        //
        //        dump(" FIELD = $field");
        //
        //        clsTestBase2::clickOnFirstMenuContext("items");
        //        self::assertTrue(clsTestBase2::findOneById('common_dialog')->isDisplayed());
        //
        //        //Set Zero
        //        clsTestBase2::findOneById("btn_set_value_all_item_selecting")->click();
        //
        //        sss(1);
        //        clsTestBase2::clickAlertDialogOK();
        //        sss(1);
        //        clsTestBase2::findOneById("save-all-data")->click();
        //        $browser->refresh();
        //        sss(2);
        //
        //        $mm = $drv->findElements(WebDriverBy::xpath("input[@data-field='$field'][contains(@class, 'input_value_to_post')]"));
        //        foreach ($mm AS $obj){
        //            self::assertTrue(empty($obj->getAttribute('value')));
        //        }

        clsTestBase2::findOneByXPath("//input[contains(@class,'select_one_check')]", 1)->click();
        clsTestBase2::findOneByXPath("//input[contains(@class,'select_one_check')]", 4)->click();
        clsTestBase2::findOneByXPath("//input[contains(@class,'select_one_check')]", 3)->click();

        /////
        $obj = clsTestBase2::findOneByXPath("//i[contains(@class,'icon_tool_for_field')][@data-field='$field']");
        $obj->click();
        $field = $obj->getAttribute('data-field');

        dump(" FIELD2 = $field");

        clsTestBase2::clickOnFirstMenuContext('items');
        self::assertTrue(clsTestBase2::findOneById('common_dialog')->isDisplayed());

        clsTestBase2::findOneById('input_set_this_value_to_all_item_field')->sendKeys(1);

        sss(1);
        clsTestBase2::findOneById('btn_set_value_all_item_selecting')->click();
        sss(1);
        //        clsTestBase2::clickAlertDialogOK();

        //Truoc khi save all, cac value phai ok
        $val1 = clsTestBase2::findOneByXPath("//input[contains(@class,'input_value_to_post')][@data-field='$field']", 0)->getAttribute('value');
        $val2 = clsTestBase2::findOneByXPath("//input[contains(@class,'input_value_to_post')][@data-field='$field']", 2)->getAttribute('value');
        $val3 = clsTestBase2::findOneByXPath("//input[contains(@class,'input_value_to_post')][@data-field='$field']", 3)->getAttribute('value');

        dump(" VAL = $val1, $val2, $val3");

        self::assertTrue($val1 == '1');
        self::assertTrue($val2 == '1');
        self::assertTrue($val3 == '1');

        clsTestBase2::findOneById('save-all-data')->click();
        sss(2);
        $browser->refresh();
        sss(2);

        //Sau khi refresh, check lại ok
        $val1 = clsTestBase2::findOneByXPath("//input[contains(@class,'input_value_to_post')][@data-field='$field']", 0)->getAttribute('value');
        $val2 = clsTestBase2::findOneByXPath("//input[contains(@class,'input_value_to_post')][@data-field='$field']", 2)->getAttribute('value');
        $val3 = clsTestBase2::findOneByXPath("//input[contains(@class,'input_value_to_post')][@data-field='$field']", 3)->getAttribute('value');

        dump(" VAL = $val1, $val2, $val3");

        self::assertTrue($val1 == '1');
        self::assertTrue($val2 == '1');
        self::assertTrue($val3 == '1');

        //Todo?

    }

    public function testClickSetFieldTextArea1()
    {
        $this->testClickSetFieldValueText('textarea1');
    }

    public function testClickSetFieldNumber()
    {
        $this->testClickSetFieldValueText('number1');
    }

    public function deleteAllValueOfAColumnField($field)
    {

        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();


        // $browser->script("$('.divTable2Cell.icon_tool_for_field').css('display', 'inline');");

        try {
            //Xóa tất cả
            clsTestBase2::findOneByXPath("//input[contains(@class,'select_all_check')]", 0)->click();
            
            //Chọn cmd col
            $obj = clsTestBase2::findOneByXPath("//i[contains(@class,'icon_tool_for_field')][@data-field='$field']");
            
            // Scroll and wait before clicking
            $this->waitForXPath("//i[contains(@class,'icon_tool_for_field')][@data-field='$field']");

            //Ko ro scrool ở đây để làm gì:
            // $this->getDrv()->executeScript("arguments[0].scrollIntoView(true);", [$obj]);

            sleep(1);
            
            // Retry logic for clicking
            $maxAttempts = 3;
            $clicked = false;
            
            for ($i = 0; $i < $maxAttempts; $i++) {
                try {
                    $obj->click();
                    $clicked = true;
                    break;
                } catch (\Exception $e) {
                    if ($i < $maxAttempts - 1) {
                        echo "Click attempt " . ($i + 1) . " failed on deleteAllValueOfAColumnField, retrying...\n";
                        sleep(1);
                        $obj = clsTestBase2::findOneByXPath("//i[contains(@class,'icon_tool_for_field')][@data-field='$field']");
                    }
                }
            }
            
            if (!$clicked) {
                throw new \Exception("Could not click on field tool icon for field '$field' after $maxAttempts attempts");
            }
            
            sss(1);
            dump(" FIELD3 = $field");
            clsTestBase2::clickOnFirstMenuContext('items');
            sss(1);
            self::assertTrue(clsTestBase2::findOneById('common_dialog')->isDisplayed());
            sss(1);
            //Set Zero all Field
            clsTestBase2::findOneById('btn_set_value_all_item_selecting')->click();
            sss(1);
            //        clsTestBase2::clickAlertDialogOK();

            //Save all to DB
            clsTestBase2::findOneById('save-all-data')->click();
            sss(2);
            $browser->refresh();
            sss(1);
            $mm = $drv->findElements(WebDriverBy::xpath("input[@data-field='$field'][contains(@class, 'input_value_to_post')]"));
            //Chắc chắn là mọi value đều rỗng
            foreach ($mm as $obj) {
                self::assertTrue(empty($obj->getAttribute('value')));
            }
        } catch (\Exception $e) {
            echo "\n\n=== ERROR IN deleteAllValueOfAColumnField ===\n";
            echo "Field: $field\n";
            echo "Error: " . $e->getMessage() . "\n";
            echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
            echo "\nStack Trace:\n" . $e->getTraceAsString() . "\n";
            echo "=== END ERROR ===\n\n";
            throw $e;
        }
    }

    public function testClickSetFieldSingleRadioValueTree($field = 'parent_id', $posTree = 0)
    {

        DemoFolderTbl::where('name', null)->forceDelete();
        DemoFolderTbl::where('name', '')->forceDelete();

        //Chắc chắn taglist có 2 phần tử
        $mm = DemoFolderTbl::where('parent_id', 0)->get();
        self::assertTrue(count($mm) > 1);

        $this->testLoginTrueAcc();
        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();
        clsTestBase2::$driver = $drv;
        $objMeta = DemoTbl::getMetaObj();
        $browser->visit('/admin/demo-api');

        sleep(10);
        //Chạy js để display inline class sau:
        //.divTable2Cell.icon_tool_for_field


        $this->deleteAllValueOfAColumnField($field);

        //        sss(20);

        //Chọn 3 hàng đầu
        clsTestBase2::findOneByXPath("//input[contains(@class,'select_one_check')]", 1)->click();
        clsTestBase2::findOneByXPath("//input[contains(@class,'select_one_check')]", 4)->click();
        clsTestBase2::findOneByXPath("//input[contains(@class,'select_one_check')]", 3)->click();

        /////
        $obj = clsTestBase2::findOneByXPath("//i[contains(@class,'icon_tool_for_field')][@data-field='$field']");
        $obj->click();
        $field = $obj->getAttribute('data-field');

        dump(" FIELD4 = $field");

        clsTestBase2::clickOnFirstMenuContext('items');
        self::assertTrue(clsTestBase2::findOneById('common_dialog')->isDisplayed());

        //Xem Tree hiện ra
        sss(2);

        $obj = clsTestBase2::findOneByXPath("//input[@class='radio_box_node1']",1)->click();

        //$obj = clsTestBase2::findOneByXPath("//input:checked[@class='radio_box_node1']");

        $parent = clsTestBase2::findOneByXPath("//input[@class='radio_box_node1']/..",1);
        $nodeId = $parent->getAttribute('data-tree-node-id');
        dump(" Node ID = $nodeId");
        //
//                sss(111);
        //
        //        return;

        clsTestBase2::findOneById('btn_set_value_all_item_selecting')->click();

        //        clsTestBase2::clickAlertDialogOK();

        //Truoc khi save all, cac value phai ok
        $val1 = clsTestBase2::findOneByXPath("//input[contains(@class,'input_value_to_post')][@data-field='$field']", 0)->getAttribute('value');
        $val2 = clsTestBase2::findOneByXPath("//input[contains(@class,'input_value_to_post')][@data-field='$field']", 2)->getAttribute('value');
        $val3 = clsTestBase2::findOneByXPath("//input[contains(@class,'input_value_to_post')][@data-field='$field']", 3)->getAttribute('value');

        dump(" VAL = $val1, $val2, $val3");

        self::assertTrue($val1 == $nodeId);
        self::assertTrue($val2 == $nodeId);
        self::assertTrue($val3 == $nodeId);

        clsTestBase2::findOneById('save-all-data')->click();
        sss(2);

        $browser->refresh();
        sss(1);

        ///////////////////////////////////////////////
        //Sau khi refresh, check lại vẫn ok
        $val1 = clsTestBase2::findOneByXPath("//input[contains(@class,'input_value_to_post')][@data-field='$field']", 0)->getAttribute('value');
        $val2 = clsTestBase2::findOneByXPath("//input[contains(@class,'input_value_to_post')][@data-field='$field']", 2)->getAttribute('value');
        $val3 = clsTestBase2::findOneByXPath("//input[contains(@class,'input_value_to_post')][@data-field='$field']", 3)->getAttribute('value');

        dump(" VAL = $val1, $val2, $val3");

        self::assertTrue($val1 == $nodeId, " $val1 == $nodeId ?");
        self::assertTrue($val2 == $nodeId);
        self::assertTrue($val3 == $nodeId);

        //zzzzzzzzz
    }

    public function testClickSetFieldSingleRadioValue2Tree()
    {
        $this->testClickSetFieldSingleRadioValueTree('parent2');
    }

    public function testClickSetFieldMultiCheckBoxValueTree($field = 'parent_multi')
    {

        //Chắc chắn taglist có 2 phần tử
        $mm = DemoFolderTbl::where('parent_id', 0)->get();
        self::assertTrue(count($mm) > 1);

        $this->testLoginTrueAcc();
        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();
        clsTestBase2::$driver = $drv;
        $objMeta = DemoTbl::getMetaObj();
        $browser->visit('/admin/demo-api');

        $this->deleteAllValueOfAColumnField($field);

        sss(1);

        //        sss(20);
        //Chọn 3 hàng đầu
        clsTestBase2::findOneByXPath("//input[contains(@class,'select_one_check')]", 1)->click();
        clsTestBase2::findOneByXPath("//input[contains(@class,'select_one_check')]", 4)->click();
        clsTestBase2::findOneByXPath("//input[contains(@class,'select_one_check')]", 3)->click();

        /////
        $obj = clsTestBase2::findOneByXPath("//i[contains(@class,'icon_tool_for_field')][@data-field='$field']");
        $obj->click();
        $field = $obj->getAttribute('data-field');

        dump(" FIELD5 = $field");

        clsTestBase2::clickOnFirstMenuContext('items');
        self::assertTrue(clsTestBase2::findOneById('common_dialog')->isDisplayed());

        //Xem Tree hiện ra
        sss(2);

        clsTestBase2::findOneByXPath("//input[@class='check_box_node1']", 0)->click();
        clsTestBase2::findOneByXPath("//input:checked[@class='check_box_node1']");
        clsTestBase2::findOneByXPath("//input[@class='check_box_node1']", 1)->click();
        clsTestBase2::findOneByXPath("//input:checked[@class='check_box_node1']");
        //        $parent = $obj->findElement(WebDriverBy::xpath("/parent::*"))->click();
        //        $parent = $obj->findElement(WebDriverBy::xpath("/.."))->click();
        // sss(1000);

        $parent = clsTestBase2::findOneByXPath("//input[@class='check_box_node1']/..");
        $nodeId = $parent->getAttribute('data-tree-node-id');
        dump(" Node ID = $nodeId");

        $parent2 = clsTestBase2::findOneByXPath("//input[@class='check_box_node1']/..", 1);
        $nodeId2 = $parent2->getAttribute('data-tree-node-id');

        dump(" Node ID2 = $nodeId2");

        clsTestBase2::findOneById('btn_set_value_all_item_selecting')->click();

        //        clsTestBase2::clickAlertDialogOK();

        //Truoc khi save all, cac value phai ok
        $val1 = clsTestBase2::findOneByXPath("//input[contains(@class,'input_value_to_post')][@data-field='$field']", 0)->getAttribute('value');
        $val2 = clsTestBase2::findOneByXPath("//input[contains(@class,'input_value_to_post')][@data-field='$field']", 2)->getAttribute('value');
        $val3 = clsTestBase2::findOneByXPath("//input[contains(@class,'input_value_to_post')][@data-field='$field']", 3)->getAttribute('value');

        dump(" VAL = $val1, $val2, $val3");

        self::assertTrue($val1 == $nodeId.','.$nodeId2);
        self::assertTrue($val2 == $nodeId.','.$nodeId2);
        self::assertTrue($val3 == $nodeId.','.$nodeId2);

        clsTestBase2::findOneById('save-all-data')->click();
        sss(2);
        $browser->refresh();
        sss(2);
        //Sau khi refresh, check lại ok
        $val1 = clsTestBase2::findOneByXPath("//input[contains(@class,'input_value_to_post')][@data-field='$field']", 0)->getAttribute('value');
        $val2 = clsTestBase2::findOneByXPath("//input[contains(@class,'input_value_to_post')][@data-field='$field']", 2)->getAttribute('value');
        $val3 = clsTestBase2::findOneByXPath("//input[contains(@class,'input_value_to_post')][@data-field='$field']", 3)->getAttribute('value');
        dump(" VAL = $val1, $val2, $val3");
        self::assertTrue($val1 == $nodeId.','.$nodeId2 || $val1 == "$nodeId2,$nodeId");
        self::assertTrue($val2 == $nodeId.','.$nodeId2 || $val2 == "$nodeId2,$nodeId");
        self::assertTrue($val3 == $nodeId.','.$nodeId2 || $val3 == "$nodeId2,$nodeId");
    }

    public function testClickSetFieldMultiCheckBoxValue2Tree()
    {
        $this->testClickSetFieldMultiCheckBoxValueTree('parent_multi2');
    }
}
