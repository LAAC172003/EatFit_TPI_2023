<?php

namespace Eatfit\Api\Controllers;

use Eatfit\Api\Core\ApiValue;
use Eatfit\Api\Core\Request;
use Eatfit\Api\Models\User;
use Exception;

class UserController
{
    /**
     * Récupère les informations de l'utilisateur en fonction du jeton d'authentification.
     *
     * @return ApiValue Les informations de l'utilisateur, avec un code de statut HTTP 200.
     * @throws Exception
     */
    public static function getUserByToken(): ApiValue
    {
        return new ApiValue(User::getUserByToken(), 200);
    }

    /**
     * Récupère les informations de l'utilisateur en fonction de l'email et du mot de passe.
     *
     * @param Request $request La requête contenant l'email et le mot de passe de l'utilisateur.
     * @return ApiValue Les informations de l'utilisateur, avec un code de statut HTTP 200.
     * @throws Exception
     */
    public function read(Request $request): ApiValue
    {
        return new ApiValue(User::read($request->getData(['email', "password"])), 200);
    }

    /**
     * Crée un nouvel utilisateur en utilisant les données fournies.
     *
     * @param Request $request La requête contenant les données du nouvel utilisateur.
     * @return ApiValue Les informations de l'utilisateur nouvellement créé, avec un code de statut HTTP 201.
     * @throws Exception
     */
    public function create(Request $request): ApiValue
    {
        return new ApiValue(User::create($request->getData(['email', "password", "confirm_password", "username"])), 201);
    }

    /**
     * Met à jour les informations de l'utilisateur en utilisant les données fournies.
     *
     * @param Request $request La requête contenant les données de mise à jour pour l'utilisateur.
     * @return ApiValue Les informations de l'utilisateur mise à jour, avec un code de statut HTTP 200.
     * @throws Exception
     */
    public function update(Request $request): ApiValue
    {
        return new ApiValue(User::update($request->getData(['email', "password", "confirm_password", "username"], false)), 200);
    }

    /**
     * Supprime l'utilisateur actuellement authentifié.
     *
     * @return ApiValue Un message indiquant que l'utilisateur a été supprimé, avec un code de statut HTTP 200.
     * @throws Exception
     */
    public function delete(): ApiValue
    {
        return new ApiValue(User::delete(), 200);
    }

    /**
     * Authentifie l'utilisateur en utilisant l'email et le mot de passe fournis.
     *
     * @param Request $request La requête contenant l'email et le mot de passe de l'utilisateur.
     * @return ApiValue Les informations de l'utilisateur authentifié, avec un code de statut HTTP 200.
     * @throws Exception
     */
    public function login(Request $request): ApiValue
    {
        return new ApiValue(User::authenticate($request->getData(['email', "password"])), 200);
    }
}