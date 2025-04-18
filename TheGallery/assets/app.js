import './bootstrap.js';
// import 'bootstrap/dist/js/bootstrap.bundle.min';

/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

// console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

// document.addEventListener('DOMContentLoaded', function () {
//     const fileInput = document.getElementById('user_profilePicture');
//     const previewImage = document.getElementById('profilePicturePreview');

//     fileInput.addEventListener('change', function (event) {
//         const file = event.target.files[0];
//         if (file) { 
//             const reader = new FileReader();
//             reader.onload = function (e) {
//                 previewImage.src = e.target.result;
//             };
//             reader.readAsDataURL(file);
//         }
//     });
// });

document.addEventListener('DOMContentLoaded', function () {
    const cookieBanner = document.getElementById('cookie-banner');
    const acceptCookies = document.getElementById('accept-cookies');

    if(!localStorage.getItem('cookiesAccepted')) {
        cookieBanner.style.display = 'flex';// affiche la bannière
    }

    acceptCookies.addEventListener('click', function () {
        localStorage.setItem('cookiesAccepted', 'true'); // sauvegarde les cookies dans le localStorage
        cookieBanner.style.display = 'none'; // cache la bannière
    });
});


document.addEventListener('DOMContentLoaded', function () {

    // Sélectionne tous les formulaires de like par leur classe
    const likeForms = document.querySelectorAll('.like-form');

    // Pour chaque formulaire, on intercepte la soumission
    likeForms.forEach(form => {
        form.addEventListener('submit', function (event) {
            // 1. Empêche le rechargement de la page
            event.preventDefault();

            

            // 3. Récupère l'URL d'action (où on envoie la requête AJAX)
            const actionUrl = form.getAttribute('action');

            // 4. Envoie la requête AJAX en POST vers cette URL
            fetch(actionUrl, {
                method: 'POST',
                headers: {
                    // Pas forcément obligatoire, mais indique qu'on fait une requête AJAX
                    'X-Requested-With': 'XMLHttpRequest'
                }
                // Ici, on n'envoie pas de body particulier (pas de JSON supplémentaire),
                // mais tu peux ajouter un "body: JSON.stringify(...)" si besoin
            })
                .then(response => response.json()) // Convertit la réponse en JSON
                .then(data => {
                    // 5. data contient la réponse JSON envoyée par le contrôleur
                    //    Par exemple : { success: true, action: "liked", likes: 5 }

                    // console.log("Réponse JSON reçue :", data);

                    // 6. Mettre à jour le compteur de likes
                    //    le <span class="like-count"> se trouve
                    //    dans le même conteneur parent que le formulaire
                    const likeCountElement = form.parentElement.querySelector('.like-count');
                    if (likeCountElement && data.likes !== undefined) {
                        // Met à jour le texte avec la nouvelle valeur
                        likeCountElement.textContent = data.likes;
                    }

                    // 7. Optionnel : animer le bouton
                    const likeButton = form.querySelector('.like-button');
                    if (likeButton) {
                        likeButton.classList.add('animate-like');
                        setTimeout(() => {
                            likeButton.classList.remove('animate-like');
                        }, 500);
                    }
                })
                .catch(error => {
                    // 8. En cas d'erreur AJAX, on l'affiche dans la console
                    console.error("Erreur AJAX :", error);
                });
        });
    });
});






document.addEventListener('DOMContentLoaded', function () {
    // Sélectionne tous les éléments ayant la classe "selectable-image"
    const selectableImages = document.querySelectorAll('.selectable-image');
    // Récupère l'élément input caché qui stockera l'ID de l'image sélectionnée
    const selectedImageIdInput = document.getElementById('selectedImageId');
    // Récupère le conteneur qui affichera la prévisualisation de l'image
    const previewContainer = document.getElementById('selectedImagePreview');
    // Récupère l'élément <img> qui affichera la prévisualisation
    const previewImg = document.getElementById('previewImg');

    // Pour chaque image cliquable, on attache un écouteur d'événement sur le clic
    selectableImages.forEach(img => {
        img.addEventListener('click', function () {
            // Si l'image cliquée est déjà sélectionnée, on la désélectionne
            if (this.classList.contains('selected')) {
                // Retirer la classe "selected" pour indiquer la désélection
                this.classList.remove('selected');
                // Vider le champ caché (aucune image sélectionnée)
                selectedImageIdInput.value = '';
                // Vider l'aperçu en retirant la source de l'image
                previewImg.src = '';
                // Masquer le conteneur de prévisualisation
                previewContainer.style.display = 'none';
            } else {
                // Sinon, d'abord désélectionner toutes les images
                selectableImages.forEach(i => i.classList.remove('selected'));
                // Ajouter la classe "selected" à l'image cliquée pour un feedback visuel
                this.classList.add('selected');

                // Récupérer l'ID de l'image depuis l'attribut data-id ou "data-"attribute, html personnalisé
                const imgId = this.getAttribute('data-id');
                // Mettre à jour le champ caché avec l'ID de l'image sélectionnée
                selectedImageIdInput.value = imgId;

                // Mettre à jour l'image de prévisualisation avec la source de l'image cliquée
                previewImg.src = this.getAttribute('src');
                // Afficher le conteneur de prévisualisation
                previewContainer.style.display = 'block';
            }
        });
    });
});



/* script filtre tag */
document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('[data-filter]');
    const items = document.querySelectorAll('.image-item');

    buttons.forEach(btn => {
        btn.addEventListener('click', () => {
            buttons.forEach(filterButton => filterButton.classList.remove('active'));
            btn.classList.add('active');

            const filter = btn.getAttribute('data-filter');
            items.forEach(item => {
                const tag = item.getAttribute('data-tag');
                item.style.display = (filter === 'all' || tag === filter) ? 'block' : 'none';
            });
        });
    });
});


/* gérer dropdown bootstrap 5 des tags */

document.addEventListener('DOMContentLoaded', function () {
    //  Filtrage des POSTS
    const postDropdownItems = document.querySelectorAll('#postTagFilterMenu .dropdown-item');
    const postItems = document.querySelectorAll('.post-item');

    postDropdownItems.forEach(filterButton => {
        filterButton.addEventListener('click', function (e) {
            e.preventDefault();

            // Met à jour le bouton actif
            postDropdownItems.forEach(b => b.classList.remove('active')); // b is temporary variable name
            filterButton.classList.add('active');

            const filter = filterButton.getAttribute('data-filter');

            postItems.forEach(item => {
                const tag = item.getAttribute('data-tag');
                item.style.display = (filter === 'all' || tag === filter) ? 'block' : 'none';
            });

            // Met à jour le texte du bouton
            const dropdownToggle = document.getElementById('filterDropdown');
            dropdownToggle.textContent = filterButton.textContent.trim();
        });
    });

    //  Filtrage des IMAGES
    const imageDropdownItems = document.querySelectorAll('#imageTagFilterMenu .dropdown-item');
    const imageItems = document.querySelectorAll('.image-item');

    imageDropdownItems.forEach(filterButton => {
        filterButton.addEventListener('click', function (e) {
            e.preventDefault();

            imageDropdownItems.forEach(b => b.classList.remove('active'));
            filterButton.classList.add('active');

            const filter = filterButton.getAttribute('data-filter');

            imageItems.forEach(item => {
                const tag = item.getAttribute('data-tag');
                item.style.display = (filter === 'all' || tag === filter) ? 'block' : 'none';
            });

            const dropdownToggle = document.getElementById('filterImageDropdown');
            dropdownToggle.textContent = filterButton.textContent.trim();
        });
    });
});