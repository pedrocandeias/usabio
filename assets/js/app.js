import 'bootstrap'; // this loads all bootstrap JS
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


document.addEventListener('DOMContentLoaded', () => {
  // Task Groups
  const taskGroupList = document.getElementById('task-group-list');
  if (taskGroupList) {
    new Sortable(taskGroupList, {
      animation: 150,
      handle: '.card-header',
      onEnd() {
        const order = Array.from(document.querySelectorAll('.task-group')).map(el => el.dataset.id);
        reorderAndToast('/index.php?controller=TaskGroup&action=reorder', { order });
      }
    });
  }

  // Tasks
  initSortableList('.task-list', {
    animation: 150,
    onEnd(evt) {
        const groupId = evt.from.dataset.groupId;
        const order = Array.from(evt.from.querySelectorAll('.task-item')).map(el => el.dataset.id);
        reorderAndToast('/index.php?controller=Task&action=reorder', { group_id: groupId, order });
      }
      
  });

  // Questionnaire Groups
  const qgList = document.getElementById('questionnaire-group-list');
  if (qgList) {
    new Sortable(qgList, {
      animation: 150,
      handle: '.card-header',
      onEnd() {
        const order = Array.from(document.querySelectorAll('.questionnaire-group')).map(el => el.dataset.id);
        reorderAndToast('/index.php?controller=QuestionnaireGroup&action=reorder', { order });
      }
    });
  }

  // Questions
  initSortableList('.question-list', {
    animation: 150,
    onEnd(evt) {
        const groupId = evt.from.dataset.groupId;
        const order = Array.from(evt.from.querySelectorAll('.question-item')).map(el => el.dataset.id);
        reorderAndToast('/index.php?controller=Question&action=reorder', { group_id: groupId, order });
      }
      
  });
});