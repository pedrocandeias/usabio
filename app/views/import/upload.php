<?php $title = 'Start Task Session'; ?>
<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container">
       <a href="/index.php?controller=Project&action=index" class="btn btn-secondary mb-4">← Back to Projects</a>    
    </div>
       
<div class="container py-5">
    <h3 class="mb-4">📥 Import Participants into <strong><?php echo htmlspecialchars($project['title']); ?></strong></h3>

    <form method="POST" action="/index.php?controller=Import&action=importParticipantsCSV" enctype="multipart/form-data">
    <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">    
        <label class="form-label">Upload CSV File</label>
        <div class="mb-3"> 
            <div class="form-text">CSV must include headers: <code>participant_name, participant_age, participant_gender, participant_academic_level, test_ids.</code></div>  
        </div>
        <div class="mb-3">
            Example: <a href="/index.php?controller=Import&action=downloadSampleParticipantsCSV" class="btn btn-outline-secondary">📄 Download Example CSV</a>
        </div>
        <div class="mb-3 mt-3">
            <input type="file" name="csv_file" class="form-control" accept=".csv" required>
        </div>

        <button type="submit" class="btn btn-primary">🚀 Import Participants</button>
    </form>
</div>

<div class="container py-5">
    <h3 class="mb-3">📄 Import Participant Custom Fields (CSV)</h3>
    <form method="POST" action="/index.php?controller=Import&action=importCustomFieldsCSV" enctype="multipart/form-data">
        <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">

        <label class="form-label">Upload CSV File</label>
        <div class="mb-3">
            <div class="form-text">
                CSV must include headers:<br>
                <code>label, field_type, options, position</code><br>
                <small>Valid <strong>field_type</strong> values: <code>text</code>, <code>number</code>, <code>select</code></small>
            </div>
        </div>
        <dvi class="mb-3">
            Example: <a href="/index.php?controller=Import&action=downloadSampleCustomFieldsCSV" class="btn btn-outline-secondary">
            📄 Download Sample CSV</a>
        </dvi>
        <div class="mb-3 mt-3">
            <input type="file" name="csv_file" class="form-control" accept=".csv" required>
        </div>
        <button type="submit" class="btn btn-primary">🚀 Import Custom Fields</button>
    </form>
</div>


<div class="container py-5">
    <h3 class="mb-4">📥 Import Tasks into <strong><?php echo htmlspecialchars($project['title']); ?></strong></h3>
    <form method="POST" action="/index.php?controller=Import&action=importTasksCSV" enctype="multipart/form-data">
        <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
        <label class="form-label">Upload CSV File</label>
        <div class="mb-3"> 
            <div class="form-text">
                CSV must include headers:<br>
                <code>task_text, task_type, task_options, preset_type, script, scenario, metrics, position.</code>
            </div>
        </div>
        <div class="mb-3">
            Example: <a href="/index.php?controller=Import&action=downloadSampleTasksCSV" class="btn btn-outline-secondary">
                📄 Download Sample CSV
                </a>
        </div>

        <div class="mb-3">
            <label class="form-label">Select Task Group</label>
            <select name="task_group_id" class="form-select" required>
                <option value="">-- Choose Task Group --</option>
                <?php foreach ($taskGroups as $group): ?>
                    <option value="<?php echo $group['id']; ?>">
                        <?php echo htmlspecialchars($group['test_title'] . ' → ' . $group['title']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
           
            <input type="file" name="csv_file" class="form-control" accept=".csv" required>
        </div>


        <button type="submit" class="btn btn-primary">🚀 Import Tasks</button>   
       

        
    </form>
</div>

<div class="container py-5">
<h3 class="mb-4">📥 Import Questions (CSV) into <strong><?php echo htmlspecialchars($project['title']); ?></strong></h3>
            
<form method="POST" action="/index.php?controller=Import&action=importQuestionsCSV" enctype="multipart/form-data">
        <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
         <div class="mb-3">   
            <div class="form-text">
                CSV must include headers:<br>
                <code>text, question_type, question_options, preset_type, position</code>
            </div>
        </div>

        <div class="mb-3">
        Example: <a href="/index.php?controller=Import&action=downloadSampleQuestionsCSV" class="btn btn-outline-secondary">
                📄 Download Sample CSV
            </a>
        </div>

        <div class="mb-3">
        <label class="form-label">Select Questionnaire Group</label>
    
            <select name="questionnaire_group_id" class="form-select" required>
                <option value="">-- Choose Questionnaire Group --</option>
                <?php foreach ($questionnaireGroups as $group): ?>
                    <option value="<?php echo $group['id']; ?>">
                        <?php echo htmlspecialchars($group['test_title'] . ' → ' . $group['title']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
             <input type="file" name="csv_file" class="form-control" accept=".csv" required>
        </div>

        <button type="submit" class="btn btn-primary">🚀 Import Questions</button>
    </form>
</div>


<?php require __DIR__ . '/../layouts/footer.php'; ?>
<?php require __DIR__ . '/../layouts/footer_scripts.php'; ?>

</body>
</html>
