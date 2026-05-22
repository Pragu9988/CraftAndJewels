document.querySelector('.appointment').addEventListener('load', function() {
    // Hide the loader when the widget has finished loading
    document.querySelector('.loader').style.display = 'none';
});

// Set a timeout to hide the loader after a specific time (e.g., 10 seconds)
setTimeout(function() {
    document.querySelector('.loader').style.display = 'none';
}, 3000);