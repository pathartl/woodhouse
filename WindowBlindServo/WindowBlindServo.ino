// WindowBlindServo.ino

#include <IRremote.h>
#include <Servo.h> 

int RECV_PIN = 3;
int SERVO_PIN = 2;
int LED_PIN = 13;

Servo servo;

IRrecv irrecv(RECV_PIN);

decode_results results;

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
        Serial.println("Blinds up");
        servo.attach(SERVO_PIN);
        servo.write(80);
        delay(1300 * 4);
        servo.detach();
        break;
      // Blinds open
      case 3:
        // Stub
        break;
      // Blinds down
      case 4:
        Serial.println("Blinds down");
        servo.attach(SERVO_PIN);
        servo.write(100);
        delay(1700 * 4);
        servo.detach();
        break;

    }

    irrecv.resume(); // Receive the next value

  }



}