document.addEventListener('DOMContentLoaded', function() {
    var ajax = new Ajax();
    var addCategoryModalBtn = document.getElementById('add-category-modal-btn');
    var addCategoryUrl = addCategoryModalBtn.getAttribute('data-add-url');
    var addCategoryTitle = document.getElementById('add-category-title');
    var newsCategoriesContainer = document.getElementById('news-categories-container');
    var deleteCategoryUrl = newsCategoriesContainer.getAttribute('data-delete-url');
    var updateCategoryUrl = newsCategoriesContainer.getAttribute('data-update-url');

    newsCategoriesContainer.addEventListener('click', function(event) {
        var target = event.target;
        var categoryElement = event.target.closest('.badge-info');
        var categoryId = categoryElement.getAttribute('data-id');
        var categoryTitleElement = categoryElement.querySelector('.category-title');
        var editInputElement = categoryElement.querySelector('.edit-category-input');
        var saveButton = categoryElement.querySelector('.save-category-btn');
        var cancelButton = categoryElement.querySelector('.cancel-edit-category-btn');
        var editButton = categoryElement.querySelector('.edit-category-btn');

        if (target.classList.contains('edit-category-btn')) {
            categoryTitleElement.style.display = 'none';
            editInputElement.style.display = 'inline-block';
            saveButton.style.display = 'inline-block';
            cancelButton.style.display = 'inline-block';
            editButton.style.display = 'none';
        }

        if (target.classList.contains('save-category-btn')) {
            var newTitle = editInputElement.value;

            if (newTitle.trim() !== '') {
                updateCategoryTitle(categoryId, newTitle, categoryTitleElement, editInputElement, saveButton, editButton, cancelButton);
            }
        }

        if (target.classList.contains('cancel-edit-category-btn')) {
            cancelEditCategory(categoryTitleElement, editInputElement, saveButton, editButton, cancelButton);
        }

        if (target.classList.contains('fa-trash-o')) {
            if (!confirm('Are you sure you want to delete this category')) {
                return;
            }

            if (categoryId) {
                deleteNewsCategory(categoryId, categoryElement);
            }
        }
    });

    function cancelEditCategory(titleElement, inputElement, saveButton, editButton, cancelButton) {
        titleElement.style.display = 'inline-block';
        inputElement.style.display = 'none';
        saveButton.style.display = 'none';
        cancelButton.style.display = 'none';
        editButton.style.display = 'inline-block';

        inputElement.value = titleElement.textContent;
    }

    function updateCategoryTitle(id, newTitle, titleElement, inputElement, saveButton, editButton, cancelButton) {
        var updateUrl = updateCategoryUrl.replace('0', id);
        var data = { title: newTitle };

        ajax.request('POST', updateUrl, data, function(error, response) {
            if (error) {
                showNotification('Error updating category!', 'error');
            } else {
                var jsonResponse = JSON.parse(response);
                if (jsonResponse.status === 'success') {
                    titleElement.textContent = newTitle;
                    titleElement.style.display = 'inline-block';
                    inputElement.style.display = 'none';
                    saveButton.style.display = 'none';
                    cancelButton.style.display = 'none';
                    editButton.style.display = 'inline-block';
                    showNotification('Category updated successfully', 'success');
                } else {
                    showNotification('Error: ' + jsonResponse.message, 'error');
                }
            }
        });
    }

    function deleteNewsCategory(id, categoryElement) {
        var deleteUrl = deleteCategoryUrl.replace('0', id);

        ajax.request('DELETE', deleteUrl, null, function(error, response) {
            if (error) {
                showNotification('Error deleting category!', 'error');
            } else {
                var jsonResponse = JSON.parse(response);
                if (jsonResponse.status === 'success') {
                    categoryElement.remove();
                    showNotification('Category deleted successfully', 'success');
                } else {
                    showNotification('Error: ' + jsonResponse.message, 'error');
                }
            }
        });
    }

    function addNewsCategoryToDOM(category) {
        var span = document.createElement('span');
        span.classList.add('badge', 'badge-info');
        span.setAttribute('data-id', category.id);

        var titleSpan = document.createElement('span');
        titleSpan.classList.add('category-title');
        titleSpan.textContent = category.title;

        var editInput = document.createElement('input');
        editInput.type = 'text';
        editInput.classList.add('edit-category-input');
        editInput.value = category.title;

        var editIcon = document.createElement('i');
        editIcon.classList.add('fa', 'fa-pencil', 'edit-category-btn');

        var saveIcon = document.createElement('i');
        saveIcon.classList.add('fa', 'fa-check', 'save-category-btn');

        var deleteIcon = document.createElement('i');
        deleteIcon.classList.add('fa', 'fa-trash-o', 'delete-category-btn');

        var cancelIcon = document.createElement('i');
        cancelIcon.classList.add('fa', 'fa-times', 'cancel-edit-category-btn');

        span.appendChild(titleSpan);
        span.appendChild(editInput);
        span.appendChild(editIcon);
        span.appendChild(saveIcon);
        span.appendChild(cancelIcon);
        span.appendChild(deleteIcon);

        newsCategoriesContainer.appendChild(span);
    }


    var addNewsCategory = function() {
        var data = {
            title: addCategoryTitle.value
        };

        ajax.request('POST', addCategoryUrl, data, function(error, response) {
            if (error) {
                showNotification('Error saving category!', 'error');
            } else {
                var jsonResponse = JSON.parse(response);
                if (jsonResponse.status === 'success') {
                    addNewsCategoryToDOM(jsonResponse.category);
                    showNotification('Category added successfully', 'success');
                } else {
                    showNotification('Error: ' + jsonResponse.message, 'error');
                }
            }
        })
    }

    var modal = new Modal('add-category-modal', addNewsCategory);

    if (addCategoryModalBtn) {
        addCategoryModalBtn.addEventListener('click', function() {
            modal.open();
        });
    }
});
