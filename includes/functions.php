<?php
// includes/functions.php
/* Mostra mensagem de alerta */
function showAlert($key, $type, $messages) {
    if (isset($_GET[$key])) {
        $status = $_GET[$key];
        if (isset($messages[$status])) {
            $alertClass = $type === 'success' ? 'bg-green-100 border-green-500 text-green-700' : 'bg-red-100 border-red-500 text-red-700';
            echo '<div class="' . $alertClass . ' px-4 py-3 rounded relative mb-4 border-l-4" role="alert">';
            echo '<p>' . $messages[$status] . '</p>';
            echo '</div>';
        }
    }
}
/* Formata data e hora no padrão brasileiro*/
function formatDateTime($dateTime) {
    return date('d/m/Y H:i', strtotime($dateTime));
}
/* Verifica se uma string está vazia */
function isEmpty($str) {
    return trim($str) === '';
}
/* Verifica se um valor é numérico
 */
function isNumeric($val) {
    return is_numeric($val);
}
/* Valida uma data
 */
function isValidDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}
/* Verifica se um usuário tem permissão para determinada ação
 */
function hasPermission($action) {
    // Implementação básica, pode ser expandida para um sistema de permissões mais complexo
    return isLoggedIn(); // Por enquanto, qualquer usuário logado tem todas as permissões
}
/* Redireciona para uma URL com uma mensagem
 */
function redirect($url, $param, $value) {
    header("Location: $url?$param=$value");
    exit;
}
/* Gera um token CSRF
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
/**
 * Verifica se um token CSRF é válido
 */
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && $_SESSION['csrf_token'] === $token;
}