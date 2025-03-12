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

//   document.querySelectorAll('.like').forEach(button => {
//     button.addEventListener('click', async () => {
//         const postId = button.getAttribute('post-like');
//         const likeCountSpan = button.nextElementSibling; // Le <span> qui affiche le nombre de likes

//         const response = await fetch(`/like/${postId}`, { method: 'POST' });
//         const data = await response.json();

//         if (data.success) {
//             likeCountSpan.textContent = data.likeCount;
//         }
//     });
// });

document.querySelectorAll('.like').forEach(function(button) {
    button.addEventListener('click', function() {
      // Récupérer l'ID du post depuis l'attribut data
      var postId = button.getAttribute('post-like');
      // Le span suivant contient le nombre de likes
      var likeCountSpan = button.nextElementSibling;
  
      // Envoyer une requête POST à l'URL /like/{postId}
      fetch('/like/' + postId, { method: 'POST' })
        .then(function(response) {
          // Convertir la réponse en JSON
          return response.json();
        })
        .then(function(data) {
          // Si l'opération a réussi, mettre à jour le compteur de likes
          if (data.success) {
            likeCountSpan.textContent = data.likeCount;
          }
        })
        .catch(function(error) {
          console.error('Erreur lors du like:', error);
        });
    });
  });


// document.addEventListener('DOMContentLoaded', function () {
//     const likeButton = document.querySelector(".pixelarticons-heart");

//     likeButton.addEventListener('click', function () {
//         const likeCount = likeButton.nextElementSibling;
//         let count = parseInt(likeCount.innerText);

//         // Vérifie si le bouton a déjà été cliqué
//         if (likeButton.classList.contains('liked')) {
//             // Retire le like
//             count--;
//             likeButton.classList.remove('liked');
//         } else {
//             // Ajoute un like
//             count++;
//             likeButton.classList.add('liked');
//         }
//         likeCount.innerText = count;
//     });
// });

// document.addEventListener('DOMContentLoaded', function () {
//     const likeButtons = document.querySelectorAll(".pixelarticons-heart");

//     likeButtons.forEach(button => {
//         button.addEventListener('click', function () {
//             const likeCount = this.nextElementSibling;
//             let count = parseInt(likeCount.innerText);

//             if (this.classList.contains('liked')) {
//                 count--;
//                 this.classList.remove('liked');
//             } else {
//                 count++;
//                 this.classList.add('liked');
//             }
//             likeCount.innerText = count;
//         });
//     });
// });

document.addEventListener('DOMContentLoaded', function() {
    var fileInput = document.querySelector('input[type="file"]');
    var imagePreview = document.getElementById('image-preview');

    fileInput.addEventListener('change', function(event) {
        var file = event.target.files[0];
        if (file && file.type.startsWith('/image/')) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var img = document.createElement('img');
                img.src = e.target.result;
                img.style.maxWidth = '100%';
                img.style.height = 'auto';
                imagePreview.innerHTML = '';
                imagePreview.appendChild(img);
            };
            reader.readAsDataURL(file);
        } else {
            imagePreview.innerHTML = 'Please select a valid image file.';
        }
    });
});