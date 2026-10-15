<?php

function csrf(){
    return "<input type='hidden' value='".md5(rand())."' name='_token' />";
}

/**
 * Returns (and lazily creates) a session-bound CSRF token. Unlike the legacy
 * csrf() helper this token is cryptographically random and stored server side,
 * so it can actually be verified on the receiving request.
 */
function csrf_token(){
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (empty($_SESSION["csrf_token"])) {
        $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
    }
    return $_SESSION["csrf_token"];
}

function csrf_field(){
    return "<input type='hidden' name='_token' value='"
        . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . "' />";
}

/**
 * Constant-time verification of a submitted token against the session token.
 */
function csrf_verify($token){
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $expected = isset($_SESSION["csrf_token"]) ? (string)$_SESSION["csrf_token"] : '';
    return $expected !== '' && is_string($token) && hash_equals($expected, $token);
}

?>