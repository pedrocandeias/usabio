<?php
require_once __DIR__ . '/BaseController.php';

class EmailTemplateController extends BaseController
{
    public function index()
    {
        $this->requireSuperadmin();

        $stmt = $this->pdo->query("SELECT * FROM email_templates ORDER BY template_key ASC");
        $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $breadcrumbs = [
            ['label' => 'Admin', 'url' => '', 'active' => false],
            ['label' => 'Email Templates', 'url' => '', 'active' => true]
        ];

        include __DIR__ . '/../views/email_templates/index.php';
    }

    public function edit()
    {
        $this->requireSuperadmin();

        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /index.php?controller=EmailTemplate&action=index&error=missing_id');
            exit;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM email_templates WHERE id = ?");
        $stmt->execute([$id]);
        $template = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$template) {
            header('Location: /index.php?controller=EmailTemplate&action=index&error=not_found');
            exit;
        }

        $breadcrumbs = [
            ['label' => 'Admin', 'url' => '', 'active' => false],
            ['label' => 'Email Templates', 'url' => '/index.php?controller=EmailTemplate&action=index', 'active' => false],
            ['label' => 'Edit Template', 'url' => '', 'active' => true],
        ];

        include __DIR__ . '/../views/email_templates/edit.php';
    }

    public function update()
    {
        $this->requireSuperadmin();

        $id = $_POST['id'] ?? null;
        $subject = trim($_POST['subject'] ?? '');
        $body = trim($_POST['body'] ?? '');

        if (!$id || !$subject || !$body) {
            header('Location: /index.php?controller=EmailTemplate&action=edit&id=' . $id . '&error=missing_fields');
            exit;
        }

        $stmt = $this->pdo->prepare("
            UPDATE email_templates 
            SET subject = ?, body = ?, updated_at = NOW() 
            WHERE id = ?
        ");
        $stmt->execute([$subject, $body, $id]);

        header('Location: /index.php?controller=EmailTemplate&action=index&success=updated');
        exit;
    }
}
