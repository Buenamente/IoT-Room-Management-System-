#include <WiFi.h>
#include <HTTPClient.h>
#include <SPI.h>
#include <MFRC522.h>
#include <Keypad.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>

// WiFi credentials
const char* ssid = "PLDTHOMEFIBR9ZWdG";
const char* password = "PLDTWIFIS2yHq";

// Server address
const char* serverName = "http://192.168.1.7/testing/verify_access.php"; // Update if necessary

// Room location
const char* roomLocation = "Room 1"; // Room name

// RFID setup
#define SS_PIN 10
#define RST_PIN 14
MFRC522 rfid(SS_PIN, RST_PIN);  // Create MFRC522 instance

// Keypad setup
const byte ROWS = 4;
const byte COLS = 3;
char keys[ROWS][COLS] = {
  {'1', '2', '3'},
  {'4', '5', '6'},
  {'7', '8', '9'},
  {'*', '0', '#'}
};
byte rowPins[ROWS] = {4, 5, 6, 7}; 
byte colPins[COLS] = {8, 3, 46};

Keypad keypad = Keypad(makeKeymap(keys), rowPins, colPins, ROWS, COLS);

// Relay setup
const int relayPin = 1;
const int relayOnDuration = 5000;

// Egress button setup
const int egressButtonPin = 40;  // Pin for the egress button

// LCD setup
LiquidCrystal_I2C lcd(0x27, 16, 2);

// Buffer for keypad input
String inputBuffer = "";
const int MAX_DIGITS = 10;

// Debouncing settings
#define DEBOUNCE_DELAY 50
unsigned long lastDebounceTime = 0;
char lastKey = NO_KEY;

// Check-in status
bool isCheckedIn = false;  // Track if the user is checked in

void setup() {
    Serial.begin(115200);
    SPI.begin(11, 12, 13);  
    rfid.PCD_Init();        

    pinMode(relayPin, OUTPUT);
    digitalWrite(relayPin, LOW); 

    pinMode(egressButtonPin, INPUT_PULLUP);  // Initialize egress button as input with pull-up

    Serial.println("RFID Reader Initialized");

    // Initialize WiFi
    Serial.println("Connecting to WiFi...");
    WiFi.begin(ssid, password);
    while (WiFi.status() != WL_CONNECTED) {
        delay(1000);
        Serial.print(".");
    }
    Serial.println("\nConnected to WiFi");
    Serial.print("ESP32 IP Address: ");
    Serial.println(WiFi.localIP());

    // Initialize the LCD
    Wire.begin(2, 42); 
    lcd.init();
    lcd.backlight();
    lcd.setCursor(0, 0);
    lcd.print("System Ready");
}

void loop() {
    if (!rfid.PICC_IsNewCardPresent() || !rfid.PICC_ReadCardSerial()) {
        checkKeypad();
        
        // Only check the egress button if the user is checked in
        if (isCheckedIn) {
            checkEgressButton();  // Check if the egress button is pressed
        }
        
    } else {
        String rfidTag = "";
        for (byte i = 0; i < rfid.uid.size; i++) {
            rfidTag += String(rfid.uid.uidByte[i] < 0x10 ? "0" : "");
            rfidTag += String(rfid.uid.uidByte[i], HEX);
            if (i < rfid.uid.size - 1) {
                rfidTag += "-";
            }
        }
        rfidTag.toUpperCase();

        lcd.clear();
        lcd.setCursor(0, 0);
        lcd.print("RFID: ");
        lcd.setCursor(0, 1);
        lcd.print(rfidTag);

        delay(2000);

        String response = sendToServer("rfid=" + rfidTag + "&location=" + String(roomLocation));

        if (response.indexOf("\"status\":\"success\"") != -1) {
            lcd.clear();
            if (isCheckedIn) {
                lcd.setCursor(0, 0);
                lcd.print("Check Out");
                isCheckedIn = false;  // User checked out
            } else {
                lcd.setCursor(0, 0);
                lcd.print("Check In");
                isCheckedIn = true;  // User checked in
            }
            unlockSolenoid();
        } else {
            lcd.clear();
            lcd.setCursor(0, 0);
            lcd.print("Access Denied");
        }
    }
}

void checkKeypad() {
    char key = keypad.getKey();
    if (key) {
        unsigned long currentTime = millis();
        if ((currentTime - lastDebounceTime) > DEBOUNCE_DELAY) {
            lastDebounceTime = currentTime;

            Serial.print("Key Pressed: ");
            Serial.println(key);

            if (key == '#') {
                if (inputBuffer.length() > 0) {
                    String data = "pin=" + inputBuffer + "&location=" + String(roomLocation);

                    Serial.print("Sending to server: ");
                    Serial.println(data);

                    String response = sendToServer(data);
                    
                    if (response.indexOf("\"status\":\"success\"") != -1) {
                        lcd.clear();
                        if (isCheckedIn) {
                            lcd.setCursor(0, 0);
                            lcd.print("Check Out");
                            isCheckedIn = false;  // User checked out
                        } else {
                            lcd.setCursor(0, 0);
                            lcd.print("Check In");
                            isCheckedIn = true;  // User checked in
                        }
                        unlockSolenoid();
                    } else {
                        lcd.clear();
                        lcd.setCursor(0, 0);
                        lcd.print("Access Denied");
                    }

                    inputBuffer = "";
                }
            } else if (key == '*') {
                inputBuffer = "";
                Serial.println("Buffer cleared");
                lcd.clear();
                lcd.setCursor(0, 0);
                lcd.print("Buffer Cleared");
            } else if (isDigit(key)) {
                if (isCheckedIn) { // Allow PIN entry only if the user is checked in
                    if (inputBuffer.length() < MAX_DIGITS) {
                        inputBuffer += key;
                        lcd.clear();
                        lcd.setCursor(0, 0);
                        lcd.print("PIN: ");
                        lcd.setCursor(0, 1);
                        lcd.print(inputBuffer);
                    }
                } else {
                    lcd.clear();
                    lcd.setCursor(0, 0);
                    lcd.print("Check In First");
                }
            }
        }
    }
}


void checkEgressButton() {
    if (isCheckedIn && digitalRead(egressButtonPin) == LOW) {  // Egress button pressed
        Serial.println("Egress button pressed");

        // Unlock the door immediately without sending any data to the server
        lcd.clear();
        lcd.setCursor(0, 0);
        lcd.print("Emergency Exit");
        unlockSolenoid();  // Trigger the relay to unlock the solenoid

        // Wait for a short time to debounce the button press
        delay(1000);
    } else {
        // Uncomment this line if you want to show a message on the LCD when the button is disabled
        // lcd.clear(); 
        // lcd.setCursor(0, 0); 
        // lcd.print("Button Disabled");
    }
}

String sendToServer(String data) {
    String response = "";

    if (WiFi.status() == WL_CONNECTED) {
        WiFiClient client;
        HTTPClient http;

        http.begin(client, serverName);
        http.addHeader("Content-Type", "application/x-www-form-urlencoded");

        String httpRequestData = data;
        Serial.print("Sending HTTP POST request: ");
        Serial.println(httpRequestData);

        int httpResponseCode = http.POST(httpRequestData);

        if (httpResponseCode > 0) {
            response = http.getString();
            Serial.print("Response code: ");
            Serial.println(httpResponseCode);
            Serial.print("Response: ");
            Serial.println(response);
        } else {
            Serial.print("Error code: ");
            Serial.println(httpResponseCode);
        }

        http.end();
    }

    return response;
}

void unlockSolenoid() {
    digitalWrite(relayPin, HIGH);
    delay(relayOnDuration);
    digitalWrite(relayPin, LOW);
}