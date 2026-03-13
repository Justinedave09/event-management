/**
 * Modern Theme JavaScript
 * Adds modern interactions and animations
 */

$(document).ready(function() {
    
    // Add fade-in animation to content
    $('.content').addClass('fade-in');
    
    // Add hover effects to boxes
    $('.box').hover(
        function() {
            $(this).addClass('box-hover');
        },
        function() {
            $(this).removeClass('box-hover');
        }
    );
    
    // Smooth scrolling for anchor links
    $('a[href^="#"]').on('click', function(event) {
        var target = $(this.getAttribute('href'));
        if( target.length ) {
            event.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 100
            }, 1000);
        }
    });
    
    // Add loading animation to buttons on form submit
    $('form').on('submit', function() {
        var submitBtn = $(this).find('button[type="submit"]');
        var originalText = submitBtn.html();
        
        submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Processing...');
        submitBtn.prop('disabled', true);
        
        // Re-enable after 5 seconds (fallback)
        setTimeout(function() {
            submitBtn.html(originalText);
            submitBtn.prop('disabled', false);
        }, 5000);
    });
    
    // Add ripple effect to buttons
    $('.btn').on('click', function(e) {
        var ripple = $('<span class="ripple"></span>');
        var size = Math.max($(this).outerWidth(), $(this).outerHeight());
        var x = e.pageX - $(this).offset().left - size / 2;
        var y = e.pageY - $(this).offset().top - size / 2;
        
        ripple.css({
            width: size,
            height: size,
            left: x,
            top: y
        }).appendTo(this);
        
        setTimeout(function() {
            ripple.remove();
        }, 600);
    });
    
    // Auto-hide alerts after 5 seconds
    $('.alert').each(function() {
        var alert = $(this);
        setTimeout(function() {
            alert.fadeOut('slow');
        }, 5000);
    });
    
    // Add modern tooltips
    $('[data-toggle="tooltip"]').tooltip({
        container: 'body',
        animation: true,
        delay: { show: 500, hide: 100 }
    });
    
    // Enhance table interactions
    $('.table tbody tr').hover(
        function() {
            $(this).addClass('table-row-hover');
        },
        function() {
            $(this).removeClass('table-row-hover');
        }
    );
    
    // Add confirmation dialogs with modern styling
    $('[data-confirm]').on('click', function(e) {
        e.preventDefault();
        var message = $(this).data('confirm');
        var href = $(this).attr('href');
        
        if (confirm(message)) {
            window.location.href = href;
        }
    });
    
    // Enhance form validation feedback
    $('.form-control').on('blur', function() {
        var input = $(this);
        var formGroup = input.closest('.form-group');
        
        if (input.val().trim() === '' && input.prop('required')) {
            formGroup.addClass('has-error');
            input.addClass('error-shake');
            setTimeout(function() {
                input.removeClass('error-shake');
            }, 500);
        } else {
            formGroup.removeClass('has-error').addClass('has-success');
        }
    });
    
    // Add modern search functionality
    if ($('#search-input').length) {
        $('#search-input').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('.searchable-table tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
    }
    
    // Add smooth transitions to sidebar
    $('.sidebar-menu li a').on('click', function() {
        $('.sidebar-menu li').removeClass('active');
        $(this).parent().addClass('active');
    });
    
    // Add modern calendar enhancements
    if (typeof $.fn.fullCalendar !== 'undefined') {
        // Enhance FullCalendar with modern styling
        $('.fc-button').addClass('btn-modern');
        $('.fc-event').addClass('event-modern');
    }
    
    // Add progressive loading for images
    $('img').each(function() {
        $(this).on('load', function() {
            $(this).addClass('img-loaded');
        });
    });
    
    // Add modern dropdown animations
    $('.dropdown').on('show.bs.dropdown', function() {
        $(this).find('.dropdown-menu').addClass('dropdown-animate');
    });
    
    // Add keyboard navigation support
    $(document).on('keydown', function(e) {
        // ESC key to close modals/dropdowns
        if (e.keyCode === 27) {
            $('.modal').modal('hide');
            $('.dropdown.open').removeClass('open');
        }
    });
    
    // Add modern pagination
    $('.pagination a').on('click', function(e) {
        e.preventDefault();
        var link = $(this);
        link.addClass('loading');
        
        // Simulate loading (replace with actual AJAX call)
        setTimeout(function() {
            link.removeClass('loading');
            // Add your pagination logic here
        }, 1000);
    });
    
    // Add modern form enhancements
    $('.form-control').on('focus', function() {
        $(this).parent().addClass('form-group-focus');
    }).on('blur', function() {
        $(this).parent().removeClass('form-group-focus');
    });
    
    // Add notification system
    window.showNotification = function(message, type = 'info') {
        var notification = $('<div class="notification notification-' + type + '">' +
            '<i class="fa fa-' + (type === 'success' ? 'check' : type === 'error' ? 'times' : 'info') + '"></i>' +
            '<span>' + message + '</span>' +
            '<button class="notification-close">&times;</button>' +
            '</div>');
        
        $('body').append(notification);
        
        setTimeout(function() {
            notification.addClass('notification-show');
        }, 100);
        
        notification.find('.notification-close').on('click', function() {
            notification.removeClass('notification-show');
            setTimeout(function() {
                notification.remove();
            }, 300);
        });
        
        // Auto-hide after 5 seconds
        setTimeout(function() {
            notification.removeClass('notification-show');
            setTimeout(function() {
                notification.remove();
            }, 300);
        }, 5000);
    };
    
});

// Add CSS for animations and effects
var modernCSS = `
<style>
.ripple {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: scale(0);
    animation: ripple-animation 0.6s linear;
    pointer-events: none;
}

@keyframes ripple-animation {
    to {
        transform: scale(4);
        opacity: 0;
    }
}

.error-shake {
    animation: shake 0.5s;
}

@keyframes shake {
    0%, 20%, 40%, 60%, 80% { transform: translateX(-2px); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(2px); }
}

.table-row-hover {
    background-color: var(--light-gray) !important;
    transform: scale(1.01);
    transition: all 0.3s ease;
}

.img-loaded {
    opacity: 1;
    transition: opacity 0.3s ease;
}

.dropdown-animate {
    animation: dropdownFadeIn 0.3s ease;
}

@keyframes dropdownFadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.form-group-focus {
    transform: scale(1.02);
    transition: transform 0.3s ease;
}

.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    background: white;
    border-radius: 12px;
    padding: 15px 20px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    border-left: 4px solid #007bff;
    z-index: 9999;
    transform: translateX(400px);
    transition: transform 0.3s ease;
    max-width: 300px;
}

.notification-show {
    transform: translateX(0);
}

.notification-success {
    border-left-color: #28a745;
}

.notification-error {
    border-left-color: #dc3545;
}

.notification-warning {
    border-left-color: #ffc107;
}

.notification-close {
    background: none;
    border: none;
    float: right;
    font-size: 18px;
    cursor: pointer;
    margin-left: 10px;
}

.btn {
    position: relative;
    overflow: hidden;
}

.loading {
    pointer-events: none;
    opacity: 0.7;
}
</style>
`;

$('head').append(modernCSS);