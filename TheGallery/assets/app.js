import './bootstrap.js';
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




document.addEventListener('DOMContentLoaded', function () {
    const likeButton = document.querySelector("pixelarticons-heart");

    likeButton.addEventListener('click', function (event) {
        const likeCount = likeButton.nextElementSibling;
        let count = parseInt(likeCount.innerText);

        // Vérifie si le bouton a déjà été cliqué
        if (likeButton.classList.contains('liked')) {
            // Retire le like
            count--;
            likeButton.classList.remove('liked');
        } else {
            // Ajoute un like
            count++;
            likeButton.classList.add('liked');
        }
        likeCount.innerText = count;
    });
});