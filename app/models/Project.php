<?php

class Project
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function all()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM projects ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM projects WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO projects (
                title,
                description,
                owner_id, 
                product_under_test,
                business_case,
                test_objectives,
                participants,
                equipment,
                responsibilities,
                location_dates,
                test_procedure,
                project_image,
                created_at,
                updated_at
            ) VALUES (
                :title,
                :description,
                :owner_id,
                :product_under_test,
                :business_case,
                :test_objectives,
                :participants,
                :equipment,
                :responsibilities,
                :location_dates,
                :test_procedure,
                :project_image,
                NOW(),
                NOW()
            )
        ");
    
        $stmt->execute([
            ':title' => $data['title'] ?? '',
            ':description' => $data['description'] ?? '',
            ':owner_id' => $data['owner_id'] ?? 0,
            ':product_under_test' => $data['product_under_test'] ?? '',
            ':business_case' => $data['business_case'] ?? '',
            ':test_objectives' => $data['test_objectives'] ?? '',
            ':participants' => $data['participants'] ?? '',
            ':equipment' => $data['equipment'] ?? '',
            ':responsibilities' => $data['responsibilities'] ?? '',
            ':location_dates' => $data['location_dates'] ?? '',
            ':test_procedure' => $data['test_procedure'] ?? '',
            ':project_image' => $data['project_image'] ?? '',
        ]);
    
        return $this->pdo->lastInsertId();
    }
    
    public function update($id, $data)
    {
        $stmt = $this->pdo->prepare("
            UPDATE projects SET
                title = :title,
                description = :description,
                product_under_test = :product_under_test,
                business_case = :business_case,
                test_objectives = :test_objectives,
                participants = :participants,
                equipment = :equipment,
                responsibilities = :responsibilities,
                location_dates = :location_dates,
                test_procedure = :test_procedure,
                project_image = :project_image,
                updated_at = NOW()
            WHERE id = :id
        ");

        $stmt->execute([
            ':id' => $id,
            ':title' => $data['title'] ?? '',
            ':description' => $data['description'] ?? '',
            ':product_under_test' => $data['product_under_test'] ?? '',
            ':business_case' => $data['business_case'] ?? '',
            ':test_objectives' => $data['test_objectives'] ?? '',
            ':participants' => $data['participants'] ?? '',
            ':equipment' => $data['equipment'] ?? '',
            ':responsibilities' => $data['responsibilities'] ?? '',
            ':location_dates' => $data['location_dates'] ?? '',
            ':test_procedure' => $data['test_procedure'] ?? '',
            ':project_image' => $data['project_image'] ?? '',
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM projects WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
