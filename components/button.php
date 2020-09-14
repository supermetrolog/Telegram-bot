<?php 
use app\modules\avtovinbot\models\StateName;
$emoji = require __DIR__ . '/emoji.php';

return [
StateName::ENTER_VIN => ['TEXT' => 'Ввести VIN', 'NEXT' => StateName::ENTER_VIN],
StateName::START_COMMAND => ['TEXT' => '/start', 'NEXT' => StateName::ENTER_VIN],
];
