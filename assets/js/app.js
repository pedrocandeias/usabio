import 'bootstrap';
import { Toast } from 'bootstrap';

window.showSavedToast = function () {
    const toastEl = document.getElementById('savedToast');
    if (toastEl) {
        const toast = new Toast(toastEl);
        toast.show();
    }
};

function initSortableList(selector, options) {
    const lists = document.querySelectorAll(selector);
    lists.forEach(list => new Sortable(list, options));
}

function reorderAndToast(url, payload) {
    fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    }).then(() => showSavedToast());
}

function toggleComplete(id) {
    const card = document.getElementById(`task-${id}`);
    card.classList.toggle('bg-light');
    card.classList.toggle('opacity-50');
}

// 🔄 PARTICIPANT MODE LOGIC
document.addEventListener('DOMContentLoaded', () => {
    const participantMode = document.getElementById('participant_mode');
    const assignedBlock = document.getElementById('assignedParticipantBlock');
    const detailsBlock = document.getElementById('participantDetails');
    const nameInput = document.querySelector('input[name="participant_name"]');

    if (participantMode && assignedBlock && detailsBlock) {
        participantMode.addEventListener('change', function () {
            const mode = this.value;
            assignedBlock.classList.add('d-none');
            detailsBlock.classList.add('d-none');

            if (mode === 'assigned') {
                assignedBlock.classList.remove('d-none');
            } else if (mode === 'custom') {
                detailsBlock.classList.remove('d-none');
                nameInput.value = '';
                nameInput.disabled = false;
            } else if (mode === 'anonymous') {
                detailsBlock.classList.remove('d-none');
                const randomName = 'Participant' + Math.floor(Math.random() * 1000000);
                nameInput.value = randomName;
                nameInput.disabled = true;
            }
        });
    }

    // Assigned participant selected → prefill info
    const participantSelect = document.getElementById('participant_id');
    if (participantSelect && typeof participants !== 'undefined') {
        participantSelect.addEventListener('change', function () {
            const selectedId = this.value;
            const participant = participants.find(p => p.id == selectedId);
            const customFieldIds = typeof window.customFieldIds !== 'undefined' ? window.customFieldIds : [];

            if (!participant) return;

            document.querySelector('input[name="participant_name"]').value = participant.participant_name || '';
            document.querySelector('input[name="participant_age"]').value = participant.participant_age || '';
            document.querySelector('select[name="participant_gender"]').value = participant.participant_gender || '';
            document.querySelector('select[name="participant_academic_level"]').value = participant.participant_academic_level || '';

            const customFields = participant.custom_fields || {};
            customFieldIds.forEach(fieldId => {
                const input = document.querySelector(`[name="custom_field[${fieldId}]"]`);
                if (input) input.value = customFields[fieldId] || '';
            });

            detailsBlock.classList.remove('d-none');
        });
    }

    // 🔄 Sortables
    initSortableList('#task-group-list', {
        animation: 150,
        handle: '.card-header',
        onEnd() {
            const order = Array.from(document.querySelectorAll('.task-group')).map(el => el.dataset.id);
            reorderAndToast('/index.php?controller=TaskGroup&action=reorder', { order });
        }
    });

    initSortableList('.task-list', {
        animation: 150,
        onEnd(evt) {
            const groupId = evt.from.dataset.groupId;
            const order = Array.from(evt.from.querySelectorAll('.task-item')).map(el => el.dataset.id);
            reorderAndToast('/index.php?controller=Task&action=reorder', { group_id: groupId, order });
        }
    });

    initSortableList('#questionnaire-group-list', {
        animation: 150,
        handle: '.card-header',
        onEnd() {
            const order = Array.from(document.querySelectorAll('.questionnaire-group')).map(el => el.dataset.id);
            reorderAndToast('/index.php?controller=QuestionnaireGroup&action=reorder', { order });
        }
    });

    initSortableList('.question-list', {
        animation: 150,
        onEnd(evt) {
            const groupId = evt.from.dataset.groupId;
            const order = Array.from(evt.from.querySelectorAll('.question-item')).map(el => el.dataset.id);
            reorderAndToast('/index.php?controller=Question&action=reorder', { group_id: groupId, order });
        }
    });

    // ✅ Toast messages
    const urlParams = new URLSearchParams(window.location.search);
    const successKey = urlParams.get('success');
    if (successKey && document.getElementById('savedToast')) {
        const messages = {
            participants_imported: '✅ Participants imported successfully!',
            tasks_imported: '✅ Tasks imported successfully!',
            questions_imported: '✅ Questions imported successfully!',
            custom_fields_imported: '✅ Custom fields imported successfully!',
            project_duplicated: '✅ Project duplicated!',
            test_duplicated: '✅ Test duplicated!',
            project_imported: '✅ Project imported successfully!',
            test_imported: '✅ Test imported successfully!',
            task_group_imported: '✅ Task group imported successfully!',
            task_imported: '✅ Task imported successfully!',
            questionnaire_group_imported: '✅ Questionnaire group imported successfully!',
            questionnaire_imported: '✅ Questionnaire imported successfully!',
            project_deleted: '✅ Project deleted!',
            test_deleted: '✅ Test deleted!',
            participant_deleted: '✅ Participant deleted!',
            participant_updated: '✅ Participant updated!',
            participant_duplicated: '✅ Participant duplicated!',
            sus_exported: '✅ SUS exported successfully!',
            settings_updated: '✅ Settings updated!',
        };

        const bodySpan = document.getElementById('toastMessage');
        if (bodySpan && messages[successKey]) {
            bodySpan.textContent = messages[successKey];
            window.showSavedToast();
        }
    }

    // 🧠 AI generation form
    const aiForm = document.getElementById('ai-generate-form');
    if (aiForm) {
        aiForm.addEventListener('submit', function () {
            document.getElementById('promptInput').disabled = true;
            document.getElementById('generateBtn').disabled = true;
            document.getElementById('loading-indicator').classList.remove('d-none');

            const jokes = [
                "Why did the industrial designer bring a sketchpad to dinner? Because form always follows function—even with food.",
                "How many industrial designers does it take to screw in a lightbulb? None. They’ll sketch 50 variations of the fixture instead.",
                "User: 'It looks great!' Designer: 'Great, but have you tried holding it upside-down?'",
                "Industrial design: where 'ergonomic' means 'we hope it feels okay after 4 prototypes.'",
                "If you love something, set it free. If it comes back, your client probably rejected the design."
            ];
            const joke = jokes[Math.floor(Math.random() * jokes.length)];
            document.getElementById('jokeArea').textContent = joke;
        });
    }
});
