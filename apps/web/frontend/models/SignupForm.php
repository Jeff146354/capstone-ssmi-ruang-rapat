<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;

/**
 * Signup form
 *
 * DEVELOPER NOTE — Email Verification:
 * Currently, accounts are activated immediately (no email verification).
 * To enable email verification in the future:
 *
 * 1. Change $user->status to User::STATUS_INACTIVE in signup()
 * 2. Uncomment the generateEmailVerificationToken() line
 * 3. Uncomment the sendEmail() call
 * 4. Configure SMTP in common/config/main-local.php (set useFileTransport to false)
 * 5. Update SiteController::actionSignup() to NOT auto-login, and show
 *    a "check your email" message instead
 *
 * The email template already exists at common/mail/emailVerify-html.php
 * The verify action already exists at SiteController::actionVerifyEmail()
 * Everything is wired — you just need to flip these flags and configure SMTP.
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Username sudah digunakan.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Email sudah terdaftar.'],

            ['password', 'required'],
            ['password', 'string', 'min' => Yii::$app->params['user.passwordMinLength']],
        ];
    }

    /**
     * Signs user up — immediately active, no email verification.
     *
     * @return User|null the saved user, or null on failure
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->username   = $this->username;
        $user->email      = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->status     = User::STATUS_ACTIVE; // Active immediately — no email verification
        $user->created_at = time();
        $user->updated_at = time();

        // To enable email verification, uncomment these two lines:
        // $user->generateEmailVerificationToken();
        // $user->status = User::STATUS_INACTIVE;

        return $user->save(false) ? $user : null;

        // To enable email verification, replace the line above with:
        // return $user->save(false) && $this->sendEmail($user) ? $user : null;
    }

    /**
     * Sends confirmation email to user.
     * Currently unused — activate by uncommenting in signup() above.
     */
    protected function sendEmail(User $user): bool
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
            ->setTo($this->email)
            ->setSubject('Verifikasi Akun — ' . Yii::$app->name)
            ->send();
    }
}
