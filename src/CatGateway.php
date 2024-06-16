<?php

class CatGateway
{
    private PDO $conn;

    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
    }

    public function getAll(): array
    {
        $sql = 'SELECT * FROM cat';

        $stmt = $this->conn->query($sql);

        $data = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $this->prepare($row);
        }

        return $data;
    }

    public function create(array $data): bool|string
    {
        $sql = "INSERT INTO cat (name, age, is_happy) VALUES (:name, :age, :is_happy)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':name', $data['name'], PDO::PARAM_STR);

        $stmt->bindParam(':age', $data['age'], PDO::PARAM_INT);

        $isHappy = (bool)$data['is_happy'] ?? false;
        $stmt->bindParam(':is_happy', $isHappy, PDO::PARAM_BOOL);

        $stmt->execute();

        return $this->conn->lastInsertId();
    }

    public function get(string $id): array|false
    {
        $sql = 'SELECT * from cat WHERE id = :id';

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (is_array($data)) {
            $data = $this->prepare($data);
        }

        return $data;
    }

    public function update(array $current, array $new): int
    {
        $sql = "UPDATE cat 
                SET name = :name, age = :age, is_happy = :is_happy 
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $name = $new['name'] ?? $current['name'];
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);

        $age = $new['age'] ?? $current['age'];
        $stmt->bindParam(':age', $age, PDO::PARAM_INT);

        $isHappy = $new['is_happy'] ?? $current['is_happy'];
        $stmt->bindParam(':is_happy', $isHappy, PDO::PARAM_BOOL);

        $stmt->bindParam(':id', $current['id'], PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }

    public function delete($id): int
    {
        $sql = "DELETE FROM cat WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }


    private function prepare(array $data): array
    {
        $data['is_happy'] = (bool)$data['is_happy'];
        return $data;
    }
}