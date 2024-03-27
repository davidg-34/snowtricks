currentPage = 1
d = null
async function loadMore() {
    currentPage++;
    data = await fetch('/ajax/tricks/' + currentPage)  
    html = await data.text()
    document.getElementById('tricks_container').innerHTML += html
}


