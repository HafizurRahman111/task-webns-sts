import Pusher from 'pusher-js';

const pusher = new Pusher('be980d5e2df9f77069ea', {
    cluster: 'ap2',
    forceTLS: true,
});

export default pusher;