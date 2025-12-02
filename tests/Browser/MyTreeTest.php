<?php

namespace Tests\Feature;

use App\Models\GiaPha;
use App\Models\MyTreeInfo;
use Facebook\WebDriver\Interactions\WebDriverActions;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys;
use Http\Message\Cookie;
use Illuminate\Support\Facades\Auth;
use LadLib\Common\Tester\clsTestBase2;
use Laravel\Dusk\Browser;
use phpDocumentor\Reflection\Location;
use Tests\Browser\DuskTestCaseBase;

/**
 *  Filter autocomplete top filter
 *  Edit, Add item
 */
class MyTreeTest extends DuskTestCaseBase
{
    protected function hasHeadlessDisabled(): bool
    {
        return true;

        return parent::hasHeadlessDisabled();
    }

    public function MyTreeJs01()
    {
        dump('=== Check ' . get_called_class() . '/' . __FUNCTION__);
        //        $this->testLoginTrueAcc();
        //        $browser = $this->getBrowserLogined();
        //        $browser = new Browser();
        //        $drv = $this->getDrv();
        //$browser->assertSee("parent_id");
        //        $brs->assertSee("parent_id");
        $this->browse(function ($browser) {
            $browser->visit('/tool1/lad_tree_vn/build-tree-top-down-recursive-doing.php?tester=1');
            $browser->assertDontSee('HaveError:');
            $browser->assertSee('TEST OK!');

            $browser->visit('/my-tree')
                ->assertSee('Đăng nhập để tạo cây của bạn');

        });
    }

    public function testEditMyTreeBanner()
    {
        GiaPha::withTrashed()->where('name', 'LIKE', 'for_test_del_tree.%')->forceDelete();
        MyTreeInfo::where('name', 'LIKE', 'for_test_del_tree.%')->forceDelete();
        MyTreeInfo::where('name', 'LIKE', 'for_test_del.%')->forceDelete();
        MyTreeInfo::where('title', '=', '.123456')->forceDelete();

        //        $this->testLoginTrueAcc();

        self::assertTrue(file_exists('e:\Projects\laravel2022-01\laravel01\tests\anh3.jpg'));

        $tree = $this->testCreateNewMyTree(1);
        $idEnc = qqgetRandFromId_($tree->id);
        $name = $tree->name;
        $title = $tree->title;

        dump("TITLE = $title");

        sss(1);
        $this->browse(function ($browser) use ($idEnc, $name) {
            $browser->visit('/my-tree?pid=' . $idEnc)
                ->assertSee($name);
        });

        $browser = $this->getBrowserLogined();
        $drv = $this->getDrv();
        clsTestBase2::$driver = $drv;

        $browser->assertDontSee('☰');
        clsTestBase2::findOneById('svg_cont_node_banner_id')->click();
        $browser->assertSee('☰');
        clsTestBase2::findOneContainClassAndVisible('div', 'node_edit_btn_banner')->click();

        clsTestBase2::findOneById('banner_name1')->sendKeys('.123');
        clsTestBase2::findOneById('banner_title1')->sendKeys('.123456');

        sss(1);
        dump(' -- ' . $title);
        //        sss(1111);
        self::assertTrue(clsTestBase2::findOneById('svg_text_path_banner_name')->getText() == $name . '.123');
        self::assertTrue(clsTestBase2::findOneById('svg_text_path_banner_title')->getText() == $title . '.123456');

        clsTestBase2::findOneById('save_banner_info1')->click();
        sss(2);
        //        $browser->assertSee($name.'.123');
        $browser->refresh();

        sss(2);

        $browser->assertSee($name . '.123');
        $browser->assertSee($title . '.123456');

        sss(1);
        //banner_name_margin_top
        //banner_fontsize_name
        //banner_name_curver
        //banner_title1
        //banner_title_margin_top
        //banner_title_curver
        //banner_width
        //banner_height
        //banner_margin_top

        clsTestBase2::findOneById('svg_cont_node_banner_id')->click();
        usleep(1);
        $browser->assertSee('☰');
        clsTestBase2::findOneContainClassAndVisible('div', 'node_edit_btn_banner')->click();

        sleep(1);
        clsTestBase2::findOneById('banner_name_margin_top')->clear()->sendKeys(11);
        clsTestBase2::findOneById('banner_name_curver')->clear()->sendKeys(111);
        clsTestBase2::findOneById('banner_title_margin_top')->clear()->sendKeys(11);
        clsTestBase2::findOneById('banner_title_curver')->clear()->sendKeys(111);
        clsTestBase2::findOneById('banner_width')->clear()->sendKeys(800);
        clsTestBase2::findOneById('banner_height')->clear()->sendKeys(200);
        clsTestBase2::findOneById('banner_margin_top')->clear()->sendKeys(11);

        dump(__LINE__);

        //        sss(1111);

        //        self::assertTrue(clsTestBase2::findOneByXPath("//div[@id='banner_img_id'][contains(@style, '11px')]")!==null);
        //        self::assertTrue(clsTestBase2::findOneByXPath("//div[@id='banner_title_id'][contains(@style, '11px')]")!==null);
        self::assertTrue(clsTestBase2::findOneById('svg_text_banner_name')->getAttribute('font-size') == '30px');
        self::assertTrue(clsTestBase2::findOneById('svg_text_banner_title')->getAttribute('font-size') == '20px');

        dump(__LINE__);

        clsTestBase2::findOneById('select_banner_img1')->click();
        usleep(111);

        dump(__LINE__);

        $fileName = 'banner-bg01.png';
        //        clsTestBase2::findOneByXPath("//img[contains(@data-src, '/images/border-banner-bg1/0-trang-white.png')]")->click();
        clsTestBase2::findOneByXPath("//img[@data-src='/images/border-banner-bg1/$fileName']")->click();
        dump(__LINE__);

        clsTestBase2::findOneByTextName('Đóng lại..')->click();
        //        clsTestBase2::findOneByXPath("//div[@class='ui-dialog-buttonset'][not(@hidden)]")->click();
        dump(__LINE__);

        clsTestBase2::findOneById('save_banner_info1')->click();

        sss(1);

        dump(__LINE__);

        self::assertTrue(clsTestBase2::findOneByXPath("//div[@id='banner_img_id'][contains(@style, '$fileName')]") !== null);
        dump(__LINE__);
        //Chưa kiểm tra fontsize

        $browser->refresh();
        usleep(111);
        self::assertTrue(clsTestBase2::findOneByXPath("//div[@id='banner_img_id'][contains(@style, '$fileName')]") !== null);

        //        self::assertTrue(clsTestBase2::findOneByXPath("//div[@id='banner_img_id'][contains(@style, '11px')]")!==null);
        //        self::assertTrue(clsTestBase2::findOneByXPath("//div[@id='banner_title_id'][contains(@style, '11px')]")!==null);

        clsTestBase2::findOneById('svg_text_path_banner_title')->click();
        usleep(111000);
//                sss(111);
        $browser->assertSee('☰');

        clsTestBase2::findOneContainClassAndVisible('div', 'node_edit_btn_banner')->click();

        //Chắc chắn sau khi rs, thì các giá trị vẫn còn đó:
        self::assertTrue(clsTestBase2::findOneById('banner_name_margin_top')->getAttribute('value') == 11);
        self::assertTrue(clsTestBase2::findOneById('banner_name_curver')->getAttribute('value') == 111);
        self::assertTrue(clsTestBase2::findOneById('banner_title_margin_top')->getAttribute('value') == 11);
        self::assertTrue(clsTestBase2::findOneById('banner_title_curver')->getAttribute('value') == 111);
        self::assertTrue(clsTestBase2::findOneById('banner_width')->getAttribute('value') == 800);
        self::assertTrue(clsTestBase2::findOneById('banner_height')->getAttribute('value') == 200);
        self::assertTrue(clsTestBase2::findOneById('banner_margin_top')->getAttribute('value') == 11);

        dump(__LINE__);
        ///////////////////
        $browser->visit('/member/tree-mng');
        self::assertTrue(clsTestBase2::findOneByXPath("//input[contains(@value, '$name')]") !== null);
        $browser->visit('/member/my-tree-info');
        self::assertTrue(clsTestBase2::findOneByXPath("//input[contains(@value, '$name.123')]") !== null);

        //        $browser->assertSee($title.'.123456');
        $newName = '';

        //        sss(11111);

        GiaPha::withTrashed()->where('name', 'LIKE', 'for_test_del_tree.%')->forceDelete();
        MyTreeInfo::where('name', 'LIKE', 'for_test_del_tree.%')->forceDelete();
    }

    public function testCreateNewMyTree($returnOneCreate = 0)
    {
        //        $this->testLoginTrueAcc();

        self::assertTrue(file_exists('e:\Projects\laravel2022-01\laravel01\tests\anh3.jpg'));

        GiaPha::withTrashed()->where('name', 'LIKE', 'for_test_del_tree.%')->forceDelete();

        $this->testLoginTrueAcc('member@abc.com');

        $uid = Auth::id();


        $drv = $this->getDrv();


//        ['name' => 'cookie_name', 'value' => 'cookie_value']
//        $drv->manage()->addCookie(['_tglx863516839' => Auth::user()->getUserToken()]);

        $tk = Auth::user()->getUserToken();

//        $cc = new \Facebook\WebDriver\Cookie('_tglx863516839', $tk);

//        $cc->setDomain('test2023.galaxycloud.vn');
//        $drv->manage()->addCookie($cc);

        $browser = $this->getBrowserLogined();

        clsTestBase2::$driver = $drv;

        dump("UID = $uid");
        //        die();


//        $browser->addCookie("_tglx863516839", Auth::user()->getUserToken() ,  time() + 3600 * 24 * 180, [], 0);


        $browser->visit('/my-tree');
        $browser->script('document.cookie = "_tglx863516839=' . $tk . '; expires=Wed,31 Dec 2025 01:01:01 GMT; path=/"');
        $browser->refresh();

        $browser->assertSee('Danh sách cây');

        $tk = Auth::user()->getUserToken();

//        sss(200);

        $mm = GiaPha::where(['user_id' => $uid, 'parent_id' => 0])->get();
        foreach ($mm as $obj) {
            if (!$obj->married_with) {
                $browser->assertSee($obj->name);
            }
        }

        $name = 'for_test_del_tree.' . time();
        //
        clsTestBase2::findOneById('first_member_name_of_tree')->sendKeys($name);

        clsTestBase2::findOneById('btn_create_new_tree')->click();

        sleep(2);
        $browser->assertSee("$name");

        $newObj = GiaPha::where(['user_id' => $uid, 'name' => $name])->first();
        if ($returnOneCreate) {
            return $newObj;
        }

        $idEnc = qqgetRandFromId_($newObj->id);
        //        $browser->assertDontSee("☰");
        clsTestBase2::findOneById('svg_cont_node_' . $idEnc)->click();
        $browser->assertSee('☰');

        clsTestBase2::findOneById('id_node_menu_' . $idEnc)->click();

        dump(__LINE__);
        usleep(10000);
        clsTestBase2::findOneByTextName('Thêm con')->click();

        dump(__LINE__);
        usleep(10000);
        clsTestBase2::findOneById('new_name')->sendKeys($name . '-1');
        dump('1113');

        $phone = '0912345678';
        $date = '2011-11-11';
        $email = 'abc@gmail.com';
        $order = '2222';

        dump(__LINE__);

        clsTestBase2::findOneById('new_gender2')->click();
        clsTestBase2::findOneById('new_birthday')->sendKeys($date);
        clsTestBase2::findOneById('new_orders')->sendKeys($order);

        dump(__LINE__);
        clsTestBase2::findOneByClassName('view_more_prop')->click();
        usleep(1000);
        dump(__LINE__);
        clsTestBase2::findOneById('phone_number')->sendKeys($phone);
        dump(__LINE__);
        clsTestBase2::findOneById('email_address')->sendKeys($email);

        $browser->attach('input#file_id', 'e:\Projects\laravel2022-01\laravel01\tests\anh3.jpg');
        dump(__LINE__);

        clsTestBase2::findOneById('save_new_member')->click();

        sss(1);

        //class="node_img"
        //"//span[contains(@class, 'span_auto_complete')]"
        //Chắc chắn là có ảnh hiện lên
        $objThum = clsTestBase2::findOneByXPath("//div[@class='node_img'][contains(@src, '/')]");
        self::assertTrue($objThum != null);

        $imgLink = $objThum->getAttribute('src');

        ///Copy doan nay xuong duoi:
        $browser->assertDontSee('Thuộc tính khác');
        $browser->assertSee($name . '-1');
        clsTestBase2::findOneByTextName($name . '-1')->click();
        clsTestBase2::findOneContainClassAndVisible('div', 'node_edit_btn')->click();
        clsTestBase2::findOneByTextName('Sửa')->click();
        usleep(100000);
        $browser->assertSee('Thuộc tính khác');
        self::assertTrue(clsTestBase2::findOneById('new_name')->getAttribute('value') == $name . '-1');

        dump(__LINE__);
        self::assertTrue(clsTestBase2::findOneById('new_birthday')->getAttribute('value') == $date);
        self::assertTrue(clsTestBase2::findOneById('new_orders')->getAttribute('value') == $order);
        self::assertTrue(clsTestBase2::findOneById('phone_number')->getAttribute('value') == $phone);
        self::assertTrue(clsTestBase2::findOneById('email_address')->getAttribute('value') == $email);

        clsTestBase2::findOneById('close_dlg_node_detail')->click();
        usleep(1000);

        //////////////////////////////////////
        /// //Tạo vợ chồng

        dump(__LINE__);
        clsTestBase2::findOneByTextName($name . '-1')->click();
        usleep(10000);

        dump(__LINE__);
        $browser->assertSee('☰');
        dump('111-3');
        clsTestBase2::findOneContainClassAndVisible('div', 'node_edit_btn')->click();
        usleep(10000);
        clsTestBase2::findOneByTextName('Thêm vợ chồng')->click();

        dump(__LINE__);
        usleep(10000);
        clsTestBase2::findOneById('new_name')->sendKeys($name . '-2');

        dump(__LINE__);
        clsTestBase2::findOneById('new_gender2')->click();
        clsTestBase2::findOneById('new_birthday')->sendKeys('11.11');
        clsTestBase2::findOneById('new_orders')->sendKeys('22.22');

        dump(__LINE__);
        if (!clsTestBase2::findOneById('phone_number')) {
            clsTestBase2::findOneByClassName('view_more_prop')->click();
        }
        usleep(1000);

        dump(__LINE__);

        clsTestBase2::findOneById('phone_number')->sendKeys('33.33');

        dump(__LINE__);
        clsTestBase2::findOneById('email_address')->sendKeys('44.44');

        dump(__LINE__);
        clsTestBase2::findOneById('save_new_member')->click();

        dump(__LINE__);
        sleep(1);

        //        sss(111);

        //////////////////////////////////////
        //Kiểm tra lại sau khi đã add:
        $browser->refresh();

        dump(__LINE__);
        usleep(11111);
        //        sleep(2);
        ///Copy doan nay xuong duoi:
        $browser->assertDontSee('Thuộc tính khác');
        //        self::assertTrue(clsTestBase2::findOneByXPath("//*[contains(@class,'node_name_one') and contains(text(),'$name-2')]") != null);
        //*[@class='myclass' and contains(text(),'qwerty')]

        dump(__LINE__);
        $browser->assertSee($name . '-2');
        $browser->assertSee($name . '-1');

        dump(__LINE__);
        clsTestBase2::findOneByTextName($name . '-1')->click();

        dump(__LINE__);
        //        sss();
        foreach (clsTestBase2::findAllContainClass('div', 'node_edit_btn') as $menu) {
            if ($menu->isDisplayed()) {
                $menu->click();
                break;
            }
        }

        dump(__LINE__);
        clsTestBase2::findOneByTextName('Sửa')->click();
        usleep(100000);
        $browser->assertSee('Thuộc tính khác');
        self::assertTrue(clsTestBase2::findOneById('new_name')->getAttribute('value') == $name . '-1');
        dump('1113');
        self::assertTrue(clsTestBase2::findOneById('new_birthday')->getAttribute('value') == $date);
        self::assertTrue(clsTestBase2::findOneById('new_orders')->getAttribute('value') == $order);
        self::assertTrue(clsTestBase2::findOneById('phone_number')->getAttribute('value') == $phone);
        self::assertTrue(clsTestBase2::findOneById('email_address')->getAttribute('value') == $email);

        //Chắc chắn là có ảnh hiện lên
        self::assertTrue(clsTestBase2::findOneByXPath("//div[@class='node_img'][contains(@src, '/')]") != null);

        //Kiểm tra trong /member/tree-mng sẽ thấy các id mới và ảnh link chính xác:
        $browser->visit('/member/tree-mng');

        dump("imgLink = $imgLink");
        $bnameImg = basename($imgLink);
        self::assertTrue(clsTestBase2::findOneByXPath("//img[contains(@src, '/')]") != null);

        //xóa hết
        GiaPha::withTrashed()->where('name', 'LIKE', 'for_test_del_tree.%')->forceDelete();

    }

    public function testMoveNodeMyTreeWithMouse()
    {
        //        $this->testLoginTrueAcc();
        GiaPha::withTrashed()->where('name', 'LIKE', 'for_test_del_tree.%')->forceDelete();
        $this->testLoginTrueAcc('dungbkhn02@gmail.com');
        $uid = Auth::id();
        $drv = $this->getDrv();
//        ['name' => 'cookie_name', 'value' => 'cookie_value']
//        $drv->manage()->addCookie(['_tglx863516839' => Auth::user()->getUserToken()]);
        $tk = Auth::user()->getUserToken();
//        $cc = new \Facebook\WebDriver\Cookie('_tglx863516839', $tk);
//        $cc->setDomain('test2023.galaxycloud.vn');
//        $drv->manage()->addCookie($cc);
        $browser = $this->getBrowserLogined();
        clsTestBase2::$driver = $drv;
        dump("UID = $uid, $tk");
        //        die();
//        $browser->addCookie("_tglx863516839", Auth::user()->getUserToken() ,  time() + 3600 * 24 * 180, [], 0);
        $browser->visit('/my-tree');
        $browser->maximize();
        $browser->script('document.cookie = "_tglx863516839=' . $tk . '; expires=Wed,31 Dec 2025 01:01:01 GMT; path=/"');
        $browser->script('document.cookie = "_tglx__863516839=' . $tk . '; expires=Wed,31 Dec 2025 01:01:01 GMT; path=/"');
        $browser->refresh();
        $browser->assertSee('Danh sách cây');


        $value = $drv->executeScript('return tree1.spaceBetweenCellY + " , " + tree1.heightCell + ", " + tree1.spaceBetweenCellX + ", " +tree1.widthCell + " | " + tree1.spaceXBetweenCellDevidedBy');

        $widthCell = $drv->executeScript('return tree1.widthCell');
        $spaceXBetweenCellDevidedBy = $drv->executeScript('return tree1.spaceXBetweenCellDevidedBy');

        $oneDonVi = $widthCell / $spaceXBetweenCellDevidedBy;

        dump("V = $oneDonVi = $widthCell / $spaceXBetweenCellDevidedBy");

//        sss(200);
        $mm = GiaPha::where(['user_id' => $uid, 'parent_id' => 0])->get();
        foreach ($mm as $obj) {
            if (!$obj->married_with) {
                $browser->assertSee($obj->name);
            }
        }

        $name = 'for_test_del_tree.' . time();//
        clsTestBase2::findOneById('first_member_name_of_tree')->sendKeys($name);
        clsTestBase2::findOneById('btn_create_new_tree')->click();
        sleep(1);
        $browser->assertSee("$name");

        $newObj = GiaPha::where(['user_id' => $uid, 'name' => $name])->first();
        $idEnc = qqgetRandFromId_($newObj->id);
        //        $browser->assertDontSee("☰");
        clsTestBase2::findOneById('svg_cont_node_' . $idEnc)->click();
//        $browser->assertSee('☰');
        $element1 = clsTestBase2::findOneById("svg_cont_node_$idEnc");
        //Tạo con:
        clsTestBase2::findOneById('id_node_menu_' . $idEnc)->click();
        usleep(10000);
        clsTestBase2::findOneByTextName('Thêm con')->click();
        usleep(10000);

        //Tạo phần tử thứ 2:
        $name1 = $name . '-1';
        clsTestBase2::findOneById('new_name')->sendKeys($name1);
        clsTestBase2::findOneByClassName('view_more_prop')->click();
        clsTestBase2::findOneById('save_new_member')->click();

        usleep(200000);

        $newObj2 = GiaPha::where(['user_id' => $uid, 'name' => $name1])->first();
        $idEnc2 = qqgetRandFromId_($newObj2->id);

        $element2 = clsTestBase2::findOneById("svg_cont_node_$idEnc2");

        $y10 = $element1->getAttribute('y');
        $y20 = $element2->getAttribute('y');
        //Tính khoảng cách Dy
        $dY = intval($y20) - intval($y10);

        /* Đoạn mẫu này di chuyển được node...
        $drv->getKeyboard()->pressKey(WebDriverKeys::CONTROL);
        usleep(100000);
        // Khởi tạo đối tượng Action
        $actions = new WebDriverActions($drv);
        // Di chuyển phần tử bằng cách kéo thả
        $actions->clickAndHold($element)
            ->moveByOffset(1, 1) // di chuyển 100px sang phải và 200px xuống
            ->release()
            ->perform();
        $drv->getMouse()->mouseDown()->mouseMove(null,100,200)->mouseUp();
        */
        $x10 = $element1->getAttribute('x');
        $y10 = $element1->getAttribute('y');
        $x20 = $element2->getAttribute('x');
        $y20 = $element2->getAttribute('y');

        //////////////////////////////////////////////
        //Di chuyen elm1 , kiem tra vi tri ok?
        $moveX1 = 91;
        usleep(100000);

        $drv->getKeyboard()->pressKey(WebDriverKeys::CONTROL);
        usleep(100000);
        // Khởi tạo đối tượng Action
        $actions = new WebDriverActions($drv);

        $actions->click($element1)->perform();
//        $actions->clickAndHold($element1)
//            ->moveByOffset(100, 100)->release()->perform();
        usleep(100000);
        $drv->getKeyboard()->pressKey(WebDriverKeys::CONTROL);
        usleep(100000);
        $drv->getMouse()->mouseDown()->mouseMove(null,$moveX1,round( $dY * 1.1))->mouseUp();
        $drv->getKeyboard()->releaseKey(WebDriverKeys::CONTROL);

        usleep(100000);

        $x11 = $element1->getAttribute('x');
        $y11 = $element1->getAttribute('y');


        $dta = (round($moveX1 / $oneDonVi) * $oneDonVi);

        self::assertTrue($x10 + $dta == $x11 , " $x10 + $dta  == $x11  ");
        self::assertTrue($y10 + $dY == $y11 , " $y10 + $dY == $y11 ");



        //////////////////////////////////////////////
        //Di chuyen elm2 , kiem tra vi tri ok?
        $moveX1 = -66;

        $drv->getKeyboard()->pressKey(WebDriverKeys::CONTROL);
        usleep(200000);
        // Khởi tạo đối tượng Action
        $actions = new WebDriverActions($drv);
        // Di chuyển phần tử bằng cách kéo thả
        $actions->clickAndHold($element2)
            ->moveByOffset(0, 1)->release()->perform();
        usleep(100000);
        $drv->getMouse()->mouseDown()->mouseMove(null,$moveX1,round(- $dY * 1.1))->mouseUp();
        $drv->getKeyboard()->releaseKey(WebDriverKeys::CONTROL);


        $x21 = $element2->getAttribute('x');
        $y21 = $element2->getAttribute('y');
        self::assertTrue($x20 + (round($moveX1 / $oneDonVi) * $oneDonVi) == $x21 , " $x20 + $moveX1  == $x21  ");
        self::assertTrue($y20 - $dY == $y21 , " $y20 + $dY == $y21 ");

        //////////////////////////////////////////////
        //Di chuyen cả elm1 + elm2 , kiem tra vi tri ok?
        //Esc để clear select...
        $drv->getKeyboard()->pressKey(WebDriverKeys::ESCAPE);
        $drv->getKeyboard()->releaseKey(WebDriverKeys::ESCAPE);
        usleep(100000);

        $x13 = $element1->getAttribute('x');
        $y13 = $element1->getAttribute('y');
        $x23 = $element2->getAttribute('x');
        $y23 = $element2->getAttribute('y');

        $dY = - $dY;
        $moveX1 = 151;
        $moveY1 = round($dY * 1.4);
        usleep(100000);
        // Khởi tạo đối tượng Action
        $actions = new WebDriverActions($drv);
        // Di chuyển phần tử bằng cách kéo thả
        $drv->getKeyboard()->pressKey(WebDriverKeys::CONTROL);
        $actions->click($element2)->perform();
        usleep(100000);
        $actions = new WebDriverActions($drv);
        $actions->click($element1)->perform();
        $drv->getKeyboard()->releaseKey(WebDriverKeys::CONTROL);
        usleep(100000);
//        $actions = new WebDriverActions($drv);
        $drv->getKeyboard()->pressKey(WebDriverKeys::CONTROL);
//        $actions->click($element2)->perform();
        usleep(100000);
        $drv->getMouse()->mouseDown()->mouseMove(null,$moveX1,$moveY1)->mouseUp();
        $drv->getKeyboard()->releaseKey(WebDriverKeys::CONTROL);

        $x14 = $element1->getAttribute('x');
        $y14 = $element1->getAttribute('y');
        $x24 = $element2->getAttribute('x');
        $y24 = $element2->getAttribute('y');



        self::assertTrue($x13 + (round($moveX1 / $oneDonVi) * $oneDonVi) == $x14 , "$moveX1:  $x13 + ".(round($moveX1 / $oneDonVi) * $oneDonVi)."  == $x14  ");
        self::assertTrue($x23 + (round($moveX1 / $oneDonVi) * $oneDonVi) < $x24 + $oneDonVi &&
            $x23 + (round($moveX1 / $oneDonVi) * $oneDonVi) > $x24 - $oneDonVi
            , "$moveX1:  $x23 + ".(round($moveX1 / $oneDonVi) * $oneDonVi)."  == $x24  ");
//        self::assertTrue($y23 + $dY == $y24 , " $y23 + $dY == $y24 ");
        self::assertTrue($y13 + $dY == $y14 , " $y13 + $dY == $y14 ");


//        sss(1000);
//        sss(60);
        ////////////////Refresh de dam bao da ghi vao DB
        $browser->refresh();
//        sss(100);
        $element1 = clsTestBase2::findOneById("svg_cont_node_$idEnc");
        $element2 = clsTestBase2::findOneById("svg_cont_node_$idEnc2");
        $x14 = $element1->getAttribute('x');
        $y14 = $element1->getAttribute('y');
        $x24 = $element2->getAttribute('x');
        $y24 = $element2->getAttribute('y');

        self::assertTrue($x13 + (round($moveX1 / $oneDonVi) * $oneDonVi) == $x14 , " $x13 + ". (round($moveX1 / $oneDonVi) * $oneDonVi) ."  == $x14  ");
        self::assertTrue($x23 + (round($moveX1 / $oneDonVi) * $oneDonVi) == $x24 , " $x23 + ".(round($moveX1 / $oneDonVi) * $oneDonVi)."  == $x24  ");
        self::assertTrue($y23 == $y24 , " $y23 == $y24 ");
//        self::assertTrue($y13 + $dY == $y14 , " $y13 + $dY == $y14 ");

        //xóa hết
        GiaPha::withTrashed()->where('name', 'LIKE', 'for_test_del_tree.%')->forceDelete();
    }

    public function testMoveItemNodeHtmlOK()
    {
        $this->testLoginTrueAcc('dungbkhn02@gmail.com');
        $driver = $this->getDrv();
        // Navigate to the page
        $driver->get('http://10.0.0.28:5501/008-click%20ctrl%20th%C3%AC%20di%20chuy%E1%BB%83n%20t%E1%BB%ABng%20ph%E1%BA%A7n%20t%E1%BB%AD.html');
// Find and select the element to drag and drop
        $element = $driver->findElement(WebDriverBy::id('rect2'));
// Create WebDriverActions object
        $actions = new WebDriverActions($driver);
// Drag and drop the element while holding down the Ctrl key
//        $actions
////            ->keyDown($element, WebDriverKeys::CONTROL)
//            ->clickAndHold($element)
//            ->moveByOffset(100, 200)
//            ->release()
////            ->keyUp($element,WebDriverKeys::CONTROL)
//            ->perform();

        $actions
            ->keyDown($element, WebDriverKeys::CONTROL)->perform();

        sss(1);
        $actions->clickAndHold($element)
            ->moveByOffset(10, 10)
            ->release()
//            ->keyUp($element,WebDriverKeys::CONTROL)
            ->perform();
        sss(1);
        $actions->clickAndHold($element)
            ->moveByOffset(10, 10)
            ->release()
//            ->keyUp($element,WebDriverKeys::CONTROL)
            ->perform();

//        $actions
//            ->keyUp($element, WebDriverKeys::CONTROL)->perform();

//        sss(1);
// Close the browser
        $driver->quit();
    }
}
