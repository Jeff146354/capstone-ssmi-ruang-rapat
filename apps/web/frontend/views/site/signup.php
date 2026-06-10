<?php
/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \frontend\models\SignupForm $model */

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Url;

$this->title = 'Sign Up';
?>

<style>
.signup-page {
    min-height: calc(100vh - 72px);
    background: linear-gradient(135deg, #151C27 0%, #1e2d45 60%, #2a1a0e 100%);
    display: flex; align-items: center; justify-content: center;
    padding: 60px 16px;
    position: relative; overflow: hidden;
}
.signup-page::before {
    content: '';
    position: absolute; inset: 0;
    background:
        radial-gradient(ellipse 50% 40% at 85% 15%, rgba(255,107,0,.12) 0%, transparent 60%),
        radial-gradient(ellipse 40% 50% at 15% 85%, rgba(99,102,241,.1) 0%, transparent 50%);
    pointer-events: none;
}
.signup-card {
    position: relative; z-index: 1;
    width: 100%; max-width: 480px;
    background: rgba(255,255,255,.97);
    backdrop-filter: blur(4px);
    border-radius: 16px;
    border: 1px solid rgba(226,191,176,.3);
    box-shadow: 0 20px 60px rgba(0,0,0,.2);
    padding: 40px 36px;
}
.signup-card .sc-title {
    color: #151C27; font-size: 28px; font-weight: 700;
    text-align: center; margin-bottom: 6px;
}
.signup-card .sc-subtitle {
    color: #575E70; font-size: 14px; text-align: center;
    margin-bottom: 32px;
}
.ipb-field { margin-bottom: 20px; }
.ipb-field label {
    display: block;
    color: #151C27; font-size: 15px; font-weight: 600;
    margin-bottom: 6px;
}
.ipb-field .input-wrap { position: relative; }
.ipb-field .input-icon {
    position: absolute; left: 14px; top: 22px;
    color: #575E70; font-size: 14px; pointer-events: none;
    z-index: 1;
}
.ipb-field input,
.ipb-field .form-control {
    width: 100%;
    padding: 14px 16px 14px 42px !important;
    background: #fff;
    border: 1.5px solid #E2BFB0 !important;
    border-radius: 10px !important;
    font-size: 15px; color: #151C27;
    outline: none; transition: border-color .15s, box-shadow .15s;
    box-shadow: none !important;
}
.ipb-field input::placeholder,
.ipb-field .form-control::placeholder { color: #9CA3AF; }
.ipb-field input:focus,
.ipb-field .form-control:focus {
    border-color: #FF6B00 !important;
    box-shadow: 0 0 0 3px rgba(255,107,0,.12) !important;
}
.ipb-field .help-block,
.ipb-field .invalid-feedback { color: #ef4444; font-size: 12px; margin-top: 4px; display: block; }
.ipb-field .has-error .form-control,
.ipb-field .is-invalid { border-color: #ef4444 !important; }
.ipb-field .form-group { margin-bottom: 0; }

.btn-signup-primary {
    width: 100%; padding: 15px;
    background: #FF6B00; color: #fff;
    font-size: 16px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .5px;
    border: none; border-radius: 10px; cursor: pointer;
    box-shadow: 0 4px 15px rgba(255,107,0,.3);
    transition: background .15s, transform .1s;
    margin-top: 8px;
}
.btn-signup-primary:hover {
    background: #A04100;
    transform: translateY(-1px);
}

.ipb-divider {
    position: relative; text-align: center; margin: 24px 0;
}
.ipb-divider::before {
    content: '';
    position: absolute; top: 50%; left: 0; right: 0;
    height: 1px; background: #E2BFB0;
}
.ipb-divider span {
    position: relative; background: #fff;
    padding: 0 14px;
    color: #9CA3AF; font-size: 12px; font-weight: 600;
    letter-spacing: .5px;
}

.btn-google {
    width: 100%; padding: 14px;
    background: #fff; color: #151C27;
    font-size: 15px; font-weight: 600;
    border: 1.5px solid #E2BFB0; border-radius: 10px;
    cursor: pointer; display: flex; align-items: center;
    justify-content: center; gap: 12px;
    text-decoration: none; transition: background .15s, border-color .15s;
}
.btn-google:hover { background: #fafafa; border-color: #d1a090; color: #151C27; }

.signin-link {
    text-align: center; margin-top: 20px;
    color: #575E70; font-size: 14px;
}
.signin-link a { color: #A04100; font-weight: 600; text-decoration: none; }
.signin-link a:hover { text-decoration: underline; }
</style>

<div class="signup-page">
    <div class="signup-card">
        <div class="sc-title">Buat Akun</div>
        <div class="sc-subtitle">Daftar untuk mengakses IPB University Facility Management</div>

        <?php if (Yii::$app->session->hasFlash('error')): ?>
            <div class="alert alert-danger py-2"><?= Html::encode(Yii::$app->session->getFlash('error')) ?></div>
        <?php endif ?>
        <?php if (Yii::$app->session->hasFlash('success')): ?>
            <div class="alert alert-success py-2"><?= Html::encode(Yii::$app->session->getFlash('success')) ?></div>
        <?php endif ?>

        <?php $form = ActiveForm::begin([
            'id'          => 'form-signup',
            'fieldConfig' => ['template' => '{input}{error}'],
        ]); ?>

        <!-- Username -->
        <div class="ipb-field">
            <label>Username</label>
            <div class="input-wrap">
                <i class="fas fa-user input-icon"></i>
                <?= $form->field($model, 'username')->textInput([
                    'placeholder' => 'Masukkan username',
                    'autofocus'   => true,
                    'style'       => 'padding-left:42px',
                    'class'       => '',
                ])->label(false) ?>
            </div>
        </div>

        <!-- Email -->
        <div class="ipb-field">
            <label>Email</label>
            <div class="input-wrap">
                <i class="fas fa-envelope input-icon"></i>
                <?= $form->field($model, 'email')->input('email', [
                    'placeholder' => 'nama@ipb.ac.id',
                    'style'       => 'padding-left:42px',
                    'class'       => '',
                ])->label(false) ?>
            </div>
        </div>

        <!-- Password -->
        <div class="ipb-field">
            <label>Password</label>
            <div class="input-wrap">
                <i class="fas fa-lock input-icon"></i>
                <?= $form->field($model, 'password')->passwordInput([
                    'placeholder' => '••••••••',
                    'style'       => 'padding-left:42px',
                    'class'       => '',
                ])->label(false) ?>
            </div>
        </div>

        <button type="submit" class="btn-signup-primary">DAFTAR SEKARANG</button>

        <?php ActiveForm::end(); ?>

        <div class="ipb-divider"><span>ATAU</span></div>

        <!-- Google signup -->
        <button id="google-signup" class="btn-google">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
            </svg>
            Daftar dengan Google
        </button>

        <div class="signin-link">
            Sudah punya akun? <a href="<?= Url::to(['/site/login']) ?>">Sign In</a>
        </div>
    </div>
</div>

<script type="module">
    import { initializeApp } from "https://www.gstatic.com/firebasejs/11.9.1/firebase-app.js";
    import { getAuth, signInWithPopup, GoogleAuthProvider } from "https://www.gstatic.com/firebasejs/11.9.1/firebase-auth.js";

    const firebaseConfig = {
    apiKey: "AIzaSyCQAezj1CPbr7sR79GhHgWZ2R_y56pBJfE",
    authDomain: "kepston17-8c88b-20c7d.firebaseapp.com",
    projectId: "kepston17-8c88b-20c7d",
    storageBucket: "kepston17-8c88b-20c7d.firebasestorage.app",
    messagingSenderId: "20528068934",
    appId: "1:20528068934:web:2fe85dfc2d9db9d8bfcd47",
    measurementId: "G-5CNPEB9SHD"
    }; 

    const app = initializeApp(firebaseConfig);
    const auth = getAuth(app);
    const provider = new GoogleAuthProvider();

    document.getElementById('google-signup').addEventListener('click', async () => {
        try {
            const result = await signInWithPopup(auth, provider);
            const token = await result.user.getIdToken();
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) throw new Error('CSRF token not found');

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?= Url::to(['site/firebase-signup']) ?>';

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
            console.error('Signup failed:', error);
            alert('Signup dengan Google gagal. Silakan coba lagi.');
        }
    });
</script>
