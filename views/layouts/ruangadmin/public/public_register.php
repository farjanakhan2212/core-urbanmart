<?php
global $reg_errors, $reg_old, $csrf_token;
$reg_errors = isset($reg_errors) ? $reg_errors : [];
$reg_old = isset($reg_old) ? $reg_old : [];
$old = function ($key) use ($reg_old) {
    return isset($reg_old[$key]) ? htmlspecialchars((string)$reg_old[$key]) : '';
};
$error = function ($key) use ($reg_errors) {
    return isset($reg_errors[$key]) ? htmlspecialchars((string)$reg_errors[$key]) : '';
};
$has_error = function ($key) use ($reg_errors) {
    return isset($reg_errors[$key]) ? ' is-invalid' : '';
};
$aria_desc = function ($key) {
    return 'aria-describedby="' . $key . '-error"';
};
?>

<?php section("public/public_header.php"); ?>

<style>
    .um-reg-add, .um-reg-body {
        -webkit-font-smoothing: antialiased;
    }
    #um-register {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        background-color: #0a1f44;
    }
    /* Full-screen grocery themed background */
    .um-reg-cover {
        position: relative;
        flex: 1 0 auto;
        display: flex;
        flex-direction: column;
        background-image: url('<?= $base_url ?>/img/grocery-1232944-1280-jpg.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;
    }
    .um-reg-cover::before {
        content: "";
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background: linear-gradient(150deg, rgba(9, 28, 66, 0.94) 0%, rgba(10, 82, 38, 0.86) 55%, rgba(9, 28, 66, 0.93) 100%);
    }
    .um-reg-inner {
        position: relative;
        z-index: 1;
        width: 100%;
        max-width: 620px;
        margin: 0 auto;
        padding: 28px 16px 10px;
        text-align: center;
    }
    /* Branding */
    .um-reg-brand .um-reg-logo {
        max-height: 56px;
        max-width: 60px;
        filter: drop-shadow(0 2px 6px rgba(0, 0, 0, 0.25));
    }
    .um-reg-brand-name {
        font-size: 26px;
        font-weight: 800;
        letter-spacing: 0.4px;
        color: #ffffff;
        margin: 8px 0 2px;
    }
    .um-reg-brand-name i, .um-reg-brand-name .dw {
        color: #62e6a0;
        margin-right: 4px;
    }
    .um-reg-brand-tag {
        font-size: 13px;
        font-weight: 500;
        color: rgba(255, 255, 255, 0.78);
        letter-spacing: 3px;
        text-transform: uppercase;
        margin-bottom: 18px;
    }
    /* Registration card */
    .um-reg-card {
        position: relative;
        z-index: 1;
        width: 100%;
        max-width: 520px;
        margin: 0 auto;
        background: #ffffff;
        border-radius: 18px;
        box-shadow: 0 24px 60px rgba(3, 14, 40, 0.45), 0 4px 14px rgba(3, 14, 40, 0.22);
        padding: 34px 34px 28px;
        text-align: left;
    }
    .um-reg-card h1 {
        font-size: 23px;
        font-weight: 800;
        color: #0e2b54;
        margin: 0 0 4px;
    }
    .um-reg-card .um-reg-sub {
        font-size: 14px;
        color: #6a7686;
        margin-bottom: 24px;
    }
    .um-reg-alert {
        border-radius: 10px;
        font-size: 14px;
        padding: 11px 14px;
        margin-bottom: 18px;
    }
    /* Fields */
    .um-reg-field { margin-bottom: 16px; }
    .um-reg-field label {
        font-size: 13px;
        font-weight: 600;
        color: #33445c;
        margin-bottom: 5px;
    }
    .um-reg-field label .req { color: #e0313c; }
    .um-input-group {
        border: 1px solid #d7dee8;
        border-radius: 10px;
        background: #fbfcfe;
        transition: border-color .18s ease, box-shadow .18s ease;
        overflow: hidden;
    }
    .um-input-group:focus-within {
        border-color: #1f7a4d;
        box-shadow: 0 0 0 3px rgba(31, 122, 77, 0.15);
        background: #ffffff;
    }
    .um-input-group.is-invalid, .um-input-group.has-error {
        border-color: #e0313c;
        box-shadow: 0 0 0 3px rgba(224, 49, 60, 0.10);
    }
    .um-input-group .input-group-text {
        background: transparent;
        border: 0;
        color: #5c6f87;
        padding: 0 0 0 14px;
        font-size: 15px;
    }
    .um-input-group .form-control {
        border: 0;
        box-shadow: none;
        background: transparent;
        height: 46px;
        font-size: 14px;
        color: #1b2735;
        padding-left: 10px;
    }
    .um-input-group .form-control:focus { background: transparent; }
    .um-input-group .um-pass-btn {
        background: transparent;
        border: 0;
        color: #7a8a9e;
        cursor: pointer;
        padding: 0 14px 0 4px;
        font-size: 16px;
        line-height: 1;
    }
    .um-input-group select.form-control { cursor: pointer; }
    .um-error-text {
        display: block;
        font-size: 12px;
        color: #e0313c;
        margin-top: 5px;
        line-height: 1.4;
    }
    /* Terms */
    .um-reg-terms { margin: 4px 0 20px; }
    .um-reg-terms .form-check-input {
        margin-top: 3px;
        border-color: #b7c2d1;
    }
    .um-reg-terms .form-check-input:checked {
        background-color: #1f7a4d;
        border-color: #1f7a4d;
    }
    .um-reg-terms .form-check-label {
        font-size: 13.5px;
        color: #3d4d63;
    }
    .um-reg-terms .form-check-label a { color: #0b6e3f; font-weight: 600; }
    /* Button */
    .um-reg-btn {
        width: 100%;
        height: 50px;
        border: 0;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 700;
        letter-spacing: 0.3px;
        color: #ffffff;
        background: linear-gradient(135deg, #0e6b40 0%, #1d9a5d 100%);
        box-shadow: 0 10px 22px rgba(16, 116, 66, 0.35);
        transition: transform .15s ease, box-shadow .15s ease, opacity .15s ease;
    }
    .um-reg-btn:hover:not(:disabled) {
        background: linear-gradient(135deg, #0b5c37 0%, #178a51 100%);
        box-shadow: 0 14px 28px rgba(16, 116, 66, 0.42);
        transform: translateY(-1px);
    }
    .um-reg-btn:focus { outline: none; box-shadow: 0 0 0 3px rgba(29, 154, 93, 0.35); }
    .um-reg-btn:disabled { opacity: 0.72; cursor: not-allowed; }
    /* Login link */
    .um-reg-login {
        text-align: center;
        font-size: 14px;
        color: #5a6a7f;
        margin-top: 18px;
    }
    .um-reg-login a { color: #0b6e3f; font-weight: 800; }
    .um-reg-login a:hover { text-decoration: underline; }
    /* Footer strip */
    .um-reg-foot {
        position: relative;
        z-index: 1;
        text-align: center;
        font-size: 12px;
        color: rgba(255, 255, 255, 0.6);
        padding: 16px 20px 20px;
    }
    .um-hide { display: none; }
    @media (max-width: 575.98px) {
        .um-reg-card { padding: 26px 20px 22px; border-radius: 14px; }
        .um-reg-brand-name { font-size: 22px; }
        .um-reg-inner { padding-top: 18px; }
        .um-reg-card h1 { font-size: 20px; }
    }
</style>

<div id="um-register">
    <div class="um-reg-cover">
        <div class="um-reg-inner">
            <div class="um-reg-brand">
                <img class="um-reg-logo" src="<?= asset() ?>/vendors/images/urbanmart.png" alt="<?= htmlspecialchars($app_name) ?>" />
                <div class="um-reg-brand-name"><i class="dw dw-shopping-basket"></i>UrbanMart Grocery</div>
                <div class="um-reg-brand-tag">Fresh &amp; Affordable</div>
            </div>
        </div>

        <?php if (isset($_SESSION["flash"])) { ?>
            <?php if (!empty($_SESSION["flash"]["success"])) { ?>
                <div class="um-reg-inner"><div class="um-reg-alert bg-success text-white"><?= htmlspecialchars($_SESSION["flash"]["success"]) ?></div></div>
            <?php } ?>
            <?php if (!empty($_SESSION["flash"]["error"])) { ?>
                <div class="um-reg-inner"><div class="um-reg-alert bg-danger text-white"><?= htmlspecialchars($_SESSION["flash"]["error"]) ?></div></div>
            <?php } ?>
            <?php unset($_SESSION["flash"]); ?>
        <?php } ?>

        <div class="um-reg-inner">
            <div class="um-reg-card">
                <h1>Create an Account</h1>
                <p class="um-reg-sub">Register to get started with UrbanMart Grocery</p>

                <?php if (isset($reg_errors["_form"])) { ?>
                    <div class="um-reg-alert bg-danger text-white"><?= htmlspecialchars($reg_errors["_form"]); ?></div>
                <?php } ?>

                <form method="post" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" id="um-reg-form" novalidate autocomplete="on">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token) ?>" />

                    <div class="um-reg-field">
                        <label for="username">Username <span class="text-muted font-weight-normal">(Optional)</span></label>
                        <div class="um-input-group<?= $has_error("username") ?>">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="icon-copy dw dw-user1"></i></span>
                            </div>
                            <input type="text" id="username" name="username" class="form-control"
                                   placeholder="e.g. rahim_uddin"
                                   value="<?= $old("username") ?>"
                                   maxlength="50" <?= $aria_desc("username") ?> />
                        </div>
                        <span class="um-error-text" id="username-error"><?= $error("username") ?></span>
                        <span class="um-error-text" style="color:#7a8a9e">A username is auto-created from your email if left blank.</span>
                    </div>

                    <div class="um-reg-field">
                        <label for="email">Email Address <span class="req">*</span></label>
                        <div class="um-input-group<?= $has_error("email") ?>">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="icon-copy dw dw-email"></i></span>
                            </div>
                            <input type="email" id="email" name="email" class="form-control"
                                   placeholder="you@example.com"
                                   value="<?= $old("email") ?>"
                                   maxlength="50" required <?= $aria_desc("email") ?> />
                        </div>
                        <span class="um-error-text" id="email-error"><?= $error("email") ?></span>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="um-reg-field">
                                <label for="password">Password <span class="req">*</span></label>
                                <div class="um-input-group<?= $has_error("password") ?>">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="icon-copy dw dw-padlock1"></i></span>
                                    </div>
                                    <input type="password" id="password" name="password" class="form-control"
                                           placeholder="Min. 8 characters"
                                           maxlength="72" required <?= $aria_desc("password") ?> />
                                    <button type="button" class="um-pass-btn" data-toggle-target="password" aria-label="Show password">
                                        <i class="icon-copy dw dw-eye"></i>
                                    </button>
                                </div>
                                <span class="um-error-text" id="password-error"><?= $error("password") ?></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="um-reg-field">
                                <label for="confirm_password">Confirm Password <span class="req">*</span></label>
                                <div class="um-input-group<?= $has_error("confirm_password") ?>">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="icon-copy dw dw-padlock1"></i></span>
                                    </div>
                                    <input type="password" id="confirm_password" name="confirm_password" class="form-control"
                                           placeholder="Re-enter password"
                                           maxlength="72" required <?= $aria_desc("confirm_password") ?> />
                                    <button type="button" class="um-pass-btn" data-toggle-target="confirm_password" aria-label="Show confirm password">
                                        <i class="icon-copy dw dw-eye"></i>
                                    </button>
                                </div>
                                <span class="um-error-text" id="confirm_password-error"><?= $error("confirm_password") ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="um-reg-terms">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="terms" name="terms" value="1" <?= isset($_POST["terms"]) ? "checked" : "" ?> />
                            <label class="form-check-label" for="terms">
                                I agree to the <a href="#" data-toggle="modal" data-target="#um-terms-modal">Terms &amp; Conditions</a> and <a href="#" data-toggle="modal" data-target="#um-privacy-modal">Privacy Policy</a>
                            </label>
                        </div>
                        <span class="um-error-text" id="terms-error"><?= $error("terms") ?></span>
                    </div>

                    <button type="submit" name="register" id="um-submit" class="um-reg-btn">
                        <span id="um-btn-label">Create Account</span>
                        <span id="um-btn-loading" class="um-hide">
                            <span class="spinner-grow spinner-grow-sm mr-2" role="status" aria-hidden="true"></span>Creating Account...
                        </span>
                    </button>

                    <div class="um-reg-login">
                        Already have an account? <a href="<?= $base_url ?>">Sign In</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="um-reg-foot">&copy; <?= date("Y") ?> UrbanMart Grocery. All rights reserved.</div>
    </div>
</div>

<div class="modal fade" id="um-terms-modal" tabindex="-1" role="dialog" aria-hidden="true" style="position:fixed;z-index:9999">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="background:#0e2b54">
                <h5 class="modal-title">Terms &amp; Conditions</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body small" style="max-height:55vh;overflow:auto">
                <p>By creating an account with UrbanMart Grocery you agree to:</p>
                <ul>
                    <li>Provide accurate and up-to-date personal information.</li>
                    <li>Keep your account credentials private and secure.</li>
                    <li>Use the platform in accordance with applicable laws.</li>
                </ul>
                <p class="mb-0">UrbanMart Grocery may update these terms from time to time. Continued use of your account after changes constitutes acceptance.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="um-privacy-modal" tabindex="-1" role="dialog" aria-hidden="true" style="position:fixed;z-index:9999">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="background:#0e2b54">
                <h5 class="modal-title">Privacy Policy</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body small" style="max-height:55vh;overflow:auto">
                <p>UrbanMart Grocery respects your privacy. We collect only the information needed to operate your account and process your requests.</p>
                <ul>
                    <li>Your personal data is used for account management and service improvement.</li>
                    <li>We never sell your personal information to third parties.</li>
                </ul>
                <p class="mb-0">Contact our support team with any privacy questions.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        "use strict";

        var form = document.getElementById("um-reg-form");
        if (!form) return;

        var submitBtn = document.getElementById("um-submit");
        var btnLabel = document.getElementById("um-btn-label");
        var btnLoading = document.getElementById("um-btn-loading");

        function setError(id, message) {
            var textEl = document.getElementById(id + "-error");
            var groupEl = form.querySelector("[name='" + id + "']");
            if (groupEl) {
                var wrapper = groupEl.closest(".um-input-group");
                if (wrapper) {
                    if (message) wrapper.classList.add("has-error");
                    else wrapper.classList.remove("has-error");
                }
            }
            if (textEl) textEl.textContent = message || "";
        }

        function clearError(id) { setError(id, ""); }

        var EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        var USERNAME_RE = /^[a-zA-Z0-9_]{3,50}$/;

        function validate() {
            var ok = true;
            var firstError = null;

            var user = document.getElementById("username");
            if (user.value.trim() && !USERNAME_RE.test(user.value.trim())) { setError("username", "Username must be 3-50 characters (letters, numbers, underscore)."); ok = false; firstError = firstError || user; }
            else { clearError("username"); }

            var email = document.getElementById("email");
            if (!email.value.trim()) { setError("email", "Email address is required."); ok = false; firstError = firstError || email; }
            else if (!EMAIL_RE.test(email.value.trim())) { setError("email", "Please enter a valid email address."); ok = false; firstError = firstError || email; }
            else { clearError("email"); }

            var password = document.getElementById("password");
            if (!password.value) { setError("password", "Password is required."); ok = false; firstError = firstError || password; }
            else if (password.value.length < 8) { setError("password", "Password must be at least 8 characters long."); ok = false; firstError = firstError || password; }
            else { clearError("password"); }

            var confirm = document.getElementById("confirm_password");
            if (!confirm.value) { setError("confirm_password", "Please confirm your password."); ok = false; firstError = firstError || confirm; }
            else if (password.value !== confirm.value) { setError("confirm_password", "Passwords do not match."); ok = false; firstError = firstError || confirm; }
            else { clearError("confirm_password"); }

            var terms = document.getElementById("terms");
            if (!terms.checked) { setError("terms", "You must agree to the Terms & Conditions and Privacy Policy."); ok = false; firstError = firstError || terms; }
            else { clearError("terms"); }

            if (firstError) firstError.focus();
            return ok;
        }

        ["username", "email", "password", "confirm_password", "terms"].forEach(function (id) {
            var el = document.getElementById(id);
            if (el) {
                el.addEventListener("input", function () { clearError(id); });
                el.addEventListener("change", function () { clearError(id); });
            }
        });

        document.querySelectorAll(".um-pass-btn").forEach(function (btn) {
            btn.addEventListener("click", function () {
                var target = document.getElementById(btn.getAttribute("data-toggle-target"));
                if (!target) return;
                var show = target.type === "password";
                target.type = show ? "text" : "password";
                btn.setAttribute("aria-label", show ? "Hide password" : "Show password");
                btn.setAttribute("aria-pressed", show ? "true" : "false");
            });
        });

        form.addEventListener("submit", function (e) {
            if (!validate()) { e.preventDefault(); return; }
            submitBtn.disabled = true;
            btnLabel.classList.add("um-hide");
            btnLoading.classList.remove("um-hide");
        });
    })();
</script>

<?php section("public/public_footer.php"); ?>