<?php

namespace Eatfit\Api\Core\Db;

use PDOStatement;

class SqlResult
{
    private array $values = [];

    public function __construct(PDOStatement $statement)
    {
        $rows = $statement->fetchAll();
        foreach ($rows as $row) {
            $tempRow = $row;
            for ($i = 0; $i < count($row); $i++) {
                unset($tempRow[$i]);
            }
            $this->values[] = $tempRow;
        }
    }

    /**
     * Récupère le nombre total de lignes dans le résultat.
     *
     * @return int Le nombre total de lignes.
     */
    public function getRowCount(): int
    {
        return count($this->values);
    }

    /**
     * Vérifie si le résultat est vide.
     *
     * @return bool True si le résultat est vide, sinon False.
     */
    public function isEmpty(): bool
    {
        return empty($this->values);
    }

    /**
     * Récupère toutes les valeurs du résultat.
     *
     * @return array Les valeurs du résultat.
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * Récupère la première ligne du résultat.
     *
     * @return array|null La première ligne du résultat, ou null si le résultat est vide.
     */
    public function getFirstRow(): ?array
    {
        return $this->values[0] ?? null;
    }

    /**
     * Récupère une ligne spécifique du résultat en fonction de l'indice de ligne.
     *
     * @param int $rowIndex L'indice de ligne.
     * @return array|null La ligne spécifique du résultat, ou null si l'indice de ligne est invalide.
     */
    public function getRow(int $rowIndex): ?array
    {
        return $this->values[$rowIndex] ?? null;
    }

    /**
     * Récupère une colonne spécifique du résultat en fonction de l'étiquette de colonne.
     *
     * @param string $columnLabel L'étiquette de colonne.
     * @return array Les valeurs de la colonne spécifique.
     */
    public function getColumn(string $columnLabel): array
    {
        return array_column($this->values, $columnLabel);
    }
}
