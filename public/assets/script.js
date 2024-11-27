let currentPage = 1; // Pour la version classique
let currentPageTiny = 1; // Pour la version mobile (accordéon)

// Version classique
async function loadMore() {
    try {
        currentPage++;
        const response = await fetch('/ajax/tricks/' + currentPage);
        if (!response.ok) throw new Error('Erreur lors du chargement des données.');
        const html = await response.text();
        document.getElementById('tricks_container').innerHTML += html;
    } catch (error) {
        console.error('Erreur :', error.message);
    }
}

// Version tiny (accordéon)
async function loadMoreTiny() {
    try {
        currentPageTiny++;
        const response = await fetch('/ajax/tricks/' + currentPageTiny);
        if (!response.ok) throw new Error('Erreur lors du chargement des données.');
        const html = await response.text();
        document.getElementById('tricks_container_tiny').innerHTML += html;
    } catch (error) {
        console.error('Erreur :', error.message);
    }
}









/* let currentPage = 1; // Pour la version classique
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
} */