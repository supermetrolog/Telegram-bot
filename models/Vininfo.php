<?php
namespace app\models;

use app\models\parser\ClassHTML;
use app\models\parser\Parser;
use yii\base\Model;

use Yii;
class Vininfo extends Model
{
    public function getInfo($vin)
    {
        $parser = new Parser();
        return $parser->parse();
    }

}
