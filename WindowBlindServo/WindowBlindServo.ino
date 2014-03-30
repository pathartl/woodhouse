// WindowBlindServo.ino

#include <IRremote.h>
#include <Servo.h> 

// Pin definitions
int RECV_PIN = 4;
int SERVO_PIN = 2;
int LED_PIN = 3;
int POT_PIN = 0;
int BLINK_LED_PIN = 13;
int PHOTO_PIN = 1;

int BLIND_STATUS = 1; // Deprecated

int numReadings = 10;

// Pot limits
int POT_MAX = 930;
int POT_MIN = 20;
int POT_MID = (POT_MAX - POT_MIN) / 2;
int POT_DEAD = 20; // Deadzone
int POT;

Servo servo;

IRrecv irrecv(RECV_PIN);
IRsend irsend;

decode_results results;

boolean enableRemoteControlMode = false;

long previousMillis = 0;
long enableStartMillis = 0;

int BLINK_STATE = LOW;

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
			if (readPot() < (POT_MID - POT_DEAD)) {
				servo.write(80);
			}

			// More than the mid should rotate down

			if (readPot() > (POT_MID + POT_DEAD)) {
				servo.write(100);
			}

			while ( (readPot() > (POT_MID + POT_DEAD)) || (readPot() < (POT_MID - POT_DEAD)) ) {
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

    switch (results.value) {
      // Get blind status
      case 3735879681:

      	delay(10000);
      	POT = readPot();

      	if (POT > POT_MAX) {
      		Serial.println("Sending status up");
      		irsend.sendNEC(0xDEADF000, 32);
      	} else if (POT < (POT_MID + POT_DEAD) && POT > (POT_MID - POT_DEAD)) {
      		Serial.println("Sending status open");
      		irsend.sendNEC(0xDEADF001, 32);
      	} else if (POT < POT_MIN) {
      		Serial.println("Sending status down");
      		irsend.sendNEC(0xDEADF002, 32);
      	}
  
        break;
      // Blinds up
      case 3735879682:
        positionBlinds(-1);
        break;
      // Blinds open
      case 3735879683:
        positionBlinds(0);       
        break;
      // Blinds down
      case 3735879684:
        positionBlinds(1);
        break;

      //
      // IR Remote control mode
      //

      // Enable remote control mode
      case 1832551305:

    	if (enableRemoteControlMode) {
    		enableRemoteControlMode = false;
    	} else {
	    	enableRemoteControlMode = true;
	    	enableStartMillis = millis();
	    }

	    break;
	  // Control mode up
	  case 3742155556:
	    if (enableRemoteControlMode) {
			positionBlinds(-1);
			enableRemoteControlMode = false;
	    }
	  	break;
	  // Control mode open
	  case 3509233553:
	    if (enableRemoteControlMode) {
			positionBlinds(0);
			enableRemoteControlMode = false;
	    }
	  	break;
	  // Control mode down
	  case 3559566408:
	    if (enableRemoteControlMode) {
			positionBlinds(1);
			enableRemoteControlMode = false;
	    }
	  	break;
    }

    irrecv.resume(); // Receive the next value

  }

  if (enableRemoteControlMode) {
  	unsigned long currentMillis = millis();
  	// Do some LED blinking to indicate the control status
  	if (currentMillis - previousMillis > 500) {
  		previousMillis = currentMillis;

  		if (BLINK_STATE == LOW) {
  		    BLINK_STATE = HIGH;
  		} else {
  			BLINK_STATE = LOW;
  		}

  		digitalWrite(BLINK_LED_PIN, BLINK_STATE);
  	}

  	// If it's been running for more than 6 seconds
  	if (currentMillis - enableStartMillis > 6000) {
  		// Stop
  		enableRemoteControlMode = false;
  	}

  } else {
  	digitalWrite(BLINK_LED_PIN, LOW);
  }

}
