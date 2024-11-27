let currentPage = 1; // Pour la version classique
let currentPageTiny = 1; // Pour la version mobile (accordéon)

// Version classique
async function loadMore() {
    currentPage++;
    const response = await fetch('/ajax/tricks/' + currentPage);
    const html = await response.text();
    document.getElementById('tricks_container').innerHTML += html;
}

// Version tiny (accordéon)
async function loadMoreTiny() {
    currentPageTiny++;
    const response = await fetch('/ajax/tricks/' + currentPageTiny);
    const html = await response.text();
    document.getElementById('tricks_container_tiny').innerHTML += html;
}