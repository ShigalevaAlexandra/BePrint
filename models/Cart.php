<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cart".
 *
 * @property int $id_cart
 * @property int $user_id
 * @property int $service_id
 * @property int $count
 * @property int|null $order_id
 *
 * @property Orders $order
 * @property Services $services
 * @property Users $user
 */
class Cart extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cart';
    }

    /**
    * {@inheritdoc}
    */

    //--description of the data received--------------------------------------------------------------------------------------------------------------//
    public function rules()
    {
        return [
            [['user_id', 'service_id', 'count'], 'required'],
            [['user_id', 'service_id', 'count', 'order_id'], 'integer'],
            ['count', 'match', 'pattern' => '/[0-9]+$/iu', 'message' =>'Только цифры'],
            [['service_id'], 'exist', 'skipOnError' => true, 'targetClass' => Services::class, 'targetAttribute' => ['service_id' => 'id_service']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['user_id' => 'id_user']],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Orders::class, 'targetAttribute' => ['order_id' => 'id_order']],
        ];
    }
    //-----------------------------------------------------------------------------------------------------------------------------------------------//

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_cart' => 'Id Cart',
            'user_id' => 'User ID',
            'service_id' => 'Service ID',
            'count' => 'Count',
            'order_id' => 'Order ID',
        ];
    }

    /**
     * Gets query for [[Orders]].
     *
     * @return \yii\db\ActiveQuery
     */

    //--class methods-----------------------------------------------------------------------//
    public function getOrder()
    {
        return $this->hasOne(Orders::class, ['id_order' => 'order_id']);
    }

    /**
     * Gets query for [[Services]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getServices()
    {
        return $this->hasOne(Services::class, ['id_service' => 'service_id']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::class, ['id_user' => 'user_id']);
    }
    //-------------------------------------------------------------------------------------//
}
