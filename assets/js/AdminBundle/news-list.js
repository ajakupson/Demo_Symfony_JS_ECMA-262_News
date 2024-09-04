document.addEventListener('DOMContentLoaded', function() {
    var ajax = new Ajax();
    var addNewsModalBtn = document.getElementById('add-news-modal-btn');
    var addNewsUrl = addNewsModalBtn.getAttribute('data-add-url');
    var newsTitle = document.getElementById('news-title');
    var newsShortDescription = document.getElementById('news-short-description');
    var newsContent = document.getElementById('news-content');
    var newsInsertDate = document.getElementById('news-insert-date');
    var newsPicture = document.getElementById('news-picture');
    var newsCategories = document.getElementById('news-categories');
    var newsContainer = document.getElementById('news-container');
    var deleteNewsUrl = newsContainer.getAttribute('data-delete-url');


    // Обработчик для удаления новости
    newsContainer.addEventListener('click', function(event) {
        if (event.target.classList.contains('fa-trash-o')) {
            var newsElement = event.target.closest('.news-item');
            var newsId = newsElement.getAttribute('data-id');

            if (newsId) {
                deleteNews(newsId, newsElement);
            }
        }
    });

    newsContainer.addEventListener('click', function(event) {
        if (event.target.classList.contains('delete-button')) {
            var newsElement = event.target.closest('.news-item');
            var newsId = newsElement.getAttribute('data-id');

            if (newsId) {
                deleteNews(newsId, newsElement);
            }
        }
    });

    function deleteNews(id, newsElement) {
        if (!confirm('Are you sure you want to delete this news?')) {
            return;
        }

        var deleteUrl = deleteNewsUrl.replace('0', id);

        ajax.request('DELETE', deleteUrl, null, function(error, response) {
            if (error) {
                showNotification('Error deleting news!', 'error');
            } else {
                var jsonResponse = JSON.parse(response);
                if (jsonResponse.status === 'success') {
                    newsElement.remove();
                    showNotification('News deleted successfully', 'success');

                    if (newsContainer.children.length === 0) {
                        var currentPage = new URLSearchParams(window.location.search).get('page') || 1;
                        currentPage = parseInt(currentPage);

                        if (currentPage > 1) {
                            window.location.href = '?page=' + (currentPage - 1);
                        } else {
                            window.location.reload();
                        }
                    }

                } else {
                    showNotification('Error: ' + jsonResponse.message, 'error');
                }
            }
        });
    }

    var addNews = function() {
        var formData = new FormData();
        formData.append('title', newsTitle.value);
        formData.append('shortDescription', newsShortDescription.value);
        formData.append('content', newsContent.value);

        var formattedDate = new Date(newsInsertDate.value).toISOString().slice(0, 19).replace('T', ' ');
        formData.append('insertDate', formattedDate);

        if (newsPicture.files.length > 0) {
            formData.append('picture', newsPicture.files[0]);
        }

        var selectedCategories = Array.from(newsCategories.selectedOptions).map(option => option.value);
        selectedCategories.forEach(category => formData.append('categories[]', category));

        ajax.request('POST', addNewsUrl, formData, function(error, response) {
            if (error) {
                showNotification('Error saving news!', 'error');
            } else {
                var jsonResponse = JSON.parse(response);
                if (jsonResponse.status === 'success') {
                    showNotification('News added successfully', 'success');
                    window.location.reload();
                } else {
                    showNotification('Error: ' + jsonResponse.message, 'error');
                }
            }
        });
    }

    var modal = new Modal('add-news-modal', addNews);

    if (addNewsModalBtn) {
        addNewsModalBtn.addEventListener('click', function() {
            modal.open();
        });
    }
});