<?php

namespace Tests\Browser;

use App\Models\User;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Support\Facades\Auth;
use LadLib\Common\Tester\clsTestBase2;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DuskTestCaseBase extends DuskTestCase
{
    //False là tắt trình duyệt
    protected function hasHeadlessDisabled(): bool
    {
        return false;
        //        return true;
    }

    /**
     * @var Browser
     */
    public static $browsersAfterLogin;

    /**
     * @return Browser
     */
    public function getBrowserLogined()
    {
        return self::$browsersAfterLogin;
    }

    public function openFilterButton()
    {
        $btnOpen = clsTestBase2::findOneById('add_field_btn_filter');
        if(! $btnOpen) return;
        $txt = trim($btnOpen->getText());
        if (! strstr($txt, 'Thu gọn')) {
            $btnOpen->click();
        }
    }

    /**
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    public function getDrv()
    {
        //        self::$browsersAfterLogin->driver->manage()->addCookie('123');
        return self::$browsersAfterLogin->driver;
    }

    public function testLoginTrueAcc($emailOrUid = null)
    {
        $user = new User();
        if ($emailOrUid) {
            if (! is_numeric($emailOrUid)) {
                $user = User::where('email', $emailOrUid)->first();
            } else {
                $user = User::find($emailOrUid);
            }
        } else {
            $user = User::where('email', 'admin@abc.com')->first();
        }

        if (! $user) {
            exit("\n\n Not found user $emailOrUid ");
        }

        $this->assertTrue($user != null);

        //$user->email = 'admin@abc.com';
        $brsReturn = null;

        $this->browse(function ($browser) use ($user, &$brsReturn) {

            if ($browser instanceof Browser);

            //            $browser->visit('/login')
            //                ->type('email', $user->email)
            //                ->type('password', '111111')
            //                ->press('submit')->assertPathIs('/admin')
            //                ->visit('/admin');

            $browser->driver->manage()->deleteAllCookies(); // Xóa tất cả các cookie trước đó (nếu cần)

            $browser->loginAS($user);

            Auth::login($user);

            //zzzzzz 23.3.2024: bỏ cái này đi thì ok, nếu ko sẽ bị báo lỗi : invalid cookie domain
            //12.4: Bỏ đi thì lại lỗi tester, ko set cookie api được
            // Fix: Set cookie properly with correct path syntax
            try {
                $browser->driver->manage()->addCookie([
                    'name' => '_tglx863516839',
                    'value' => $user->getUserToken(),
                    'expiry' => time() + 3600 * 24 * 180,
                    'path' => '/',
                    'domain' => 'localhost',
                    'secure' => false,
                    'httpOnly' => false,
                ]);
            } catch (\Exception $e) {
                // If cookie domain fails, continue without it
                // The loginAS() method should handle authentication
            }

            $brsReturn = $browser;

            self::$browsersAfterLogin = $browser;

            if ($browser instanceof Browser);

        });

        return $brsReturn;

        //        $this->browse(function ($first, $second) {
        //            $first->loginAs(User::where('email', 'admin@abc.com')->first())
        //                ->visit('/home');
        //        });
    }

    /**
     * Wait for element with given XPath to appear
     *
     * @param string $xpath XPath expression
     * @param int $seconds Timeout in seconds
     * @return void
     */
    public function waitForXPath(string $xpath, int $seconds = 5)
    {
        $browser = self::$browsersAfterLogin;
        if (!$browser) {
            throw new \Exception("No browser instance available");
        }

        $browser->waitUsing($seconds, 100, function () use ($xpath) {
            try {
                $element = $this->getDrv()->findElement(WebDriverBy::xpath($xpath));
                return $element !== null && $element->isDisplayed();
            } catch (\Exception $e) {
                return false;
            }
        });
    }

    /**
     * Find element by XPath
     *
     * @param string $xpath XPath expression
     * @return \Facebook\WebDriver\WebDriverElement|null
     */
    public function findByXPath(string $xpath)
    {
        try {
            return $this->getDrv()->findElement(WebDriverBy::xpath($xpath));
        } catch (\Exception $e) {
            return null;
        }
    }
}
