<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "services".
 *
 * @property int $id_service
 * @property string $title
 * @property int $type_id
 * @property string $photo_service
 * @property string $description
 * @property int $price
 * @property string $date_added
 *
 * @property Cart[] $carts
 * @property TypesServices $typesServices
 */
class Services extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'services';
    }

    /**
     * {@inheritdoc}
     */

    //--description of the data received--------------------------------------------------------------------------------------------------------------------------//
    public function rules()
    {
        return [
            [['title', 'type_id', 'photo_service', 'description', 'price'], 'required'],
            [['type_id', 'price'], 'integer'],
            [['description'], 'string'],
            ['title', 'match', 'pattern' => '/^[а-яё\s0-9\s"\s]+$/iu', 'message'=>'Название услуги может состоять только из кириллицы, цифр, пробелов и ковычок'],
            ['type_id', 'match', 'pattern' => '/^[0-9]+$/iu', 'message'=>'Идентификатор - числовое значение'],
            ['price', 'match', 'pattern' => '/^[\d]+$/iu', 'message'=>'Цена услуги является числовым значением'],
            [['date_added'], 'safe'],
            [['title', 'photo_service'], 'string', 'max' => 250],
            [['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => TypesServices::class, 'targetAttribute' => ['type_id' => 'id_type']],
        ];
    }
    //-----------------------------------------------------------------------------------------------------------------------------------------------------------//

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_service' => 'Id Service',
            'title' => 'Title',
            'type_id' => 'Type ID',
            'photo_service' => 'pPhoto Service',
            'description' => 'Description',
            'price' => 'Price',
            'date_added' => 'Date Added',
        ];
    }

    /**
     * Gets query for [[Carts]].
     *
     * @return \yii\db\ActiveQuery
     */

    //--class methods-----------------------------------------------------------------------//
    public function getCarts()
    {
        return $this->hasMany(Cart::class, ['service_id' => 'id_service']);
    }

    /**
     * Gets query for [[TypesServices]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTypesServices()
    {
        return $this->hasOne(TypesServices::class, ['id_type' => 'type_id']);
    }
    //--------------------------------------------------------------------------------------//
}
