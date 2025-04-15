<?php

class ParticipantCustomFieldController
{
    protected $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function index()
    {
        $project_id = $_GET['project_id'] ?? 0;

        $stmt = $this->pdo->prepare("SELECT * FROM participants_custom_fields WHERE project_id = ? ORDER BY position ASC");
        $stmt->execute([$project_id]);
        $fields = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $breadcrumbs = [
            ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=show&id=' . $project_id, 'active' => false],
            ['label' => 'Custom Fields', 'url' => '/index.php?controller=ParticipantCustomField&action=index&project_id=' . $project_id, 'active' => false],
            ['label' => 'Edit Field', 'url' => '', 'active' => true],
        ];


        include __DIR__ . '/../views/participants_custom_fields/index.php';
    }

    public function create()
    {
        $project_id = $_GET['project_id'] ?? 0;
        $field = null;

        $breadcrumbs = [
            ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=show&id=' . $project_id, 'active' => false],
            ['label' => 'Custom Fields', 'url' => '/index.php?controller=ParticipantCustomField&action=index&project_id=' . $project_id, 'active' => false],
            ['label' => 'Edit Field', 'url' => '', 'active' => true],
        ];

        include __DIR__ . '/../views/participants_custom_fields/form.php';
    }

    public function store()
    {
        $stmt = $this->pdo->prepare(
            "
            INSERT INTO participants_custom_fields (project_id, label, field_type, options, position)
            VALUES (?, ?, ?, ?, ?)
        "
        );
        $stmt->execute(
            [
            $_POST['project_id'],
            $_POST['label'],
            $_POST['field_type'],
            $_POST['options'] ?? null,
            $_POST['position'] ?? 0
            ]
        );

        header("Location: /index.php?controller=ParticipantCustomField&action=index&project_id=" . $_POST['project_id']);
        exit;
    }

    public function edit()
    {
        $id = $_GET['id'] ?? 0;
        $stmt = $this->pdo->prepare("SELECT * FROM participants_custom_fields WHERE id = ?");
        $stmt->execute([$id]);
        $field = $stmt->fetch(PDO::FETCH_ASSOC);
        $project_id = $field['project_id'] ?? 0;
        $breadcrumbs = [
            ['label' => 'Projects', 'url' => '/index.php?controller=Project&action=show&id=' . $project_id, 'active' => false],
            ['label' => 'Custom Fields', 'url' => '/index.php?controller=ParticipantCustomField&action=index&project_id=' . $project_id, 'active' => false],
            ['label' => 'Edit Field', 'url' => '', 'active' => true],
        ];


        include __DIR__ . '/../views/participants_custom_fields/form.php';
    }

    public function update()
    {
        $stmt = $this->pdo->prepare(
            "
            UPDATE participants_custom_fields SET
                label = ?, field_type = ?, options = ?, position = ?
            WHERE id = ?
        "
        );
        $stmt->execute(
            [
            $_POST['label'],
            $_POST['field_type'],
            $_POST['options'] ?? null,
            $_POST['position'] ?? 0,
            $_POST['id']
            ]
        );

        

        header("Location: /index.php?controller=ParticipantCustomField&action=index&project_id=" . $_POST['project_id']);
        exit;
    }

    public function destroy()
    {
        $id = $_GET['id'] ?? 0;
        $project_id = $_GET['project_id'] ?? 0;

        $stmt = $this->pdo->prepare("DELETE FROM participants_custom_fields WHERE id = ?");
        $stmt->execute([$id]);

        header("Location: /index.php?controller=ParticipantCustomField&action=index&project_id=$project_id");
        exit;
    }
}
