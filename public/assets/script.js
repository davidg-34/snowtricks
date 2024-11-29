let currentPage = 1; // Pour les tricks grand écran
let currentCommentsPage = 1; // Pour les commentaires sur mobile

// Fonction pour charger plus de tricks
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

// Fonction pour charger plus de commentaires
async function loadMoreComments(trickSlug) {
    console.log('Loading more comments for trick:', trickSlug);
    try {
        currentCommentsPage++;
        const response = await fetch('/ajax/comments/' + trickSlug + '/' + currentCommentsPage);
        if (!response.ok) throw new Error('Erreur lors du chargement des données.');
        const html = await response.text();
        document.getElementById('comments_container').innerHTML += html;
    } catch (error) {
        console.error('Erreur :', error.message);
    }
}