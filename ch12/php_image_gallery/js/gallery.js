/**
 * Image Gallery JavaScript
 * 
 * This file contains the JavaScript code for the image gallery application.
 * It handles fetching images from the API, displaying them, and submitting the contact form.
 */

// DOM elements
const galleryContainer = document.getElementById('gallery-container');
const searchForm = document.getElementById('search-form');
const contactForm = document.getElementById('contact-form');
const messageContainer = document.getElementById('message-container');
const totalImagesElement = document.getElementById('total-images');
const searchTermDisplay = document.getElementById('search-term-display');
const noResultsContainer = document.getElementById('no-results-container');

// API URL
const API_URL = 'api.php';

/**
 * Fetch images from the API
 * @param {string} searchTerm - The search term
 * @param {string} sortBy - The sort order
 * @returns {Promise} - Promise that resolves with the images
 */
async function fetchImages(searchTerm = '', sortBy = 'default') {
    try {
        const url = new URL(API_URL, window.location.origin);
        url.searchParams.append('search', searchTerm);
        url.searchParams.append('sort', sortBy);
        
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error fetching images:', error);
        showMessage('Error loading images. Please try again later.', 'danger');
        return { status: 'error', count: 0, data: [] };
    }
}

/**
 * Display images in the gallery
 * @param {Array} images - Array of image objects
 * @param {string} searchTerm - The search term
 */
function displayImages(images, searchTerm = '') {
    // Update the count display
    totalImagesElement.textContent = images.length;
    
    // Update search term display
    if (searchTerm) {
        searchTermDisplay.innerHTML = ` for search: "<strong>${escapeHtml(searchTerm)}</strong>"`;
    } else {
        searchTermDisplay.innerHTML = '';
    }
    
    // Show/hide no results message
    if (images.length === 0) {
        galleryContainer.innerHTML = '';
        noResultsContainer.classList.remove('d-none');
    } else {
        noResultsContainer.classList.add('d-none');
        
        // Create HTML for images
        const imagesHtml = images.map(image => `
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <img src="${escapeHtml(image.url)}"
                        class="card-img-top" style="height: 200px; object-fit: cover;"
                        alt="${escapeHtml(image.title)}">
                    <div class="card-body">
                        <h5 class="card-title">${escapeHtml(image.title)}</h5>
                        <p class="card-text text-muted small">Source: ${escapeHtml(image.source)}</p>
                    </div>
                </div>
            </div>
        `).join('');
        
        galleryContainer.innerHTML = imagesHtml;
    }
}

/**
 * Submit the contact form
 * @param {Event} event - The form submit event
 */
async function submitContactForm(event) {
    event.preventDefault();
    
    // Get form data
    const formData = new FormData(contactForm);
    const name = formData.get('name');
    const email = formData.get('email');
    const message = formData.get('comment');
    
    // Validate form data
    if (!name || !email || !message) {
        showMessage('Please fill in all fields', 'danger');
        return;
    }
    
    try {
        // Send the data to the API
        const response = await fetch(API_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                type: 'contact',
                name,
                email,
                message
            })
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            showMessage(data.message, 'success');
            contactForm.reset();
        } else {
            showMessage(data.message, 'danger');
        }
    } catch (error) {
        console.error('Error submitting form:', error);
        showMessage('Error submitting form. Please try again later.', 'danger');
    }
}

/**
 * Show a message to the user
 * @param {string} message - The message to display
 * @param {string} type - The message type (success, danger, etc.)
 */
function showMessage(message, type = 'info') {
    messageContainer.innerHTML = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    // Scroll to the message
    messageContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

/**
 * Escape HTML special characters
 * @param {string} unsafe - The unsafe string
 * @returns {string} - The escaped string
 */
function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

/**
 * Initialize the application
 */
async function initGallery() {
    // Add event listeners
    searchForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        const formData = new FormData(searchForm);
        const searchTerm = formData.get('search');
        const sortBy = formData.get('sort');
        
        // Update URL parameters
        const url = new URL(window.location);
        url.searchParams.set('search', searchTerm);
        url.searchParams.set('sort', sortBy);
        window.history.pushState({}, '', url);
        
        // Fetch and display images
        const result = await fetchImages(searchTerm, sortBy);
        if (result.status === 'success') {
            displayImages(result.data, searchTerm);
        }
    });
    
    contactForm.addEventListener('submit', submitContactForm);
    
    // Get initial search parameters from URL
    const urlParams = new URLSearchParams(window.location.search);
    const searchTerm = urlParams.get('search') || '';
    const sortBy = urlParams.get('sort') || 'default';
    
    // Set initial form values
    document.getElementById('search').value = searchTerm;
    document.getElementById('sort').value = sortBy;
    
    // Fetch and display initial images
    const result = await fetchImages(searchTerm, sortBy);
    if (result.status === 'success') {
        displayImages(result.data, searchTerm);
    }
}

// Initialize the gallery when the DOM is loaded
document.addEventListener('DOMContentLoaded', initGallery);
