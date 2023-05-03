const WebSocket = require('ws');
const amqp = require('amqplib');

const port = 3000;
const host = 'hl-rabbitmq';
const user = 'rabbit';
const password = '1234567890';

const clients = [];

const wss = new WebSocket.Server({
    port: port
});

wss.on('connection', (ws) => {
    console.log('Client connected');

    ws.on('message', async (message) => {
        try {
            const data = JSON.parse(message);

            if (data.type === 'subscribe') {

                let queue = data.payload.channel;

                console.log('Client subscribe to channel: ' + queue);

                if (clients[queue] === undefined) {
                    clients[queue] = {
                        amqp: null,
                        connections: []
                    };

                    clients[queue].connections.push(ws);

                    clients[queue].amqp = await amqp.connect('amqp://' + host, {
                        credentials: require('amqplib').credentials.plain(user, password)
                    });

                    const channel = await clients[queue].amqp.createChannel();
                    await channel.assertQueue(queue);
                    await channel.consume(queue, (message) => {
                        message = JSON.parse(message.content.toString());

                        for (let i = 0; i < clients[queue].connections.length; i++) {
                            clients[queue].connections[i].send(msg('posted', message));
                        }

                    }, {
                        noAck: true
                    });
                } else {
                    clients[queue].connections.push(ws);
                }

            }
        } catch (err) {
            console.error(err);
        }
    });
});

function msg(type, payload) {
    return JSON.stringify({
        type: type,
        payload: payload
    });
}
