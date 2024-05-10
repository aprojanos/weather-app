const csrftoken = document.querySelector('meta[name="csrf-token"]').getAttribute('Content');

if ("serviceWorker" in navigator) {
    window.addEventListener("load", function () {
        navigator.serviceWorker.register("/sw.js");
    });
}

if (!('Notification' in window)) {
    Toastify({text: 'This browser does not support notifications.'}).showToast();
}

function urlBase64ToUint8Array(base64String) {
    var padding = "=".repeat((4 - (base64String.length % 4)) % 4);
    var base64 = (base64String + padding).replace(/\-/g, "+").replace(/_/g, "/");

    var rawData = window.atob(base64);
    var outputArray = new Uint8Array(rawData.length);

    for (var i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}

export function enablePushNotifications(params) {
    navigator.serviceWorker.ready.then(registration => {
        registration.pushManager.getSubscription().then(subscription => {
            if (subscription) {
                console.log('Subscription already exists.')
                return subscription;
            }
            console.log('VITE_VAPID_PUBLIC_KEY', import.meta.env.VITE_VAPID_PUBLIC_KEY);
            const serverKey = urlBase64ToUint8Array(import.meta.env.VITE_VAPID_PUBLIC_KEY);

            return registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: serverKey
            });
        }).then(subscription => {
            if (!subscription) {
                Toastify({text: 'Error occured while subscribing.'}).showToast();
                return;
            }
            subscribe(subscription, params);
        });
    });
}

export function disablePushNotifications() {
    navigator.serviceWorker.ready.then(registration => {
        registration.pushManager.getSubscription().then(subscription => {
            if (!subscription) {
                Toastify({text: 'No subscription found.'}).showToast();
                return;
            }

            subscription.unsubscribe().then(() => {
                axios({
                    url: '/weather/unsubscribe',
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrftoken
                    },
                    data: JSON.stringify({
                        endpoint: subscription.endpoint
                    })
                })
                    .then(response => {
                        console.log('Success:', response.data);
                        Toastify({text: response.data.message}).showToast();
                    })
                    .catch((error) => {
                        console.error('Error:', error);
                        Toastify({text: error.message}).showToast();
                    });
            })
        });
    });
}

function subscribe(sub, params) {
    const key = sub.getKey('p256dh')
    const token = sub.getKey('auth')
    const contentEncoding = (PushManager.supportedContentEncodings || ['aesgcm'])[0]

    const data = {
        endpoint: sub.endpoint,
        public_key: key ? btoa(String.fromCharCode.apply(null, new Uint8Array(key))) : null,
        auth_token: token ? btoa(String.fromCharCode.apply(null, new Uint8Array(token))) : null,
        encoding: contentEncoding,
    };

    axios({
        url: '/weather/subscribe',
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrftoken
        },
        data: JSON.stringify({...data, ...params}),
    })
        .then(response => {
            console.log('Success:', response.data);
            Toastify({text: response.data.message}).showToast();
        })
        .catch((error) => {
            Toastify({text: error.message}).showToast();
        });
}

