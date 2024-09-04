document.addEventListener('DOMContentLoaded', function() {
    var ajax = new Ajax();
    var newsTitle = document.getElementById('news-title');
    var newsShortDescription = document.getElementById('news-short-description');
    var newsContent = document.getElementById('news-content');
    var newsInsertDate = document.getElementById('news-insert-date');
    var newsPicture = document.getElementById('news-picture');
    var newsCategories = document.getElementById('news-categories');
    var btnUpdateNews = document.getElementById('btn-update-news');
    var formUpdateNews = document.getElementById('form-update-news');
    var updateNewsUrl = formUpdateNews.getAttribute('data-update-url');
    var commentsList = document.getElementById('comments-list');
    var deleteNewsCommentUrl = commentsList.getAttribute('data-delete-comment-url');

    btnUpdateNews.addEventListener('click', function(event) {
        if(!(confirm('Are you sure you want to update this news?'))) {
            return;
        }

        updateNews();
    });

    commentsList.addEventListener('click', function(event) {
        if (event.target.classList.contains('delete-comment-btn')) {
            if(!(confirm('Are you sure you want to delete this comment?'))) {
                return;
            }

            var commentElement = event.target.closest('.comment-item');
            var commentId = commentElement.getAttribute('data-id');
            deleteComment(commentId, commentElement);
        }
    });

    function deleteComment(id, commentElement) {
        var deleteUrl= deleteNewsCommentUrl.replace('0', id);

        ajax.request('DELETE', deleteUrl, null, function(error, response) {
            if (error) {
                showNotification('Error deleting comment!', 'error');
            } else {
                var jsonResponse = JSON.parse(response);
                if (jsonResponse.status === 'success') {
                    commentElement.remove();
                    showNotification('Comment deleted successfully', 'success');
                } else {
                    showNotification('Error: ' + jsonResponse.message, 'error');
                }
            }
        });
    }

    var updateNews = function() {
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

        ajax.request('POST', updateNewsUrl, formData, function(error, response) {
            if (error) {
                showNotification('Error updating news!', 'error');
            } else {
                var jsonResponse = JSON.parse(response);
                if (jsonResponse.status === 'success') {
                    showNotification('News updated successfully', 'success');
                    window.location.reload();
                } else {
                    showNotification('Error: ' + jsonResponse.message, 'error');
                }
            }
        });
    }
});