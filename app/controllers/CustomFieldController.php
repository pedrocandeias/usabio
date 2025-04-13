<?php

class CustomFieldController
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function store()
    {
        $data = $_POST;

        $stmt = $this->pdo->prepare("
            INSERT INTO test_custom_fields (test_id, label, field_type, options, position)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $data['test_id'],
            $data['label'],
            $data['field_type'],
            $data['options'] ?? null,
            $data['position'] ?? 0
        ]);

        header("Location: /index.php?controller=Test&action=show&id=" . $data['test_id']. "#custom-fields-list");
        exit;
    }

    public function destroy()
    {
        $id = $_GET['id'];
        $testId = $_GET['test_id'];

        $stmt = $this->pdo->prepare("DELETE FROM test_custom_fields WHERE id = ?");
        $stmt->execute([$id]);

        header("Location: /index.php?controller=Test&action=show&id=" . $testId. "#custom-fields-list");
        exit;
    }
}