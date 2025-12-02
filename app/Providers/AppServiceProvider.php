<?php

namespace App\Providers;

use App\Components\Recusive;
use App\Components\Route2;
use App\Http\ControllerApi\DemoControllerApi;
use App\Http\ControllerApi\UserControllerApi;
use App\Models\Category;
use App\Models\DemoTbl;
use App\Models\User;
use App\Repositories\OrderItemRepositoryInterface;
use App\Repositories\OrderItemRepositorySql;
use App\Repositories\BlockUiRepositoryInterface;
use App\Repositories\BlockUiRepositorySql;
use App\Repositories\ChangeLogRepositoryInterface;
use App\Repositories\ChangeLogRepositorySql;
use App\Repositories\DemoFolderRepositoryInterface;
use App\Repositories\DemoFolderRepositorySql;
use App\Repositories\DemoMgRepositoryInterface;
use App\Repositories\DemoMgRepositoryMg;
use App\Repositories\DemoRepositoryInterface;
use App\Repositories\EventPaymentRepositoryInterface;

use App\Repositories\MonitorSettingRepositoryInterface;

use App\Repositories\MonitorConfigRepositoryInterface;

use App\Repositories\PlanDefineValueRepositoryInterface;

use App\Repositories\PlanDefineRepositoryInterface;

use App\Repositories\CrmAppInfoRepositoryInterface;

use App\Repositories\CrmMessageGroupRepositoryInterface;

use App\Repositories\PlanNameRepositoryInterface;

use App\Repositories\PlanCostItemRepositoryInterface;

use App\Repositories\EventFaceInfoRepositoryInterface;

use App\Repositories\FaceDataRepositoryInterface;

use App\Repositories\EventUserPaymentRepositoryInterface;

use App\Repositories\TestMongo1RepositoryInterface;

use App\Repositories\CrmMessageRepositoryInterface;

use App\Repositories\CrmMessagesRepositoryInterface;

use App\Repositories\MediaVendorRepositoryInterface;

use App\Repositories\MediaActorRepositoryInterface;

use App\Repositories\MediaLinkRepositoryInterface;

use App\Repositories\MediaAuthorRepositoryInterface;

use App\Repositories\MediaCatRepositoryInterface;

use App\Repositories\MediaItemRepositoryInterface;

use App\Repositories\TaskInfoRepositoryInterface;

use App\Repositories\UploaderInfoRepositoryInterface;

use App\Repositories\AffiliateLogRepositoryInterface;

use App\Repositories\PaymentRepositoryInterface;

use App\Repositories\CartItemRepositoryInterface;

use App\Repositories\CartRepositoryInterface;

use App\Repositories\EventSettingRepositoryInterface;

use App\Repositories\DepartmentEventRepositoryInterface;

use App\Repositories\DepartmentUserRepositoryInterface;

use App\Repositories\ProductUsageRepositoryInterface;

use App\Repositories\ProductAttributeRepositoryInterface;

use App\Repositories\AssetCategoryRepositoryInterface;

use App\Repositories\AssetsRepositoryInterface;

use App\Repositories\TagDemoRepositoryInterface;
use App\Repositories\TagRepositoryInterface;
use App\Repositories\DepartmentRepositoryInterface;

use App\Repositories\FileSharePermissionRepositoryInterface;

use App\Repositories\EventRegisterRepositoryInterface;

use App\Repositories\ConferenceCatRepositoryInterface;

use App\Repositories\ConferenceInfoRepositoryInterface;

use App\Repositories\TmpDownloadSessionRepositoryInterface;

use App\Repositories\CloudServerRepositoryInterface;

use App\Repositories\NotificationRepositoryInterface;

use App\Repositories\PayMoneylogRepositoryInterface;

use App\Repositories\DownloadLogRepositoryInterface;

use App\Repositories\DemoRepositorySql;
use App\Repositories\EventPaymentRepositorySql;

use App\Repositories\MonitorSettingRepositorySql;

use App\Repositories\MonitorConfigRepositorySql;

use App\Repositories\PlanDefineValueRepositorySql;

use App\Repositories\PlanDefineRepositorySql;

use App\Repositories\CrmAppInfoRepositorySql;

use App\Repositories\CrmMessageGroupRepositorySql;

use App\Repositories\PlanNameRepositorySql;

use App\Repositories\PlanCostItemRepositorySql;

use App\Repositories\EventFaceInfoRepositorySql;

use App\Repositories\FaceDataRepositorySql;

use App\Repositories\EventUserPaymentRepositorySql;

use App\Repositories\TestMongo1RepositorySql;

use App\Repositories\CrmMessageRepositorySql;

use App\Repositories\CrmMessagesRepositorySql;

use App\Repositories\MediaVendorRepositorySql;

use App\Repositories\MediaActorRepositorySql;

use App\Repositories\MediaLinkRepositorySql;

use App\Repositories\MediaAuthorRepositorySql;

use App\Repositories\MediaCatRepositorySql;

use App\Repositories\MediaItemRepositorySql;

use App\Repositories\TaskInfoRepositorySql;

use App\Repositories\UploaderInfoRepositorySql;

use App\Repositories\AffiliateLogRepositorySql;

use App\Repositories\PaymentRepositorySql;

use App\Repositories\CartItemRepositorySql;

use App\Repositories\CartRepositorySql;

use App\Repositories\EventSettingRepositorySql;

use App\Repositories\DepartmentEventRepositorySql;

use App\Repositories\DepartmentUserRepositorySql;

use App\Repositories\ProductUsageRepositorySql;

use App\Repositories\ProductAttributeRepositorySql;

use App\Repositories\AssetCategoryRepositorySql;

use App\Repositories\AssetsRepositorySql;

use App\Repositories\DepartmentRepositorySql;

use App\Repositories\FileSharePermissionRepositorySql;

use App\Repositories\EventRegisterRepositorySql;

use App\Repositories\ConferenceCatRepositorySql;

use App\Repositories\ConferenceInfoRepositorySql;

use App\Repositories\TmpDownloadSessionRepositorySql;

use App\Repositories\CloudServerRepositorySql;

use App\Repositories\NotificationRepositorySql;

use App\Repositories\PayMoneylogRepositorySql;

use App\Repositories\DownloadLogRepositorySql;

use App\Repositories\DonViHanhChinhRepositoryInterface;
use App\Repositories\DonViHanhChinhRepositorySql;
use App\Repositories\EventAndUserRepositoryInterface;
use App\Repositories\EventAndUserRepositorySql;
use App\Repositories\EventInfoRepositoryInterface;
use App\Repositories\EventInfoRepositorySql;
use App\Repositories\EventSendActionRepositoryInterface;
use App\Repositories\EventSendActionRepositorySql;
use App\Repositories\EventSendInfoLogRepositoryInterface;
use App\Repositories\EventSendInfoLogRepositorySql;
use App\Repositories\EventUserGroupRepositoryInterface;
use App\Repositories\EventUserGroupRepositorySql;
use App\Repositories\EventUserInfoRepositoryInterface;
use App\Repositories\EventUserInfoRepositorySql;
use App\Repositories\FileCloudRepositoryInterface;
use App\Repositories\FileCloudRepositorySql;
use App\Repositories\FileReferRepositoryInterface;
use App\Repositories\FileReferRepositorySql;
use App\Repositories\FileRepositoryInterface;
use App\Repositories\FileRepositorySql;
use App\Repositories\FolderFileRepositoryInterface;
use App\Repositories\FolderFileRepositorySql;
use App\Repositories\HatecoCertificateRepositoryInterface;
use App\Repositories\HatecoCertificateRepositorySql;
use App\Repositories\HrConfigSessionOrgIdSalaryRepositoryInterface;
use App\Repositories\HrConfigSessionOrgIdSalaryRepositorySql;
use App\Repositories\HrContractRepositoryInterface;
use App\Repositories\HrContractRepositorySql;
use App\Repositories\HrEmployeeRepositoryInterface;
use App\Repositories\HrEmployeeRepositorySql;
use App\Repositories\HrExpenseColMngRepositoryInterface;
use App\Repositories\HrExpenseColMngRepositorySql;
use App\Repositories\HrExtraCostEmployeeRepositoryInterface;
use App\Repositories\HrExtraCostEmployeeRepositorySql;
use App\Repositories\HrJobRepositoryInterface;
use App\Repositories\HrJobRepositorySql;
use App\Repositories\HrJobTitleRepositoryInterface;
use App\Repositories\HrJobTitleRepositorySql;
use App\Repositories\HrKpiCldvRepositoryInterface;
use App\Repositories\HrKpiCldvRepositorySql;
use App\Repositories\HrLogTaskRepositoryInterface;
use App\Repositories\HrLogTaskRepositorySql;
use App\Repositories\HrMessageTaskRepositoryInterface;
use App\Repositories\HrMessageTaskRepositorySql;
use App\Repositories\HrOrgSettingRepositoryInterface;
use App\Repositories\HrOrgSettingRepositorySql;
use App\Repositories\HrOrgTreeRepositoryInterface;
use App\Repositories\HrOrgTreeRepositorySql;
use App\Repositories\HrSalaryMonthUserRepositoryInterface;
use App\Repositories\HrSalaryMonthUserRepositorySql;
use App\Repositories\HrSalaryRepositoryInterface;
use App\Repositories\HrSalaryRepositorySql;
use App\Repositories\HrSampleTimeEventRepositoryInterface;
use App\Repositories\HrSampleTimeEventRepositorySql;
use App\Repositories\HrSessionTypeRepositoryInterface;
use App\Repositories\HrSessionTypeRepositorySql;
use App\Repositories\HrTaskRepositoryInterface;
use App\Repositories\HrTaskRepositorySql;
use App\Repositories\HrTimeSheetRepositoryInterface;
use App\Repositories\HrTimeSheetRepositorySql;
use App\Repositories\HrUserExpenseRepositoryInterface;
use App\Repositories\HrUserExpenseRepositorySql;
use App\Repositories\LogUserRepositoryInterface;
use App\Repositories\LogUserRepositorySql;
use App\Repositories\MediaFolderRepositoryInterface;
use App\Repositories\MediaFolderRepositorySql;
//use App\Repositories\MediaItemRepositoryInterface;
////use App\Repositories\MediaItemRepositorySql;
//use App\Repositories\MediaLinkRepositoryInterface;
//use App\Repositories\MediaLinkRepositorySql;
use App\Repositories\MenuTreeRepositoryInterface;
use App\Repositories\MenuTreeRepositorySql;
use App\Repositories\MoneyAndTagRepositoryInterface;
use App\Repositories\MoneyAndTagRepositorySql;
use App\Repositories\MoneyLogRepositoryInterface;
use App\Repositories\MoneyLogRepositorySql;
use App\Repositories\MoneyTagRepositoryInterface;
use App\Repositories\MoneyTagRepositorySql;
use App\Repositories\MonitorItemRepositoryInterface;
use App\Repositories\MonitorItemRepositorySql;
use App\Repositories\MyDocumentCatRepositoryInterface;
use App\Repositories\MyDocumentCatRepositorySql;
use App\Repositories\MyDocumentRepositoryInterface;
use App\Repositories\MyDocumentRepositorySql;
use App\Repositories\MyTreeInfoRepositoryInterface;
use App\Repositories\MyTreeInfoRepositorySql;
use App\Repositories\NetworkMarketingRepositoryInterface;
use App\Repositories\NetworkMarketingRepositorySql;
use App\Repositories\NewsFolderRepositoryInterface;
use App\Repositories\NewsFolderRepositorySql;
use App\Repositories\NewsRepositoryInterface;
use App\Repositories\NewsRepositorySql;
use App\Repositories\OcrImageRepositoryInterface;
use App\Repositories\OcrImageRepositorySql;
use App\Repositories\OrderInfoRepositoryInterface;
use App\Repositories\OrderInfoRepositorySql;
use App\Repositories\OrderShipRepositoryInterface;
use App\Repositories\OrderShipRepositorySql;
use App\Repositories\PartnerInfoRepositoryInterface;
use App\Repositories\PartnerInfoRepositorySql;
use App\Repositories\ProductFolderRepositoryInterface;
use App\Repositories\ProductFolderRepositorySql;
use App\Repositories\ProductRepositoryInterface;
use App\Repositories\ProductRepositorySql;
use App\Repositories\ProductVariantOptionRepositoryInterface;
use App\Repositories\ProductVariantOptionRepositorySql;
use App\Repositories\ProductVariantRepositoryInterface;
use App\Repositories\ProductVariantRepositorySql;
use App\Repositories\QuizChoiceRepositoryInterface;
use App\Repositories\QuizChoiceRepositorySql;
use App\Repositories\QuizClassRepositoryInterface;
use App\Repositories\QuizClassRepositorySql;
use App\Repositories\QuizFlashCardRepositoryInterface;
use App\Repositories\QuizFlashCardRepositorySql;
use App\Repositories\QuizFolderRepositoryInterface;
use App\Repositories\QuizFolderRepositorySql;
use App\Repositories\QuizQuestionRepositoryInterface;
use App\Repositories\QuizQuestionRepositorySql;
use App\Repositories\QuizSessionInfoTestRepositoryInterface;
use App\Repositories\QuizSessionInfoTestRepositorySql;
use App\Repositories\QuizTestQuestionRepositoryInterface;
use App\Repositories\QuizTestQuestionRepositorySql;
use App\Repositories\QuizTestRepositoryInterface;
use App\Repositories\QuizTestRepositorySql;
use App\Repositories\QuizUserAndTestRepositoryInterface;
use App\Repositories\QuizUserAndTestRepositorySql;
use App\Repositories\QuizUserAnswerRepositoryInterface;
use App\Repositories\QuizUserAnswerRepositorySql;
use App\Repositories\QuizUserClassRepositoryInterface;
use App\Repositories\QuizUserClassRepositorySql;
use App\Repositories\RequestInfoRepositoryInterface;
use App\Repositories\RequestInfoRepositorySql;
use App\Repositories\RoleUserRepositoryInterface;
use App\Repositories\RoleUserRepositorySql;
use App\Repositories\SiteMngRepositoryInterface;
use App\Repositories\SiteMngRepositorySql;
use App\Repositories\SkuRepositoryInterface;
use App\Repositories\SkuRepositorySql;
use App\Repositories\SkusProductVariantOptionRepositoryInterface;
use App\Repositories\SkusProductVariantOptionRepositorySql;
use App\Repositories\SpendingRepositoryInterface;
use App\Repositories\SpendingRepositorySql;

use App\Repositories\TagRepositorySql;
use App\Repositories\TagDemoRepositorySql;
use App\Repositories\TelesaleRepositoryInterface;
use App\Repositories\TelesaleRepositorySql;
use App\Repositories\Todo2RepositoryInterface;
use App\Repositories\Todo2RepositorySql;
use App\Repositories\TransportInfoRepositoryInterface;
use App\Repositories\TransportInfoRepositorySql;
use App\Repositories\TreeMngColFixRepositoryInterface;
use App\Repositories\TreeMngColFixRepositorySql;
use App\Repositories\TreeMngRepositoryInterface;
use App\Repositories\TreeMngRepositorySql;
use App\Repositories\TreeMngUserRepositoryInterface;
use App\Repositories\TreeMngUserRepositorySql;
use App\Repositories\TypingLessonRepositoryInterface;
use App\Repositories\TypingLessonRepositorySql;
use App\Repositories\TypingTestResultRepositoryInterface;
use App\Repositories\TypingTestResultRepositorySql;
use App\Repositories\UserCloudRepositoryInterface;
use App\Repositories\UserCloudRepositorySql;
use App\Repositories\UserGroupRepositoryInterface;
use App\Repositories\UserGroupRepositorySql;
use App\Repositories\UserRepositoryInterface;
use App\Repositories\UserRepositorySql;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->isLocal()) {
//            $this->app->register(TelescopeServiceProvider::class);
        }

        $ipLad = @file_get_contents('/var/glx/weblog/myip_ok.txt');
        if ($ipLad) {
            $ipLad = explode('#', $ipLad)[0];
        }

        //https://stackoverflow.com/questions/54905292/laravel-debugger-enable-disable-depending-on-ip-address-while-caching-implemen
        $allowedIps = ['127.0.0.1']; // Thay thế bằng IP bạn muốn cho phép
        if ($ipLad) {
            $allowedIps = ['127.0.0.1', $ipLad, '58.186.44.168'];
        }


        //zzzzzz lad allow ip debug
        if (in_array(Request::ip(), $allowedIps)) {
            config(['app.debug' => true]);
        }

        config(['app.debug' => true]);

        //$this->app->bind(UserRepositoryInterface::class, DbUserRepository::class);

        //Bind auto model với Controller, để sử dụng trong api:
        //        $this->app->bind('model_with_controller_'.DemoControllerApi::class, DemoTbl::class);
        //        $this->app->bind('model_with_controller_'.UserControllerApi::class, User::class);

        $this->app->bind(DemoRepositoryInterface::class, DemoRepositorySql::class);
$this->app->bind(EventPaymentRepositoryInterface::class, EventPaymentRepositorySql::class);

$this->app->bind(MonitorSettingRepositoryInterface::class, MonitorSettingRepositorySql::class);

$this->app->bind(MonitorConfigRepositoryInterface::class, MonitorConfigRepositorySql::class);

$this->app->bind(PlanDefineValueRepositoryInterface::class, PlanDefineValueRepositorySql::class);

$this->app->bind(PlanDefineRepositoryInterface::class, PlanDefineRepositorySql::class);

$this->app->bind(CrmAppInfoRepositoryInterface::class, CrmAppInfoRepositorySql::class);

$this->app->bind(CrmMessageGroupRepositoryInterface::class, CrmMessageGroupRepositorySql::class);

$this->app->bind(PlanNameRepositoryInterface::class, PlanNameRepositorySql::class);

$this->app->bind(PlanCostItemRepositoryInterface::class, PlanCostItemRepositorySql::class);

$this->app->bind(EventFaceInfoRepositoryInterface::class, EventFaceInfoRepositorySql::class);

$this->app->bind(FaceDataRepositoryInterface::class, FaceDataRepositorySql::class);

$this->app->bind(EventUserPaymentRepositoryInterface::class, EventUserPaymentRepositorySql::class);

$this->app->bind(TestMongo1RepositoryInterface::class, TestMongo1RepositorySql::class);

$this->app->bind(CrmMessageRepositoryInterface::class, CrmMessageRepositorySql::class);

$this->app->bind(CrmMessagesRepositoryInterface::class, CrmMessagesRepositorySql::class);

$this->app->bind(MediaVendorRepositoryInterface::class, MediaVendorRepositorySql::class);

$this->app->bind(MediaActorRepositoryInterface::class, MediaActorRepositorySql::class);

$this->app->bind(MediaLinkRepositoryInterface::class, MediaLinkRepositorySql::class);

$this->app->bind(MediaAuthorRepositoryInterface::class, MediaAuthorRepositorySql::class);

$this->app->bind(MediaCatRepositoryInterface::class, MediaCatRepositorySql::class);

$this->app->bind(MediaItemRepositoryInterface::class, MediaItemRepositorySql::class);

$this->app->bind(TaskInfoRepositoryInterface::class, TaskInfoRepositorySql::class);

$this->app->bind(UploaderInfoRepositoryInterface::class, UploaderInfoRepositorySql::class);

$this->app->bind(AffiliateLogRepositoryInterface::class, AffiliateLogRepositorySql::class);

$this->app->bind(PaymentRepositoryInterface::class, PaymentRepositorySql::class);

$this->app->bind(CartItemRepositoryInterface::class, CartItemRepositorySql::class);

$this->app->bind(CartRepositoryInterface::class, CartRepositorySql::class);

$this->app->bind(EventSettingRepositoryInterface::class, EventSettingRepositorySql::class);

$this->app->bind(DepartmentEventRepositoryInterface::class, DepartmentEventRepositorySql::class);

$this->app->bind(DepartmentUserRepositoryInterface::class, DepartmentUserRepositorySql::class);

$this->app->bind(ProductUsageRepositoryInterface::class, ProductUsageRepositorySql::class);

$this->app->bind(ProductAttributeRepositoryInterface::class, ProductAttributeRepositorySql::class);

$this->app->bind(AssetCategoryRepositoryInterface::class, AssetCategoryRepositorySql::class);

$this->app->bind(AssetsRepositoryInterface::class, AssetsRepositorySql::class);

$this->app->bind(DepartmentRepositoryInterface::class, DepartmentRepositorySql::class);

$this->app->bind(FileSharePermissionRepositoryInterface::class, FileSharePermissionRepositorySql::class);

$this->app->bind(EventRegisterRepositoryInterface::class, EventRegisterRepositorySql::class);

$this->app->bind(ConferenceCatRepositoryInterface::class, ConferenceCatRepositorySql::class);

$this->app->bind(ConferenceInfoRepositoryInterface::class, ConferenceInfoRepositorySql::class);

$this->app->bind(TmpDownloadSessionRepositoryInterface::class, TmpDownloadSessionRepositorySql::class);

$this->app->bind(CloudServerRepositoryInterface::class, CloudServerRepositorySql::class);

$this->app->bind(NotificationRepositoryInterface::class, NotificationRepositorySql::class);

$this->app->bind(PayMoneylogRepositoryInterface::class, PayMoneylogRepositorySql::class);

$this->app->bind(DownloadLogRepositoryInterface::class, DownloadLogRepositorySql::class);

        $this->app->bind(FileReferRepositoryInterface::class, FileReferRepositorySql::class);

        $this->app->bind(EventSendActionRepositoryInterface::class, EventSendActionRepositorySql::class);

        $this->app->bind(EventSendInfoLogRepositoryInterface::class, EventSendInfoLogRepositorySql::class);

        $this->app->bind(EventAndUserRepositoryInterface::class, EventAndUserRepositorySql::class);

        $this->app->bind(EventInfoRepositoryInterface::class, EventInfoRepositorySql::class);

        $this->app->bind(EventUserInfoRepositoryInterface::class, EventUserInfoRepositorySql::class);

        $this->app->bind(EventUserGroupRepositoryInterface::class, EventUserGroupRepositorySql::class);

        $this->app->bind(MediaLinkRepositoryInterface::class, MediaLinkRepositorySql::class);

        $this->app->bind(MediaFolderRepositoryInterface::class, MediaFolderRepositorySql::class);

        $this->app->bind(MediaItemRepositoryInterface::class, MediaItemRepositorySql::class);

        $this->app->bind(MyDocumentCatRepositoryInterface::class, MyDocumentCatRepositorySql::class);

        $this->app->bind(MyDocumentRepositoryInterface::class, MyDocumentRepositorySql::class);

        $this->app->bind(UserGroupRepositoryInterface::class, UserGroupRepositorySql::class);

        $this->app->bind(QuizUserClassRepositoryInterface::class, QuizUserClassRepositorySql::class);

        $this->app->bind(QuizClassRepositoryInterface::class, QuizClassRepositorySql::class);

        $this->app->bind(QuizSessionInfoTestRepositoryInterface::class, QuizSessionInfoTestRepositorySql::class);

        $this->app->bind(QuizFolderRepositoryInterface::class, QuizFolderRepositorySql::class);

        $this->app->bind(RoleUserRepositoryInterface::class, RoleUserRepositorySql::class);

        $this->app->bind(MonitorItemRepositoryInterface::class, MonitorItemRepositorySql::class);

        $this->app->bind(HatecoCertificateRepositoryInterface::class, HatecoCertificateRepositorySql::class);

        $this->app->bind(HrExpenseColMngRepositoryInterface::class, HrExpenseColMngRepositorySql::class);

        $this->app->bind(HrKpiCldvRepositoryInterface::class, HrKpiCldvRepositorySql::class);

        $this->app->bind(QuizFlashCardRepositoryInterface::class, QuizFlashCardRepositorySql::class);

        $this->app->bind(HrOrgSettingRepositoryInterface::class, HrOrgSettingRepositorySql::class);

        $this->app->bind(HrSessionTypeRepositoryInterface::class, HrSessionTypeRepositorySql::class);

        $this->app->bind(HrConfigSessionOrgIdSalaryRepositoryInterface::class, HrConfigSessionOrgIdSalaryRepositorySql::class);

        $this->app->bind(HrUserExpenseRepositoryInterface::class, HrUserExpenseRepositorySql::class);

        $this->app->bind(HrSampleTimeEventRepositoryInterface::class, HrSampleTimeEventRepositorySql::class);

        $this->app->bind(ProductFolderRepositoryInterface::class, ProductFolderRepositorySql::class);

        $this->app->bind(HrExtraCostEmployeeRepositoryInterface::class, HrExtraCostEmployeeRepositorySql::class);

        $this->app->bind(TypingLessonRepositoryInterface::class, TypingLessonRepositorySql::class);

        $this->app->bind(TypingTestResultRepositoryInterface::class, TypingTestResultRepositorySql::class);

        $this->app->bind(HrSalaryMonthUserRepositoryInterface::class, HrSalaryMonthUserRepositorySql::class);

        $this->app->bind(HrSalaryRepositoryInterface::class, HrSalaryRepositorySql::class);

        $this->app->bind(LogUserRepositoryInterface::class, LogUserRepositorySql::class);

        $this->app->bind(HrTimeSheetRepositoryInterface::class, HrTimeSheetRepositorySql::class);

        $this->app->bind(HrOrgTreeRepositoryInterface::class, HrOrgTreeRepositorySql::class);

        $this->app->bind(HrMessageTaskRepositoryInterface::class, HrMessageTaskRepositorySql::class);

        $this->app->bind(QuizUserAndTestRepositoryInterface::class, QuizUserAndTestRepositorySql::class);

        $this->app->bind(HrJobTitleRepositoryInterface::class, HrJobTitleRepositorySql::class);

        $this->app->bind(HrLogTaskRepositoryInterface::class, HrLogTaskRepositorySql::class);

        $this->app->bind(HrJobRepositoryInterface::class, HrJobRepositorySql::class);

        $this->app->bind(HrTaskRepositoryInterface::class, HrTaskRepositorySql::class);

        $this->app->bind(HrContractRepositoryInterface::class, HrContractRepositorySql::class);

        $this->app->bind(HrJobRepositoryInterface::class, HrJobRepositorySql::class);

        $this->app->bind(HrEmployeeRepositoryInterface::class, HrEmployeeRepositorySql::class);

        $this->app->bind(QuizTestRepositoryInterface::class, QuizTestRepositorySql::class);

        $this->app->bind(QuizTestQuestionRepositoryInterface::class, QuizTestQuestionRepositorySql::class);

        $this->app->bind(QuizUserAnswerRepositoryInterface::class, QuizUserAnswerRepositorySql::class);

        $this->app->bind(QuizChoiceRepositoryInterface::class, QuizChoiceRepositorySql::class);

        $this->app->bind(OcrImageRepositoryInterface::class, OcrImageRepositorySql::class);

        $this->app->bind(QuizQuestionRepositoryInterface::class, QuizQuestionRepositorySql::class);

        $this->app->bind(NetworkMarketingRepositoryInterface::class, NetworkMarketingRepositorySql::class);

        $this->app->bind(TreeMngColFixRepositoryInterface::class, TreeMngColFixRepositorySql::class);

        $this->app->bind(SiteMngRepositoryInterface::class, SiteMngRepositorySql::class);

        $this->app->bind(SpendingRepositoryInterface::class, SpendingRepositorySql::class);

        $this->app->bind(PartnerInfoRepositoryInterface::class, PartnerInfoRepositorySql::class);

        $this->app->bind(ChangeLogRepositoryInterface::class, ChangeLogRepositorySql::class);

        $this->app->bind(TelesaleRepositoryInterface::class, TelesaleRepositorySql::class);

        $this->app->bind(NewsFolderRepositoryInterface::class, NewsFolderRepositorySql::class);

        $this->app->bind(OrderShipRepositoryInterface::class, OrderShipRepositorySql::class);

        $this->app->bind(DonViHanhChinhRepositoryInterface::class, DonViHanhChinhRepositorySql::class);

        $this->app->bind(BlockUiRepositoryInterface::class, BlockUiRepositorySql::class);

        $this->app->bind(OrderInfoRepositoryInterface::class, OrderInfoRepositorySql::class);

        $this->app->bind(RequestInfoRepositoryInterface::class, RequestInfoRepositorySql::class);

        $this->app->bind(OrderItemRepositoryInterface::class, OrderItemRepositorySql::class);

        $this->app->bind(SkusProductVariantOptionRepositoryInterface::class, SkusProductVariantOptionRepositorySql::class);

        $this->app->bind(ProductVariantOptionRepositoryInterface::class, ProductVariantOptionRepositorySql::class);

        $this->app->bind(ProductVariantRepositoryInterface::class, ProductVariantRepositorySql::class);

        $this->app->bind(SkuRepositoryInterface::class, SkuRepositorySql::class);

        $this->app->bind(ProductRepositoryInterface::class, ProductRepositorySql::class);

        $this->app->bind(TransportInfoRepositoryInterface::class, TransportInfoRepositorySql::class);

        $this->app->bind(MyTreeInfoRepositoryInterface::class, MyTreeInfoRepositorySql::class);

        $this->app->bind(TreeMngUserRepositoryInterface::class, TreeMngUserRepositorySql::class);

        $this->app->bind(TreeMngRepositoryInterface::class, TreeMngRepositorySql::class);

        $this->app->bind(MoneyAndTagRepositoryInterface::class, MoneyAndTagRepositorySql::class);

        $this->app->bind(MoneyTagRepositoryInterface::class, MoneyTagRepositorySql::class);

        $this->app->bind(DemoFolderRepositoryInterface::class, DemoFolderRepositorySql::class);

        $this->app->bind(UserRepositoryInterface::class, UserRepositorySql::class);

        $this->app->bind(TagRepositoryInterface::class, TagRepositorySql::class);
        $this->app->bind(TagDemoRepositoryInterface::class, TagDemoRepositorySql::class);

        $this->app->bind(FileRepositoryInterface::class, FileRepositorySql::class);
        $this->app->bind(MenuTreeRepositoryInterface::class, MenuTreeRepositorySql::class);

        $this->app->bind(FolderFileRepositoryInterface::class, FolderFileRepositorySql::class);

        $this->app->bind(FileCloudRepositoryInterface::class, FileCloudRepositorySql::class);

        $this->app->bind(UserCloudRepositoryInterface::class, UserCloudRepositorySql::class);

        $this->app->bind(NewsRepositoryInterface::class, NewsRepositorySql::class);

        $this->app->bind(Todo2RepositoryInterface::class, Todo2RepositorySql::class);

        $this->app->bind(MoneyLogRepositoryInterface::class, MoneyLogRepositorySql::class);

        $this->app->bind(DemoMgRepositoryInterface::class, DemoMgRepositoryMg::class);

        //        $this->app->bind(UserRepositoryInterface::class, DbUserRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // dành cho ms < 5.7.7
        // Schema::defaultStringLength(191);
        Paginator::useBootstrap();

        //$this->app->bind(DemoTbl::class, User::class);

        //Share category cho mọi view
        //        $recusive = new Recusive(Category::all());
        //        $htmlOptionSearchHeader = $recusive->categoryRecusive($parentId = '');
        //        View::share('htmlOptionSearchHeader', $htmlOptionSearchHeader);

        // Note: Dynamic routes are now loaded via LoadDynamicRoutes middleware
        // This ensures they NEVER get cached, even with route:cache
    }
}
