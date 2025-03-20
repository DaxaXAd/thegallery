import './bootstrap.js';
// import 'bootstrap/dist/js/bootstrap.bundle.min';

/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('user_profilePicture');
    const previewImage = document.getElementById('profilePicturePreview');

    fileInput.addEventListener('change', function (event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                previewImage.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
});


//   document.addEventListener('DOMContentLoaded', function () {
//     // API to not scroll automatically when reloading page
//     if ('scrollRestoration' in history) {
//         history.scrollRestoration = 'manual';
//     }


//     const savedPos = sessionStorage.getItem('positionClick');
//     if (savedPos !== null) {
//         window.scrollTo(0, parseInt(savedPos, 10));
//         sessionStorage.removeItem('positionClick'); // Optionnel : supprime après restauration
//     }

//     const likeButtons = document.querySelectorAll('.like-button');

//     likeButtons.forEach(button => {
//         button.addEventListener('click', function (event) {
//              // Enregistre la position verticale du clic dans un tableau dans le sessionStorage
//             // let positionClick = sessionStorage.getItem('positionClick');
//             // if (positionClick) {
//             //     positionClick = JSON.parse(positionClick);
//             // } else {
//             //     positionClick = [];
//             // } 
//             // // Ajoute la position verticale du clic
//             // positionClick.push(event.pageY);
//             sessionStorage.setItem('positionClick', window.scrollY);

//             // Ajoute une classe CSS pour l’animation
//             this.classList.add('animate-like');

//             // Retire la classe après 0.5s (durée de l’animation)
//             setTimeout(() => {
//                 this.classList.remove('animate-like');
//             }, 500);

//             // Laisse le formulaire se soumettre normalement
//             // => la page va se recharger, et le nouveau compteur
//             //    sera affiché via Twig
//         });
//     });
// });

document.addEventListener('DOMContentLoaded', function () {
    // Désactive la restauration automatique du scroll (optionnel)
    if ('scrollRestoration' in history) {
        history.scrollRestoration = 'manual';
    }

    // Sélectionne tous les formulaires de like par leur classe
    const likeForms = document.querySelectorAll('.like-form');

    // Pour chaque formulaire, on intercepte la soumission
    likeForms.forEach(form => {
        form.addEventListener('submit', function (event) {
            // 1. Empêche le rechargement de la page
            event.preventDefault();

            // 2. (Optionnel) Enregistre la position du scroll pour la restaurer après coup
            sessionStorage.setItem('positionClick', window.scrollY);

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

                    console.log("Réponse JSON reçue :", data);

                    // 6. Mettre à jour le compteur de likes
                    //    On suppose que le <span class="like-count"> se trouve
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

    // 9. (Optionnel) Restaure la position du scroll si on l'a enregistrée précédemment
    const savedPos = sessionStorage.getItem('positionClick');
    if (savedPos !== null) {
        window.scrollTo(0, parseInt(savedPos, 10));
        sessionStorage.removeItem('positionClick');
    }
});



/* select/déselect pre-image */

document.addEventListener('DOMContentLoaded', function () {
    const selectableImages = document.querySelectorAll('.selectable-image');
    const selectedImageIdInput = document.getElementById('selectedImageId');
    const previewContainer = document.getElementById('selectedImagePreview');
    const previewImg = document.getElementById('previewImg');

    selectableImages.forEach(img => {
        img.addEventListener('click', function () {
            // Si on clique sur une image déjà sélectionnée,
            // on la désélectionne (toggle)
            if (this.classList.contains('selected')) {
                this.classList.remove('selected');
                selectedImageIdInput.value = '';
                previewImg.src = '';
                previewContainer.style.display = 'none';
            } else {
                // Sinon, on enlève la sélection sur les autres images
                selectableImages.forEach((i) => i.classList.remove('selected'));

                // Puis on sélectionne celle-ci
                this.classList.add('selected');
                const imgId = this.getAttribute('data-id');
                const imgSrc = this.getAttribute('src');
                selectedImageIdInput.value = imgId;
                previewImg.src = imgSrc;
                previewContainer.style.display = 'block';
            }
        });
    });
});
