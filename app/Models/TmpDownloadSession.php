<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use LadLib\Common\UrlHelper1;
use LadLib\Laravel\Database\TraitModelExtra;


/**
 * - TmpDownloadSession để đánh dấu URL tải là hợp lệ!
 * - Xóa các session tải tạm sau 1 giờ
 CREATE EVENT IF NOT EXISTS delete_tmp_download_sessions
 ON SCHEDULE EVERY 10 MINUTE
 STARTS CURRENT_TIMESTAMP
 DO
 DELETE FROM tmp_download_sessions WHERE created_at < CURRENT_TIMESTAMP - INTERVAL 24 HOUR;
 *
 */
class TmpDownloadSession extends ModelGlxBase
{
    use HasFactory, TraitModelExtra;
    protected $guarded = [];
    public static function deleteDoneSession($fid, $uid, $tokenEnc)
    {
//        dump("Delete...");
        self::where('fid', $fid)->where('user_id', $uid)->whereRaw('done_bytes >= file_size')->delete();

    }

    /**
     * Lây session cuối cùng chưa full download nếu có:
     * @param $fid
     * @param $uid
     * @param $tokenEnc
     * @return void
     */
    static function getLastSessionNotFull($fid, $uid, $tokenEnc)
    {
        return self::where('fid', $fid)
            ->where('token', $tokenEnc)
            ->where("user_id", $uid)
            ->whereRaw('(done_bytes IS NULL OR done_bytes < file_size)')
            ->latest()->first();
    }

    /** Lấy link tải, có insert session download để Bắt buộc user cần login tải, chống ăn cắp link...
     * @param $ide
     * @param $name
     * @return string
     * @throws \Exception
     */
    static function getLinkDownload4s($ide, $uid) {

        $u4s = new \App\Components\U4sHelper($uid);
        $nQuotaDownload = $u4s->objUserCloud->getQuotaDailyDownload() * _GB;
        $nDownloadDone = $u4s->getDownloadToday();
        $mInfo = $u4s->getQuotaAllOfUser();


        //User còn gói tải không:

        if ($nDownloadDone > $nQuotaDownload) {
        //    loi("Bạn đã tải quá dung lượng cho phép trong 24h:  <br/>        " . ByteSize($nDownloadDone) . " > " . ByteSize($nQuotaDownload));
        }

        $expireDate = $u4s->getVipExpiredDate();

        if($expireDate < nowyh()){
            $email = $u4s->objUserCms?->email;
            loi("Tài khoản hết hạn VIP: $email, $expireDate");
        }

        if(isUUidStr($ide))
            $idf = FileUpload::where("ide__", $ide)->first()->id;
        else
            $idf = dfh1b($ide);

        $obj = FileUpload::find($idf);
        if (!$obj) {
            loi("Not found file!");
        }
        if ($obj->cloud_id)
            $clf = FileCloud::find($obj->cloud_id);
        else
            $clf = FileCloud::find($idf);

        if (!$clf) {
            loi("Not found file cloud! $idf");
        }

        $sv = new \App\Models\CloudServer();

        if(!$uid){
            loi("Bạn cần đăng nhập để tải file!");
        }

        $uidE = qqgetRandFromId_($uid);

        $locationx = 'not_replicated_yet';
        if($clf->location1){
            $locationx = $clf->location1[2];
            //Lấy ký tự cuối truyền sang, sdb thì lấy b là đủ
            //Chuyen het location sang
            $locationx = $clf->location1;
        }

        //Tạo từng giờ, để tránh user refesh thì bị tạo lặp nhiều lần
        $tokenEnc = STH(date("Y.m.d.H")."|". $locationx."|$clf->id");

//        echo "<br/>\n $idf, $uid, $tokenEnc";

        //Nếu chưa có session thì tạo mới
        //TmpDownloadSession để đánh dấu URL tải là hợp lệ!
        //tìm 1 cái chưa full download
        $ss = TmpDownloadSession::getLastSessionNotFull($idf, $uid, $tokenEnc);
        if (!$ss){
            $ss = new TmpDownloadSession();
            $ss->user_id = $uid;
            $ss->name = $obj->name;
            $ss->user_id_file = $obj->user_id;
            $ss->token = $tokenEnc;
            $ss->done_bytes = 0;
            $ss->fid = $idf;
            $ss->file_size = $obj->file_size;
            $ss->ip_address = request()->ip();
            $ss->server = $clf->server1;
            $ss->location = $clf->location1;
            $ss->save();
        }

        $mainDomain = UrlHelper1::getDomainHostName();

        if(isDebugIp()){
//            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            print_r($clf->toArray());
//            echo "</pre>";
//            die();
        }

        $proxy = $clf->server1;
        $objSv = $sv->where("domain", $clf->server1)->first();
        if($objSv)
            $proxy = $objSv->proxy_domain;

        if($proxy)
            $link = "https://$proxy/81/?fid=$ide&uid=$uid&tokenEnc=$tokenEnc";
        else
            $link = "/81/?fid=$ide&uid=$uid&tokenEnc=$tokenEnc";

//        if($mainDomain == 'v2.4share.vn')
//            $link = "/tool/dl?fid=$ide&uid=$uidE&tokenEnc=$tokenEnc";

        return ['dlink'=>$link, 'sid' => $ss->id, 'done_bytes' => $ss->done_bytes];
    }
}
