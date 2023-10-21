drop database if exists entrega1;
create database if not exists entrega1;
use entrega1;

create table Usuario(
                        idUsuario int auto_increment not null primary key,
                        nombre varchar(32) not null,
                        apellido varchar(32) not null,
                        fecha_nac int not null,
                        sexo varchar(15) not null,
                        pais varchar(32) not null,
                        ciudad varchar(32) not null,
                        mail varchar(50) not null,
                        usuario varchar(25) not null unique,
                        contrasena varchar(32) not null,
                        fotoPerfil varchar(60)
);
CREATE TABLE Pregunta (
                          idPregunta INT AUTO_INCREMENT PRIMARY KEY,
                          pregunta VARCHAR(255),
                          categoria VARCHAR(15)
);
CREATE TABLE Respuesta (
                           idRespuesta INT AUTO_INCREMENT PRIMARY KEY,
                           idPregunta INT,
                           respuesta VARCHAR(255),
                           esCorrecta BOOLEAN,
                           FOREIGN KEY (idPregunta) REFERENCES Pregunta(idPregunta)
);
CREATE TABLE Partida (
                         id INT AUTO_INCREMENT PRIMARY KEY,
                         puntaje_obtenido INT,
                         fecha_partida TIMESTAMP,
                         idUsuario INT,
                         FOREIGN KEY (idUsuario) REFERENCES Usuario(idUsuario)
);

-- Inserts de datos

INSERT INTO Usuario (nombre, apellido, fecha_nac, sexo, pais, ciudad, mail, usuario, contrasena, fotoPerfil)
VALUES ("Juan Alberto","Dominguez", "1980", "Hombre", "Argentina", "Haedo", "JuanAlberto@hotmail.com", "admin", "admin", "../public/fotosPerfil/1000.jpg"),
       ("Norville","Rogers", "1980", "Hombre", "Estados Unidos", "California", "shaggy@hotmail.com", "shaggy_08", "shaggy_08", "../public/fotosPerfil/1001.jpg"),
       ("Bruce","Wayne", "1975", "Hombre", "Estados Unidos", "Gotham city", "batman@hotmail.com", "batman.24", "batman.24", "../public/fotosPerfil/1002.jpg");

INSERT INTO Partida (puntaje_obtenido, fecha_partida, idusuario)
VALUES (7, NOW(), 1), (17, NOW(),1), (7, NOW(), 1), (17, NOW(),1), (8, NOW(), 2), (10, NOW(), 2), (10, NOW(), 2), (2, NOW(), 3), (0, NOW(), 2);

INSERT INTO Pregunta (pregunta, categoria) VALUES
                                               ('¿Cuál es la capital de Francia?', 'Geografía'),
                                               ('¿Quién escribió Romeo y Julieta?', 'Arte'),
                                               ('¿Cuál es el símbolo químico del oxígeno?', 'Ciencia'),
                                               ('¿Cuál es el deporte más popular en Brasil?', 'Deporte'),
                                               ('¿Quién es el actor principal de la película "Titanic"?', 'Entretenimiento'),
                                               ('¿En qué año comenzó la Primera Guerra Mundial?', 'Historia'),
                                               ('¿Cuál es el río más largo del mundo?', 'Geografía'),
                                               ('¿En qué año se firmó la Declaración de Independencia de los Estados Unidos?', 'Historia'),
                                               ('¿Quién pintó la Mona Lisa?', 'Arte'),
                                               ('¿Cuál es el planeta más cercano al Sol?', 'Ciencia'),
                                               ('¿Quién ganó el Mundial de Fútbol en 2022?', 'Deporte'),
                                               ('¿Cuál es la película más taquillera de todos los tiempos?', 'Entretenimiento');

INSERT INTO Respuesta (idPregunta, respuesta, esCorrecta) VALUES
                                            (1, 'París', 1), (1, 'Londres', 0), (1, 'Lisboa', 0),(1, 'Madrid', 0),
                                            (2, 'William Shakespeare', 1),(2, 'Charles Dickens', 0),(2, 'Guy Fawkes', 0),(2, 'Jane Austen', 0),
                                            (3, 'O', 1),(3, 'H2O', 0),(3, 'H', 0),(3, 'O2', 0),
                                            (4, 'Fútbol', 1),(4, 'Baloncesto', 0),(4, 'Voleibol', 0),(4, 'Tenis', 0),
                                            (5, 'Leonardo DiCaprio', 1),(5, 'Brad Pitt', 0),(5, 'Tom Hanks', 0),(5, 'Will Smith', 0),
                                            (6, '1914', 1),(6, '1939', 0),(6, '1945', 0),(6, '1918', 0),
                                            (7, 'El río Amazonas', 1),(7, 'El río Nilo', 0),(7, 'El río Misisipi', 0),(7, 'El río Yangtsé', 0),
                                            (8, '1776', 1),(8, '1789', 0),(8, '1812', 0),(8, '1865', 0),
                                            (9, 'Leonardo da Vinci', 1),(9, 'Vincent van Gogh', 0),(9, 'Pablo Picasso', 0),(9, 'Miguel Ángel', 0),
                                            (10, 'Mercurio', 1),(10, 'Venus', 0),(10, 'Marte', 0),(10, 'Júpiter', 0),
                                            (11, 'Francia', 0),(11, 'Brasil', 0),(11, 'Alemania', 0),(11, 'Argentina', 1),
                                            (12, 'Avengers: Endgame', 1),(12, 'Avatar', 0),(12, 'Titanic', 0),(12, 'Star Wars: El despertar de la Fuerza', 0);

