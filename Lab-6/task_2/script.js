document.addEventListener('DOMContentLoaded', function() {
    const noteForm = document.getElementById('note-form');
    const noteIdInput = document.getElementById('note-id');
    const titleInput = document.getElementById('title');
    const contentInput = document.getElementById('content');
    const titleError = document.getElementById('title-error');
    const contentError = document.getElementById('content-error');
    const saveBtn = document.getElementById('save-btn');
    const cancelBtn = document.getElementById('cancel-btn');
    const formTitle = document.getElementById('form-title');
    const notesList = document.getElementById('notes-list');

    const API_URL = 'api.php';

    loadNotes();

    noteForm.addEventListener('submit', saveNote);
    cancelBtn.addEventListener('click', resetForm);

    function loadNotes() {
        notesList.innerHTML = '<div class="loading">Loading notes...</div>';

        fetch(API_URL)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    displayNotes(data.data);
                } else {
                    notesList.innerHTML = '<div class="error">Error loading notes</div>';
                    console.error(data.message);
                }
            })
            .catch(error => {
                notesList.innerHTML = '<div class="error">Error connecting to server</div>';
                console.error('Error:', error);
            });
    }

    function displayNotes(notes) {
        if (notes.length === 0) {
            notesList.innerHTML = '<div class="loading">No notes found. Create your first note!</div>';
            return;
        }

        notesList.innerHTML = '';

        notes.forEach(note => {
            const noteElement = document.createElement('div');
            noteElement.className = 'note';
            noteElement.innerHTML = `
                <div class="note-header">
                    <div class="note-title">${escapeHtml(note.title)}</div>
                    <div class="note-actions">
                        <button class="edit-btn" data-id="${note.id}">Edit</button>
                        <button class="delete-btn" data-id="${note.id}">Delete</button>
                    </div>
                </div>
                <div class="note-content">${escapeHtml(note.content).replace(/\n/g, '<br>')}</div>
                <div class="note-date">
                    Created: ${formatDate(note.created_at)}
                    ${note.updated_at !== note.created_at ? `<br>Updated: ${formatDate(note.updated_at)}` : ''}
                </div>
            `;

            notesList.appendChild(noteElement);
            noteElement.querySelector('.edit-btn').addEventListener('click', () => editNote(note));
            noteElement.querySelector('.delete-btn').addEventListener('click', () => deleteNote(note.id));
        });
    }

    function saveNote(e) {
        e.preventDefault();

        titleError.textContent = '';
        contentError.textContent = '';

        let isValid = true;

        if (titleInput.value.trim() === '') {
            titleError.textContent = 'Title is required';
            isValid = false;
        }

        if (contentInput.value.trim() === '') {
            contentError.textContent = 'Content is required';
            isValid = false;
        }

        if (!isValid) {
            return;
        }

        const noteData = {
            title: titleInput.value.trim(),
            content: contentInput.value.trim()
        };
        const isUpdate = noteIdInput.value !== '';

        if (isUpdate) {
            noteData.id = noteIdInput.value;
        }

        const requestOptions = {
            method: isUpdate ? 'PUT' : 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(noteData)
        };
        fetch(API_URL, requestOptions)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    resetForm();
                    loadNotes();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error connecting to server');
            });
    }

    function editNote(note) {
        // Fill the form with note data
        noteIdInput.value = note.id;
        titleInput.value = note.title;
        contentInput.value = note.content;

        formTitle.textContent = 'Edit Note';
        saveBtn.textContent = 'Update Note';
        cancelBtn.style.display = 'block';

        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    function deleteNote(id) {
        if (confirm('Are you sure you want to delete this note?')) {
            const requestOptions = {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id: id })
            };

            fetch(API_URL, requestOptions)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        loadNotes();
                        if (noteIdInput.value == id) {
                            resetForm();
                        }
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error connecting to server');
                });
        }
    }

    function resetForm() {
        noteForm.reset();
        noteIdInput.value = '';
        formTitle.textContent = 'Add New Note';
        saveBtn.textContent = 'Save Note';
        cancelBtn.style.display = 'none';
        titleError.textContent = '';
        contentError.textContent = '';
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleString();
    }
    function escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
});