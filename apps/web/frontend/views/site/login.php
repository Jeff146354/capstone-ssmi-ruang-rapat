<?php
/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \common\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Sign In';
?>

<style>
.login-page {
    min-height: calc(100vh - 72px);
    background-image: url('https://images.unsplash.com/photo-1497366216548-37526070297c?w=1280&q=80');
    background-size: cover;
    background-position: center;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 88px 16px;
    position: relative;
}
.login-page::before {
    content: '';
    position: absolute; inset: 0;
    background: rgba(0,0,0,0.35);
}
/* Decorative blobs */
.login-blob-1 {
    position: absolute; width: 256px; height: 256px;
    right: 80px; top: 40px;
    background: #FFDBCC; border-radius: 9999px;
    filter: blur(32px); opacity: .15; pointer-events: none;
}
.login-blob-2 {
    position: absolute; width: 320px; height: 320px;
    left: 64px; bottom: 80px;
    background: #D9DFF5; border-radius: 9999px;
    filter: blur(32px); opacity: .25; pointer-events: none;
}

.login-card {
    position: relative; z-index: 1;
    width: 100%; max-width: 480px;
    background: rgba(255,255,255,.95);
    backdrop-filter: blur(2px);
    border-radius: 12px;
    border: 1px solid rgba(226,191,176,.3);
    box-shadow: 0 4px 20px rgba(0,0,0,.05);
    padding: 32px;
    display: flex; flex-direction: column; gap: 32px;
}

/* Header */
.login-card .lc-title {
    color: #151C27; font-size: 28px; font-weight: 700;
    line-height: 36px; text-align: center;
}
.login-card .lc-subtitle {
    color: #575E70; font-size: 14px; font-weight: 400;
    line-height: 20px; text-align: center; margin-top: 4px;
}

/* Fields */
.ipb-field-label {
    color: #151C27; font-size: 16px; font-weight: 600;
    line-height: 24px; margin-bottom: 4px; display: block;
}
.ipb-field-label-row {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 4px;
}
.ipb-field-label-row a {
    color: #A04100; font-size: 12px; font-weight: 500;
    letter-spacing: .24px; text-decoration: none;
}
.ipb-field-label-row a:hover { text-decoration: underline; }

.ipb-input {
    width: 100%;
    padding: 17px 16px 17px 40px;
    background: #fff;
    border: 1px solid #E2BFB0;
    border-radius: 8px;
    font-size: 14px; font-weight: 400; color: #151C27;
    outline: none; transition: border-color .15s;
}
.ipb-input::placeholder { color: #6B7280; }
.ipb-input:focus { border-color: #FF6B00; box-shadow: 0 0 0 3px rgba(255,107,0,.1); }
.ipb-input-wrap { position: relative; }
.ipb-input-icon {
    position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
    color: #575E70; font-size: 14px; pointer-events: none;
}
.ipb-input-icon-right {
    position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
    color: #575E70; font-size: 14px; cursor: pointer;
}

/* Remember me */
.ipb-checkbox-row {
    display: flex; align-items: center; gap: 8px;
}
.ipb-checkbox {
    width: 20px; height: 20px;
    border: 1px solid #E2BFB0; border-radius: 4px;
    accent-color: #FF6B00; cursor: pointer;
}
.ipb-checkbox-label {
    color: #575E70; font-size: 14px; font-weight: 400; line-height: 20px;
}

/* Buttons */
.btn-ipb-primary {
    width: 100%;
    padding: 16px;
    background: #FF6B00;
    color: #fff; font-size: 16px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .4px;
    border: none; border-radius: 8px; cursor: pointer;
    transition: background .15s;
}
.btn-ipb-primary:hover { background: #A04100; }

.btn-ipb-google {
    width: 100%;
    padding: 16px;
    background: #fff;
    color: #151C27; font-size: 16px; font-weight: 600;
    border: 1px solid #E2BFB0; border-radius: 8px; cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 16px;
    text-decoration: none; transition: background .15s;
}
.btn-ipb-google:hover { background: #fafafa; color: #151C27; }

/* Divider */
.ipb-divider {
    position: relative; text-align: center;
}
.ipb-divider::before {
    content: '';
    position: absolute; top: 50%; left: 0; right: 0;
    height: 1px; background: #E2BFB0;
}
.ipb-divider span {
    position: relative; background: #fff;
    padding: 0 16px;
    color: #575E70; font-size: 12px; font-weight: 500; letter-spacing: .24px;
}

/* Sign up link */
.ipb-signup-link {
    text-align: center;
    color: #575E70; font-size: 14px; font-weight: 400; line-height: 20px;
}
.ipb-signup-link a { color: #A04100; text-decoration: none; }
.ipb-signup-link a:hover { text-decoration: underline; }

/* Error */
.field-error { color: #ef4444; font-size: 12px; margin-top: 4px; }
</style>

<div class="login-page">
    <div class="login-blob-1"></div>
    <div class="login-blob-2"></div>

    <div class="login-card">
        <!-- Header -->
        <div>
            <div class="lc-title">Welcome Back</div>
            <div class="lc-subtitle">Sign in to access IPB University Facility Management</div>
        </div>

        <!-- Flash messages -->
        <?php if (Yii::$app->session->hasFlash('error')): ?>
            <div class="alert alert-danger py-2 mb-0">
                <?= Html::encode(Yii::$app->session->getFlash('error')) ?>
            </div>
        <?php endif ?>

        <!-- Form -->
        <?php $form = ActiveForm::begin([
            'id'                     => 'login-form',
            'enableClientValidation' => false,
            'fieldConfig'            => ['template' => '{input}{error}'],
        ]); ?>

        <div style="display:flex; flex-direction:column; gap:24px;">

            <!-- Username -->
            <div>
                <label class="ipb-field-label" for="loginform-username">Username</label>
                <div class="ipb-input-wrap">
                    <i class="fas fa-user ipb-input-icon"></i>
                    <?= $form->field($model, 'username')->textInput([
                        'class'       => 'ipb-input',
                        'placeholder' => 'Enter your username',
                        'autofocus'   => true,
                        'id'          => 'loginform-username',
                    ])->label(false) ?>
                </div>
            </div>

            <!-- Password -->
            <div>
                <div class="ipb-field-label-row">
                    <label class="ipb-field-label mb-0" for="loginform-password">Password</label>
                    <a href="<?= \yii\helpers\Url::to(['/site/request-password-reset']) ?>">Lupa Password?</a>
                </div>
                <div class="ipb-input-wrap">
                    <i class="fas fa-lock ipb-input-icon"></i>
                    <?= $form->field($model, 'password')->passwordInput([
                        'class'       => 'ipb-input',
                        'placeholder' => '••••••••',
                        'id'          => 'loginform-password',
                    ])->label(false) ?>
                    <i class="fas fa-eye ipb-input-icon-right" id="togglePassword" title="Tampilkan password"></i>
                </div>
            </div>

            <!-- Remember me -->
            <div class="ipb-checkbox-row">
                <?= $form->field($model, 'rememberMe')->checkbox([
                    'class'    => 'ipb-checkbox',
                    'template' => '{input}',
                ])->label(false) ?>
                <label class="ipb-checkbox-label" for="loginform-rememberme">Remember Me</label>
            </div>

            <!-- Submit -->
            <button type="submit" class="btn-ipb-primary">LOGIN</button>

            <!-- Divider -->
            <div class="ipb-divider"><span>OR</span></div>

            <!-- Google login -->
            <a href="#" class="btn-ipb-google" id="google-login">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                Login with Google
            </a>

            <!-- Sign up -->
            <div class="ipb-signup-link">
                Don't have an account? <a href="<?= \yii\helpers\Url::to(['/site/signup']) ?>">Sign Up</a>
            </div>

        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<script type="module">
import { initializeApp } from "https://www.gstatic.com/firebasejs/11.9.1/firebase-app.js";
import { getAuth, signInWithPopup, GoogleAuthProvider } from "https://www.gstatic.com/firebasejs/11.9.1/firebase-auth.js";

const firebaseConfig = {
    apiKey: "AIzaSyBjTlnbDoPMFiCO5IKwKPtFfy5OahPBgj0",
    authDomain: "kepston17-8c88b.firebaseapp.com",
    projectId: "kepston17-8c88b",
    storageBucket: "kepston17-8c88b.firebasestorage.app",
    messagingSenderId: "121678774667",
    appId: "1:121678774667:web:ecd5c8e9a61cd9cc176f8e",
    measurementId: "G-9GBJQB2DJ6"
};

const app = initializeApp(firebaseConfig);
const auth = getAuth(app);
const provider = new GoogleAuthProvider();

document.getElementById('google-login').addEventListener('click', async (e) => {
    e.preventDefault();
    try {
        const result = await signInWithPopup(auth, provider);
        const token = await result.user.getIdToken();
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) throw new Error('CSRF token not found');

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= \yii\helpers\Url::to(['site/firebase-login']) ?>';

        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '<?= Yii::$app->request->csrfParam ?>';
        csrfInput.value = csrfToken.content;
        form.appendChild(csrfInput);

        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'idToken';
        input.value = token;
        form.appendChild(input);

        document.body.appendChild(form);
        form.submit();
    } catch (error) {
        console.error('Login failed:', error);
        alert('Login dengan Google gagal. Pastikan akun Anda sudah terdaftar melalui Sign Up Google terlebih dahulu.');
    }
});
</script>

<script>
// Toggle password visibility
document.getElementById('togglePassword').addEventListener('click', function () {
    const input = document.getElementById('loginform-password');
    const isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';
    this.classList.toggle('fa-eye', !isPassword);
    this.classList.toggle('fa-eye-slash', isPassword);
});
</script>
