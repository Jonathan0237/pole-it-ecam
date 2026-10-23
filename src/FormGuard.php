<?php

declare(strict_types=1);

namespace PoleIt;

/**
 * Protections communes aux formulaires publics :
 * jeton CSRF, champ piège anti-robot (honeypot) et limite d'envois par session.
 */
final class FormGuard
{
    public const HONEYPOT = 'site_web';
    private const MIN_INTERVAL = 15;

    /** @return string|null message d'erreur, ou null si la requête est acceptable */
    public static function check(string $form): ?string
    {
        if (!Csrf::check($_POST['_csrf'] ?? null)) {
            return 'Votre session a expiré. Merci de renvoyer le formulaire.';
        }
        if (($_POST[self::HONEYPOT] ?? '') !== '') {
            return 'Envoi refusé.';
        }
        $last = $_SESSION['last_submit'][$form] ?? 0;
        if (time() - $last < self::MIN_INTERVAL) {
            return 'Merci de patienter quelques secondes avant un nouvel envoi.';
        }
        return null;
    }

    public static function markSubmitted(string $form): void
    {
        $_SESSION['last_submit'][$form] = time();
    }

    public static function honeypotField(): string
    {
        return '<div class="hp" aria-hidden="true"><label>Ne pas remplir<input type="text" name="'
            . self::HONEYPOT . '" tabindex="-1" autocomplete="off"></label></div>';
    }
}
