(function(global) {
    function Modal(modalId, callback) {
        this.modal = document.getElementById(modalId);
        this.closeButtons = this.modal ? this.modal.querySelectorAll('.modal-close-button') : [];
        this.saveButton = this.modal ? this.modal.querySelector('.modal-save-button') : null;

        this.callback = null;
        if (typeof callback === 'function') {
            this.callback = callback;
        }

        if (!this.modal) {
            console.error('Modal window with ID ' + modalId + ' not found!');
            return;
        }

        var self = this;

        Array.prototype.forEach.call(this.closeButtons, function(button) {
            button.addEventListener('click', function() {
                self.close();
            });
        });

        if (this.saveButton) {
            this.saveButton.addEventListener('click', function() {
                if (typeof self.callback === 'function') {
                    self.callback();
                }
                self.close();
            });
        }

        window.addEventListener('click', function(event) {
            if (event.target === self.modal) {
                self.close();
            }
        });
    }

    Modal.prototype.open = function() {
        if (this.modal) {
            this.modal.style.display = 'flex';
        }
    };

    Modal.prototype.close = function() {
        if (this.modal) {
            this.modal.style.display = 'none';
        }
    };

    global.Modal = Modal;

}(this));