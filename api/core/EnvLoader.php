<?php

namespace Eatfit\Api\Core;

use RuntimeException;

class EnvLoader
{
    private string $envFilePath;

    /**
     * Constructeur EnvLoader.
     *
     * @param string $envFilePath Le chemin du fichier .env
     */
    public function __construct(string $envFilePath)
    {
        $this->envFilePath = $envFilePath;
    }

    /**
     * Charge les variables d'environnement depuis le fichier .env.
     *
     * @throws RuntimeException Si le fichier .env n'existe pas
     */
    public function load(): void
    {
        if (!file_exists($this->envFilePath)) throw new RuntimeException("Le fichier .env n'existe pas : {$this->envFilePath}", 404);
        $fileContents = file_get_contents($this->envFilePath);
        $lines = preg_split('/\r\n|\r|\n/', $fileContents);

        foreach ($lines as $line) {
            if (preg_match('/^\s*([\w\.\-]+)\s*=\s*(.*)?\s*$/', $line, $matches)) {
                list(, $name, $value) = $matches;
                $value = trim($value);
                if (preg_match('/^"(.*)"$/s', $value, $matches)) $value = $matches[1];
                $_ENV[$name] = $value;
            }
        }
    }
}
