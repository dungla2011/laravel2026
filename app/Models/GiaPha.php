<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use LadLib\Laravel\Database\TraitModelExtra;

class GiaPha extends ModelGlxBase
{
    use HasFactory, SoftDeletes, TraitModelExtra, SnowflakeId;

    protected $guarded = [];

    public function countMember()
    {
        $nC = $this->countChild();
        //Đếm v/c
        $mm = $this->where('married_with', $this->id)->count();
        if ($mm) {
            return $nC + $mm + 1;
        }

        return $nC + 1;
    }

    public function getLinkPublic()
    {
        return '/my-tree-info/'.qqgetRandFromId_($this->id);
    }


    static function getTreeCTE($pid, $strField = "*")
    {
        $strField = str_replace(" ", "", $strField);
        $strF = "f.".str_replace(",", ", f.", $strField);
        $query = "
            WITH RECURSIVE gia_pha_tree AS (
                SELECT $strField
                FROM gia_phas
                WHERE id = :id AND deleted_at IS NULL
                UNION ALL
                SELECT $strF
                FROM gia_phas f
                INNER JOIN gia_pha_tree ft ON f.parent_id = ft.id
                WHERE f.deleted_at IS NULL
            )
            SELECT * FROM gia_pha_tree;
        ";

        return DB::select($query, ['id' => $pid]);
    }
    /**
     * @return GiaPha[]
     */
    function getCacVoChong()
    {
        return GiaPha::where('married_with', $this->id)->get();
    }

    /**
     * @return GiaPha
     */
    function getBoMe()
    {
        return $this->where('id', $this->parent_id)->first();
    }

    /**
     * @return GiaPha[]
     */
    function getCacCon()
    {
        return $this->where('parent_id', $this->id)->get();
    }


    static function getCountOver($n = 200)
    {
        $mmOver = [5=>0, 20=> 0, 50 => 0, 100 => 0, 200 => 0, 300 => 0, 400 => 0, 500 => 0, 1000 => 0];
        $results = \App\Models\GiaPha::select('user_id', \DB::raw('count(*) as total'))
            ->groupBy('user_id')
            ->get();

        foreach ($results as $result) {
            foreach ($mmOver as $key => $value) {
                if ($result->total > $key) {
                    $mmOver[$key]++;
                }
            }
        }

        return $mmOver;
    }

    public function getObjNameAndTitle($reverse = 0)
    {
        if ($this->title) {
            if ($reverse) {
                return "$this->title - $this->name";
            } else {
                return "$this->name - $this->title";
            }
        }

        return $this->name;
    }

    public function getValidateRuleInsert()
    {

        //        if(!isIPDebug())
        //            return;
        //OK: '/^([^`\$<>]+)$/u'; //Chuỗi bất kỳ không chứa `$<>
        $sreg = '/^([^`\$<>]+)$/u';

        return [
            //            'name'=>'required|regex:/^([^<>]+)$/u|max:200',
            'name' => 'sometimes|required|regex:'.$sreg.'|max:200',
            'email_address' => 'nullable|email|regex:'.$sreg.'|max:200',
            'phone_number' => 'nullable|regex:'.$sreg.'|max:20',
            'title' => 'nullable|regex:'.$sreg.'|max:200',
            'summary' => 'nullable|regex:'.$sreg.'|max:2000',
            'home_address' => 'nullable|regex:'.$sreg.'|max:300',
            'last_name' => 'nullable|regex:'.$sreg.'|max:100',
            'sur_name' => 'nullable|regex:'.$sreg.'|max:100',
            'place_heaven' => 'nullable|regex:'.$sreg.'|max:2000',
            //home_address last_name sur_name place_heaven place_heaven
        ];
    }

    public function getValidateRuleUpdate($id = null)
    {
        return $this->getValidateRuleInsert();
    }

    /**
     * Overwide để thêm married vào
     *
     * @param  null  $mField
     * @return array
     *
     * @throws \Exception
     */
    public function getAllTreeDeep($pid, $mField = null, $getLevel = null)
    {
        if (! is_numeric($pid)) {
            $pid = qqgetIdFromRand_($pid);
        }
        if (! $obj = $this->find($pid)) {
            loi("Not found $pid!");
        }
        $m1 = $this->where('married_with', $obj->id)->get()->toArray();
        $obj = $obj->toArray();
        $mm = $this->getChildRecursive($pid, $mField);

        return array_merge([$obj], $m1, $mm);
    }

    public function getAllTreeDeepCTE($pid, $strField = "*", $getLevel = null)
    {
        if (! is_numeric($pid)) {
            $pid = qqgetIdFromRand_($pid);
        }
        if (! $obj = $this->find($pid)) {
            loi("Not found $pid!");
        }

        $strField = str_replace(" ", "", $strField);
        $strF = "f.".str_replace(",", ", f.", $strField);

        if($strField && $strField != "*"){
            $mmSl = explode(',', $strField);
            $m1 = $this->select($mmSl)->where('married_with', $obj->id)->get()->toArray();
        }
        else
            $m1 = $this->where('married_with', $obj->id)->get()->toArray();

        $table = $this->getTable();
        $query = "
            WITH RECURSIVE all_tree AS (
                SELECT $strField
                FROM $table
                WHERE id = :id AND deleted_at IS NULL
                UNION ALL
                SELECT $strF
                FROM $table f
                INNER JOIN all_tree ft ON f.parent_id = ft.id
                WHERE f.deleted_at IS NULL
            )
            SELECT * FROM all_tree;
        ";

        $m00 =  DB::select($query, ['id' => $pid]);
        if(isDebugIp()){

//            die("xxx");

            //Sắp xếp theo orders mảng này, từ cao xuống thấp
//            $m00 = collect($m00)->sortByDesc('orders')->values()->all();

//        die();
        }

        $mm = [];
        foreach ($m00 AS &$m0){
            $mm[] = (array) $m0;
        }
//        return $m1;
//        $mId = [];
//        foreach ($mm0 as $obj)
//        {
//            $mId[] = $obj->id;
//        }
//        $mm = \App\Models\GiaPha::whereIn("id", $mId)->get();

//        return array_merge([$obj], $m1, $mm);
        return array_merge( $mm, $m1);
    }

    public static function toolFixVoChong()
    {
        if (! isCli()) {
            exit('Not cli!');
        }
        $mm = \App\Models\GiaPha::all();
        //$mm = \App\Models\GiaPha::where('user_id', 16)->get();
        $cc = 0;
        foreach ($mm as &$obj) {
            $cc++;
            //Tìm xem có là con của vợ lẽ chồng 2 hay không:
            if ($obj->child_of_second_married) {
                //Nếu có thì xem vợ lẽ này có chồng là cha của obj ko
                //Nếu ko thì xóa mẹ 2 này đi
                foreach ($mm as &$obj1) {

                    if ($obj1->user_id != $obj->user_id) {
                        continue;
                    }

                    if ($obj1->id == $obj->child_of_second_married) {
                        if ($obj1->married_with !== $obj->parent_id) {
                            echo "<br/>\n ($cc) Mẹ 2 ko đúngx: $obj->id ($obj->user_id),  $obj->name ($obj1->name , $obj1->married_with !== $obj->parent_id)";
                            $obj->child_of_second_married = null;
                            //                    $obj->update();
                            //                            $obj->child_of_second_married = '';
                        }
                    }
                }
            }
        }
    }


    static function getNewNodeStat($field = 'user_id')
    {
        //Check neu table ton tai:
        if (! \Schema::hasTable('gia_phas')) {
            return json_encode([]);
        }

        $statByDay = \DB::table('gia_phas')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as counts');
        $statByDay = $statByDay->where('created_at', '>=', Carbon::now()->subDays(90))
//            ->where('file_size', '>', 0)
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        return json_encode($statByDay->toArray());
    }
}
