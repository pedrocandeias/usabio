<?php

class ProjectInvite
{
    protected $pdo;

    public $id;
    public $project_id;
    public $moderator_id;
    public $status;
    public $created_at;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Busca todos os convites pendentes para um moderador específico
     */
    public function getPendingInvitesForModerator($moderator_id)
    {
        $stmt = $this->pdo->prepare("
            SELECT pi.*, p.title AS project_title
            FROM project_invites pi
            INNER JOIN projects p ON pi.project_id = p.id
            WHERE pi.moderator_id = ? AND pi.status = 'pending'
            ORDER BY pi.created_at DESC
        ");
        $stmt->execute([$moderator_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cria um novo convite
     */
    public function createInvite($project_id, $moderator_id)
{
    // Verifica se já existe um convite para este moderador neste projeto
    $stmt = $this->pdo->prepare("
        SELECT 1 FROM project_invites WHERE project_id = ? AND moderator_id = ?
    ");
    $stmt->execute([$project_id, $moderator_id]);

    if ($stmt->fetchColumn()) {
        // Já existe — não duplica
        throw new Exception("⚠️ This user has already been invited to this project.");
    }

    // Caso não exista, insere normalmente
    $stmt = $this->pdo->prepare("
        INSERT INTO project_invites (project_id, moderator_id, status, created_at)
        VALUES (?, ?, 'pending', NOW())
    ");
    return $stmt->execute([$project_id, $moderator_id]);
}

    /**
     * Atualiza o status de um convite
     */
    public function updateInviteStatus($invite_id, $status)
    {
        $stmt = $this->pdo->prepare("
            UPDATE project_invites SET status = ? WHERE id = ?
        ");
        return $stmt->execute([$status, $invite_id]);
    }

    /**
     * Obtém convite por ID
     */
    public function find($invite_id)
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM project_invites WHERE id = ?
        ");
        $stmt->execute([$invite_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Verifica se já existe um convite pendente entre projeto e moderador
     */
    public function inviteExists($project_id, $moderator_id)
    {
        $stmt = $this->pdo->prepare("
            SELECT id FROM project_invites 
            WHERE project_id = ? AND moderator_id = ? AND status = 'pending'
        ");
        $stmt->execute([$project_id, $moderator_id]);
        return (bool)$stmt->fetch();
    }

	public function getPendingInvitesForProject($project_id)
	{
		$stmt = $this->pdo->prepare("
			SELECT * FROM project_invites 
			WHERE project_id = ? AND status = 'pending'
		");
		$stmt->execute([$project_id]);
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
}
