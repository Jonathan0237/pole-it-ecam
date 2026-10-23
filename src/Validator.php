<?php

declare(strict_types=1);

namespace PoleIt;

/**
 * Validation côté serveur. Les contrôles HTML5 du navigateur ne sont qu'un
 * confort : seule cette validation fait foi.
 *
 *   $v = new Validator($data);
 *   $v->required('email', 'E-mail')->email('email', 'E-mail');
 *   if ($v->fails()) { $v->errors(); }
 */
final class Validator
{
    /** @var array<string, string> */
    private array $errors = [];

    public function __construct(private readonly array $data)
    {
    }

    public function required(string $field, string $label): self
    {
        if ($this->value($field) === '') {
            $this->add($field, "Le champ « {$label} » est obligatoire.");
        }
        return $this;
    }

    public function email(string $field, string $label): self
    {
        $value = $this->value($field);
        if ($value !== '' && filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            $this->add($field, "« {$label} » n'est pas une adresse e-mail valide.");
        }
        return $this;
    }

    public function length(string $field, string $label, int $min, int $max): self
    {
        $value = $this->value($field);
        $len = mb_strlen($value);
        if ($value !== '' && ($len < $min || $len > $max)) {
            $this->add($field, "« {$label} » doit contenir entre {$min} et {$max} caractères.");
        }
        return $this;
    }

    public function name(string $field, string $label): self
    {
        $value = $this->value($field);
        if ($value !== '' && !preg_match("/^[\p{L}][\p{L}' .-]*$/u", $value)) {
            $this->add($field, "« {$label} » contient des caractères non autorisés.");
        }
        return $this;
    }

    public function postalCode(string $field, string $label): self
    {
        $value = $this->value($field);
        if ($value !== '' && !preg_match('/^\d{5}$/', $value)) {
            $this->add($field, "« {$label} » doit comporter 5 chiffres.");
        }
        return $this;
    }

    public function phone(string $field, string $label): self
    {
        $value = preg_replace('/[\s.-]/', '', $this->value($field)) ?? '';
        if ($value !== '' && !preg_match('/^\+?\d{9,15}$/', $value)) {
            $this->add($field, "« {$label} » n'est pas un numéro valide.");
        }
        return $this;
    }

    public function integer(string $field, string $label, int $min, int $max): self
    {
        $value = $this->value($field);
        if ($value !== '' && filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => $min, 'max_range' => $max]]) === false) {
            $this->add($field, "« {$label} » doit être un nombre entre {$min} et {$max}.");
        }
        return $this;
    }

    /** @param list<string> $allowed */
    public function in(string $field, string $label, array $allowed): self
    {
        $value = $this->value($field);
        if ($value !== '' && !in_array($value, $allowed, true)) {
            $this->add($field, "Valeur invalide pour « {$label} ».");
        }
        return $this;
    }

    public function accepted(string $field, string $message): self
    {
        if (!in_array($this->value($field), ['1', 'on', 'yes'], true)) {
            $this->add($field, $message);
        }
        return $this;
    }

    public function date(string $field, string $label): self
    {
        $value = $this->value($field);
        if ($value !== '') {
            $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $value);
            if ($date === false || $date->format('Y-m-d') !== $value) {
                $this->add($field, "« {$label} » n'est pas une date valide.");
            }
        }
        return $this;
    }

    public function fails(): bool
    {
        return $this->errors !== [];
    }

    /** @return array<string, string> une erreur (la première) par champ */
    public function errors(): array
    {
        return $this->errors;
    }

    private function value(string $field): string
    {
        $value = $this->data[$field] ?? '';
        return is_string($value) ? trim($value) : '';
    }

    private function add(string $field, string $message): void
    {
        $this->errors[$field] ??= $message;
    }
}
