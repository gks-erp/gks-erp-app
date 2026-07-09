
///console.log('_notification_worker.js load');

const gks_notification_clients = new Map();

function gks_notification_ping() {
  const now = Date.now();
  gks_notification_clients.forEach((lastPong, port) => {
    port.postMessage({ type: 'ping' });
    //console.log('send ping');
  });
}
var gks_notification_last_datajson='';
function gks_notification_checkNotifications() {
  fetch('/my/admin-notification_get_data.php')
  .then(r => r.json())
  .then(datajson => {
    
    
    //console.log(datajson);
    datajson_str = JSON.stringify(datajson);
    if (gks_notification_last_datajson===datajson_str) {
      return;
    }
    gks_notification_last_datajson=datajson_str;
    //console.log('num clients (before)',gks_notification_clients.size);
    gks_notification_ping();
    setTimeout(function() {
      const now = Date.now();
      gks_notification_clients.forEach((lastPong, port) => {
        diafora=now - lastPong;
        if (diafora < 2000) {
          try {
            port.postMessage(datajson);
            datajson.ps=false;
          } catch(e) { 
            gks_notification_clients.delete(port); 
          }
        } else {
          gks_notification_clients.delete(port);
        }
      });
      //console.log('num clients (after)',gks_notification_clients.size);
    } , 1000);
  })
  .catch(err => {
    console.error('Notification check failed:', err);
    data={success:false, message: 'Y29ubmVjdGlvbiBlcnJvcg=='};//connection error
    gks_notification_clients.forEach(client => client.postMessage(data));
  });
}
setInterval(gks_notification_checkNotifications, 15000);

self.onconnect = function(e) {
  //console.log('onconnect');
  const port = e.ports[0];
  gks_notification_clients.set(port, Date.now());
  port.onmessage = function(msg) {
    if (msg.data.type === 'pong') {
      //console.log('get pong');
      gks_notification_clients.set(port, Date.now());
    }
  };

  port.start();
};

