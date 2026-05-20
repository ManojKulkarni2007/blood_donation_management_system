// theme.js - Global High-Performance Theme Switcher Engine
document.addEventListener('DOMContentLoaded', () => {
    const theme = localStorage.getItem('theme');
    if (theme === 'dark') {
        document.documentElement.classList.add('dark-theme');
        document.body.classList.add('dark-theme');
    } else {
        document.documentElement.classList.remove('dark-theme');
        document.body.classList.remove('dark-theme');
    }

    // Attach click event to all theme toggle buttons
    const themeToggles = document.querySelectorAll('.theme-toggle');
    themeToggles.forEach(toggle => {
        toggle.addEventListener('click', () => {
            document.documentElement.classList.toggle('dark-theme');
            document.body.classList.toggle('dark-theme');
            
            if (document.documentElement.classList.contains('dark-theme')) {
                localStorage.setItem('theme', 'dark');
            } else {
                localStorage.setItem('theme', 'light');
            }
        });
    });
});

// Immediate execution in head block to prevent layout theme flashing
(function() {
    if (localStorage.getItem('theme') === 'dark') {
        document.documentElement.classList.add('dark-theme');
        // Body might not be parsed yet, so DOMContentLoaded listener will handle body class.
    } else {
        document.documentElement.classList.remove('dark-theme');
    }
})();
