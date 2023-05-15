<?php

namespace Eatfit\Site\Core;


class Session
{
    protected const FLASH_KEY = 'flash_messages';

    /**
     * Constructeur de la classe Session.
     * Initialise la session et les messages flash.
     */
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $_SESSION[self::FLASH_KEY] = $_SESSION[self::FLASH_KEY] ?? [];
        foreach ($_SESSION[self::FLASH_KEY] as $key => &$flashMessage) {
            $flashMessage['remove'] = true;
        }
    }

    /**
     * Définit un message flash avec une clé spécifiée.
     *
     * @param string $key La clé du message flash
     * @param string $message Le contenu du message flash
     */
    public function setFlash(string $key, string $message): void
    {
        $_SESSION[self::FLASH_KEY][$key] = [
            'remove' => false,
            'value' => $message
        ];
    }

    /**
     * Récupère un message flash associé à une clé spécifiée.
     * Le message flash est ensuite supprimé de la session.
     *
     * @param string $key La clé du message flash
     * @return string|null Le contenu du message flash ou null s'il n'existe pas
     */
    public function getFlash(string $key): ?string
    {
        return $_SESSION[self::FLASH_KEY][$key]['value'] ?? null;
    }

    /**
     * Définit une valeur dans la session avec une clé spécifiée.
     *
     * @param string $key La clé de la valeur
     * @param mixed $value La valeur à enregistrer
     */
    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Récupère une valeur de la session en utilisant la clé spécifiée.
     *
     * @param string $key La clé de la valeur
     * @return mixed|null La valeur correspondante ou null si elle n'existe pas
     */
    public function get(string $key)
    {
        return $_SESSION[$key] ?? null;
    }

    /**
     * Supprime une valeur de la session en utilisant la clé spécifiée.
     *
     * @param string $key La clé de la valeur à supprimer
     */
    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function __destruct()
    {
        $this->removeFlashMessages();
    }

    /**
     * Supprime les messages flash de la session qui ont été marqués pour suppression.
     */
    private function removeFlashMessages(): void
    {
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach ($flashMessages as $key => $flashMessage) {
            if ($flashMessage['remove']) unset($flashMessages[$key]);
        }
        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }
}