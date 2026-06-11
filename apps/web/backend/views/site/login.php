<?php
/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \common\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Admin Login';
?>

<style>
.admin-login-page {
    min-height: 100vh;
    display: flex; align-items: center; justify-content: center;
    padding: 40px 16px;
    background: linear-gradient(135deg, #151C27 0%, #1a2332 50%, #0f1419 100%);
    position: relative; overflow: hidden;
}
.admin-login-page::before {
    content: '';
    position: absolute; inset: 0;
    background:
        radial-gradient(ellipse 40% 50% at 80% 20%, rgba(255,107,0,.08) 0%, transparent 60%),
        radial-gradient(ellipse 30% 40% at 20% 80%, rgba(99,102,241,.06) 0%, transparent 50%);
    pointer-events: none;
}

.admin-login-card {
    position: relative; z-index: 1;
    width: 100%; max-width: 420px;
    background: rgba(26,35,50,.9);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255,107,0,.15);
    border-radius: 20px;
    box-shadow: 0 24px 80px rgba(0,0,0,.4);
    padding: 40px 36px;
}

.admin-login-card .brand {
    text-align: center; margin-bottom: 32px;
}
.admin-login-card .brand-icon {
    width: 56px; height: 56px; border-radius: 14px;
    background: rgba(255,107,0,.12); border: 1px solid rgba(255,107,0,.3);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 16px; font-size: 24px; color: #FF6B00;
}
.admin-login-card .brand h2 {
    color: #fff; font-size: 22px; font-weight: 700; margin: 0 0 4px;
}
.admin-login-card .brand p {
    color: #8A94A6; font-size: 13px; margin: 0;
}

/* Fields */
.admin-field { margin-bottom: 20px; }
.admin-field label {
    display: block; color: #C8CED8; font-size: 13px;
    font-weight: 600; margin-bottom: 6px;
}
.admin-field .input-wrap { position: relative; }
.admin-field .input-icon {
    position: absolute; left: 14px; top: 0;
    height: 46px; display: flex; align-items: center;
    color: #6B7684; font-size: 14px; pointer-events: none; z-index: 1;
}
.admin-field input,
.admin-field .form-control {
    width: 100%;
    padding: 13px 16px 13px 42px !important;
    background: rgba(255,255,255,.05) !important;
    border: 1.5px solid rgba(255,255,255,.1) !important;
    border-radius: 10px !important;
    font-size: 14px; color: #E8ECF2 !important;
    outline: none; transition: border-color .15s, box-shadow .15s;
    box-shadow: none !important;
}
.admin-field input::placeholder { color: #6B7684 !important; }
.admin-field input:focus,
.admin-field .form-control:focus {
    border-color: #FF6B00 !important;
    box-shadow: 0 0 0 3px rgba(255,107,0,.15) !important;
    background: rgba(255,255,255,.08) !important;
}
.admin-field .help-block,
.admin-field .invalid-feedback { color: #ef4444; font-size: 12px; margin-top: 4px; display: block; }
.admin-field .form-group { margin-bottom: 0; }

/* Remember me */
.admin-checkbox {
    display: flex; align-items: center; gap: 10px; margin-bottom: 24px;
}
.admin-checkbox input[type="checkbox"] {
    width: 18px !important; height: 18px !important;
    padding: 0 !important; accent-color: #FF6B00;
    border-radius: 4px !important; margin: 0 !important;
    flex-shrink: 0;
}
.admin-checkbox label {
    color: #8A94A6; font-size: 13px; font-weight: 400; margin: 0;
    cursor: pointer;
}
/* Hide Yii's form-group wrapper margin inside checkbox area */
.admin-checkbox .form-group { margin-bottom: 0; }

/* Submit */
.btn-admin-login {
    width: 100%; padding: 14px;
    background: #FF6B00; color: #fff;
    font-size: 15px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .5px;
    border: none; border-radius: 10px; cursor: pointer;
    box-shadow: 0 4px 20px rgba(255,107,0,.3);
    transition: background .15s, transform .1s;
}
.btn-admin-login:hover { background: #A04100; transform: translateY(-1px); }

/* Alert */
.admin-alert {
    background: rgba(239,68,68,.1); border: 1px solid rgba(239,68,68,.3);
    color: #fca5a5; border-radius: 10px;
    padding: 12px 16px; margin-bottom: 20px;
    font-size: 13px; display: flex; align-items: center; gap: 8px;
}
.admin-alert i { color: #ef4444; }

.admin-footer {
    text-align: center; margin-top: 24px;
    color: #6B7684; font-size: 12px;
}
</style>

<div class="admin-login-page">
    <div class="admin-login-card">

        <div class="brand">
            <div class="brand-icon"><i class="fas fa-shield-alt"></i></div>
            <h2>Admin Panel</h2>
            <p>IPB Reserve — Facility Management</p>
        </div>

        <?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
            <div class="admin-alert">
                <i class="fas fa-exclamation-circle"></i>
                <?= $message ?>
            </div>
        <?php endforeach ?>

        <?php $form = ActiveForm::begin([
            'id'          => 'login-form',
            'fieldConfig' => ['template' => '{input}{error}'],
        ]); ?>

        <div class="admin-field">
            <label>Username</label>
            <div class="input-wrap">
                <i class="fas fa-user input-icon"></i>
                <?= $form->field($model, 'username')->textInput([
                    'placeholder' => 'Enter admin username',
                    'autofocus'   => true,
                    'class'       => 'form-control',
                ])->label(false) ?>
            </div>
        </div>

        <div class="admin-field">
            <label>Password</label>
            <div class="input-wrap">
                <i class="fas fa-lock input-icon"></i>
                <?= $form->field($model, 'password')->passwordInput([
                    'placeholder' => '••••••••',
                    'class'       => 'form-control',
                ])->label(false) ?>
            </div>
        </div>

        <div class="admin-checkbox">
            <?= $form->field($model, 'rememberMe')->checkbox([
                'template' => '{input}',
            ])->label(false) ?>
            <label>Remember me</label>
        </div>

        <button type="submit" class="btn-admin-login">
            <i class="fas fa-sign-in-alt me-2"></i>LOGIN
        </button>

        <?php ActiveForm::end(); ?>

        <div class="admin-footer">
            © <?= date('Y') ?> IPB Reserve — SSMI IPB University
        </div>
    </div>
</div>
