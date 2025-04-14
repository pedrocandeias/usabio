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
        // Safely read from POST or GET
        $data = $_POST;
        $projectId = $data['project_id'] ?? $_GET['project_id'] ?? $_GET['id'] ?? 0;
    
        if (!$projectId) {
            echo "Missing project ID.";
            exit;
        }
    
        // Insert the custom field
        $stmt = $this->pdo->prepare("
            INSERT INTO participants_custom_fields (project_id, label, field_type, options, position)
            VALUES (?, ?, ?, ?, ?)
        ");
    
        $stmt->execute([
            $projectId,
            $data['label'],
            $data['field_type'],
            $data['options'] ?? null,
            $data['position'] ?? 0
        ]);
    
        header("Location: /index.php?controller=Project&action=show&id=$projectId#custom-fields-list");
        exit;
    }
    



    public function destroy()
    {
        $id = $_GET['id'];
        $projectId = $_GET['project_id'];

        $stmt = $this->pdo->prepare("DELETE FROM participants_custom_fields WHERE id = ?");
        $stmt->execute([$id]);

        header("Location: /index.php?controller=Project&action=show&id=" . $projecttId. "#custom-fields-list");
        exit;
    }
}