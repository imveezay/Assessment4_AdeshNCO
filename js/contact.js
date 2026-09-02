// ============================================================
// Adesh & Co. — Enquiry form: client-side validation.
// This ONLY provides fast in-browser feedback. The form still
// submits to contact.php, which re-validates everything and
// performs the actual database insert server-side.
// ============================================================

document.addEventListener('DOMContentLoaded', function () {
  var form = document.getElementById('contact-form');
  if (!form) return;

  var status = document.getElementById('form-status');

  var validators = {
    name: function (v) {
      if (!v.trim()) return 'Enter your full name.';
      if (v.trim().length < 2) return 'Name looks too short.';
      return '';
    },
    email: function (v) {
      var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!v.trim()) return 'Enter your email address.';
      if (!re.test(v.trim())) return 'Enter a valid email address, e.g. name@example.com.';
      return '';
    },
    phone: function (v) {
      if (!v.trim()) return '';
      var re = /^[0-9 +()-]{6,}$/;
      if (!re.test(v.trim())) return 'Enter a valid phone number.';
      return '';
    },
    eventType: function (v) {
      if (!v) return 'Select the type of event.';
      return '';
    },
    eventDate: function (v) {
      if (!v) return 'Select a preferred date.';
      var today = new Date();
      today.setHours(0, 0, 0, 0);
      var chosen = new Date(v);
      if (chosen < today) return 'Choose a date in the future.';
      return '';
    },
    message: function (v) {
      if (!v.trim()) return 'Tell us a little about the event you\'re planning.';
      if (v.trim().length < 10) return 'A few more details would help (10+ characters).';
      return '';
    },
    consent: function (checked) {
      if (!checked) return 'Please agree to be contacted before submitting.';
      return '';
    }
  };

  function showFieldError(field, message) {
    var errorEl = document.getElementById(field + '-error');
    if (errorEl) errorEl.textContent = message;
    var input = document.getElementById(field);
    if (input) input.setAttribute('aria-invalid', message ? 'true' : 'false');
  }

  function validateField(field) {
    var input = document.getElementById(field);
    if (!input) return true;
    var value = input.type === 'checkbox' ? input.checked : input.value;
    var message = validators[field] ? validators[field](value) : '';
    showFieldError(field, message);
    return !message;
  }

  Object.keys(validators).forEach(function (field) {
    var input = document.getElementById(field);
    if (!input) return;
    input.addEventListener('blur', function () { validateField(field); });
    input.addEventListener('input', function () {
      if (input.getAttribute('aria-invalid') === 'true') validateField(field);
    });
    input.addEventListener('change', function () {
      if (input.tagName === 'SELECT' || input.type === 'date') validateField(field);
    });
  });

  form.addEventListener('submit', function (e) {
    var fields = Object.keys(validators);
    var allValid = true;
    fields.forEach(function (field) {
      if (!validateField(field)) allValid = false;
    });

    if (!allValid) {
      e.preventDefault(); // stop the browser posting an invalid form
      status.classList.remove('success');
      status.textContent = 'Please fix the highlighted fields below and try again.';
      status.classList.add('error', 'is-visible');
      status.setAttribute('role', 'alert');
      var firstInvalid = form.querySelector('[aria-invalid="true"]');
      if (firstInvalid) firstInvalid.focus();
      return;
    }

    // Valid: let the form submit normally to contact.php (server validates
    // again and inserts into the database, then redisplays this page with
    // a server-rendered success/error message).
  });
});
