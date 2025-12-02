<?php

/**
 * Nếu đã clone rồi thì clone lại thì sao????
 */

require_once '../../index.php';

class clsTmpCounter
{
    public static $count = 0;

    public static $total = 0;

    public static $max = 500;

    public static $mm = [];

    public static $limitLevel = 10;

    public $id;

    public $name;

    public $parent_id;

    public $spouse;

    public $gender;

    public $child_of_spouse;

    /**
     * @return mixed
     */
    public function setId($id = 0)
    {
        if (! $id) {
            $this->id = clsTmpCounter::$count;
        } else {
            $this->id = $id;
        }
    }

    public function setName($str = 0)
    {
        if (! $str) {
            $this->name = $this->id;
        } else {
            $this->name = $str;
        }
    }
}

//clsTmpCounter::$count = 0;

//\App\Models\GiaPha::where(['user_id'=>1, 'tmp_old_id'=>-1000])->forceDelete();

//\App\Models\GiaPha::withTrashed()->where(['user_id'=>1, 'tmp_old_id'=>-1000])->restore();

//\App\Models\GiaPha::withTrashed()->where(['user_id'=>1])->where('created_at', '>', '2023-03-07')->restore();

echo "<br/>\n Start ...";
//
//return;

$mName1 = \LadLib\Common\ClsTreeHelper1::getRandNameVnMale();
$mName2 = \LadLib\Common\ClsTreeHelper1::getRandNameVnFemale();

sinhCay(0, 1);

function sinhCay($padP, $level, $pr = 0)
{

    global $mName1, $mName2;

    //    clsTmpCounter::$count++;
    //    if($level > 3)
    //        return;
    // $par = clsTmpCounter::$count;
    //Tạo ngẫu nghiên số con từ 1 đến 10
    //    echo "<br/>\n " . str_repeat("-", $level) . " $parent (L=$level) myChild: " ;
    if (! $pr) {
        $nC = 1;
    } else {
        $nC = rand(3, 10);
    }

    if ($level <= 1) {
        $nC = 5;
    }

    if ($level > 5) {
        $nC = rand(0, 3);
    }

    if ($nC == 0) {
        echo "\n No child (1): ";
    }
    for ($i = 1; $i <= $nC; $i++) {
        clsTmpCounter::$count++;

        if (clsTmpCounter::$count > clsTmpCounter::$max) {
            return;
        }

        echo ' <br> '.str_repeat('-', $level);
        echo "  $padP-$i ($nC), L=$level, id = ".clsTmpCounter::$count.", p=$pr x ";

        $obj = new clsTmpCounter();
        $obj->setId();

        $rand = rand(1, 5);
        if ($rand <= 3 || $pr == 0) {
            $obj->gender = 1;
        } else {
            $obj->gender = 2;
        }

        if ($obj->gender == 1) {
            $obj->setName($mName1[clsTmpCounter::$count]);
        }
        if ($obj->gender == 2) {
            $obj->setName($mName2[clsTmpCounter::$count]);
        }

        $obj->parent_id = $pr;
        clsTmpCounter::$mm[] = $obj;

        if ($level + 1 > clsTmpCounter::$limitLevel) {
            echo "\n Max child: ".clsTmpCounter::$limitLevel;

            continue;
        }

        //        echo "\n have child: ";
        sinhCay("$padP-$i", $level + 1, clsTmpCounter::$count);
    }
}

echo '<pre> >>> '.__FILE__.'('.__LINE__.')<br/>';
print_r(clsTmpCounter::$mm);
echo '</pre>';

//return;

$cc = 0;
if (isCli()) {
    if (count(clsTmpCounter::$mm) > 300) {
        foreach (clsTmpCounter::$mm as &$obj) {
            $cc++;
            $gp = new \App\Models\GiaPha();
            $idDb = $gp->insertGetId(['name' => $obj->name,
                'user_id' => 1,
                'gender' => $obj->gender,
                'parent_id' => $obj->parent_id,
                'tmp_old_id' => -1000]);
            $oldId = $obj->id;
            $obj->setId($idDb);
            foreach (clsTmpCounter::$mm as &$obj1) {
                if ($obj1->parent_id == $oldId) {
                    $obj1->parent_id = $idDb;
                }
            }
            //    $obj->setName(clsTmpCounter::$count);
            //    $obj->parent_id = $pr;
            echo "<br/>\n $cc. Insert ... $idDb ";
        }
    }
}

echo '<pre> >>> '.__FILE__.'('.__LINE__.')<br/>';
print_r(clsTmpCounter::$mm);
echo '</pre>';
//
//echo json_encode(clsTmpCounter::$mm);
