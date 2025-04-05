document.addEventListener('DOMContentLoaded', function() {
    const API_BASE_URL = '../backend';
    const tabBtns = document.querySelectorAll('.tab-btn');
    const formContainers = document.querySelectorAll('.form-container');
    const loginForm = document.getElementById('login');
    const registerForm = document.getElementById('register');
    const authContainer = document.getElementById('auth-container');
    const userDashboard = document.getElementById('user-dashboard');
    const userNameSpan = document.getElementById('user-name');
    const logoutBtn = document.getElementById('logout-btn');
    const refreshUsersBtn = document.getElementById('refresh-users-btn');
    const usersList = document.getElementById('users-list');
    const usersMessage = document.getElementById('users-message');
    const editUserModal = document.getElementById('edit-user-modal');
    const closeModalBtn = document.querySelector('.close-modal');
    const editUserForm = document.getElementById('edit-user-form');
    const editUserId = document.getElementById('edit-user-id');
    const editUserName = document.getElementById('edit-user-name');
    const editUserEmail = document.getElementById('edit-user-email');
    const editMessage = document.getElementById('edit-message');

    const showMessage = (messageElement, text, type, duration = 2000, callback = null) => {
        messageElement.textContent = text;
        messageElement.className = `message ${type}`;

        if (messageElement.timeoutId) {
            clearTimeout(messageElement.timeoutId);
        }

        messageElement.timeoutId = setTimeout(() => {
            messageElement.textContent = '';
            messageElement.className = 'message';
            if (callback) callback();
        }, duration);
    };

    const checkLoggedIn = () => {
        const user = JSON.parse(localStorage.getItem('user'));
        if (user) {
            authContainer.classList.add('hidden');
            userDashboard.classList.remove('hidden');
            userNameSpan.textContent = user.name;
            getUsers();
        }
    };

    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const tabId = btn.getAttribute('data-tab');

            tabBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            formContainers.forEach(form => {
                if (form.id === `${tabId}-form`) {
                    form.classList.add('active');
                } else {
                    form.classList.remove('active');
                }
            });
        });
    });

    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const email = document.getElementById('login-email').value;
        const password = document.getElementById('login-password').value;
        const messageDiv = document.getElementById('login-message');

        try {
            const response = await fetch(`${API_BASE_URL}/login.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ email, password })
            });

            const data = await response.json();

            if (data.success) {
                showMessage(messageDiv, 'Login successful!', 'success', 1500, () => {
                    authContainer.classList.add('hidden');
                    userDashboard.classList.remove('hidden');
                    userNameSpan.textContent = data.user.name;
                    getUsers();
                });

                localStorage.setItem('user', JSON.stringify(data.user));
            } else {
                showMessage(messageDiv, data.error, 'error');
            }
        } catch (error) {
            showMessage(messageDiv, 'An error occurred. Please try again.', 'error');
            console.error('Error:', error);
        }
    });

    registerForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const name = document.getElementById('register-name').value;
        const email = document.getElementById('register-email').value;
        const password = document.getElementById('register-password').value;
        const messageDiv = document.getElementById('register-message');

        try {
            const response = await fetch(`${API_BASE_URL}/register.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ name, email, password })
            });

            const data = await response.json();

            if (data.success) {
                registerForm.reset();
                showMessage(messageDiv, 'Registration successful! You can now login.', 'success', 1500, () => {
                    tabBtns[0].click();
                });
            } else {
                showMessage(messageDiv, data.error, 'error');
            }
        } catch (error) {
            showMessage(messageDiv, 'An error occurred. Please try again.', 'error');
            console.error('Error:', error);
        }
    });

    logoutBtn.addEventListener('click', () => {
        localStorage.removeItem('user');
        userDashboard.classList.add('hidden');
        authContainer.classList.remove('hidden');
        loginForm.reset();
    });

    const getUsers = async () => {
        try {
            const response = await fetch(`${API_BASE_URL}/get_users.php`);
            const data = await response.json();

            if (data.success) {
                renderUsers(data.users);
            } else {
                showMessage(usersMessage, data.error, 'error');
            }
        } catch (error) {
            showMessage(usersMessage, 'Failed to fetch users. Please try again.', 'error');
            console.error('Error:', error);
        }
    };

    const renderUsers = (users) => {
        usersList.innerHTML = '';

        if (users.length === 0) {
            showMessage(usersMessage, 'No users found.', '');
            return;
        }

        users.forEach(user => {
            const row = document.createElement('tr');

            row.innerHTML = `
                <td>${user.id}</td>
                <td>${user.name}</td>
                <td>${user.email}</td>
                <td>${user.created_at}</td>
                <td>
                    <button class="btn action-btn edit-btn" data-id="${user.id}" data-name="${user.name}" data-email="${user.email}">Edit</button>
                    <button class="btn action-btn delete-btn" data-id="${user.id}">Delete</button>
                </td>
            `;

            usersList.appendChild(row);
        });

        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-id');
                const name = btn.getAttribute('data-name');
                const email = btn.getAttribute('data-email');
                editUserId.value = id;
                editUserName.value = name;
                editUserEmail.value = email;

                // Show modal with animation
                editUserModal.classList.remove('hidden');
                setTimeout(() => {
                    editUserModal.style.opacity = '1';
                }, 10);
            });
        });

        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                if (confirm('Are you sure you want to delete this user?')) {
                    const id = btn.getAttribute('data-id');
                    await deleteUser(id);
                }
            });
        });
    };

    refreshUsersBtn.addEventListener('click', getUsers);

    closeModalBtn.addEventListener('click', () => {
        editUserModal.style.opacity = '0';
        setTimeout(() => {
            editUserModal.classList.add('hidden');
            editMessage.textContent = '';
            editMessage.className = 'message';
        }, 300);
    });

    editUserForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const id = editUserId.value;
        const name = editUserName.value;
        const email = editUserEmail.value;

        try {
            const response = await fetch(`${API_BASE_URL}/update_user.php`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id, name, email })
            });

            const data = await response.json();

            if (data.success) {
                showMessage(editMessage, 'User updated successfully!', 'success', 1500, () => {
                    // Hide modal with animation
                    editUserModal.style.opacity = '0';
                    setTimeout(() => {
                        editUserModal.classList.add('hidden');
                        getUsers();
                    }, 300);
                });
            } else {
                showMessage(editMessage, data.error, 'error');
            }
        } catch (error) {
            showMessage(editMessage, 'An error occurred. Please try again.', 'error');
            console.error('Error:', error);
        }
    });

    const deleteUser = async (id) => {
        try {
            const response = await fetch(`${API_BASE_URL}/delete_user.php?id=${id}`, {
                method: 'DELETE'
            });

            const data = await response.json();

            if (data.success) {
                showMessage(usersMessage, 'User deleted successfully!', 'success', 1500, () => {
                    getUsers();
                });
            } else {
                showMessage(usersMessage, data.error, 'error');
            }
        } catch (error) {
            showMessage(usersMessage, 'Failed to delete user. Please try again.', 'error');
            console.error('Error:', error);
        }
    };

    window.addEventListener('click', (e) => {
        if (e.target === editUserModal) {
            closeModalBtn.click();
        }
    });

    checkLoggedIn();
});