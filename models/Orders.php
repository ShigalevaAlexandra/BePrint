<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "orders".
 *
 * @property int $id_order
 * @property string $phone
 * @property string $date_desired
 * @property string $payment_type
 * @property string $comments
 * @property string $date_create
 *
 * @property Cart[] $carts
 */
class Orders extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orders';
    }

    /**
     * {@inheritdoc}
     */

    //--description of the data received---------------------------------------------------------------------------------------------------//
    public function rules()
    {
        return [
            [['phone', 'date_desired'], 'required'],
            [['date_desired', 'date_create'], 'safe'],
            [['payment_type', 'comments'], 'string'],
            [['phone'], 'string', 'max' => 20],
            ['phone', 'match', 'pattern' => '/^\+7\(\d{3,}\)\d{3,}-\d{2,}-\d{2,}$/', 'message'=>'формат номера телефона: +7(999)999-99-99'],
            ['payment_type', 'match', 'pattern' => '/^[а-я\s]+$/iu', 'message'=>'онлайн или при получении'],
            ['date_desired', 'match', 'pattern' => '/^\d{4}\-\d{1,2}\-\d{1,2}+$/iu', 'message'=>'в формате 2024-12-20'],
            
        ];
    }
    //------------------------------------------------------------------------------------------------------------------------------------//

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_order' => 'Id Order',
            'phone' => 'Phone',
            'date_desired' => 'Date Desired',
            'payment_type' => 'Payment Type',
            'comments' => 'Comments',
            'date_created' => 'Date Created',
        ];
    }

    /**
     * Gets query for [[Carts]].
     *
     * @return \yii\db\ActiveQuery
     */

    //--class methods---------------------------------------------------------------//
    public function getCarts()
    {
        return $this->hasMany(Cart::class, ['order_id' => 'id_order']);
    }
    //-----------------------------------------------------------------------------//
}
