<?php

class Participant
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function allByProject($projectId)
    {
        $stmt = $this->pdo->prepare("
            SELECT e.id, e.participant_name, e.timestamp, e.participant_age, e.participant_gender, e.participant_academic_level, COUNT(r.id) AS response_count
            FROM evaluations e
            LEFT JOIN responses r ON r.evaluation_id = e.id
            WHERE e.test_id IN (SELECT id FROM tests WHERE project_id = ?)
            GROUP BY e.id
            ORDER BY e.timestamp DESC
        ");
        $stmt->execute([$projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM evaluations WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function customFields($evaluationId)
    {
        $stmt = $this->pdo->prepare("
            SELECT f.label, f.field_type, d.value
            FROM evaluation_custom_data d
            JOIN test_custom_fields f ON d.field_id = f.id
            WHERE d.evaluation_id = ?
            ORDER BY f.position
        ");
        $stmt->execute([$evaluationId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
