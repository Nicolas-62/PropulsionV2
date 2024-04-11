function showContent(entry) {
    console.log('show content infoss')

    // Cacher tous les contenus
    let contentDivs = document.getElementsByClassName('content')[0].children;
    for (let i = 0; i < contentDivs.length; i++) {
        contentDivs[i].classList.add('hidden');
    }

    // Afficher le contenu correspondant à l'entrée sélectionnée
    let selectedContent = document.getElementById(entry);
    selectedContent.classList.remove('hidden');
}