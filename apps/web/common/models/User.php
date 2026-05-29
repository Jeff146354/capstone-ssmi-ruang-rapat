<?php

namespace common\models;

use Yii;
use yii\web\IdentityInterface;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;



/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string|null $password_reset_token
 * @property string $email
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property string|null $verification_token
 * @property string $role
 * @property string|null $sso_provider
 * @property string|null $sso_uid
 * @property int $priority
 * @property string|null $booking_suspended_until
 * @property bool $requires_manual_approval
 * @property string|null $priority_boost_until
 *
 * @property Reservation[] $reservations
 * @property Notification[] $notifications
 * @property UserStrike[] $strikes
 */
class User extends ActiveRecord implements IdentityInterface
{

    /**
     * ENUM field values
     */
    const ROLE_ADMIN = 'admin';
    const ROLE_USER = 'user';
    const SSO_PROVIDER_GOOGLE = 'google';
    const SSO_PROVIDER_FIREBASE = 'firebase';
    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;

    /**
     * Priority levels — higher number = higher priority
     */
    const PRIORITY_MAHASISWA = 1;
    const PRIORITY_STAFF     = 2;
    const PRIORITY_DOSEN     = 3;
    const PRIORITY_ADMIN     = 99;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['password_reset_token', 'verification_token', 'sso_provider'], 'default', 'value' => null],
            [['booking_suspended_until', 'priority_boost_until'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 10],
            [['role'], 'default', 'value' => 'user'],
            [['priority'], 'default', 'value' => self::PRIORITY_MAHASISWA],
            [['requires_manual_approval'], 'default', 'value' => false],
            [['username', 'auth_key', 'password_hash', 'email', 'created_at', 'updated_at'], 'required'],
            [['status', 'created_at', 'updated_at', 'priority'], 'integer'],
            [['requires_manual_approval'], 'boolean'],
            [['role', 'sso_provider'], 'string'],
            [['booking_suspended_until', 'priority_boost_until'], 'safe'],
            [['username', 'password_hash', 'password_reset_token', 'email', 'verification_token'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            ['role', 'in', 'range' => array_keys(self::optsRole())],
            ['sso_provider', 'in', 'range' => array_keys(self::optsSsoProvider())],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['password_reset_token'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'verification_token' => 'Verification Token',
            'role' => 'Role',
            'sso_provider' => 'Sso Provider',
        ];
    }

    /**
     * Gets query for [[Reservations]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReservations()
    {
        return $this->hasMany(Reservation::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Notifications]].
     */
    public function getNotifications()
    {
        return $this->hasMany(Notification::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[UserStrikes]].
     */
    public function getStrikes()
    {
        return $this->hasMany(UserStrike::class, ['user_id' => 'id']);
    }

    /**
     * Check if user is currently suspended from booking.
     */
    public function isSuspended(): bool
    {
        if (!$this->booking_suspended_until) {
            return false;
        }
        return strtotime($this->booking_suspended_until) > time();
    }

    /**
     * Get the effective priority (boosted if priority_boost_until is active).
     */
    public function getEffectivePriority(): int
    {
        if ($this->priority_boost_until && strtotime($this->priority_boost_until) > time()) {
            return $this->priority + 1; // temporary boost above same-role peers
        }
        return $this->priority;
    }

    /**
     * Apply a priority boost that expires after configured days.
     */
    public function applyPriorityBoost(): void
    {
        $days = \common\models\BookingRule::getInt('priority_boost_days', 7);
        $this->priority_boost_until = date('Y-m-d H:i:s', strtotime("+{$days} days"));
        $this->save(false);
    }

    public static function optsPriority(): array
    {
        return [
            self::PRIORITY_MAHASISWA => 'Mahasiswa',
            self::PRIORITY_STAFF     => 'Staff',
            self::PRIORITY_DOSEN     => 'Dosen',
            self::PRIORITY_ADMIN     => 'Admin',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds user by verification email token
     *
     * @param string $token verify email token
     * @return static|null
     */
    public static function findByVerificationToken($token)
    {
        return static::findOne([
            'verification_token' => $token,
            'status' => self::STATUS_INACTIVE
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }



    /**
     * column role ENUM value labels
     * @return string[]
     */
    public static function optsRole()
    {
        return [
            self::ROLE_ADMIN => 'admin',
            self::ROLE_USER => 'user',
        ];
    }

    /**
     * column sso_provider ENUM value labels
     * @return string[]
     */
    public static function optsSsoProvider()
    {
        return [
            self::SSO_PROVIDER_GOOGLE => 'google',
            self::SSO_PROVIDER_FIREBASE => 'firebase',
        ];
    }

    /**
     * @return string
     */
    public function displayRole()
    {
        return self::optsRole()[$this->role];
    }

    /**
     * @return bool
     */
    public function isRoleAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function setRoleToAdmin()
    {
        $this->role = self::ROLE_ADMIN;
    }

    /**
     * @return bool
     */
    public function isRoleUser()
    {
        return $this->role === self::ROLE_USER;
    }

    public function setRoleToUser()
    {
        $this->role = self::ROLE_USER;
    }

    /**
     * @return string
     */
    public function displaySsoProvider()
    {
        return self::optsSsoProvider()[$this->sso_provider];
    }

    /**
     * @return bool
     */
    public function isSsoProviderGoogle()
    {
        return $this->sso_provider === self::SSO_PROVIDER_GOOGLE;
    }

    public function setSsoProviderToGoogle()
    {
        $this->sso_provider = self::SSO_PROVIDER_GOOGLE;
    }

    /**
     * @return bool
     */
    public function isSsoProviderFirebase()
    {
        return $this->sso_provider === self::SSO_PROVIDER_FIREBASE;
    }

    public function setSsoProviderToFirebase()
    {
        $this->sso_provider = self::SSO_PROVIDER_FIREBASE;
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        // Not used in basic login, return null unless you're doing API login
        return null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Generates new token for email verification
     */
    public function generateEmailVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
}
