<?php

namespace laac\eatFitTpi2023\core\db;

use PDO;
use PDOStatement;

class SqlResult
{
    private array $values = [];

    public function __construct(PDOStatement $statement)
    {
        // Fetch all rows from the statement
        $rows = $statement->fetchAll();
        foreach ($rows as $row) {
            $tempRow = $row;
            for ($i = 0; $i < count($row); $i++) {
                unset($tempRow[$i]);
            }
            $this->values[] = $tempRow;
        }
    }

    public function getRowCount(): int
    {
        return count($this->values);
    }

    public function isEmpty(): bool
    {
        return empty($this->values);
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function getFirstRow(): ?array
    {
        return $this->values[0] ?? null;
    }

    public function getRow(int $rowIndex): ?array
    {
        return $this->values[$rowIndex] ?? null;
    }

    public function getColumn(string $columnLabel): array
    {
        return array_column($this->values, $columnLabel);
    }
}
