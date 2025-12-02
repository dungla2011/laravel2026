<?php

namespace Tests\Feature;

use App\Models\DemoTbl;
use App\Models\Tag;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Support\Facades\DB;
use LadLib\Common\Tester\clsTestBase2;
use Tests\Browser\DuskTestCaseBase;

/**
 *  Filter autocomplete top filter
 *  Edit, Add item
 */
class DemoUseTreeFolderTest extends DuskTestCaseBase
{
    protected function hasHeadlessDisabled(): bool
    {
        return true;

        return parent::hasHeadlessDisabled();
    }

    /**
     * Test multi field autocomplete: đưa tag name vào , thay đổi, save, kiểm tra KQ
     */
    public function testClickSingleTreeFolderChangeValueInput()
    {

        $obj = DemoTbl::first();

        $this->testLoginTrueAcc();
        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();

        clsTestBase2::$driver = $drv;

        $objMeta = DemoTbl::getMetaObj();
        $browser->visit("/admin/demo-api/edit/$obj->id");

        //$mField = ['parent', 'parent2', 'parent_multi', 'parent_multi2',];

        //Xóa hết node folder parent của item

        //$mm = clsTestBase2::$driver->findElements(WebDriverBy::xpath("//span[contains(@class, 'one_node_name')] | //span[contains(@class, 'full_node_path_name')]"));
        $mm = clsTestBase2::$driver->findElements(WebDriverBy::xpath("//span[contains(@class, 'one_node_name')]"));
        foreach ($mm as $m1) {
            $m1->click();
        }

        clsTestBase2::findOneById('save-one-data')->click();
        sss(2);
        $browser->refresh();

        //Chắc chắn là ko còn node tree nào:
        self::assertTrue(! trim(clsTestBase2::findOneByXPath("//input[@data-field='parent_id']")->getAttribute('value')));
        self::assertTrue(! trim(clsTestBase2::findOneByXPath("//input[@data-field='parent2']")->getAttribute('value')));
        self::assertTrue(! trim(clsTestBase2::findOneByXPath("//input[@data-field='parent_multi']")->getAttribute('value')));
        self::assertTrue(! trim(clsTestBase2::findOneByXPath("//input[@data-field='parent_multi2']")->getAttribute('value')));
        self::assertTrue(! clsTestBase2::findOneByXPath("//span[contains(@class, 'one_node_name')]"));

        self::assertTrue(! clsTestBase2::findOneById('dialog_tree')->isDisplayed());

        clsTestBase2::findOneByXPath("//button[@data-field='parent_id']")->click();
        self::assertTrue(clsTestBase2::findOneById('dialog_tree')->isDisplayed());
        clsTestBase2::findOneById('close_select_tree')->click();
        self::assertTrue(! clsTestBase2::findOneById('dialog_tree')->isDisplayed());

        clsTestBase2::findOneByXPath("//button[@data-field='parent2']")->click();
        self::assertTrue(clsTestBase2::findOneById('dialog_tree')->isDisplayed());
        clsTestBase2::findOneById('close_select_tree')->click();
        self::assertTrue(! clsTestBase2::findOneById('dialog_tree')->isDisplayed());

        clsTestBase2::findOneByXPath("//button[@data-field='parent_multi']")->click();
        self::assertTrue(clsTestBase2::findOneById('dialog_tree')->isDisplayed());
        clsTestBase2::findOneById('close_select_tree')->click();
        self::assertTrue(! clsTestBase2::findOneById('dialog_tree')->isDisplayed());

        clsTestBase2::findOneByXPath("//button[@data-field='parent_multi2']")->click();
        self::assertTrue(clsTestBase2::findOneById('dialog_tree')->isDisplayed());
        clsTestBase2::findOneById('close_select_tree')->click();
        self::assertTrue(! clsTestBase2::findOneById('dialog_tree')->isDisplayed());

    }

    //todo: khong hieu sao loi
    public function testSaveSingleNodeTreeFolder($field = 'parent_id')
    {

        //        if($field == 'parent')
        //            return;

        $obj = DemoTbl::first();

        $this->testLoginTrueAcc();
        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();

        clsTestBase2::$driver = $drv;

        $objMeta = DemoTbl::getMetaObj();
        $browser->visit("/admin/demo-api/edit/$obj->id");

        //        dump(clsTestBase2::findOneByXPath("//input[@data-field='$field']")->getAttribute('value'));
        //        return;

        $mm = clsTestBase2::$driver->findElements(WebDriverBy::xpath("//span[contains(@class, 'one_node_name')] | //span[contains(@class, 'full_node_path_name')]"));
        foreach ($mm as $m1) {
            $m1->click();
        }

        sss(2);

        clsTestBase2::findOneByXPath("//button[@data-field='$field']")->click();
        sss(1);
        $obj = clsTestBase2::findOneByXPath("//div[contains(@class,'real_node_item')][@data-tree-node-id>0]//span[contains(@class,'node_name')]");
        $nodeName = $obj->getText();
        $nodeId0 = clsTestBase2::findOneByXPath('//div[contains(@class,"real_node_item")][@data-tree-node-id>0]')->getAttribute('data-tree-node-id');
        dump("NODEId = $nodeId0 / $nodeName");
        $obj->click();

        //Chưa đóng vội
        // clsTestBase2::findOneById('close_select_tree')->click();

        sleep(1);

        $field = trim($field);

        $val1 = clsTestBase2::findOneByXPath("//input[@data-field='$field']", 0)->getAttribute('value');
        dump("$field /  Value Found: $val1");

        self::assertTrue(clsTestBase2::findOneByXPath("//input[@data-field='$field']")->getAttribute('value') == $nodeId0);
        self::assertTrue(clsTestBase2::findOneByXPath("//span[@data-field='$field'][contains(., '$nodeName')]") != null);

        //Click node tiếp theo, xem có thay đổi content ko
        $obj = clsTestBase2::findOneByXPath("//div[contains(@class,'real_node_item')][@data-tree-node-id>$nodeId0]//span[contains(@class,'node_name')]");
        $nodeName = $obj->getText();
        $nodeId1 = clsTestBase2::findOneByXPath("//div[contains(@class,'real_node_item')][@data-tree-node-id>$nodeId0]")->getAttribute('data-tree-node-id');
        dump("NODEId = $nodeId1 / $nodeName");
        $obj->click();

        dump(clsTestBase2::findOneByXPath("//input[@data-field='$field']")->getAttribute('value'));

        self::assertTrue(clsTestBase2::findOneByXPath("//input[@data-field='$field']")->getAttribute('value') == $nodeId1);
        self::assertTrue(clsTestBase2::findOneByXPath("//span[@data-field='$field'][contains(., '$nodeName')]") != null);

        clsTestBase2::findOneById('close_select_tree')->click();
        //Refresh xem content được save ko
        //        sss(1);
        clsTestBase2::findOneById('save-one-data')->click();
        sss(2);
        $browser->refresh();

        //Xem span và input có đúng giá trị không, sau khi save DB, refresh
        //        self::assertTrue(clsTestBase2::findOneByXPath("//input[@data-field='$field']")->getAttribute('value') == $nodeId1);

        sleep(2);
        self::assertTrue(clsTestBase2::findOneByXPath("//span[@data-field='$field'][contains(., '$nodeName')]") != null);
    }

    public function testSaveSingleNodeTree_Folder_parent2()
    {
        $this->testSaveSingleNodeTreeFolder('parent2');
    }

    public function testSaveMultiNodeTreeFolder($field = 'parent_multi')
    {

        $obj = DemoTbl::first();

        $this->testLoginTrueAcc();
        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();

        clsTestBase2::$driver = $drv;

        $objMeta = DemoTbl::getMetaObj();
        $browser->visit("/admin/demo-api/edit/$obj->id");

        //Xóa hết node folder $field của item
        $mm = clsTestBase2::$driver->findElements(WebDriverBy::xpath("//span[contains(@class, 'one_node_name')] | //span[contains(@class, 'full_node_path_name')]"));
        if ($mm) {
            foreach ($mm as $m1) {
                $m1->click();
            }
        }

        sss(1);

        clsTestBase2::findOneByXPath("//button[@data-field='$field']")->click();
        sss(1); //root_tree_cls_div
        $obj = clsTestBase2::findOneByXPath("//div[contains(@class,'real_node_item')][not(contains(@class,'root_tree_cls_div'))][@data-tree-node-id>0]//span[contains(@class,'node_name')]");
        $nodeName0 = $nodeName = $obj->getText();
        $nodeId0 = clsTestBase2::findOneByXPath('//div[contains(@class,"real_node_item")][not(contains(@class,"root_tree_cls_div"))][@data-tree-node-id>0]')->getAttribute('data-tree-node-id');
        dump("NODEId = $nodeId0 / $nodeName / field = $field");

        $obj->click();
        sss(1);
        dump($getVal = clsTestBase2::findOneByXPath("//input[@data-field='$field']")->getAttribute('value'));
        dump('valx ='.clsTestBase2::findOneByXPath("//input[@data-field='$field']")->getAttribute('value'));
        self::assertEquals("$nodeId0", "$getVal");

        self::assertTrue(clsTestBase2::findOneByXPath("//span[@data-field='$field'][contains(., '$nodeName')]") != null);

        //Click node tiếp theo, xem có thay đổi content ko
        $obj = clsTestBase2::findOneByXPath("//div[contains(@class,'real_node_item')][@data-tree-node-id>$nodeId0]//span[contains(@class, 'node_name')]");
        $nodeName = $obj->getText();
        $nodeId1 = clsTestBase2::findOneByXPath("//div[contains(@class,'real_node_item')][@data-tree-node-id>$nodeId0]")->getAttribute('data-tree-node-id');
        dump("NODEId = $nodeId1 / $nodeName");
        $obj->click();
        dump(clsTestBase2::findOneByXPath("//input[@data-field='$field']")->getAttribute('value'));
        self::assertTrue(in_array($nodeId1, explode(',', clsTestBase2::findOneByXPath("//input[@data-field='$field']")->getAttribute('value'))));
        self::assertTrue(clsTestBase2::findOneByXPath("//span[@data-field='$field'][contains(., '$nodeName')]") != null);
        self::assertTrue(clsTestBase2::findOneByXPath("//span[@data-field='$field'][contains(., '$nodeName0')]") != null);

        clsTestBase2::findOneById('close_select_tree')->click();
        //Refresh xem content được save ko
        sss(1);
        clsTestBase2::findOneById('save-one-data')->click();
        sss(2);
        $browser->refresh();

        dump($nodeId1);
        dump("Nodename = '$nodeName' / $field");

        sss(3);
        //Xem span và input có đúng giá trị không, sau khi save DB, refresh
        //        self::assertTrue(clsTestBase2::findOneByXPath("//input[@data-field='$field']")->getAttribute('value') == $nodeId1);
        self::assertTrue(in_array($nodeId1, explode(',', clsTestBase2::findOneByXPath("//input[@data-field='$field']")->getAttribute('value'))));
        self::assertTrue(clsTestBase2::findOneByXPath("//span[@data-field='$field'][contains(., '$nodeName')]") != null);
        self::assertTrue(clsTestBase2::findOneByXPath("//span[@data-field='$field'][contains(., '$nodeName0')]") != null);

    }

    public function testSaveMultiNode2TreeFolder($field = 'parent_multi')
    {
        $this->testSaveMultiNodeTreeFolder('parent_multi2');
    }
}
