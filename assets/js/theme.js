// KMPBS Modern Theme JavaScript

// Theme utilities and common functions
const KMPBSTheme = {
  // Toast notification system
  showToast: function (type, message, duration = 5000) {
    const toastContainer =
      document.querySelector(".toast-container") || this.createToastContainer();

    const toastId = "toast-" + Date.now();
    const toastHTML = `
            <div id="${toastId}" class="toast toast-modern toast-${type}" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <i class="fas fa-${this.getToastIcon(type)} me-2"></i>
                    <strong class="me-auto">${this.getToastTitle(type)}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            </div>
        `;

    toastContainer.insertAdjacentHTML("beforeend", toastHTML);

    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, {
      delay: duration,
      autohide: true,
    });

    toast.show();

    // Remove toast element after it's hidden
    toastElement.addEventListener("hidden.bs.toast", function () {
      this.remove();
    });
  },

  createToastContainer: function () {
    const container = document.createElement("div");
    container.className = "toast-container";
    document.body.appendChild(container);
    return container;
  },

  getToastIcon: function (type) {
    const icons = {
      success: "check-circle",
      error: "exclamation-circle",
      warning: "exclamation-triangle",
      info: "info-circle",
    };
    return icons[type] || "info-circle";
  },

  getToastTitle: function (type) {
    const titles = {
      success: "Başarılı",
      error: "Hata",
      warning: "Uyarı",
      info: "Bilgi",
    };
    return titles[type] || "Bilgi";
  },

  // Form validation utilities
  validateEmail: function (email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
  },

  validatePhone: function (phone) {
    const cleaned = phone.replace(/\D/g, "");
    return cleaned.length === 11 && cleaned.startsWith("0");
  },

  validateTCKN: function (tcno) {
    if (tcno.length !== 11) return false;

    let sum = 0;
    for (let i = 0; i < 10; i++) {
      sum += parseInt(tcno.charAt(i));
    }

    const oddSum =
      parseInt(tcno.charAt(0)) +
      parseInt(tcno.charAt(2)) +
      parseInt(tcno.charAt(4)) +
      parseInt(tcno.charAt(6)) +
      parseInt(tcno.charAt(8));
    const evenSum =
      parseInt(tcno.charAt(1)) +
      parseInt(tcno.charAt(3)) +
      parseInt(tcno.charAt(5)) +
      parseInt(tcno.charAt(7));

    const check1 = (oddSum * 7 - evenSum) % 10;
    const check2 = sum % 10;

    return (
      check1 === parseInt(tcno.charAt(9)) &&
      check2 === parseInt(tcno.charAt(10))
    );
  },

  validatePassword: function (password) {
    const minLength = 6;
    const hasLetter = /[a-zA-Z]/.test(password);
    const hasNumber = /\d/.test(password);

    if (password.length < minLength) {
      return {
        valid: false,
        strength: "weak",
        message: "Şifre en az 6 karakter olmalıdır",
      };
    }
    if (!hasLetter) {
      return {
        valid: false,
        strength: "weak",
        message: "Şifre en az bir harf içermelidir",
      };
    }
    if (!hasNumber) {
      return {
        valid: false,
        strength: "medium",
        message: "Şifre en az bir rakam içermelidir",
      };
    }

    return { valid: true, strength: "strong", message: "Güçlü şifre!" };
  },

  // Form field validation UI
  showFieldError: function (field, message) {
    field.classList.add("is-invalid");
    field.classList.remove("is-valid");

    // Remove existing error message
    const existingError =
      field.parentNode.parentNode.querySelector(".error-message");
    if (existingError) {
      existingError.remove();
    }

    // Add new error message
    const errorDiv = document.createElement("div");
    errorDiv.className = "error-message";
    errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i>${message}`;
    field.parentNode.parentNode.appendChild(errorDiv);
  },

  showFieldSuccess: function (field) {
    field.classList.remove("is-invalid");
    field.classList.add("is-valid");

    // Remove error message
    const existingError =
      field.parentNode.parentNode.querySelector(".error-message");
    if (existingError) {
      existingError.remove();
    }
  },

  clearFieldValidation: function (field) {
    field.classList.remove("is-invalid", "is-valid");
    const existingError =
      field.parentNode.parentNode.querySelector(".error-message");
    if (existingError) {
      existingError.remove();
    }
  },

  // Phone number formatting
  formatPhone: function (value) {
    // Remove all non-digits
    let cleaned = value.replace(/\D/g, "");

    // Limit to 11 digits
    if (cleaned.length > 11) {
      cleaned = cleaned.substring(0, 11);
    }

    // Format: 0XXX XXX XX XX
    if (cleaned.length > 0) {
      if (cleaned.length <= 4) {
        return cleaned;
      } else if (cleaned.length <= 7) {
        return cleaned.slice(0, 4) + " " + cleaned.slice(4);
      } else if (cleaned.length <= 9) {
        return (
          cleaned.slice(0, 4) +
          " " +
          cleaned.slice(4, 7) +
          " " +
          cleaned.slice(7)
        );
      } else {
        return (
          cleaned.slice(0, 4) +
          " " +
          cleaned.slice(4, 7) +
          " " +
          cleaned.slice(7, 9) +
          " " +
          cleaned.slice(9)
        );
      }
    }
    return cleaned;
  },

  // Loading state management
  setButtonLoading: function (button, loading = true) {
    if (loading) {
      button.classList.add("btn-loading");
      button.disabled = true;
      button.setAttribute("data-original-text", button.innerHTML);
    } else {
      button.classList.remove("btn-loading");
      button.disabled = false;
      const originalText = button.getAttribute("data-original-text");
      if (originalText) {
        button.innerHTML = originalText;
        button.removeAttribute("data-original-text");
      }
    }
  },

  // Confirmation dialog
  confirmAction: function (message, callback) {
    if (confirm(message)) {
      callback();
    }
  },

  // Initialize common features
  init: function () {
    this.initPasswordToggles();
    this.initFormValidation();
    this.initPhoneFormatting();
    this.initTableFeatures();
    this.initAnimations();
  },

  initPasswordToggles: function () {
    const toggles = document.querySelectorAll(".password-toggle");
    toggles.forEach((toggle) => {
      toggle.addEventListener("click", function () {
        const input = this.parentNode.querySelector(
          'input[type="password"], input[type="text"]'
        );
        const icon = this.querySelector("i");

        if (input.type === "password") {
          input.type = "text";
          icon.classList.remove("fa-eye");
          icon.classList.add("fa-eye-slash");
        } else {
          input.type = "password";
          icon.classList.remove("fa-eye-slash");
          icon.classList.add("fa-eye");
        }
      });
    });
  },

  initFormValidation: function () {
    // Email validation
    const emailInputs = document.querySelectorAll('input[type="email"]');
    emailInputs.forEach((input) => {
      input.addEventListener("blur", () => {
        const email = input.value.trim();
        if (email) {
          if (this.validateEmail(email)) {
            this.showFieldSuccess(input);
          } else {
            this.showFieldError(input, "Geçerli bir e-posta adresi girin");
          }
        }
      });

      input.addEventListener("input", () => {
        this.clearFieldValidation(input);
      });
    });

    // Password validation
    const passwordInputs = document.querySelectorAll(
      'input[type="password"][name="password"]'
    );
    passwordInputs.forEach((input) => {
      input.addEventListener("input", () => {
        const password = input.value;
        const strengthResult = this.validatePassword(password);

        // Update password strength indicator if exists
        const strengthIndicator =
          input.parentNode.parentNode.querySelector(".password-strength");
        if (strengthIndicator) {
          if (password.length === 0) {
            strengthIndicator.style.display = "none";
          } else {
            strengthIndicator.style.display = "block";
            strengthIndicator.className = `password-strength ${strengthResult.strength}`;
          }
        }

        this.clearFieldValidation(input);
      });

      input.addEventListener("blur", () => {
        const password = input.value;
        if (password) {
          const strengthResult = this.validatePassword(password);
          if (strengthResult.valid) {
            this.showFieldSuccess(input);
          } else {
            this.showFieldError(input, strengthResult.message);
          }
        }
      });
    });

    // Password confirmation
    const passwordConfirmInputs = document.querySelectorAll(
      'input[name="password_confirm"]'
    );
    passwordConfirmInputs.forEach((input) => {
      input.addEventListener("input", () => {
        const password = document.querySelector('input[name="password"]').value;
        const confirmPassword = input.value;

        this.clearFieldValidation(input);

        if (confirmPassword.length > 0) {
          if (password === confirmPassword) {
            this.showFieldSuccess(input);
          } else {
            this.showFieldError(input, "Şifreler eşleşmiyor");
          }
        }
      });
    });
  },

  initPhoneFormatting: function () {
    const phoneInputs = document.querySelectorAll(
      'input[type="tel"], input[name="phone"]'
    );
    phoneInputs.forEach((input) => {
      input.addEventListener("input", () => {
        input.value = this.formatPhone(input.value);
      });

      input.addEventListener("blur", () => {
        if (input.value) {
          if (this.validatePhone(input.value)) {
            this.showFieldSuccess(input);
          } else {
            this.showFieldError(
              input,
              "Geçerli bir telefon numarası girin (0XXX XXX XX XX)"
            );
          }
        }
      });
    });
  },

  initTableFeatures: function () {
    // Delete confirmations
    const deleteButtons = document.querySelectorAll(".btn-delete");
    deleteButtons.forEach((button) => {
      button.addEventListener("click", (e) => {
        e.preventDefault();
        const itemName = button.getAttribute("data-item") || "bu öğeyi";
        this.confirmAction(
          `${itemName} silmek istediğinizden emin misiniz?\n\nBu işlem geri alınamaz.`,
          () => {
            window.location.href = button.href;
          }
        );
      });
    });

    // Search functionality
    const searchInputs = document.querySelectorAll(".table-search");
    searchInputs.forEach((input) => {
      input.addEventListener("input", () => {
        const searchTerm = input.value.toLowerCase();
        const table = input.getAttribute("data-table")
          ? document.querySelector(input.getAttribute("data-table"))
          : input.closest(".card").querySelector("table");

        if (table) {
          const rows = table.querySelectorAll("tbody tr");
          rows.forEach((row) => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? "" : "none";
          });
        }
      });
    });
  },

  initAnimations: function () {
    // Intersection Observer for animations
    const observerOptions = {
      threshold: 0.1,
      rootMargin: "0px 0px -50px 0px",
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("fade-in");
        }
      });
    }, observerOptions);

    // Observe elements that should animate
    const animateElements = document.querySelectorAll(
      ".card, .stat-card, .alert-modern"
    );
    animateElements.forEach((element) => {
      observer.observe(element);
    });
  },
};

// Initialize theme when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
  KMPBSTheme.init();

  // Auto-dismiss alerts after 5 seconds
  const alerts = document.querySelectorAll(".alert");
  alerts.forEach((alert) => {
    setTimeout(() => {
      if (alert.parentElement) {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
      }
    }, 5000);
  });

  // Form submission with loading states
  const forms = document.querySelectorAll("form");
  forms.forEach((form) => {
    form.addEventListener("submit", function () {
      const submitBtn = form.querySelector('button[type="submit"]');
      if (submitBtn) {
        KMPBSTheme.setButtonLoading(submitBtn, true);

        // Reset button after 10 seconds (timeout fallback)
        setTimeout(() => {
          KMPBSTheme.setButtonLoading(submitBtn, false);
        }, 10000);
      }
    });
  });

  // Remember me functionality for login forms
  const rememberCheckbox = document.querySelector('input[name="remember"]');
  const emailInput = document.querySelector('input[name="email"]');

  if (rememberCheckbox && emailInput) {
    // Load remembered email
    const rememberedEmail = localStorage.getItem("kmpbs_remembered_email");
    if (rememberedEmail) {
      emailInput.value = rememberedEmail;
      rememberCheckbox.checked = true;
    }

    // Save/remove email on form submission
    const loginForm = emailInput.closest("form");
    if (loginForm) {
      loginForm.addEventListener("submit", function () {
        if (rememberCheckbox.checked) {
          localStorage.setItem("kmpbs_remembered_email", emailInput.value);
        } else {
          localStorage.removeItem("kmpbs_remembered_email");
        }
      });
    }
  }
});

// Make KMPBSTheme globally available
window.KMPBSTheme = KMPBSTheme;
