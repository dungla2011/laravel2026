<?php

namespace Tests\Feature;

use App\Models\DemoFolderTbl;
use App\Models\Tag;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Illuminate\Support\Facades\DB;
use LadLib\Common\Tester\clsTestBase2;
use Tests\Browser\DuskTestCaseBase;

/**
 *  Filter autocomplete top filter
 *  Edit, Add item
 */
class DemoTreeFolderTest extends DuskTestCaseBase
{
    protected function hasHeadlessDisabled(): bool
    {
        return true;

        return parent::hasHeadlessDisabled();
    }

    public function clickToggleNodeId($nodeID)
    {
        $node = clsTestBase2::findOneByXPath("//div[contains(@class,'real_node_item')][@data-tree-node-id='$nodeID']");
        $toggle_node = $node->findElement(WebDriverBy::xpath("span[@class='toggle_node']"));
        $toggle_node->click();

    }

    public function clickMenuOnNodeId($nodeID, $menuName = null)
    {
        $node = clsTestBase2::findOneByXPath("//div[contains(@class,'real_node_item')][@data-tree-node-id='$nodeID']");
        $menu = $node->findElement(WebDriverBy::xpath("span[@class='menu_one_node']"));
        $menu->click();
        sss(1);
        self::assertTrue(! clsTestBase2::findOneByXPath("//div[contains(@class, 'modal_dialog_edit_node')]")->isDisplayed());
        clsTestBase2::findOneByXPath("//li[contains(@class, 'context-menu-item')]/span[contains(text(), '$menuName')]")->click();
    }

    public function clickMenuOnNodeName($name, $menuName = null)
    {
        $obj = clsTestBase2::findOneByXPath("//div[contains(@class,'real_node_item')]/span[contains(@class,'node_name')][contains(text(), '$name')]");
        $node = clsTestBase2::findParent($obj);
        $nodeID = $node->getAttribute('data-tree-node-id');
        dump("NODE ID: $nodeID, click $name / $menuName");

        $menu = $node->findElement(WebDriverBy::xpath("./span[@class='menu_one_node']"));
        $menu->click();
        sss(1);
        self::assertTrue(! clsTestBase2::findOneByXPath("//div[contains(@class, 'modal_dialog_edit_node')]")->isDisplayed());
        clsTestBase2::findOneByXPath("//li[contains(@class, 'context-menu-item')]/span[contains(text(), '$menuName')]")->click();
    }

    public function listAllNodeIdAndNameOfNodeId($id)
    {
        return $this->listAllNodeIdAndNameOfNodeName(null, $id);
    }

    public function listAllNodeIdAndNameOfNodeName($name, $nodeID = null)
    {
        $mRet = [];
        if (! $name && $nodeID) {
            $node = clsTestBase2::findOneByXPath("//div[contains(@class,'real_node_item')][@data-tree-node-id='$nodeID']");
        } else {
            $obj = clsTestBase2::findOneByXPath("//div[contains(@class,'real_node_item')]/span[contains(@class,'node_name')][contains(text(), '$name')]");
            $node = clsTestBase2::findParent($obj);
            $nodeID = $node->getAttribute('data-tree-node-id');
        }

        $melm = clsTestBase2::$driver->findElements(WebDriverBy::xpath("//div[contains(@class,'real_node_item')][@data-tree-node-id='$nodeID']/div"));
        //        dump($melm);
        foreach ($melm as $elm) {
            $id = $elm->getAttribute('data-tree-node-id');
            if ($id && $elm->findElement(WebDriverBy::xpath("span[contains(@class,'node_name')]"))) {
                $nameN = $elm->findElement(WebDriverBy::xpath("span[contains(@class,'node_name')]"))->getText();
                dump("Child Node: $id / $nameN ");
                $mRet[$id] = $nameN;
            }
        }

        return $mRet;
    }

    /**
     * Test multi field autocomplete: đưa tag name vào , thay đổi, save, kiểm tra KQ
     */
    public function testCreateNodeInRootRenameDelete()
    {

        $demo = DemoFolderTbl::withTrashed()->where('name', 'LIKE', 'test_create_root%')->forceDelete();

        $this->testLoginTrueAcc();
        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();

        clsTestBase2::$driver = $drv;

        //        $mmRoot = DemoFolderTbl::where("parent_id", 0)->get()->toArray();
        //        self::assertTrue(count($mmRoot) >1, 'có ít nhất 2 node ở root để test');

        $browser->visit('/admin/demo-folder/tree?open_all=1');

        sss(3);

        ////////////////////////////////////////
        /// Tạo 1 node mới thuộc gốc
        $menuRoot = clsTestBase2::findOneByXPath("//div[contains(@class,'real_node_item')][@data-tree-node-id=0]/span[@class='menu_one_node']");
        $menuRoot->click();

        clsTestBase2::findOneByXPath("//li[contains(@class, 'context-menu-item')]/span[contains(text(), 'Create')]")->click();
        self::assertTrue(clsTestBase2::findOneByXPath("//div[contains(@class, 'modal_dialog_edit_node')]")->isDisplayed());

        $nameNewNode = 'test_create_root.'.microtime(1);
        clsTestBase2::findOneByXPath("//input[contains(@class, 'new_name')][@data-cmd='create_node']")->sendKeys($nameNewNode);
        clsTestBase2::findOneByXPath("//input[contains(@class, 'btn_create')][@data-cmd='create_node']")->click();
        sss(1);

        //ChecClick thì sẽ mất dialog ẩn đi
        self::assertTrue(! clsTestBase2::findOneByXPath("//div[contains(@class, 'modal_dialog_edit_node')]")->isDisplayed());

        //Xem node vừa được tạo có trên tree ko:
        $obj = clsTestBase2::findOneByXPath("//div[contains(@class,'real_node_item')]/span[contains(@class,'node_name')][contains(text(), '$nameNewNode')]");
        self::assertTrue($obj != null);

        ///////////////////////////////////////////
        //Refresh xem tồn tại node đó
        $browser->refresh();

        dump(' sleep  2');
        sss(2);

        $obj = clsTestBase2::findOneByXPath("//div[contains(@class,'real_node_item')]/span[contains(@class,'node_name')][contains(text(), '$nameNewNode')]");
        self::assertTrue($obj != null);

        dump('NODE text: '.$obj->getText());
        self::assertTrue(trim($obj->getText()) == $nameNewNode);

        ///////////////////////
        //Đổi tên node đó
        $node = clsTestBase2::findParent($obj);
        $nodeID = $node->getAttribute('data-tree-node-id');
        dump("NODE ID: $nodeID");
        $drv->executeScript('window.scrollTo(0,document.body.scrollHeight);');
        sss(2);

        $menu = $node->findElement(WebDriverBy::xpath("./span[@class='menu_one_node']"));
        $menu->click();

        clsTestBase2::findOneByXPath("//li[contains(@class, 'context-menu-item')]/span[contains(text(), 'Rename')]")->click();
        $nameNewNode2 = 'test_create_root.'.microtime(1);
        clsTestBase2::findOneByXPath("//input[contains(@class, 'new_name')][@data-cmd='edit_name']")->clear()->sendKeys($nameNewNode2);
        clsTestBase2::findOneByXPath("//input[contains(@class, 'btn_create')][@data-cmd='edit_name']")->click();
        sss(1);
        $obj = clsTestBase2::findOneByXPath("//div[contains(@class,'real_node_item')]/span[contains(@class,'node_name')][contains(text(), '$nameNewNode2')]");

        self::assertTrue($obj != null);
        //Refresh xem tồn tại node mới đổi tên
        $browser->refresh();
        dump(' sleep  2');
        sss(2);
        $drv->executeScript('window.scrollTo(0,document.body.scrollHeight);');
        sss(2);
        $obj = clsTestBase2::findOneByXPath("//div[contains(@class,'real_node_item')]/span[contains(@class,'node_name')][contains(text(), '$nameNewNode2')]");
        self::assertTrue($obj != null);

        ////////////////////
        /// Xóa node đó
        $node = clsTestBase2::findParent($obj);
        $menu = $node->findElement(WebDriverBy::xpath("./span[@class='menu_one_node']"));
        $menu->click();
        clsTestBase2::findOneByXPath("//li[contains(@class, 'context-menu-item')]/span[contains(text(), 'Delete')]")->click();

        sss(1);

        $drv->wait()->until(
            WebDriverExpectedCondition::alertIsPresent()
        );
        $drv->switchTo()->alert()->accept();

        //Node đó ko còn tồn tại
        $del = clsTestBase2::findOneByXPath("//div[contains(@class,'real_node_item')]/span[contains(@class,'node_name')][contains(text(), '$nameNewNode2')]");
        self::assertTrue($del == null);
        $browser->refresh();
        sss(2);

        //refesh Node đó ko còn tồn tại
        $del = clsTestBase2::findOneByXPath("//div[contains(@class,'real_node_item')]/span[contains(@class,'node_name')][contains(text(), '$nameNewNode2')]");

        //        self::assertTrue($del==null);

        //Xóa node trong db
        $demo = DemoFolderTbl::withTrashed()->find($nodeID);
        $demo->forceDelete();
    }

    //Tạo 2 node ở gốc, tạo 2 node trong node, move 2 node cho nhau, move ra gốc
    public function testMoveNodeTreeFolder()
    {

        //Xóa node trong db
        $demo = DemoFolderTbl::withTrashed()->where('name', 'LIKE', 'test_create_root%')->forceDelete();
        //        $demo->forceDelete();

        $this->testLoginTrueAcc();
        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();

        clsTestBase2::$driver = $drv;

        //        $mmRoot = DemoFolderTbl::where("parent_id", 0)->get()->toArray();
        //        self::assertTrue(count($mmRoot) >1, 'có ít nhất 2 node ở root để test');

        $browser->visit('/admin/demo-folder/tree');

        sss(2);

        ////////////////////////////////////////
        /// Tạo 2 node mới thuộc gốc
        $menuRoot = clsTestBase2::findOneByXPath("//div[contains(@class,'real_node_item')][@data-tree-node-id=0]/span[@class='menu_one_node']");
        $menuRoot->click();
        sss(1);

        clsTestBase2::findOneByXPath("//li[contains(@class, 'context-menu-item')]/span[contains(text(), 'Create')]")->click();
        self::assertTrue(clsTestBase2::findOneByXPath("//div[contains(@class, 'modal_dialog_edit_node')]")->isDisplayed());

        //Tạo 2 node
        $nameNewNode1 = 'test_create_root.'.microtime(1);
        clsTestBase2::findOneByXPath("//input[contains(@class, 'new_name')][@data-cmd='create_node']")->sendKeys($nameNewNode1);
        clsTestBase2::findOneByXPath("//input[contains(@class, 'btn_create')][@data-cmd='create_node']")->click();
        sss(1);

        $menuRoot->click();
        sss(1);
        clsTestBase2::findOneByXPath("//li[contains(@class, 'context-menu-item')]/span[contains(text(), 'Create')]")->click();
        self::assertTrue(clsTestBase2::findOneByXPath("//div[contains(@class, 'modal_dialog_edit_node')]")->isDisplayed());
        $nameNewNode2 = 'test_create_root.2-'.microtime(1);
        clsTestBase2::findOneByXPath("//input[contains(@class, 'new_name')][@data-cmd='create_node']")->sendKeys($nameNewNode2);
        clsTestBase2::findOneByXPath("//input[contains(@class, 'btn_create')][@data-cmd='create_node']")->click();
        sss(1);

        //    sss(5);

        //ChecClick thì sẽ mất dialog ẩn đi
        self::assertTrue(! clsTestBase2::findOneByXPath("//div[contains(@class, 'modal_dialog_edit_node')]")->isDisplayed());

        //Xem node vừa được tạo có trên tree ko:
        $obj = clsTestBase2::findOneByXPath("//div[contains(@class,'real_node_item')]/span[contains(@class,'node_name')][contains(text(), '$nameNewNode1')]");
        self::assertTrue($obj != null);
        $node1 = clsTestBase2::findParent($obj);
        $nodeID1 = $node1->getAttribute('data-tree-node-id');
        dump("NODE ID: $nodeID1");

        //  sss(5);

        $obj = clsTestBase2::findOneByXPath("//div[contains(@class,'real_node_item')]/span[contains(@class,'node_name')][contains(text(), '$nameNewNode2')]");
        self::assertTrue($obj != null);
        $node2 = clsTestBase2::findParent($obj);
        $nodeID2 = $node2->getAttribute('data-tree-node-id');
        dump("NODE ID000: $nodeID2");

        $drv->executeScript('window.scrollTo(0,document.body.scrollHeight);');
        //Tạo 2 con trong 2 node
        ////////////////////////////////////////
        /// Tạo node mới thuộc node 1,2
        //        $menu = clsTestBase2::findOneByXPath("//div[contains(@class,'real_node_item')][@data-tree-node-id='$nodeID1']//span[@class='menu_one_node']");
        //        $menu->click();

        $this->clickMenuOnNodeId($nodeID1, 'Create');

        //        clsTestBase2::findOneByXPath("//li[contains(@class, 'context-menu-item')]/span[contains(text(), 'Create')]")->click();
        sss(1);
        self::assertTrue(clsTestBase2::findOneByXPath("//div[contains(@class, 'modal_dialog_edit_node')]")->isDisplayed());
        //Tạo node 11
        $nameNewNode11 = 'test_create_root.'.microtime(1);
        clsTestBase2::findOneByXPath("//input[contains(@class, 'new_name')][@data-cmd='create_node']")->sendKeys($nameNewNode11);
        clsTestBase2::findOneByXPath("//input[contains(@class, 'btn_create')][@data-cmd='create_node']")->click();
        sss(1);

        $menu = clsTestBase2::findOneByXPath("//div[contains(@class,'real_node_item')][@data-tree-node-id='$nodeID2']//span[@class='menu_one_node']");
        $menu->click();
        //        $this->clickMenuOnNodeId($nodeID2);
        clsTestBase2::findOneByXPath("//li[contains(@class, 'context-menu-item')]/span[contains(text(), 'Create')]")->click();
        sss(1);
        self::assertTrue(clsTestBase2::findOneByXPath("//div[contains(@class, 'modal_dialog_edit_node')]")->isDisplayed());
        //Tạo node 11
        $nameNewNode21 = 'test_create_root-2.'.microtime(1);
        clsTestBase2::findOneByXPath("//input[contains(@class, 'new_name')][@data-cmd='create_node']")->sendKeys($nameNewNode21);
        clsTestBase2::findOneByXPath("//input[contains(@class, 'btn_create')][@data-cmd='create_node']")->click();

        sss(1);
        $this->clickMenuOnNodeName($nameNewNode11, 'Cut');
        sss(1);
        $this->clickMenuOnNodeName($nameNewNode2, 'Paste');
        $mChildOf2 = $this->listAllNodeIdAndNameOfNodeName($nameNewNode2);
        //Chắc chắn $mChildOf2 phải $nameNewNode11
        self::assertTrue(in_array($nameNewNode11, $mChildOf2));

        //Move ra root
        sss(1);
        $this->clickMenuOnNodeName($nameNewNode11, 'Cut');
        sss(1);
        $this->clickMenuOnNodeId(0, 'Paste');
        sss(1);
        //Chắc chắn root có
        $mChildOfRoot = $this->listAllNodeIdAndNameOfNodeId(0);
        self::assertTrue(in_array($nameNewNode11, $mChildOfRoot));

        //Move lại vào $nameNewNode2
        sss(1);
        $this->clickMenuOnNodeName($nameNewNode11, 'Cut');
        sss(1);
        $this->clickMenuOnNodeName($nameNewNode2, 'Paste');
        $mChildOf2 = $this->listAllNodeIdAndNameOfNodeName($nameNewNode2);
        //Chắc chắn $mChildOf2 phải $nameNewNode11
        self::assertTrue(in_array($nameNewNode11, $mChildOf2));

        /////////////////////////////////////////
        //Đóng mở node2 để thấy các node trong nó ẩn hiện
        $this->clickToggleNodeId($nodeID2);
        $mm = $this->listAllNodeIdAndNameOfNodeId($nodeID2);
        self::assertTrue(count($mm) == 0);
        sss(1);
        $this->clickToggleNodeId($nodeID2);
        sss(2);
        $mm = $this->listAllNodeIdAndNameOfNodeId($nodeID2);
        self::assertTrue(count($mm) == 2);
        self::assertTrue(in_array($nameNewNode11, $mm));
        self::assertTrue(in_array($nameNewNode21, $mm));

        /////////////////////////////////////////
        //Đóng node 2 lại, không thấy các node con, move node 1 vào node 2 để thấy 3 con hiện ra
        $this->clickToggleNodeId($nodeID2);
        $mm = $this->listAllNodeIdAndNameOfNodeId($nodeID2);
        self::assertTrue(count($mm) == 0);
        sss(1);
        $this->clickMenuOnNodeName($nameNewNode1, 'Cut');
        sss(1);
        $this->clickMenuOnNodeName($nameNewNode2, 'Paste');
        sss(2);
        $mm = $this->listAllNodeIdAndNameOfNodeName($nameNewNode2);
        self::assertTrue(count($mm) == 3);
        DemoFolderTbl::withTrashed()->where('name', 'LIKE', 'test_create_root%')->forceDelete();
    }

    public function testChangeOrderFolder()
    {
        //Todo:

        //admin/demo-folder/tree?order_by=orders
        //cần drag, drop được, hoặc change trong API, sau đó xem trên UI có thay đổi ko

        self::assertTrue(true);
    }
}
