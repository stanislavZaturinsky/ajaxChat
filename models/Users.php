<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property integer $id_user
 * @property string $nickname
 * @property string $ip
 * @property string $city
 * @property string $date
 *
 * @property Messages[] $messages
 */
class Users extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nickname', 'ip'], 'required', 'message' => 'The field "{attribute}" is required'],
            [['nickname'], 'string', 'max' => 40, 'tooLong' => 'The field "{attribute}" can contain not more {max} symbols'],
            [['nickname'], 'match',
                'pattern' => '/^[a-zA-Z0-9]+$/', 'message' => 'The field "{attribute}" can contain only words and numbers without space'
            ],
            [['date'], 'safe'],
            [['ip'], 'string', 'max' => 20],
            [['city'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_user'  => 'Id User',
            'nickname' => 'Nickname',
            'ip'       => 'Ip',
            'city'     => 'City',
            'date'     => 'Date',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessages()
    {
        return $this->hasMany(Messages::className(), ['id_user' => 'id_user']);
    }

    public function setUserInfo(){
        if (!$this->ip) {
            return null;
        }

        $data = trim(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $this->ip));
        if (!$data) {
            return null;
        }

        $data = json_decode($data);
        if (!is_object($data)) {
            return null;
        }

        if (isset($data->geoplugin_city)) {
            $this->city = $data->geoplugin_city;
        }

        return null;
    }

    public function isUserExist() {
        if (!$this->nickname && !$this->ip) {
            return false;
        }

        return !empty($this::findOne(['nickname' => $this->nickname, 'ip' => $this->ip]));
    }

    public function getExistUserId() {
        if (!$this->nickname && !$this->ip) {
            return false;
        }

        $user = $this::findOne(['nickname' => $this->nickname, 'ip' => $this->ip]);
        if ($user) {
            return $user->id_user;
        }

        return null;
    }
}
