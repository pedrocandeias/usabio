<!-- Project: Usability Evaluation System -->
<!-- index.php -->
<?php

// --- Database config
require_once 'config/db.php';
// Fetch questions from the database
try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $usernameDB, $passwordDB);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $stmt = $pdo->query("SELECT text FROM questions ORDER BY id ASC");
  $questions = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
  die("Error fetching questions: " . htmlspecialchars($e->getMessage()));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Product Usability Evaluation</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    body { background-color: #f8f9fa; }
    .container {
      max-width: 600px;
      margin: 50px auto;
      background-color: #ffffff;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .timer { font-size: 1.2em; }
    .question-text { margin-bottom: 20px; }
    .hidden { display: none; }
  </style>
</head>
<body>
  <div class="container">
    <h2 class="mb-4">Sistema de avaliação de tarefas de usabilidade</h2>
    <div id="progress" class="mb-3">Perguntas <span id="current-num">1</span> de <span id="total-num"><?php echo count($questions) ?></span></div>

    <div id="question-container">
      <h4 id="question-text" class="question-text"></h4>
      <input type="text" id="answer-input" class="form-control mb-3" required>
      <div class="timer mb-3">
        Tempo nesta tarefa: <span id="question-timer">00:00</span>
      </div>
      <button id="next-button" class="btn btn-primary">Seguinte</button>
    </div>

    <div id="observation-container" class="hidden">
      <div class="form-group">
        <label for="observation">Observações/Comentários:</label>
        <textarea id="observation" class="form-control" rows="4"></textarea>
      </div>
      <button id="submit-button" class="btn btn-success">Submeter Avaliação</button>
    </div>

    <form id="result-form" method="post" action="submit.php" class="hidden">
      <input type="hidden" name="responses" id="responses">
      <input type="hidden" name="observation" id="form-observation">
    </form>
  </div>

  <script>
    const questions = <?php echo json_encode($questions) ?>;
    let currentQuestion = 0;
    let responses = [];
    let questionStartTime;
    let timerInterval;

    const totalQuestions = questions.length;
    document.getElementById('total-num').textContent = totalQuestions;

    const questionTextElem = document.getElementById('question-text');
    const answerInput = document.getElementById('answer-input');
    const questionTimerElem = document.getElementById('question-timer');
    const currentNumElem = document.getElementById('current-num');
    const nextButton = document.getElementById('next-button');
    const observationContainer = document.getElementById('observation-container');
    const questionContainer = document.getElementById('question-container');

    function startTimer() {
      questionStartTime = Date.now();
      timerInterval = setInterval(updateTimer, 1000);
    }

    function updateTimer() {
      const elapsed = Date.now() - questionStartTime;
      const minutes = Math.floor(elapsed / 60000);
      const seconds = Math.floor((elapsed % 60000) / 1000);
      questionTimerElem.textContent =
        (minutes < 10 ? "0" + minutes : minutes) + ":" +
        (seconds < 10 ? "0" + seconds : seconds);
    }

    function stopTimer() {
      clearInterval(timerInterval);
      const elapsed = Date.now() - questionStartTime;
      return Math.floor(elapsed / 1000);
    }

    function showQuestion(index) {
      if (index < totalQuestions) {
        questionTextElem.textContent = questions[index];
        answerInput.value = "";
        answerInput.focus();
        currentNumElem.textContent = index + 1;
        questionTimerElem.textContent = "00:00";
        startTimer();
      }
    }

    showQuestion(currentQuestion);

    nextButton.addEventListener('click', function () {
      if (answerInput.value.trim() === "") {
        alert("Por favor, preencha a resposta antes de avançar.");
        return;
      }
      const timeSpent = stopTimer();
      responses.push({
        question: questions[currentQuestion],
        answer: answerInput.value.trim(),
        timeSpent: timeSpent
      });
      currentQuestion++;

      if (currentQuestion < totalQuestions) {
        showQuestion(currentQuestion);
      } else {
        questionContainer.classList.add('hidden');
        observationContainer.classList.remove('hidden');
      }
    });

    document.getElementById('submit-button').addEventListener('click', function () {
      const observation = document.getElementById('observation').value.trim();
      document.getElementById('responses').value = JSON.stringify(responses);
      document.getElementById('form-observation').value = observation;
      document.getElementById('result-form').submit();
    });
  </script>
</body>
</html>
