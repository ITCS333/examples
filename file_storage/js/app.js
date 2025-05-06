// Constants
const API_URL = 'api.php';

// DOM Elements
document.addEventListener('DOMContentLoaded', function() {
    // Check which page we're on
    const currentPath = window.location.pathname;
    const pageName = currentPath.split('/').pop();
    
    // Common elements
    const logoutLink = document.getElementById('logout-link');
    if (logoutLink) {
        logoutLink.addEventListener('click', logout);
    }
    
    // Page-specific initialization
    if (pageName === 'index.php' || pageName === '') {
        initDashboard();
    } else if (pageName === 'login.php') {
        initLogin();
    } else if (pageName === 'register.php') {
        initRegister();
    }
    
    // Check for stored theme preference
    const storedTheme = localStorage.getItem('theme');
    if (storedTheme) {
        document.body.classList.add(storedTheme);
    }
});

// Initialize Dashboard Page
function initDashboard() {
    const uploadForm = document.getElementById('upload-form');
    const uploadMessage = document.getElementById('upload-message');
    const filesList = document.getElementById('files-list');
    const filesMessage = document.getElementById('files-message');
    
    // Load user's files
    loadFiles();
    
    // Handle file upload
    if (uploadForm) {
        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const fileInput = document.getElementById('file');
            const file = fileInput.files[0];
            
            if (!file) {
                showMessage(uploadMessage, 'Please select a file to upload', 'danger');
                return;
            }
            
            // Create FormData object
            const formData = new FormData();
            formData.append('file', file);
            
            // Upload file
            fetch(`${API_URL}?action=upload`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showMessage(uploadMessage, data.message, 'success');
                    uploadForm.reset();
                    loadFiles(); // Reload files list
                } else {
                    showMessage(uploadMessage, data.message, 'danger');
                }
            })
            .catch(error => {
                showMessage(uploadMessage, 'Error uploading file: ' + error, 'danger');
            });
        });
    }
    
    // Function to load user's files
    function loadFiles() {
        fetch(`${API_URL}?action=files`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    if (data.files.length === 0) {
                        filesMessage.innerHTML = '<div class="alert alert-info">You have no files uploaded yet.</div>';
                        filesList.innerHTML = '';
                    } else {
                        filesMessage.innerHTML = '';
                        renderFilesList(data.files);
                    }
                } else {
                    showMessage(filesMessage, data.message, 'danger');
                }
            })
            .catch(error => {
                showMessage(filesMessage, 'Error loading files: ' + error, 'danger');
            });
    }
    
    // Function to render files list
    function renderFilesList(files) {
        filesList.innerHTML = '';
        
        files.forEach(file => {
            const row = document.createElement('tr');
            
            row.innerHTML = `
                <td>${escapeHtml(file.filename)}</td>
                <td>${escapeHtml(file.mime_type)}</td>
                <td>${escapeHtml(file.size_formatted)}</td>
                <td>${new Date(file.uploaded_at).toLocaleString()}</td>
                <td>
                    <a href="${API_URL}?action=download&id=${file.id}" class="btn btn-sm btn-primary">Download</a>
                    <button class="btn btn-sm btn-danger delete-file" data-id="${file.id}">Delete</button>
                </td>
            `;
            
            filesList.appendChild(row);
        });
        
        // Add event listeners to delete buttons
        document.querySelectorAll('.delete-file').forEach(button => {
            button.addEventListener('click', function() {
                const fileId = this.getAttribute('data-id');
                deleteFile(fileId);
            });
        });
    }
    
    // Function to delete a file
    function deleteFile(fileId) {
        if (confirm('Are you sure you want to delete this file?')) {
            fetch(`${API_URL}?action=delete`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id: fileId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showMessage(filesMessage, data.message, 'success');
                    loadFiles(); // Reload files list
                } else {
                    showMessage(filesMessage, data.message, 'danger');
                }
            })
            .catch(error => {
                showMessage(filesMessage, 'Error deleting file: ' + error, 'danger');
            });
        }
    }
}

// Initialize Login Page
function initLogin() {
    const loginForm = document.getElementById('login-form');
    const loginMessage = document.getElementById('login-message');
    
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const rememberMe = document.getElementById('remember-me').checked;
            
            // Validate input
            if (!username || !password) {
                showMessage(loginMessage, 'Please enter both username and password', 'danger');
                return;
            }
            
            // Send login request
            fetch(`${API_URL}?action=login`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    username: username,
                    password: password,
                    remember_me: rememberMe
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Store user info in localStorage if remember me is checked
                    if (rememberMe) {
                        localStorage.setItem('user', JSON.stringify(data.user));
                    }
                    
                    showMessage(loginMessage, 'Login successful. Redirecting...', 'success');
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 1000);
                } else {
                    showMessage(loginMessage, data.message, 'danger');
                }
            })
            .catch(error => {
                showMessage(loginMessage, 'Error logging in: ' + error, 'danger');
            });
        });
    }
    
    // Check for stored credentials
    const storedUser = localStorage.getItem('user');
    if (storedUser) {
        const rememberMeCheckbox = document.getElementById('remember-me');
        if (rememberMeCheckbox) {
            rememberMeCheckbox.checked = true;
        }
    }
}

// Initialize Register Page
function initRegister() {
    const registerForm = document.getElementById('register-form');
    const registerMessage = document.getElementById('register-message');
    
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const username = document.getElementById('username').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm-password').value;
            
            // Validate input
            if (!username || !email || !password || !confirmPassword) {
                showMessage(registerMessage, 'Please fill in all fields', 'danger');
                return;
            }
            
            if (password !== confirmPassword) {
                showMessage(registerMessage, 'Passwords do not match', 'danger');
                return;
            }
            
            // Send registration request
            fetch(`${API_URL}?action=register`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    username: username,
                    email: email,
                    password: password
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showMessage(registerMessage, data.message + ' Redirecting to login...', 'success');
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 2000);
                } else {
                    showMessage(registerMessage, data.message, 'danger');
                }
            })
            .catch(error => {
                showMessage(registerMessage, 'Error registering: ' + error, 'danger');
            });
        });
    }
}

// Logout function
function logout(e) {
    if (e) e.preventDefault();
    
    fetch(`${API_URL}?action=logout`)
        .then(response => response.json())
        .then(data => {
            // Clear localStorage
            localStorage.removeItem('user');
            
            // Redirect to login page
            window.location.href = 'login.php';
        })
        .catch(error => {
            console.error('Error logging out:', error);
            // Redirect anyway
            window.location.href = 'login.php';
        });
}

// Helper function to show messages
function showMessage(element, message, type) {
    element.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>`;
}

// Helper function to escape HTML
function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}
