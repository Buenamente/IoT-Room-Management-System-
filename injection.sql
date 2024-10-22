CREATE TABLE account (
    id INT AUTO_INCREMENT PRIMARY KEY,
    FullName VARCHAR(255) NOT NULL,
    Registered DATETIME DEFAULT CURRENT_TIMESTAMP,
    Username VARCHAR(100) NOT NULL UNIQUE,
    Birthday DATE,
    Address VARCHAR(255),
    ContactNumber VARCHAR(20),
    Email VARCHAR(100),
    Role VARCHAR(50),
    Password VARCHAR(255),
    ProfilePic VARCHAR(255)
);

CREATE TABLE access_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rfid VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    access_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    role text(50) NOT NULL
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rfid VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    birthday VARCHAR(255) NOT NULL,
    address	 VARCHAR(255) NOT NULL,
    pin	INT(255) NOT NULL,
    role text(50) NOT NULL
);

CREATE TABLE rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    location VARCHAR(255) NOT NULL,
    status VARCHAR(50) DEFAULT 'Available',
    occupied_by VARCHAR(255) DEFAULT 'Not Assigned',
    role VARCHAR(50) DEFAULT 'Not Assigned'
);

CREATE TABLE `notification` (
  `Times_Attempt` int(11) NOT NULL,
  `RFID_Tag_Pin` varchar(50) NOT NULL,
  `Date` date NOT NULL,
  `Time` time NOT NULL DEFAULT current_timestamp(),
  `Location` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `activities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `RFID_Tag` varchar(50) NOT NULL,
  `Time_In` datetime NOT NULL,
  `Time_Out` datetime DEFAULT NULL,
  `Status` varchar(50) NOT NULL,
  `Location` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
