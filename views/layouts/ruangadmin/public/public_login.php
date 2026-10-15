<?php
global $login_errors;
$login_errors = isset($_SESSION["login_errors"]) ? $_SESSION["login_errors"] : [];
unset($_SESSION["login_errors"]);
$old_username = isset($_SESSION["login_old_username"]) ? $_SESSION["login_old_username"] : '';
unset($_SESSION["login_old_username"]);
$_login_error = isset($_SESSION["flash"]["error"]) ? $_SESSION["flash"]["error"] : '';
unset($_SESSION["flash"]["error"]);
$_login_success = isset($_SESSION["flash"]["success"]) ? $_SESSION["flash"]["success"] : '';
unset($_SESSION["flash"]["success"]);
unset($_SESSION["flash"]);
?>

<style>
    .um-log-body { -webkit-font-smoothing: antialiased; }
    #um-login { min-height:100vh; display:flex; flex-direction:column; background:#0a1f44; }
    .um-log-cover {
        position:relative; flex:1 0 auto; display:flex; flex-direction:column;
        background-image:url('<?= $base_url ?>/img/grocery-1232944-1280-jpg.jpg');
        background-size:cover; background-position:center; background-repeat:no-repeat; background-attachment:fixed;
    }
    .um-log-cover::before {
        content:""; position:absolute; inset:0;
        background:linear-gradient(150deg, rgba(9,28,66,0.94) 0%, rgba(10,82,38,0.86) 55%, rgba(9,28,66,0.93) 100%);
    }
    .um-log-inner {
        position:relative; z-index:1; width:100%; max-width:480px;
        margin:0 auto; padding:32px 16px 12px; text-align:center;
    }
    .um-log-brand .um-log-logo { max-height:56px; max-width:60px; filter:drop-shadow(0 2px 6px rgba(0,0,0,0.25)); }
    .um-log-brand-name { font-size:26px; font-weight:800; letter-spacing:0.4px; color:#fff; margin:8px 0 2px; }
    .um-log-brand-name .dw { color:#62e6a0; margin-right:4px; }
    .um-log-brand-tag { font-size:13px; font-weight:500; color:rgba(255,255,255,0.78); letter-spacing:3px; text-transform:uppercase; margin-bottom:20px; }
    .um-log-card {
        position:relative; z-index:1; width:100%; max-width:420px; margin:0 auto;
        background:#fff; border-radius:18px;
        box-shadow:0 24px 60px rgba(3,14,40,0.45), 0 4px 14px rgba(3,14,40,0.22);
        padding:34px 34px 28px; text-align:left;
    }
    .um-log-card h1 { font-size:23px; font-weight:800; color:#0e2b54; margin:0 0 4px; }
    .um-log-card .um-log-sub { font-size:14px; color:#6a7686; margin-bottom:24px; }
    .um-log-alert { border-radius:10px; font-size:14px; padding:11px 14px; margin-bottom:18px; }
    .um-log-field { margin-bottom:18px; }
    .um-log-field label { display:block; font-size:13px; font-weight:600; color:#33445c; margin-bottom:5px; }
    .um-log-field label .req { color:#e0313c; }
    .um-input-group {
        border:1px solid #d7dee8; border-radius:10px; background:#fbfcfe;
        transition:border-color .18s ease, box-shadow .18s ease; overflow:hidden;
    }
    .um-input-group:focus-within { border-color:#1f7a4d; box-shadow:0 0 0 3px rgba(31,122,77,0.15); background:#fff; }
    .um-input-group.has-error { border-color:#e0313c; box-shadow:0 0 0 3px rgba(224,49,60,0.10); }
    .um-input-group .input-group-text { background:transparent; border:0; color:#5c6f87; padding:0 0 0 14px; font-size:15px; }
    .um-input-group .form-control {
        border:0; box-shadow:none; background:transparent; height:46px; font-size:14px; color:#1b2735; padding-left:10px;
    }
    .um-input-group .form-control:focus { background:transparent; }
    .um-input-group .um-pass-btn {
        background:transparent; border:0; color:#7a8a9e; cursor:pointer; padding:0 14px 0 4px; font-size:16px; line-height:1;
    }
    .um-error-text { display:block; font-size:12px; color:#e0313c; margin-top:5px; line-height:1.4; }
    .um-log-extras { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:6px; }
    .um-log-remember { display:flex; align-items:center; gap:7px; }
    .um-log-remember .form-check-input { width:16px; height:16px; margin-top:0; border-color:#b7c2d1; cursor:pointer; }
    .um-log-remember .form-check-input:checked { background-color:#1f7a4d; border-color:#1f7a4d; }
    .um-log-remember .form-check-label { font-size:13.5px; color:#3d4d63; cursor:pointer; margin:0; }
    .um-log-forgot { font-size:13.5px; color:#0b6e3f; font-weight:600; text-decoration:none; }
    .um-log-forgot:hover { text-decoration:underline; }
    .um-log-btn {
        width:100%; height:50px; border:0; border-radius:10px; font-size:16px; font-weight:700; letter-spacing:0.3px;
        color:#fff; background:linear-gradient(135deg, #0e6b40 0%, #1d9a5d 100%);
        box-shadow:0 10px 22px rgba(16,116,66,0.35);
        transition:transform .15s ease, box-shadow .15s ease, opacity .15s ease;
    }
    .um-log-btn:hover:not(:disabled) {
        background:linear-gradient(135deg, #0b5c37 0%, #178a51 100%);
        box-shadow:0 14px 28px rgba(16,116,66,0.42); transform:translateY(-1px);
    }
    .um-log-btn:focus { outline:none; box-shadow:0 0 0 3px rgba(29,154,93,0.35); }
    .um-log-btn:disabled { opacity:0.72; cursor:not-allowed; }
    .um-log-register { text-align:center; font-size:14px; color:#5a6a7f; margin-top:18px; }
    .um-log-register a { color:#0b6e3f; font-weight:800; }
    .um-log-register a:hover { text-decoration:underline; }
    .um-log-foot { position:relative; z-index:1; text-align:center; font-size:12px; color:rgba(255,255,255,0.6); padding:16px 20px 20px; }
    .um-hide { display:none; }
    @media (max-width:575.98px) {
        .um-log-card { padding:26px 20px 22px; border-radius:14px; }
        .um-log-brand-name { font-size:22px; }
        .um-log-inner { padding-top:20px; }
        .um-log-card h1 { font-size:20px; }
    }
</style>

<div id="um-login">
    <div class="um-log-cover">
        <div class="um-log-inner">
            <div class="um-log-brand">
                <img class="um-log-logo" src="<?= asset() ?>/vendors/images/urbanmart.png" alt="<?= htmlspecialchars($app_name) ?>" />
                <div class="um-log-brand-name"><i class="dw dw-shopping-basket"></i>UrbanMart Grocery</div>
                <div class="um-log-brand-tag">Smart Grocery Management, Made Simple</div>
            </div>
        </div>

        <?php if ($_login_success): ?>
            <div class="um-log-inner"><div class="um-log-alert bg-success text-white"><?= htmlspecialchars($_login_success) ?></div></div>
        <?php endif; ?>
        <?php if ($_login_error): ?>
            <div class="um-log-inner"><div class="um-log-alert bg-danger text-white"><?= htmlspecialchars($_login_error) ?></div></div>
        <?php endif; ?>

        <div class="um-log-inner">
            <div class="um-log-card">
                <h1>Welcome Back!</h1>
                <p class="um-log-sub">Please sign in to continue to UrbanMart Grocery</p>

                <form method="post" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" id="um-login-form" novalidate autocomplete="on">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($_SESSION["csrf_token"]) ?>" />
                    <input type="hidden" name="remember_me" id="rm_hidden" value="0" />

                    <div class="um-log-field">
                        <label for="username">Username or Email <span class="req">*</span></label>
                        <div class="um-input-group<?= !empty($login_errors['username']) ? ' has-error' : '' ?>">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="icon-copy dw dw-user1"></i></span>
                            </div>
                            <input type="text" id="username" name="username" class="form-control"
                                   placeholder="e.g. urbanmart or you@example.com"
                                   value="<?= htmlspecialchars($old_username) ?>"
                                   maxlength="100" required
                                   aria-describedby="username-error" autocomplete="username" />
                        </div>
                        <span class="um-error-text" id="username-error"><?= htmlspecialchars($login_errors['username'] ?? '') ?></span>
                    </div>

                    <div class="um-log-field">
                        <label for="password">Password <span class="req">*</span></label>
                        <div class="um-input-group<?= !empty($login_errors['password']) ? ' has-error' : '' ?>">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="icon-copy dw dw-padlock1"></i></span>
                            </div>
                            <input type="password" id="password" name="password" class="form-control"
                                   placeholder="Enter your password"
                                   maxlength="72" required
                                   aria-describedby="password-error" autocomplete="current-password" />
                            <button type="button" class="um-pass-btn" id="um-toggle-pass"
                                    aria-label="Show password" aria-pressed="false"
                                    data-target="password">
                                <i class="dw dw-eye"></i>
                            </button>
                        </div>
                        <span class="um-error-text" id="password-error"><?= htmlspecialchars($login_errors['password'] ?? '') ?></span>
                    </div>

                    <div class="um-log-extras">
                        <div class="um-log-remember">
                            <input type="checkbox" class="form-check-input" id="remember_me" name="remember_me_val" />
                            <label class="form-check-label" for="remember_me">Remember Me</label>
                        </div>
                        <a href="#" class="um-log-forgot" onclick="return false;">Forgot Password?</a>
                    </div>

                    <button type="submit" name="SignIn" class="um-log-btn" id="um-login-btn">
                        <span id="um-btn-label">Sign In</span>
                        <span id="um-btn-loading" class="um-hide">
                            <i class="fa fa-spinner fa-spin"></i> Signing in&hellip;
                        </span>
                    </button>

                    <div class="um-log-register">
                        <span class="text-muted">New to UrbanMart Grocery?</span>
                        <a href="<?= $base_url ?>/register.php">Create an Account</a>
                    </div>
                </form>

                <div class="text-center mt-3" style="font-size:12px; color:#8d96a6;">
                    &copy; <?= date('Y') ?> UrbanMart Grocery. All rights reserved.
                </div>
            </div>
        </div>

        <div class="um-log-foot">&copy; <?= date('Y') ?> UrbanMart Grocery. All rights reserved.</div>
    </div>
</div>

<script>
(function () {
    "use strict";

    var form = document.getElementById("um-login-form");
    if (!form) return;

    var submitBtn = document.getElementById("um-login-btn");
    var btnLabel = document.getElementById("um-btn-label");
    var btnLoading = document.getElementById("um-btn-loading");
    var rmHidden = document.getElementById("rm_hidden");
    var rmCheck = document.getElementById("remember_me");

    rmCheck.addEventListener("change", function () {
        rmHidden.value = rmCheck.checked ? "1" : "0";
    });

    function setError(id, message) {
        var textEl = document.getElementById(id + "-error");
        var input = document.getElementById(id);
        if (input) {
            var wrapper = input.closest(".um-input-group");
            if (wrapper) {
                if (message) wrapper.classList.add("has-error");
                else wrapper.classList.remove("has-error");
            }
        }
        if (textEl) textEl.textContent = message || "";
    }

    function clearError(id) { setError(id, ""); }

    function validate() {
        var ok = true;
        var firstError = null;

        var username = document.getElementById("username");
        if (!username.value.trim()) {
            setError("username", "Username or email is required.");
            ok = false; firstError = firstError || username;
        } else { clearError("username"); }

        var password = document.getElementById("password");
        if (!password.value) {
            setError("password", "Password is required.");
            ok = false; firstError = firstError || password;
        } else { clearError("password"); }

        if (firstError) firstError.focus();
        return ok;
    }

    ["username", "password"].forEach(function (id) {
        var el = document.getElementById(id);
        if (el) el.addEventListener("input", function () { clearError(id); });
    });

    var toggleBtn = document.getElementById("um-toggle-pass");
    if (toggleBtn) {
        toggleBtn.addEventListener("click", function () {
            var target = document.getElementById(toggleBtn.getAttribute("data-target"));
            if (!target) return;
            var show = target.type === "password";
            target.type = show ? "text" : "password";
            var icon = toggleBtn.querySelector("i");
            if (icon) {
                icon.className = show ? "icon-copy dw dw-hide" : "icon-copy dw dw-eye";
            }
            toggleBtn.setAttribute("aria-label", show ? "Hide password" : "Show password");
            toggleBtn.setAttribute("aria-pressed", show ? "true" : "false");
        });
    }

    form.addEventListener("submit", function (e) {
        if (!validate()) { e.preventDefault(); return; }
        btnLabel.classList.add("um-hide");
        btnLoading.classList.remove("um-hide");
        setTimeout(function () {
            submitBtn.disabled = true;
        }, 0);
    });
})();
</script>