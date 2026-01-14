/**
 * Product Share Functionality
 * Handles copy link button
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // Copy link button
        $('.share-button--copy').on('click', function(e) {
            e.preventDefault();
            
            const button = $(this);
            const url = button.data('url');
            
            // Copy to clipboard
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url).then(function() {
                    // Success feedback
                    button.addClass('copied');
                    
                    // Change icon temporarily (optional)
                    const originalSVG = button.html();
                    button.html('<svg viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>');
                    
                    // Reset after 2 seconds
                    setTimeout(function() {
                        button.removeClass('copied');
                        button.html(originalSVG);
                    }, 2000);
                    
                }).catch(function(err) {
                    console.error('Could not copy text: ', err);
                    alert('Link copied to clipboard!');
                });
            } else {
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = url;
                textArea.style.position = 'fixed';
                textArea.style.left = '-999999px';
                document.body.appendChild(textArea);
                textArea.select();
                
                try {
                    document.execCommand('copy');
                    button.addClass('copied');
                    setTimeout(function() {
                        button.removeClass('copied');
                    }, 2000);
                } catch (err) {
                    console.error('Fallback: Could not copy text: ', err);
                }
                
                document.body.removeChild(textArea);
            }
        });
        
    });
    
})(jQuery);