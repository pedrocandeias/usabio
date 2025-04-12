<?php

class ResponseController
{
    private $pdo;

    public function __construct($pdo)
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['username'])) {
            header('Location: /index.php?controller=Auth&action=login');
            exit;
        }

        $this->pdo = $pdo;
    }

    public function exportCsv()
    {
        $testId = $_GET['test_id'] ?? 0;
        if (!$testId) {
            echo "Test ID missing.";
            exit;
        }

        // Fetch responses + evaluations
        $stmt = $this->pdo->prepare("
            SELECT r.*, e.participant_name, e.timestamp
            FROM responses r
            JOIN evaluations e ON r.evaluation_id = e.id
            WHERE e.test_id = ?
            ORDER BY e.timestamp ASC
        ");
        $stmt->execute([$testId]);
        $responses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Headers
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="responses_test_' . $testId . '.csv"');

        $output = fopen('php://output', 'w');

        // CSV Header row
        fputcsv($output, ['Participant', 'Timestamp', 'Type', 'Question', 'Answer', 'Time Spent (sec)']);

        foreach ($responses as $row) {
            $type = ($row['time_spent'] > 0) ? 'Task' : 'Questionnaire';

            fputcsv($output, [
                $row['participant_name'] ?: 'Anonymous',
                $row['timestamp'],
                $type,
                $row['question'],
                $row['answer'],
                $row['time_spent']
            ]);
        }

        fclose($output);
        exit;
    }


    public function exportCsvByTaskGroup()
    {
        $groupId = $_GET['group_id'] ?? 0;

        if (!$groupId) {
            echo "Group ID missing.";
            exit;
        }

        // Get task texts for the group
        $stmt = $this->pdo->prepare("
            SELECT t.task_text
            FROM tasks t
            WHERE t.task_group_id = ?
        ");
        $stmt->execute([$groupId]);
        $taskTexts = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'task_text');

        // Get responses matching those task texts
        $inClause = implode(',', array_fill(0, count($taskTexts), '?'));

        $sql = "
            SELECT r.*, e.participant_name, e.timestamp
            FROM responses r
            JOIN evaluations e ON r.evaluation_id = e.id
            WHERE r.question IN ($inClause) AND e.test_id IN (
                SELECT test_id FROM task_groups WHERE id = ?
            )
            ORDER BY e.timestamp ASC
        ";

        $params = array_merge($taskTexts, [$groupId]);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $responses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Output
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="taskgroup_responses_' . $groupId . '.csv"');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['Participant', 'Timestamp', 'Question', 'Answer', 'Time (sec)']);

        foreach ($responses as $r) {
            fputcsv($out, [
                $r['participant_name'] ?: 'Anonymous',
                $r['timestamp'],
                $r['question'],
                $r['answer'],
                $r['time_spent']
            ]);
        }

        fclose($out);
        exit;
    }

    public function exportCsvByQuestionnaireGroup()
    {
        $groupId = $_GET['group_id'] ?? 0;

        if (!$groupId) {
            echo "Group ID missing.";
            exit;
        }

        // Get questions for the group
        $stmt = $this->pdo->prepare("SELECT text FROM questions WHERE questionnaire_group_id = ?");
        $stmt->execute([$groupId]);
        $questions = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'text');

        $inClause = implode(',', array_fill(0, count($questions), '?'));

        $sql = "
            SELECT r.*, e.participant_name, e.timestamp
            FROM responses r
            JOIN evaluations e ON r.evaluation_id = e.id
            WHERE r.question IN ($inClause) AND e.test_id IN (
                SELECT test_id FROM questionnaire_groups WHERE id = ?
            )
            ORDER BY e.timestamp ASC
        ";

        $params = array_merge($questions, [$groupId]);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $responses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="questionnairegroup_responses_' . $groupId . '.csv"');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['Participant', 'Timestamp', 'Question', 'Answer']);

        foreach ($responses as $r) {
            fputcsv($out, [
                $r['participant_name'] ?: 'Anonymous',
                $r['timestamp'],
                $r['question'],
                $r['answer']
            ]);
        }

        fclose($out);
        exit;
    }

    public function exportSusCsv()
{
    $projectId = $_GET['project_id'] ?? 0;

    if (!$projectId) {
        echo "Missing project ID.";
        exit;
    }

    // Get questionnaire groups in project
    $stmt = $this->pdo->prepare("
        SELECT qg.id AS group_id
        FROM questionnaire_groups qg
        JOIN tests t ON t.id = qg.test_id
        WHERE t.project_id = ?
    ");
    $stmt->execute([$projectId]);
    $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $susBreakdown = [];

    foreach ($groups as $group) {
        $groupId = $group['group_id'];

        $stmt = $this->pdo->prepare("
            SELECT id, text FROM questions
            WHERE questionnaire_group_id = ? AND preset_type = 'SUS'
            ORDER BY position ASC
        ");
        $stmt->execute([$groupId]);
        $susQuestions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($susQuestions) !== 10) continue;

        // Get all evaluations for this project
        $stmt = $this->pdo->prepare("
            SELECT e.id AS evaluation_id, e.participant_name, r.question, r.answer
            FROM evaluations e
            JOIN responses r ON r.evaluation_id = e.id
            WHERE e.test_id IN (
                SELECT id FROM tests WHERE project_id = ?
            )
        ");
        $stmt->execute([$projectId]);
        $allResponses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $byEval = [];
        foreach ($allResponses as $resp) {
            $byEval[$resp['evaluation_id']]['participant'] = $resp['participant_name'] ?: 'Anonymous';
            $byEval[$resp['evaluation_id']]['answers'][$resp['question']] = (int) $resp['answer'];
        }

        foreach ($byEval as $entry) {
            $score = 0;
            $valid = true;
            $individualAnswers = [];

            foreach ($susQuestions as $i => $q) {
                $qText = $q['text'];
                $answer = $entry['answers'][$qText] ?? null;

                if ($answer === null || $answer < 1 || $answer > 5) {
                    $valid = false;
                    break;
                }

                $individualAnswers[] = $answer;
                $score += ($i % 2 === 0) ? ($answer - 1) : (5 - $answer);
            }

            if ($valid) {
                $sus = $score * 2.5;
                $susBreakdown[] = [
                    'participant' => $entry['participant'],
                    'answers' => $individualAnswers,
                    'score' => $sus,
                    'label' => $sus >= 85 ? 'Excellent' : ($sus >= 70 ? 'Good' : ($sus >= 50 ? 'OK' : 'Poor'))
                ];
            }
        }
    }

    header('Content-Type: text/csv');
    header("Content-Disposition: attachment; filename=\"sus_responses_project_{$projectId}.csv\"");

    $out = fopen('php://output', 'w');

    $header = ['Participant'];
    for ($i = 1; $i <= 10; $i++) $header[] = "Q$i";
    $header[] = 'SUS Score';
    $header[] = 'Label';
    fputcsv($out, $header);

    foreach ($susBreakdown as $row) {
        fputcsv($out, array_merge([$row['participant']], $row['answers'], [$row['score'], $row['label']]));
    }

    fclose($out);
    exit;
}


}
