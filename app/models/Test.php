<?php

class Test
{
    protected $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function create($data)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO tests (
                project_id,
                title,
                description,
                layout_image,
                created_at
            ) VALUES (
                :project_id,
                :title,
                :description,
                :layout_image,
                NOW()
            )
        ");

        $stmt->execute([
            ':project_id' => $data['project_id'],
            ':title' => $data['title'] ?? '',
            ':description' => $data['description'] ?? '',
            ':layout_image' => $data['layout_image'] ?? '',
        ]);

        return $this->pdo->lastInsertId();
    }

    public function update($id, $data)
    {
        $stmt = $this->pdo->prepare("
            UPDATE tests SET
                title = :title,
                description = :description,
                layout_image = :layout_image,
                updated_at = NOW()
            WHERE id = :id
        ");

        $stmt->execute([
            ':id' => $id,
            ':title' => $data['title'] ?? '',
            ':description' => $data['description'] ?? '',
            ':layout_image' => $data['layout_image'] ?? '',
        ]);
    }
}
