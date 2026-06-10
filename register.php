<?php
require_once __DIR__ . '/includes/app.php';

if (current_user()) {
    redirect('index.php');
}

$error = null;
$name = '';
$email = '';
$phone = '';
$city = '';
$fieldErrors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $password = (string)($_POST['password'] ?? '');

    if ($name === '') {
        $fieldErrors['name'] = 'Please enter your name.';
    }
    if ($email === '') {
        $fieldErrors['email'] = 'Please enter your email address.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $fieldErrors['email'] = 'Please enter a valid email address.';
    }
    if ($phone === '') {
        $fieldErrors['phone'] = 'Please enter your phone number.';
    }
    if ($city === '') {
        $fieldErrors['city'] = 'Please enter your city.';
    }
    if ($password === '') {
        $fieldErrors['password'] = 'Please enter a password.';
    } elseif (strlen($password) < 6) {
        $fieldErrors['password'] = 'Your password must contain at least 6 characters.';
    }

    if ($fieldErrors) {
        $fieldLabels = [
            'name' => 'Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'city' => 'City',
            'password' => 'Password',
        ];
        $invalidLabels = array_map(
            static fn(string $field): string => $fieldLabels[$field],
            array_keys($fieldErrors)
        );
        $error = implode(', ', $invalidLabels) . (count($invalidLabels) === 1 ? ' needs your attention.' : ' need your attention.');
    } else {
        try {
            $stmt = db()->prepare('INSERT INTO users (name, email, phone, address, password_hash, role) VALUES (?, ?, ?, ?, ?, "user")');
            $stmt->execute([$name, $email, $phone, $city, password_hash($password, PASSWORD_DEFAULT)]);
            flash('Your account was created. You can sign in now.');
            redirect('login.php');
        } catch (Throwable $exception) {
            $error = 'We could not create the account. That email may already be registered.';
        }
    }
}

render_header(t('register'));
?>
<section class="auth-panel auth-panel-wide">
  <div class="auth-heading">
    <h1>Create your account</h1>
    <p>You will use it to request appointments and follow their progress.</p>
  </div>
  <?php if ($error): ?><p class="notice error"><?= e($error) ?></p><?php endif; ?>
  <p class="auth-form-message" role="alert" hidden></p>
  <form method="post" class="stack-form auth-register-form" novalidate>
    <label>
      <span><?= e(t('name')) ?></span>
      <input type="text" name="name" autocomplete="name" required value="<?= e($name) ?>" aria-describedby="name-error"<?= isset($fieldErrors['name']) ? ' class="is-invalid" aria-invalid="true"' : '' ?>>
      <small class="field-error" id="name-error"><?= e($fieldErrors['name'] ?? '') ?></small>
    </label>
    <label>
      <span><?= e(t('email')) ?></span>
      <input type="email" name="email" autocomplete="email" required value="<?= e($email) ?>" aria-describedby="email-error"<?= isset($fieldErrors['email']) ? ' class="is-invalid" aria-invalid="true"' : '' ?>>
      <small class="field-error" id="email-error"><?= e($fieldErrors['email'] ?? '') ?></small>
    </label>
    <label>
      <span><?= e(t('phone')) ?></span>
      <input type="tel" name="phone" autocomplete="tel" required value="<?= e($phone) ?>" aria-describedby="phone-error"<?= isset($fieldErrors['phone']) ? ' class="is-invalid" aria-invalid="true"' : '' ?>>
      <small class="field-error" id="phone-error"><?= e($fieldErrors['phone'] ?? '') ?></small>
    </label>
    <label>
      <span><?= e(t('city')) ?></span>
      <input type="text" name="city" autocomplete="address-level2" required value="<?= e($city) ?>" aria-describedby="city-error"<?= isset($fieldErrors['city']) ? ' class="is-invalid" aria-invalid="true"' : '' ?>>
      <small class="field-error" id="city-error"><?= e($fieldErrors['city'] ?? '') ?></small>
    </label>
    <label class="auth-password-field">
      <span><?= e(t('password')) ?></span>
      <input type="password" name="password" autocomplete="new-password" required minlength="6" aria-describedby="password-help password-error"<?= isset($fieldErrors['password']) ? ' class="is-invalid" aria-invalid="true"' : '' ?>>
      <small id="password-help">Use at least 6 characters.</small>
      <small class="field-error" id="password-error"><?= e($fieldErrors['password'] ?? '') ?></small>
    </label>
    <button class="button primary full auth-submit" type="submit"><?= e(t('create_account')) ?></button>
  </form>
  <p class="muted-line">Already have an account? <a href="login.php"><?= e(t('sign_in')) ?></a></p>
</section>
<script>
  const registerForm = document.querySelector('.auth-register-form');
  const formMessage = document.querySelector('.auth-form-message');
  const messages = {
    name: 'Please enter your name.',
    email: 'Please enter your email address.',
    phone: 'Please enter your phone number.',
    city: 'Please enter your city.',
    password: 'Please enter a password.'
  };
  const fieldLabels = {
    name: 'Name',
    email: 'Email',
    phone: 'Phone',
    city: 'City',
    password: 'Password'
  };

  function validateRegisterField(field) {
    const errorElement = document.getElementById(`${field.name}-error`);
    let message = '';

    if (field.value.trim() === '') {
      message = messages[field.name];
    } else if (field.name === 'email' && !field.validity.valid) {
      message = 'Please enter a valid email address.';
    } else if (field.name === 'password' && field.value.length < 6) {
      message = 'Your password must contain at least 6 characters.';
    }

    field.classList.toggle('is-invalid', message !== '');
    field.setAttribute('aria-invalid', message !== '' ? 'true' : 'false');
    errorElement.textContent = message;
    return message === '';
  }

  if (registerForm && formMessage) {
    const fields = [...registerForm.querySelectorAll('input')];

    fields.forEach((field) => {
      field.addEventListener('blur', () => validateRegisterField(field));
      field.addEventListener('input', () => {
        if (field.classList.contains('is-invalid')) {
          validateRegisterField(field);
        }
      });
    });

    registerForm.addEventListener('submit', (event) => {
      const invalidFields = fields.filter((field) => !validateRegisterField(field));
      const firstInvalid = invalidFields[0];
      formMessage.hidden = !firstInvalid;

      if (firstInvalid) {
        event.preventDefault();
        const emptyFields = invalidFields
          .filter((field) => field.value.trim() === '')
          .map((field) => fieldLabels[field.name]);

        if (emptyFields.length > 0) {
          const lastField = emptyFields.pop();
          const fieldList = emptyFields.length > 0
            ? `${emptyFields.join(', ')} and ${lastField}`
            : lastField;
          formMessage.textContent = `${fieldList} ${emptyFields.length > 0 ? 'are' : 'is'} required.`;
        } else {
          formMessage.textContent = 'Please correct the information shown below.';
        }
        firstInvalid.focus();
      }
    });
  }
</script>
<?php render_footer(); ?>
