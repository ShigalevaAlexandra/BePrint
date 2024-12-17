<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "types_services".
 *
 * @property int $id_type
 * @property string $title
 *
 * @property Services[] $services
 */

class TypesServices extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */

    public static function tableName()
    {
        return 'types_services';
    }

    /**
     * {@inheritdoc}
     */

    //--description of the data received----------//
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['title'], 'string', 'max' => 250],
        ];
    }
    //--------------------------------------------//

    /**
     * {@inheritdoc}
     */

    //--class methods---------------------------------------------------------------------//
    public function attributeLabels()
    {
        return [
            'id_type' => 'Id Type',
            'title' => 'Title',
        ];
    }

    /**
     * Gets query for [[Services]].
     *
     * @return \yii\db\ActiveQuery
     */
    
    public function getServices()
    {
        return $this->hasMany(Services::class, ['type_id' => 'id_type']);
    }
    //----------------------------------------------------------------------------------//
}
