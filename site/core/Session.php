<?php

namespace Eatfit\Site\Core;


class Session
{
    protected const FLASH_KEY = 'flash_messages';

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $_SESSION[self::FLASH_KEY] = $_SESSION[self::FLASH_KEY] ?? [];
        foreach ($_SESSION[self::FLASH_KEY] as $key => &$flashMessage) {
            $flashMessage['remove'] = true;
        }
    }

    public function setFlash(string $key, string $message): void
    {
        $_SESSION[self::FLASH_KEY][$key] = [
            'remove' => false,
            'value' => $message
        ];
    }

    public function getFlash(string $key): ?string
    {
        return $_SESSION[self::FLASH_KEY][$key]['value'] ?? null;
    }

    public function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }


    public function get(string $key)
    {
        return $_SESSION[$key] ?? null;
    }


    public function remove($key)
    {
        unset($_SESSION[$key]);
    }

    public function __destruct()
    {
        $this->removeFlashMessages();
    }

    private function removeFlashMessages(): void
    {
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach ($flashMessages as $key => $flashMessage) {
            if ($flashMessage['remove']) unset($flashMessages[$key]);
        }
        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }
}