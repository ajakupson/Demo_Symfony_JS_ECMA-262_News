(function(global) {
    function Ajax() {}

    Ajax.prototype.request = function(method, url, data, callback) {
        var xhr = new XMLHttpRequest();
        xhr.open(method, url, true);

        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                if (xhr.status >= 200 && xhr.status < 300) {
                    callback(null, xhr.responseText);
                } else {
                    callback(xhr.status, null);
                }
            }
        };

        if ((method === 'POST' || method === 'PUT') && data) {
            if (!(data instanceof FormData)) {
                xhr.setRequestHeader('Content-Type', 'application/json;charset=UTF-8');
                xhr.send(JSON.stringify(data));
            } else {
                xhr.send(data);
            }
        } else {
            xhr.send();
        }
    };

    global.Ajax = Ajax;

}(this));