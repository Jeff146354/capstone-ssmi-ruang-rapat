<?php

namespace frontend\controllers;

use frontend\models\ResendVerificationEmailForm;
use frontend\models\VerifyEmailForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use common\models\User;
use Kreait\Firebase\Factory;
use yii\bootstrap5\Html;


/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                    'firebase-signup' => ['POST'],
                    'firebase-login' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
            'captcha' => [
                'class' => \yii\captcha\CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        }

        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            $user = $model->signup();
            if ($user) {
                Yii::$app->session->setFlash(
                    'success',
                    'Registrasi berhasil! Kami telah mengirim email verifikasi ke <strong>' .
                    Html::encode($model->email) .
                    '</strong>. Silakan cek inbox Anda dan klik link verifikasi untuk mengaktifkan akun.'
                );
                return $this->redirect(['/site/login']);
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            }

            Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Verify email address
     *
     * @param string $token
     * @throws BadRequestHttpException
     * @return yii\web\Response
     */
    public function actionVerifyEmail($token)
    {
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if (($user = $model->verifyEmail()) && Yii::$app->user->login($user)) {
            Yii::$app->session->setFlash('success', 'Your email has been confirmed!');
            return $this->goHome();
        }

        Yii::$app->session->setFlash('error', 'Sorry, we are unable to verify your account with provided token.');
        return $this->goHome();
    }

    /**
     * Resend verification email
     *
     * @return mixed
     */
    public function actionResendVerificationEmail()
    {
        $model = new ResendVerificationEmailForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', 'Sorry, we are unable to resend verification email for the provided email address.');
        }

        return $this->render('resendVerificationEmail', [
            'model' => $model
        ]);
    }

    /**
     * firebase login
     *
     * @return mixed
     */
    public function actionFirebaseLogin()
    {
        $idToken = Yii::$app->request->post('idToken');
        if (!$idToken) {
            throw new BadRequestHttpException("No token received.");
        }

        // Verify token with Firebase
        // $auth = new \Kreait\Firebase\Auth();
        // $auth = (new Factory)->withServiceAccount('..\..\common\firebase\kepston17-8c88b-firebase-adminsdk-fbsvc-5965324402.json')->createAuth();
        $auth = (new Factory)->withServiceAccount(Yii::getAlias('@common/firebase/kepston17-8c88b-firebase-adminsdk-fbsvc-5965324402.json'))->createAuth();

        try {
            $verifiedIdToken = $auth->verifyIdToken($idToken);
            $firebaseUserId = $verifiedIdToken->claims()->get('sub');
            $email = $verifiedIdToken->claims()->get('email');

            try {
                $user = User::find()
                    ->where(['email' => $email, 'sso_provider' => 'google'])
                    ->one();

                if (!$user) {
                    throw new \Exception('User not registered.');
                }

                // Optionally, log in the user
                Yii::$app->user->login($user);
                return $this->goHome();

            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
                return $this->redirect(['site/login']);
            }

        } catch (\Exception $e) {
            Yii::error($e->getMessage(), 'firebase');
            // throw new UnauthorizedHttpException("Invalid token.");
            return $this->redirect(['site/login']);
        }
    }


    /**
     * firebase signup
     *
     * @return mixed
     */
    public function actionFirebaseSignup()
    {
        $idToken = Yii::$app->request->post('idToken');
        if (!$idToken) {
            throw new BadRequestHttpException("No token received.");
        }

        // $auth = new \Kreait\Firebase\Auth();
        $auth = (new Factory)->withServiceAccount(Yii::getAlias('@common/firebase/kepston17-8c88b-firebase-adminsdk-fbsvc-5965324402.json'))->createAuth();

        try {
            $verifiedIdToken = $auth->verifyIdToken($idToken);
            $email = $verifiedIdToken->claims()->get('email');
            $firebaseUid = $verifiedIdToken->claims()->get('sub');

            // Check if already exists
            if (User::find()->where(['email' => $email])->exists()) {
                if (User::find()->where('sso_provider' != null)) {
                    Yii::$app->session->setFlash('warning', 'User already exists locally.');
                    return $this->redirect(['site/signup']);
                } else {
                    Yii::$app->session->setFlash('warning', 'User already exists.');
                    return $this->redirect(['site/signup']);
                }

            }

            // Create user
            $user = new User();
            $user->username = explode('@', $email)[0];
            $user->email = $email;
            $user->sso_provider = 'google';
            $user->sso_uid = $firebaseUid;
            $user->auth_key = Yii::$app->security->generateRandomString();
            $user->password_hash = Yii::$app->security->generatePasswordHash(uniqid());
            $user->status = 10;
            $user->created_at = time();
            $user->updated_at = time();
            $user->save();

            Yii::$app->user->login($user);
            return $this->goHome();

        } catch (\Exception $e) {
            Yii::error($e->getMessage(), 'firebase');
            return $this->redirect(['site/signup']);
        }
    }
}
