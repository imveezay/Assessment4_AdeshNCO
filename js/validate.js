// ============================================================
// Adesh & Co. — Small reusable client-side validation helper.
// Mirrors the server-side rules in includes/functions.php so
// feedback is instant, but the server always re-checks everything.
// ============================================================

function acValidateField(id, message) {
  var input = document.getElementById(id);
  var errorEl = document.getElementById(id + '-error');
  if (errorEl) errorEl.textContent = message || '';
  if (input) input.setAttribute('aria-invalid', message ? 'true' : 'false');
  return !message;
}

var acValidators = {
  name: function (v) {
    if (!v.trim()) return 'Enter your full name.';
    if (v.trim().length < 2) return 'Name looks too short.';
    return '';
  },
  email: function (v) {
    var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!v.trim()) return 'Enter your email address.';
    if (!re.test(v.trim())) return 'Enter a valid email address.';
    return '';
  },
  password: function (v) {
    if (v.length < 8) return 'Password must be at least 8 characters.';
    if (!/[A-Z]/.test(v)) return 'Password needs at least one uppercase letter.';
    if (!/[0-9]/.test(v)) return 'Password needs at least one number.';
    return '';
  },
  confirmPassword: function (v, passwordValue) {
    if (v !== passwordValue) return 'Passwords do not match.';
    return '';
  },
  quote: function (v) {
    if (!v.trim()) return 'Share a few words about your experience.';
    if (v.trim().length < 10) return 'A little more detail helps other clients (10+ characters).';
    return '';
  }
};

document.addEventListener('DOMContentLoaded', function () {
  // Wires up live validation for any form with [data-validate] fields.
  document.querySelectorAll('[data-validate]').forEach(function (input) {
    var rule = input.getAttribute('data-validate');
    input.addEventListener('blur', function () { runValidator(input, rule); });
    input.addEventListener('input', function () {
      if (input.getAttribute('aria-invalid') === 'true') runValidator(input, rule);
    });
  });

  function runValidator(input, rule) {
    var value = input.value;
    var message = '';
    if (rule === 'confirmPassword') {
      var pwInput = document.getElementById('password');
      message = acValidators.confirmPassword(value, pwInput ? pwInput.value : '');
    } else if (acValidators[rule]) {
      message = acValidators[rule](value);
    }
    acValidateField(input.id, message);
  }

  document.querySelectorAll('form[data-validate-form]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      var allValid = true;
      form.querySelectorAll('[data-validate]').forEach(function (input) {
        var rule = input.getAttribute('data-validate');
        var value = input.value;
        var message = '';
        if (rule === 'confirmPassword') {
          var pwInput = document.getElementById('password');
          message = acValidators.confirmPassword(value, pwInput ? pwInput.value : '');
        } else if (acValidators[rule]) {
          message = acValidators[rule](value);
        }
        if (!acValidateField(input.id, message)) allValid = false;
      });

      if (!allValid) {
        e.preventDefault();
        var status = form.querySelector('.form-status');
        if (status) {
          status.textContent = 'Please fix the highlighted fields below and try again.';
          status.classList.remove('success');
          status.classList.add('error', 'is-visible');
        }
        var firstInvalid = form.querySelector('[aria-invalid="true"]');
        if (firstInvalid) firstInvalid.focus();
      }
    });
  });
});
