<?php

namespace app\models;

use app\modules\avtovinbot\exceptions\UserException;
use Qiwi\Api\BillPayments;
use Yii;

/**
 * This is the model class for table "qiwibill".
 *
 * @property string $user_id
 * @property string $state
 * @property string $billid
 * @property int $amount
 * @property string $payUrl
 * @property string $date
 *
 * @property User $user
 */
class Qiwibill extends \yii\db\ActiveRecord
{
    public const CONTROLL = 'controll';
    public const CURRENCY = 'RUB';
    public const EMAIL = 'billypro6@gmail.com';
    public const STATUS_PAID = 'PAID';

    private const DIFFERENCE_DATE = 2000000;
    private $user;
    private $payments;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'qiwibill';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'state', 'billid', 'amount', 'payUrl'], 'required'],
            [['amount'], 'integer'],
            [['date'], 'safe'],
            [['user_id', 'state'], 'string', 'max' => 45],
            [['billid', 'payUrl'], 'string', 'max' => 255],
            [['user_id'], 'unique'],
            [['billid'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'user_id']],
        ];
    }

    public function __construct($user = null, $config = [])
    {
        $params = include(Yii::getAlias('@app') . '/config/bot_params.php');
        $this->payments = new BillPayments($params['Qiwibill_Secret_Key']);
        $this->user = $user;
        parent::__construct($config);
    }

    private function createBill($amount)
    {
        $this->user_id = $this->user->user_id;
        $this->state = $this->user->state->current;
        $this->billid = $this->payments->generateId();
        $this->amount = $amount;

        $DateTime = $this->payments->getLifetimeByDay(1);

        $fields = [
            'amount' => $this->amount,
            'currency' => self::CURRENCY,
            'comment' => $this->state,
            'expirationDateTime' => $DateTime,
            'email' => self::EMAIL,
            'account' => $this->user_id
        ];

        $response = $this->payments->createBill($this->billid, $fields);
        if (isset($response)) {
            $this->payUrl = $response['payUrl'];
            if ($this->save()) {
                return $this->payUrl;
            }
            return false;
        }
        return false;
    }
    private function getDurationDifference()
    {
        return strtotime(date('Y-m-d H:i:s')) - strtotime($this->user->qiwibill->date);
    }
    private function hasPayUrl()
    {
        if ($this->user->qiwibill->user_id !== null) {
            if ($this->getDurationDifference() > self::DIFFERENCE_DATE) {
                $this->user->qiwibill->delete();
                return false;
            }
            if ($this->user->qiwibill->state != $this->user->state->current) {
                $this->user->qiwibill->delete();
                return false;
            }
            return $this->user->qiwibill->payUrl;
        }
        return false;
    }

    public function getPayUrl($amount)
    {
        if ($this->hasPayUrl()) {
            return $this->user->qiwibill->payUrl;
        }

        $payUrl = $this->createBill($amount);
        if (!$payUrl) {
            throw new UserException('Can not create Qiwibill');
        }
        return $payUrl;
    }

    public function controll()
    {
        if ($this->user->qiwibill->user_id === null) {
            return false;
        }

        $response = $this->payments->getBillInfo($this->user->qiwibill->billid);

        if ($response['status']['value'] == self::STATUS_PAID) {
            return true;
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'state' => 'State',
            'billid' => 'Billid',
            'amount' => 'Amount',
            'payUrl' => 'Pay Url',
            'date' => 'Date',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'user_id']);
    }
}
