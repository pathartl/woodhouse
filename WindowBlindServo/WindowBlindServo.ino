// WindowBlindServo.ino

#include <IRremote.h>
#include <Servo.h> 

// Pin definitions
int RECV_PIN = 4;
int SERVO_PIN = 2;
int LED_PIN = 3;
int POT_PIN = 0;

int BLIND_STATUS = 1; // Deprecated

int numReadings = 10;

// Pot limits
int POT_MAX = 930;
int POT_MID = 490;
int POT_MIN = 20;

Servo servo;

IRrecv irrecv(RECV_PIN);

decode_results results;

// Reads the potentiometer and smooths it out by doing an average
int readPot() {
	int total;

	// Count to numReading and add the potentiometer valu to the total each time
	for (int i = 0; i < numReadings; i++) {
		total += analogRead(POT_PIN);
	}

	return total / numReadings;

}

void positionBlinds(int pos) {
	servo.attach(SERVO_PIN);
	switch (pos) {
		case -1:
			servo.write(80);
			while (readPot() < POT_MAX) {
				delay(100);
			}
			break;
		case 0:
			// A little extra logic for opening because we have to
			// check what position the blinds are in.

			// We need to rotate the blinds in the correct direction
			// Less than the mid should rotate up
			if (readPot() < (POT_MID - 20)) {
				servo.write(80);
			}

			// More than the mid should rotate down

			if (readPot() > (POT_MID + 20)) {
				servo.write(100);
			}

			while ( (readPot() > (POT_MID + 20)) || (readPot() < (POT_MID - 20)) ) {
				delay(100);
			}
			break;
		case 1:
			servo.write(100);
			while (readPot() > POT_MIN) {
				delay(100);
			}
			break;
	}
	servo.detach();
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
        positionBlinds(-1);
        break;
      // Blinds open
      case 3:
        positionBlinds(0);       
        break;
      // Blinds down
      case 4:
        positionBlinds(1);
        break;
    }

    irrecv.resume(); // Receive the next value

  }
}
