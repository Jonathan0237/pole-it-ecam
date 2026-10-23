<?php

declare(strict_types=1);

use PoleIt\View;

/** Échappe une valeur pour l'affichage HTML (protection XSS). */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Affiche une page : la vue est insérée dans le gabarit commun. */
function render(string $view, array $data = []): void
{
    echo View::render($view, $data);
}

/** Redirection HTTP 303 (après un POST réussi : pattern Post/Redirect/Get). */
function redirect(string $url): never
{
    header('Location: ' . $url, true, 303);
    exit;
}

/** Message éphémère stocké en session, affiché une seule fois. */
function flash(?string $message = null, string $type = 'success'): ?array
{
    if ($message !== null) {
        $_SESSION['flash'] = ['message' => $message, 'type' => $type];
        return null;
    }
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

/** Récupère un champ POST nettoyé (chaîne, espaces retirés). */
function input(string $key): string
{
    $value = $_POST[$key] ?? '';
    return is_string($value) ? trim($value) : '';
}

/** Chemin d'un fichier statique avec empreinte pour le cache navigateur. */
function asset(string $path): string
{
    $file = ROOT_DIR . '/public/assets/' . $path;
    $version = is_file($file) ? substr(md5((string) filemtime($file)), 0, 8) : '0';
    return '/assets/' . $path . '?v=' . $version;
}

/**
 * Champ de formulaire accessible : label, valeur précédente, message d'erreur
 * relié par aria-describedby.
 *
 * @param array{type?:string, label:string, required?:bool, attrs?:string, options?:array<string,string>, hint?:string} $o
 */
function field(string $name, array $o, array $old = [], array $errors = []): string
{
    $id = 'f-' . $name;
    $type = $o['type'] ?? 'text';
    $value = (string) ($old[$name] ?? '');
    $error = $errors[$name] ?? null;
    $required = !empty($o['required']);
    $describedBy = trim(($error ? $id . '-err ' : '') . (!empty($o['hint']) ? $id . '-hint' : ''));
    $common = sprintf(
        'id="%s" name="%s"%s%s%s %s',
        $id,
        e($name),
        $required ? ' required' : '',
        $error ? ' aria-invalid="true"' : '',
        $describedBy !== '' ? ' aria-describedby="' . $describedBy . '"' : '',
        $o['attrs'] ?? ''
    );

    $html = '<div class="field' . ($error ? ' has-error' : '') . '">';
    $html .= '<label for="' . $id . '">' . e($o['label']) . ($required ? ' <span class="req" aria-hidden="true">*</span>' : '') . '</label>';

    if ($type === 'textarea') {
        $html .= '<textarea ' . $common . ' rows="6">' . e($value) . '</textarea>';
    } elseif ($type === 'select') {
        $html .= '<select ' . $common . '><option value="">Choisir…</option>';
        foreach ($o['options'] ?? [] as $key => $label) {
            $html .= '<option value="' . e($key) . '"' . ($value === (string) $key ? ' selected' : '') . '>' . e($label) . '</option>';
        }
        $html .= '</select>';
    } else {
        $html .= '<input type="' . e($type) . '" value="' . e($value) . '" ' . $common . '>';
    }

    if (!empty($o['hint'])) {
        $html .= '<p class="hint" id="' . $id . '-hint">' . e($o['hint']) . '</p>';
    }
    if ($error) {
        $html .= '<p class="error" id="' . $id . '-err">' . e($error) . '</p>';
    }
    return $html . '</div>';
}
