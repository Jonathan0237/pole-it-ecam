<?php

declare(strict_types=1);

namespace PoleIt;

final class View
{
    /**
     * Rend une vue de src/views puis l'insère dans le gabarit layout.php.
     * Variables reconnues par le gabarit : title, description, page, hero.
     */
    public static function render(string $view, array $data = []): string
    {
        $content = self::capture($view, $data);
        if (($data['layout'] ?? 'layout') === false) {
            return $content;
        }
        return self::capture($data['layout'] ?? 'layout', $data + ['content' => $content]);
    }

    private static function capture(string $view, array $data): string
    {
        $file = ROOT_DIR . '/src/views/' . $view . '.php';
        if (!is_file($file)) {
            throw new \RuntimeException("Vue introuvable : {$view}");
        }
        extract($data, EXTR_SKIP);
        ob_start();
        require $file;
        return (string) ob_get_clean();
    }
}
