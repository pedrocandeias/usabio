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
                product_under_test, business_case, test_objectives, participants,
                equipment, responsibilities, location_dates, test_procedure
            ) VALUES (
                :product_under_test, :business_case, :test_objectives, :participants,
                :equipment, :responsibilities, :location_dates, :test_procedure
            )
        ");

        return $stmt->execute([
            ':product_under_test' => $data['product_under_test'],
            ':business_case'      => $data['business_case'],
            ':test_objectives'    => $data['test_objectives'],
            ':participants'       => $data['participants'],
            ':equipment'          => $data['equipment'],
            ':responsibilities'   => $data['responsibilities'],
            ':location_dates'     => $data['location_dates'],
            ':test_procedure'     => $data['test_procedure']
        ]);
    }

    public function update($id, $data)
    {
        $stmt = $this->pdo->prepare("
            UPDATE projects SET
                product_under_test = :product_under_test,
                business_case = :business_case,
                test_objectives = :test_objectives,
                participants = :participants,
                equipment = :equipment,
                responsibilities = :responsibilities,
                location_dates = :location_dates,
                test_procedure = :test_procedure
            WHERE id = :id
        ");

        return $stmt->execute([
            ':id'                 => $id,
            ':product_under_test' => $data['product_under_test'],
            ':business_case'      => $data['business_case'],
            ':test_objectives'    => $data['test_objectives'],
            ':participants'       => $data['participants'],
            ':equipment'          => $data['equipment'],
            ':responsibilities'   => $data['responsibilities'],
            ':location_dates'     => $data['location_dates'],
            ':test_procedure'     => $data['test_procedure']
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM projects WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
