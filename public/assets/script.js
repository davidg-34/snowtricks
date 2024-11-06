
/* let currentPage = 1;
async function loadMore() {
    currentPage++;
    const response = await fetch('/ajax/tricks/' + currentPage);
    const html = await response.text();
    document.getElementById('tricks_container').innerHTML += html;
} */

/* let currentPage = 1;
    let loading = false;

    document.getElementById('load-more-btn').addEventListener('click', function() {
        if (loading) return;  // Empêche de lancer plusieurs requêtes en parallèle

        loading = true;
        currentPage++;  // Incrémente la page à chaque clic

        fetch(`/ajax/tricks/${currentPage}`)
            .then(response => response.text())
            .then(data => {
                // Ajoute les nouveaux tricks sans effacer ceux qui sont déjà là
                document.getElementById('tricks-list').innerHTML += data;
                loading = false;  // Réinitialise le statut de chargement
            })
            .catch(error => {
                console.error('Erreur lors du chargement des tricks:', error);
                loading = false;
            });
    });
*/
