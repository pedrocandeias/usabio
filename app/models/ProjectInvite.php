<?php
class ProjectInvite extends Model {
	protected $table = 'project_invites';
	public function project() { return $this->belongsTo(Project::class); }
	public function moderator(){ return $this->belongsTo(Moderator::class); }
}