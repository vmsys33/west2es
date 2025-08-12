/**
 * Facebook-style Notification Handler
 * Automatically marks notifications as seen when clicked or when page is visited
 */

document.addEventListener('DOMContentLoaded', function() {
    // Handle notification link clicks
    const notificationLinks = document.querySelectorAll('[data-auto-mark="true"]');
    
    notificationLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            
            // If it's the notification page, mark all notifications as seen
            if (href === 'notification.php') {
                markAllNotificationsAsSeen();
            }
            
            // Add a small delay to ensure the click is processed
            setTimeout(() => {
                updateNotificationBadges();
            }, 100);
        });
    });
    
    // Function to mark all notifications as seen
    function markAllNotificationsAsSeen() {
        fetch('../functions/mark_all_notifications_seen.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                console.log('All notifications marked as seen');
                // Hide notification badge immediately
                const notificationBadge = document.querySelector('.notification-badge');
                if (notificationBadge) {
                    notificationBadge.style.display = 'none';
                }
            }
        })
        .catch(error => console.error('Error marking notifications as seen:', error));
    }
    
    // Function to update all notification badges
    function updateNotificationBadges() {
        fetch('../functions/get_notification_count.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Update notification badge
                const notificationBadge = document.querySelector('.notification-badge');
                if (notificationBadge) {
                    if (data.notification_count > 0) {
                        notificationBadge.textContent = data.notification_count;
                        notificationBadge.style.display = 'inline';
                    } else {
                        notificationBadge.style.display = 'none';
                    }
                }
                
                // Update notification count in dashboard
                const notificationCount = document.querySelector('.notification-count');
                if (notificationCount) {
                    notificationCount.textContent = data.notification_count;
                }
                
                // Update pending users badge (admin only)
                const pendingUsersBadge = document.querySelector('.pending-users-badge');
                if (pendingUsersBadge) {
                    if (data.pending_users_count > 0) {
                        pendingUsersBadge.textContent = data.pending_users_count;
                        pendingUsersBadge.style.display = 'inline';
                    } else {
                        pendingUsersBadge.style.display = 'none';
                    }
                }
                
                // Update pending users count in dashboard
                const pendingUsersCount = document.querySelector('.pending-users-count');
                if (pendingUsersCount) {
                    pendingUsersCount.textContent = data.pending_users_count;
                }
                
                // Update pending files badge (admin only)
                const pendingFilesBadge = document.querySelector('.pending-files-badge');
                if (pendingFilesBadge) {
                    if (data.pending_files_count > 0) {
                        pendingFilesBadge.textContent = data.pending_files_count;
                        pendingFilesBadge.style.display = 'inline';
                    } else {
                        pendingFilesBadge.style.display = 'none';
                    }
                }
                
                // Update pending files count in dashboard
                const pendingFilesCount = document.querySelector('.pending-files-count');
                if (pendingFilesCount) {
                    pendingFilesCount.textContent = data.pending_files_count;
                }
            }
        })
        .catch(error => console.error('Error updating badges:', error));
    }
    
    // Auto-update badges every 30 seconds (real-time updates)
    setInterval(updateNotificationBadges, 30000);
    
    // Update badges when page becomes visible (user returns to tab)
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            updateNotificationBadges();
        }
    });
    
    // Update badges when window gains focus
    window.addEventListener('focus', function() {
        updateNotificationBadges();
    });
});
