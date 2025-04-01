// public/js/klaro-config.js

window.klaroConfig = {
    elementID: 'klaro',
    privacyPolicy: '/politique-confidentialite', // lien vers ta page
    default: true,
    mustConsent: true,
    lang: 'fr',
    translations: {
        fr: {
            consentModal: {
                title: 'Nous utilisons des cookies 🍪',
                description: 'Certains sont essentiels, d’autres nous aident à améliorer l’expérience.'
            },
            ok: 'J’accepte',
            acceptAll: 'Tout accepter',
            decline: 'Tout refuser',
        }
    },
    services: [
        {
            name: 'session',
            title: 'Cookies de session',
            default: false,
            required: true,
            purposes: ['fonctionnel'],
            cookies: [
                {
                    name: 'PHPSESSID', // Nom du cookie de session
                    purpose: 'Maintenir la session active de l\'utilisateur', // Explication de ce cookie
                    required: true, // Cookie requis
                }
            ]
        }
    ]
};

// Démarrer Klaro
klaro.start(klaroConfig);
