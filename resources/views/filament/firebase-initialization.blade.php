<script src="https://cdnjs.cloudflare.com/ajax/libs/ion-sound/3.0.7/js/ion.sound.min.js"
        integrity="sha512-k0RyhyJoNdQfdrx7Yb5+zbrtFp8CVsGMJPlQkcNsNZi82GS0R09TG1F/Ar1LuUSXrkVMuk7SftnrXK35nAfdYg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.5.1/axios.min.js"
        integrity="sha512-emSwuKiMyYedRwflbZB2ghzX8Cw8fmNVgZ6yQNNXXagFzFOaQmbvQ1vmDkddHjm5AITcBIZfC7k4ShQSjgPAmQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
      integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
      crossorigin="anonymous" referrerpolicy="no-referrer"/>
<style>
    body {
        overflow: hidden!important;
    }
    .fi-timeline-item-heading{
        justify-content: space-between!important;
    }
    .custom-map-control-button{
        background: #fbfbfb;
        padding: 10px;
        font-size: 20px;
    }
</style>
@viteReactRefresh
@vite('resources/js/index.jsx')
<script>


    ion.sound({
        sounds: [
            {name: "beer_can_opening"},
            {name: "bell_ring"},
            {name: "branch_break"},
            {name: "button_click"}
        ],

        // main config
        path: "/sounds/",
        preload: true,
        multiplay: true,
        volume: 0.9
    });

</script>
<script>

    const firebaseConfig = {
        apiKey: "AIzaSyCCOmiSmFcjM6BLfRu8n04LIJumX7JawZY",
        authDomain: "kufa-36f7e.firebaseapp.com",
        projectId: "kufa-36f7e",
        storageBucket: "kufa-36f7e.appspot.com",
        messagingSenderId: "60028966937",
        appId: "1:60028966937:web:5c2677be104af6dc3bf288",
        measurementId: "G-ELYZGNZ64G"
    };
    firebase.initializeApp(firebaseConfig);

    const messaging = firebase.messaging();

    function initFirebaseMessagingRegistration() {
        messaging.requestPermission().then(function () {
            return messaging.getToken()
        }).then(function (token) {
            axios.post(route('admin.fcm-token', {'device_token': token}), {
                _method: "put",
                token,
                headers: {'X-CSRF-TOKEN': '{{csrf_token()}}'}
            })

        }).catch(function (err) {
            console.log(`Token Error :: ${err}`);
        });
    }

    initFirebaseMessagingRegistration();
    messaging.onMessage(function (payload) {
        // new Notification(payload.notification.title, {body: payload.notification.body});

        new FilamentNotification()
            .title(payload.data.title)
            .body(payload.data.body)
            .icon('heroicon-o-shopping-bag')
            .actions([
                new FilamentNotificationAction("{{__('sections.view')}}")
                    .button()
                    .url(urlMapper(payload.data.entity_type, payload.data.entity_id))
                    .openUrlInNewTab(),
            ])
            .send()
        ion.sound.play("bell_ring");

    });

    const urlMapper = function (entityType, entityID) {
        const mapping = {
            order: route('filament.admin.resources.orders.view', entityID),
            branch: route('filament.admin.resources.catalog.branches.edit', entityID)
        }
        return mapping[entityType];
    };
</script>
