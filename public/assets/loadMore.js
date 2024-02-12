document.getElementById('load-more').addEventListener('click', function() {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '/load-more', true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 3 && xhr.status === 200) {
            var data = JSON.parse(xhr.responseText);
        }
    };
    xhr.send();
});
