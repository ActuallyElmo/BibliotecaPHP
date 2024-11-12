-- create the database
CREATE DATABASE biblioteca CHARACTER SET=utf8mb4;

-- create the user and grant privileges
CREATE USER 'mereuuser'@'localhost' IDENTIFIED BY 'mereupass';
GRANT ALL ON caietul_mereu.* TO 'mereuuser'@'localhost';


USE biblioteca;

CREATE TABLE Carte (
    id_carte INT AUTO_INCREMENT PRIMARY KEY,
    titlu VARCHAR(255) NOT NULL,
    autor VARCHAR(255) NOT NULL,
    gen VARCHAR(100),
    an_publicare YEAR,
    disponibilitate BOOLEAN DEFAULT TRUE
);

CREATE TABLE Utilizator (
    id_utilizator INT AUTO_INCREMENT PRIMARY KEY,
    nume VARCHAR(100),
    prenume VARCHAR(100),
    email VARCHAR(255) UNIQUE,
    status_membru ENUM('Standard', 'Premium') DEFAULT 'Standard',
    parola VARCHAR(255) NOT NULL
);

CREATE TABLE Imprumut (
    id_imprumut INT AUTO_INCREMENT PRIMARY KEY,
    id_carte INT,
    id_utilizator INT,
    data_imprumut DATE NOT NULL,
    data_returnare DATE,
    FOREIGN KEY (id_carte) REFERENCES Carte(id_carte),
    FOREIGN KEY (id_utilizator) REFERENCES Utilizator(id_utilizator)
);