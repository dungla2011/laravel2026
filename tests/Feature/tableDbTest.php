<?php

namespace Tests\Feature;

use App\Components\Helper1;
use Illuminate\Support\Facades\DB;
use LadLib\Common\Database\MetaOfTableInDb;
use LadLib\Laravel\Database\DbHelperLaravel;
use Tests\TestCase;

class tableDbTest extends TestCase
{
    //Cần test index của từng bảng

    public function testIndexOfTable()
    {

        return;

        $tbls = DbHelperLaravel::getAllTableName();

        print_r(Helper1::getDBInfo());

        //        $tbls = ['file_uploads'];

        $pdo = DB::getPdo();
        foreach ($tbls as $tb) {

            $meta = MetaOfTableInDb::getMetaObjFromTableName($tb);
            if (! $meta) {
                continue;
            }
            $mfieldNeedIndex = $meta->getNeedIndexFieldDb();
            //            foreach ($mfield AS $field){
            //
            //            }
            if (! $mfieldNeedIndex) {
                continue;
            }

            if (! in_array('id', $mfieldNeedIndex)) {
                $mfieldNeedIndex[] = 'id';
            }

            echo "<br/>\n TBL = $tb";

            $sql = "SHOW INDEXES FROM $tb";
            $stmt = $pdo->query($sql);
            $mfieldIndexOK = [];
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {

                //            dump($row);
                $column_name = $row['Column_name'];
                //                echo "<br/>\n $column_name";
                $mfieldIndexOK[] = $column_name;
            }

            echo "<br/>\n --- Need index:";
            print_r($mfieldNeedIndex);
            echo '</pre>';
            echo "<br/>\n --- Real index:";
            print_r($mfieldIndexOK);

            $ret = array_diff($mfieldNeedIndex, $mfieldIndexOK);

            echo "<br/>\n ---111---";
            print_r($ret);

            self::assertTrue(! $ret);
            $ret = array_diff($mfieldIndexOK, $mfieldNeedIndex);
            echo "<br/>\n ---222---";
            print_r($ret);

            self::assertTrue(! $ret, "TB $tb , ".json_encode($ret));

        }

    }
}
