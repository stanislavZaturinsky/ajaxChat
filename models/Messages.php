<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "messages".
 *
 * @property integer $id_message
 * @property integer $id_user
 * @property string $text
 * @property string $date
 *
 * @property Users $idUser
 */
class Messages extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'messages';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['text'], 'required', 'message' => 'The field "{attribute}" is required'],
            [['text'], 'string', 'max' => 300, 'tooLong' => 'The field "{attribute}" can contain not more {max} symbols'],
            [['text'], 'match', 'pattern' => '/^[a-zA-Z0-9 .,;_-]+$/',
                'message' => 'The field "{attribute}" can contain only dots, commas, dashes, words and numbers'
            ],
            [['id_user'], 'integer'],
            [['date'], 'safe'],
            [['id_user'], 'exist', 'skipOnError' => true,
                'targetClass' => Users::className(), 'targetAttribute' => ['id_user' => 'id_user']
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_message' => 'Id Message',
            'id_user'    => 'Id User',
            'text'       => 'Text',
            'date'       => 'Date',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdUser()
    {
        return $this->hasOne(Users::className(), ['id_user' => 'id_user']);
    }
}
