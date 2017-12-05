<?php

namespace app\controllers;

use app\models\Messages;
use app\models\Users;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays chat page.
     *
     * @return string|array
     */
    public function actionIndex()
    {
        $modelUsers    = new Users;
        $modelMessages = new Messages;

        if (Yii::$app->request->isAjax)
        {
            if (Yii::$app->request->post('Users', 0)) {
                $model = $modelUsers;
            } else {
                $model = $modelMessages;
            }

            $model->load(Yii::$app->request->post());
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        } else {
            return $this->render('index', [
                'modelUsers'    => $modelUsers,
                'modelMessages' => $modelMessages,
                'users'         => Users::find()->all(),
                'messages'      => Messages::find()->orderBy('date ASC')->all()
            ]);
        }
    }

    /**
     * Creating user
     *
     * @return null|void
     */
    public function actionCreateUser()
    {
        if (!Yii::$app->request->isAjax) {
            return;
        }

        $model = new Users;
        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            if ($model->isUserExist()) {
                $userId = $model->getExistUserId();
            } else {
                $model->setUserInfo();
                $model->save(false);
                $userId = $model->id_user;
            }
            Yii::$app->response->format          = Response::FORMAT_JSON;
            Yii::$app->response->data['user_id'] = $userId;
        }
        return null;
    }

    /**
     * Creating message
     *
     * @return null|void
     */
    public function actionCreateMessage()
    {
        if (!Yii::$app->request->isAjax) {
            return;
        }

        $model = new Messages;
        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->save(false)) {
            $data[] = Messages::findOne(['id_message' => $model->id_message]);
            Yii::$app->response->format = Response::FORMAT_HTML;
            Yii::$app->response->data   = $this->renderPartial('_messages', ['data' => $data]);
        }
        return null;
    }
}
