#include <stdio.h>
#include <wiringPi.h>
#include <unistd.h>
#include <stdlib.h>
#include <stdarg.h>
#include <string.h>

int lights[] = {8,9,15,16,7,99};

int status = NULL;

const char * onFile = "/etc/lights/1";
const char * offFile = "/etc/lights/0";

int readStatus();

int main (int argc, char **argv)
{
  printf ("Raspberry Pi Automated Light Control\n");

  // Make sure we want to turn off a light
  if (argv[1] == NULL) {
    printf("You didn't type what light you want to toggle! Exiting!\n");
    exit(1);
  }

  // Let's set up wiringPi and some pins

  if (wiringPiSetup() == -1) return 1;

  // Do some file functions so we can keep track of the lights
  status = readStatus();
  //printf("%d\n", status);

  int i = 0;
  // Set our light pins as output
  for (i = 0; i < sizeof(lights); i++) {
    pinMode(lights[i], OUTPUT);
  }

  // If we want to toggle
  if (strcmp(argv[1], "toggle") == 0) {

    if (argc < 4) {

      printf("Toggling light %s\n", argv[2]);

    } else { // Multiple lights
      // Loop through each argument after toggle
      int j = 0;
      for (j = 0; j < argc - 2; j++) {

        // Typecast our argument to an int
        int light = (int)(argv[j + 2][0] - '0');

        // Check to see if our light exists based on the size of the lights[] array
        if (light > (sizeof(lights) / 4)) {
          printf("Cannot toggle light %d as it does not exist.\n", light);
        } else {
          // Light exists so we can toggle it!
          printf("Toggling light %d\n", light);
          flipLight(lights[light - 1]);
        }
      }
    }

  } else if (strcmp(argv[1], "all") == 0) {

    toggleAllLights();

  } else if (strcmp(argv[1], "on") == 0) {

    if (status == 0) {
      // Lights are currently off
      printf("Turning the lights on!\n");
      toggleAllLights();

    } else {
      printf("The lights are already on!\n");
    }

  } else if (strcmp(argv[1], "off") == 0) {

    if (status == 1) {
      // Lights are currently on
      printf("Turning the lights off!\n");
      toggleAllLights();

    } else {
      printf("The lights are already off!\n");
    }

  } else {
    printf("Invalid argument! You must choose either \"toggle [pin]\" or \"all\"\n");
  }

  return 0;
}

void flipLight(int pin) {
  digitalWrite(pin, 1);
  delay(140);
  digitalWrite(pin, 0);
  delay(60);
}

void toggleAllLights() {
  if (status == NULL) status = readStatus();
  if (status == 0) {
  	// Lights are currently off
    FILE *fp = fopen(onFile, "ab+");
    // Remove off file
    remove(offFile);
  } else if (status == 1) {
  	// Lights are currently on
    FILE *fp = fopen(offFile, "ab+");
    // Delete on file
    remove(onFile);
  }
  printf("Toggling all lights!\n");
  int j = 0;
  for (j = 0; j < (sizeof(lights) / 4); j++) {
    flipLight(lights[j]);
  }
}

int readStatus() {
  int out = 0;

  // If our on file is found
  if (access(onFile, F_OK) != -1) {
  	out = 1;

  // Else if our off file is found
  } else if (access(offFile, F_OK) != -1) {
  	out = 0;
  }
  return out;
}

/*char* concat(int count, ...)
{
    va_list ap;
    int i;

    // Find required length to store merged string
    int len = 1; // room for NULL
    va_start(ap, count);
    for(i=0 ; i<count ; i++)
        len += strlen(va_arg(ap, char*));
    va_end(ap);

    // Allocate memory to concat strings
    char *merged = calloc(sizeof(char),len);
    int null_pos = 0;

    // Actually concatenate strings
    va_start(ap, count);
    for(i=0 ; i<count ; i++)
    {
        char *s = va_arg(ap, char*);
        strcpy(merged+null_pos, s);
        null_pos += strlen(s);
    }
    va_end(ap);

    return merged;
}*/
