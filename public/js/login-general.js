"use strict";

const KTLogin = (function () {
    let _login;

    // --- Helper UI Functions ---

    const showAlert = (message, icon = "error", callback = null) => {
        swal.fire({
            text: message,
            icon: icon,
            buttonsStyling: false,
            confirmButtonText: "Ok, got it!",
            customClass: {
                confirmButton: "btn font-weight-bold btn-light-primary",
            },
        }).then(() => {
            KTUtil.scrollTop();
            if (callback) callback();
        });
    };

    const clearErrors = (formEl) => {
        formEl.find(".is-invalid").removeClass("is-invalid");
        formEl.find(".invalid-feedback.ajax-error").remove();
    };

    const displayErrors = (formEl, errors) => {
        $.each(errors, function (field, messages) {
            const input = formEl.find(`[name="${field}"]`);
            if (input.length) {
                input.addClass("is-invalid");
                input.after(
                    `<div class="invalid-feedback text-left ajax-error">${messages[0]}</div>`,
                );
            }
        });
    };

    const showLoading = (btn) => {
        btn.addClass("spinner spinner-right spinner-white pr-15 disabled").prop(
            "disabled",
            true,
        );
    };

    const hideLoading = (btn) => {
        btn.removeClass(
            "spinner spinner-right spinner-white pr-15 disabled",
        ).prop("disabled", false);
    };

    const showForm = (formType) => {
        _login.removeClass("login-forgot-on login-signin-on login-signup-on");
        _login.addClass(`login-${formType}-on`);
        KTUtil.animateClass(
            KTUtil.getById(`kt_login_${formType}_form`),
            "animate__animated animate__backInUp",
        );
    };

    // --- Core AJAX Submitter ---
    // Fungsi ini menangani semua pengiriman AJAX, termasuk fix Turnstile
    const submitAjaxForm = (url, formEl, btn, successCallback) => {
        let formData = formEl.serialize();

        // Paksa ambil token Turnstile untuk jaga-jaga jika serialize() terlewat
        let turnstileToken =
            formEl.find('[name="cf-turnstile-response"]').val() ||
            $('[name="cf-turnstile-response"]').val();
        if (turnstileToken && !formData.includes("cf-turnstile-response")) {
            formData +=
                "&cf-turnstile-response=" + encodeURIComponent(turnstileToken);
        }

        $.ajax({
            url: url,
            method: "POST",
            data: formData,
            headers: {
                Accept: "application/json",
            },
            beforeSend: () => showLoading(btn),
            success: successCallback,
            error: (xhr) => {
                // WAJIB: Reset turnstile di setiap error form manapun
                if (typeof turnstile !== "undefined") {
                    turnstile.reset();
                }

                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    displayErrors(formEl, xhr.responseJSON.errors);
                } else {
                    const errorMsg =
                        xhr.responseJSON?.message ||
                        "Terjadi kesalahan, silakan periksa kembali data Anda.";
                    showAlert(errorMsg, "error");
                }
            },
            complete: () => hideLoading(btn),
        });
    };

    // --- Form Handlers ---

    const handleSignInForm = () => {
        const formId = "kt_login_signin_form";
        const formEl = $(`#${formId}`);

        const validation = FormValidation.formValidation(
            KTUtil.getById(formId),
            {
                fields: {
                    username: {
                        validators: {
                            notEmpty: { message: "Username is required" },
                        },
                    },
                    password: {
                        validators: {
                            notEmpty: { message: "Password is required" },
                        },
                    },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    bootstrap: new FormValidation.plugins.Bootstrap(),
                },
            },
        );

        $("#kt_login_signin_submit").on("click", function (e) {
            e.preventDefault();
            const btn = $(this);
            clearErrors(formEl);

            validation.validate().then((status) => {
                if (status === "Valid") {
                    submitAjaxForm(
                        window.auth.login,
                        formEl,
                        btn,
                        (response) => {
                            window.location.href =
                                response.redirect || window.auth.dashboard;
                        },
                    );
                } else {
                    showAlert(
                        "Sorry, looks like there are some errors detected, please try again.",
                    );
                }
            });
        });

        // Toggle Forms
        $("#kt_login_forgot").on("click", (e) => {
            e.preventDefault();
            showForm("forgot");
        });
        $("#kt_login_signup").on("click", (e) => {
            e.preventDefault();
            showForm("signup");
        });
    };

    const handleSignUpForm = () => {
        const formId = "kt_login_signup_form";
        const formEl = $(`#${formId}`);
        const formDOM = KTUtil.getById(formId);

        const validation = FormValidation.formValidation(formDOM, {
            fields: {
                name: {
                    validators: { notEmpty: { message: "Name is required" } },
                },
                username: {
                    validators: {
                        notEmpty: { message: "Username is required" },
                    },
                },
                email: {
                    validators: {
                        notEmpty: { message: "Email address is required" },
                        emailAddress: {
                            message: "The value is not a valid email address",
                        },
                    },
                },
                password: {
                    validators: {
                        notEmpty: { message: "The password is required" },
                    },
                },
                password_confirmation: {
                    validators: {
                        notEmpty: {
                            message: "The password confirmation is required",
                        },
                        identical: {
                            compare: () =>
                                formDOM.querySelector('[name="password"]')
                                    .value,
                            message:
                                "The password and its confirm are not the same",
                        },
                    },
                },
                agree: {
                    validators: {
                        notEmpty: {
                            message: "You must accept the terms and conditions",
                        },
                    },
                },
            },
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap: new FormValidation.plugins.Bootstrap(),
            },
        });

        $("#kt_login_signup_submit").on("click", function (e) {
            e.preventDefault();
            const btn = $(this);
            clearErrors(formEl);

            validation.validate().then((status) => {
                if (status === "Valid") {
                    submitAjaxForm(window.auth.register, formEl, btn, () => {
                        showAlert(
                            "Registrasi berhasil! Silakan login.",
                            "success",
                            () => {
                                showForm("signin");
                                formEl[0].reset();
                            },
                        );
                    });
                } else {
                    showAlert(
                        "Sorry, looks like there are some errors detected, please try again.",
                    );
                }
            });
        });

        $("#kt_login_signup_cancel").on("click", (e) => {
            e.preventDefault();
            showForm("signin");
        });
    };

    const handleForgotForm = () => {
        const formId = "kt_login_forgot_form";
        const formEl = $(`#${formId}`);

        const validation = FormValidation.formValidation(
            KTUtil.getById(formId),
            {
                fields: {
                    email: {
                        validators: {
                            notEmpty: { message: "Email address is required" },
                            emailAddress: {
                                message:
                                    "The value is not a valid email address",
                            },
                        },
                    },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap(),
                },
            },
        );

        $("#kt_login_forgot_submit").on("click", function (e) {
            e.preventDefault();
            const btn = $(this);
            clearErrors(formEl);

            validation.validate().then((status) => {
                if (status === "Valid") {
                    submitAjaxForm(
                        window.auth.forgot,
                        formEl,
                        btn,
                        (response) => {
                            const successMsg =
                                response.status ||
                                "Tautan reset password telah dikirim ke email Anda.";
                            showAlert(successMsg, "success", () => {
                                showForm("signin");
                                formEl[0].reset();
                            });
                        },
                    );
                } else {
                    showAlert(
                        "Sorry, looks like there are some errors detected, please try again.",
                    );
                }
            });
        });

        $("#kt_login_forgot_cancel").on("click", (e) => {
            e.preventDefault();
            showForm("signin");
        });
    };

    // --- Public Functions ---
    return {
        init: function () {
            _login = $("#kt_login");
            handleSignInForm();
            handleSignUpForm();
            handleForgotForm();
        },
    };
})();

// Class Initialization
jQuery(document).ready(function () {
    KTLogin.init();
});
