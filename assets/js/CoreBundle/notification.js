(function(global) {
    document.addEventListener('DOMContentLoaded', function() {
        var notificationContainer = document.getElementById('notification-container');

        if (!notificationContainer) {
            notificationContainer = document.createElement('div');
            notificationContainer.id = 'notification-container';
            notificationContainer.className = 'notification-container';
            document.body.appendChild(notificationContainer);
        }

        function showNotification(message, type = 'info', duration = 3000) {
            var notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.textContent = message;

            notificationContainer.appendChild(notification);

            setTimeout(function() {
                notification.classList.add('show');
            }, 100);

            setTimeout(function() {
                hideNotification(notification);
            }, duration);
        }

        function hideNotification(notification) {
            notification.classList.remove('show');
            setTimeout(function() {
                notification.remove();
            }, 500);
        }

        global.showNotification = showNotification;
        global.hideNotification = hideNotification;
    });
})(this);
