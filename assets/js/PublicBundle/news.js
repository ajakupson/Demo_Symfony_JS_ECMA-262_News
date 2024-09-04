document.addEventListener('DOMContentLoaded', function() {
    var ajax = new Ajax();
    var addCommentBtn = document.getElementById('news-add-comment-btn');
    var addCommentForm = addCommentBtn.closest('form');
    var addCommentUrl = addCommentForm.getAttribute('data-add-news-comment-url');
    var commentsList = document.getElementById('comments-list');

    addCommentBtn.addEventListener('click', function() {
        var data = {
            username: document.getElementById('username').value,
            content: document.getElementById('content').value
        };

        ajax.request('POST', addCommentUrl, data, function(error, response) {
            if (error) {
                showNotification('Error adding comment!', 'error');
            } else {
                var jsonResponse = JSON.parse(response);
                if (jsonResponse.status === 'success') {
                    addCommentToDOM(jsonResponse.comment);
                    showNotification('Comment added successfully', 'success');
                    addCommentForm.reset();
                } else {
                    showNotification('Error: ' + jsonResponse.message, 'error');
                }
            }
        });
    });

    function addCommentToDOM(comment) {
        var li = document.createElement('li');
        li.classList.add('comment-item');

        var p = document.createElement('p');
        p.classList.add('comment-content');
        p.textContent = comment.content;

        var authorSpan = document.createElement('span');
        authorSpan.classList.add('comment-author');
        authorSpan.textContent = comment.username ? comment.username : 'Anonymous';

        var dateSpan = document.createElement('span');
        dateSpan.classList.add('comment-date');
        dateSpan.textContent = comment.createdAt;

        li.appendChild(p);
        li.appendChild(authorSpan);
        li.appendChild(dateSpan);
        commentsList.appendChild(li);
    }
});