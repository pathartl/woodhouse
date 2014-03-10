// WindowBlindServo.ino

#include <IRremote.h>
#include <Servo.h> 

int RECV_PIN = 3;
int SERVO_PIN = 2;
int LED_PIN = 13;
int BLIND_STATUS = 1; // Blinds down

Servo servo;

IRrecv irrecv(RECV_PIN);

decode_results results;

void moveBlinds(int direction) {
  if (direction == 0) {
    Serial.println("Blinds up");
    servo.attach(SERVO_PIN);
    servo.write(80);
    delay(1300 * 4);
    servo.detach();
  } else if (direction == 1) {
    Serial.println("Blinds down");
    servo.attach(SERVO_PIN);
    servo.write(100);
    delay(1600 * 4);
    servo.detach();    
  }
}

void setup()
{
  Serial.begin(9600);
  pinMode(SERVO_PIN, OUTPUT);
  Serial.print("Hello");
  irrecv.enableIRIn(); // Start the receiver
  pinMode(LED_PIN, OUTPUT);
}

void loop() {
  if (irrecv.decode(&results)) {
    Serial.println(results.value, HEX);

    // Strip the DEAD header
    switch (results.value - 3735879680) {
      // Get blind status
      case 1:
        // Stub
        break;
      // Blinds up
      case 2:
        // If blinds are open
        if (BLIND_STATUS == 0) {
          moveBlinds(0);
          BLIND_STATUS--;
        } else if (BLIND_STATUS == 1) {
        // If blinds are down
          moveBlinds(0);
          moveBlinds(0);
          BLIND_STATUS = -1;
        }
        break;
      // Blinds open
      case 3:
        // If blinds are up
        if (BLIND_STATUS == -1) {
          moveBlinds(1);
          BLIND_STATUS = 0;
        } else if (BLIND_STATUS == 1) {
        // Else if blinds are down
          moveBlinds(0);
          BLIND_STATUS = 0;
        }        
        break;
      // Blinds down
      case 4:
        // If blinds are open
        if (BLIND_STATUS == 0) {
          moveBlinds(1);
          BLIND_STATUS++;
        } else if (BLIND_STATUS == -1) {
        // Else if blinds are up
          moveBlinds(1);
          moveBlinds(1);
          BLIND_STATUS = 1;
        }
        break;
    }

    irrecv.resume(); // Receive the next value

  }
}
