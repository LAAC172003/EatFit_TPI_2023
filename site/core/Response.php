<?php

namespace Eatfit\Site\Core;


/**
 * La classe Response représente la réponse HTTP à renvoyer au client.
 * Elle fournit des méthodes pour gérer le code de statut de la réponse et les redirections.
 */
class Response
{
    /**
     * Définit le code de statut de la réponse.
     *
     * @param int $code Le code de statut HTTP.
     */
    public function statusCode(int $code): void
    {
        http_response_code($code);
    }

    /**
     * Effectue une redirection vers l'URL spécifiée.
     *
     * @param string $url L'URL de destination.
     */
    public function redirect(string $url): void
    {
        header("Location: $url");
    }
}