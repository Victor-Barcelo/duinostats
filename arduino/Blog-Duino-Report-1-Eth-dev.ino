#include <SPI.h>
#include <Ethernet.h>

#define DEBUG true

byte mac[] = { 0xDE, 0xAD, 0xBE, 0xEF, 0xFE, 0xED };
IPAddress ip(192,168,0,177);
// char server[] = "192.168.1.36/webalias/web/app.php"; 
IPAddress server(192,168,1,36);
EthernetClient client;

const int POLLING_INTERVAL = 10000; // ms
const int PIN_LIGHTSENSOR = 0;
const int PIN_TEMPSENSOR = 1;
const char API_KEY[] = "qwerty";

int light;
int temp;

void setup() {
  Serial.begin(9600);
  if(DEBUG) Serial.println("Ready!");
  // start the Ethernet connection:
  if (Ethernet.begin(mac) == 0) {
    if(DEBUG) Serial.println("Failed to configure Ethernet using DHCP");
    // try to congifure using IP address instead of DHCP:
    Ethernet.begin(mac, ip);
  }
  // give the Ethernet time to initialize:
  delay(400);
  if(DEBUG) Serial.println("connecting...");
}

void loop() {
  
  delay(400);
  light = analogRead(PIN_LIGHTSENSOR);
  temp = (125*analogRead(PIN_TEMPSENSOR))>>8;
  
  // if(DEBUG){
  // 	Serial.println(light);
  // 	Serial.println(temp);
  // }

  if (client.connect(server, 80)) {
    insertData(temp,light);
    delay(POLLING_INTERVAL - 400);
    while(client.available()) {
      char c = client.read();
      Serial.print(c);
    }
    Serial.println();
    // if the server's disconnected, stop the client:
    if (!client.connected()) {
      if(DEBUG){
        Serial.println();
        Serial.println("disconnecting.");
      } 
      client.stop();
    }
  } 
  else {
    if(DEBUG) Serial.println("connection failed");
  }

}

void insertData(int temp, int light){
    String data;
    data+="";
    data+="key=";
    data+=API_KEY;
    data+="&temperature=";
    data+=temp;
    data+="&light=";
    data+=light; 
    data+="&submit=Submit"; 
    if(DEBUG) Serial.println("connected");
    client.println("POST /duinostats/web/app_dev.php/insertsensordata HTTP/1.1");
    client.println("Host: 192.168.1.36");
    client.println("Content-Type: application/x-www-form-urlencoded");
    client.println("Connection: close");
    client.print("Content-Length: ");
    client.println(data.length());
    client.println();
    client.print(data);
    client.println();
}