// Give the service worker access to Firebase Messaging.
// Note that you can only use Firebase Messaging here. Other Firebase libraries
// are not available in the service worker.importScripts('https://www.gstatic.com/firebasejs/7.23.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js');
/*
Initialize the Firebase app in the service worker by passing in the messagingSenderId.
*/
firebase.initializeApp({
    apiKey: "AIzaSyCCOmiSmFcjM6BLfRu8n04LIJumX7JawZY",
    authDomain: "kufa-36f7e.firebaseapp.com",
    projectId: "kufa-36f7e",
    storageBucket: "kufa-36f7e.appspot.com",
    messagingSenderId: "60028966937",
    appId: "1:60028966937:web:5c2677be104af6dc3bf288",
    measurementId: "G-ELYZGNZ64G"
});


const messaging = firebase.messaging();
messaging.onBackgroundMessage((payload) => {
    console.log(payload);
    const notificationTitle = payload.data.title;
    const notificationOptions = {
        body: payload.data.body,
        icon: 'https://awscdn1.tasawk.com/wp-content/uploads/2018/08/logo-d.png',
        silent: false,
        tag: [payload.data.entity_type, payload.data.entity_id],
        vibrate: [500],
        requireInteraction: true,
    };
    self.registration.showNotification(notificationTitle, notificationOptions);
});
self.addEventListener('notificationclick', function (event) {
    const [entityType, entityID] = event.notification.tag.split(',')

    clients.openWindow(urlMapper(entityType, entityID));
});

const urlMapper = function (entityType, entityID) {
    const mapping = {
        order: `/admin/orders/${entityID}/view`,
        branch: `admin/catalog/branches/${entityID}/edit`,
    }
    return mapping[entityType];
};
