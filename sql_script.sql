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
                        estaVerificado boolean not null,
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

CREATE TABLE RespuestasUsuario (
                                   idRespuestaUsuario INT AUTO_INCREMENT PRIMARY KEY,
                                   idUsuario INT,
                                   idPregunta INT,
                                   esCorrecta BOOLEAN,
                                   FOREIGN KEY (idUsuario) REFERENCES Usuario(idUsuario),
                                   FOREIGN KEY (idPregunta) REFERENCES Pregunta(idPregunta)
);

-- Inserts de datos

INSERT INTO Usuario (nombre, apellido, fecha_nac, sexo, pais, ciudad, mail, usuario, contrasena, estaVerificado, fotoPerfil)
VALUES ("Juan Alberto","Dominguez", "1980", "Hombre", "Argentina", "Haedo", "JuanAlberto@hotmail.com", "admin", "admin", true, "../public/fotosPerfil/1000.jpg"),
       ("Norville","Rogers", "1980", "Hombre", "Estados Unidos", "California", "shaggy@hotmail.com", "shaggy_08", "shaggy_08", true, "../public/fotosPerfil/1001.jpg"),
       ("Bruce","Wayne", "1975", "Hombre", "Estados Unidos", "Gotham city", "batman@hotmail.com", "batman.24", "batman.24", true, "../public/fotosPerfil/1002.jpg"),
       ("Carlos", "Rodriguez", "1990-01-01", "Hombre", "Argentina", "Castelar", "ejemplo1@example.com", "carlos10", "contrasena1", true, "../public/perfil_placeholder.png"),
       ("Marta", "Martinez", "1995-02-02", "Mujer", "Argentina", "Castelar", "ejemplo2@example.com", "marta.14", "contrasena2", true, "../public/perfil_placeholder.png"),
       ("Lucas", "Guzman", "1988-03-03", "Hombre", "Argentina", "Castelar", "ejemplo3@example.com", "lucas_53", "contrasena3", true, "../public/perfil_placeholder.png"),
       ("Pamela", "Fernandez", "1992-04-04", "Mujer", "Uruguay", "Montevideo", "ejemplo4@example.com", "pamela-22", "contrasena4", true, "../public/perfil_placeholder.png"),
       ("Nahuel", "Hernandez", "1987-05-05", "Hombre", "Brasil", "Sao Paulo", "ejemplo5@example.com", "nahuel77", "contrasena5", true, "../public/perfil_placeholder.png"),
       ("Nahir", "Nuñez", "1991-06-06", "Mujer", "Paraguay", "Ciudad del Este", "ejemplo6@example.com", "hanirN", "contrasena6", true, "../public/perfil_placeholder.png"),
       ("Pedro", "Lopez", "1989-07-07", "Hombre", "Chile", "Santiago de Chile", "ejemplo7@example.com", "pedro_lopez", "contrasena7", true, "../public/perfil_placeholder.png"),
       ("Rocío", "Cisneros", "1996-08-08", "Mujer", "Venezuela", "Caracas", "ejemplo8@example.com", "rocio2", "contrasena8", true, "../public/perfil_placeholder.png"),
       ("Nicolas", "Velíz", "1986-09-09", "Hombre", "Ecuador", "Lima", "ejemplo9@example.com", "nico44", "contrasena9", true, "../public/perfil_placeholder.png"),
       ("Brenda", "Muñoz", "1993-10-10", "Mujer", "Colombia", "Medellin", "ejemplo10@example.com", "brendaM", "contrasena10", true, "../public/perfil_placeholder.png");

INSERT INTO Partida (puntaje_obtenido, fecha_partida, idusuario)
VALUES (7, DATE_SUB(NOW(), INTERVAL 7 DAY), 1), (17, DATE_SUB(NOW(), INTERVAL 30 DAY), 1), (7, DATE_SUB(NOW(), INTERVAL 1 DAY), 1), (17, DATE_SUB(NOW(), INTERVAL 7 DAY), 1);

INSERT INTO Partida (puntaje_obtenido, fecha_partida, idusuario)
VALUES (8, DATE_SUB(NOW(), INTERVAL 7 DAY), 2), (10, DATE_SUB(NOW(), INTERVAL 30 DAY), 2), (10, DATE_SUB(NOW(), INTERVAL 1 DAY), 2);

INSERT INTO Partida (puntaje_obtenido, fecha_partida, idusuario)
VALUES (2, DATE_SUB(NOW(), INTERVAL 7 DAY), 3), (0, DATE_SUB(NOW(), INTERVAL 30 DAY), 3), (2, DATE_SUB(NOW(), INTERVAL 1 DAY), 3);

INSERT INTO Partida (puntaje_obtenido, fecha_partida, idusuario)
VALUES (12, DATE_SUB(NOW(), INTERVAL 7 DAY), 4), (20, NOW(), 4), (14, DATE_SUB(NOW(), INTERVAL 1 DAY), 4),
       (9, DATE_SUB(NOW(), INTERVAL 7 DAY), 5), (18, DATE_SUB(NOW(), INTERVAL 30 DAY), 5), (15, DATE_SUB(NOW(), INTERVAL 1 DAY), 5),
       (6, DATE_SUB(NOW(), INTERVAL 7 DAY), 6), (22, DATE_SUB(NOW(), INTERVAL 30 DAY), 6), (11, DATE_SUB(NOW(), INTERVAL 1 DAY), 6),
       (14, DATE_SUB(NOW(), INTERVAL 7 DAY), 7), (28, DATE_SUB(NOW(), INTERVAL 30 DAY), 7), (10, DATE_SUB(NOW(), INTERVAL 1 DAY), 7),
       (10, DATE_SUB(NOW(), INTERVAL 7 DAY), 8), (22, DATE_SUB(NOW(), INTERVAL 30 DAY), 8), (8, NOW(), 8),
       (8, DATE_SUB(NOW(), INTERVAL 7 DAY), 9), (24, DATE_SUB(NOW(), INTERVAL 30 DAY), 9), (12, DATE_SUB(NOW(), INTERVAL 1 DAY), 9),
       (15, DATE_SUB(NOW(), INTERVAL 7 DAY), 10), (32, DATE_SUB(NOW(), INTERVAL 30 DAY), 10), (13, DATE_SUB(NOW(), INTERVAL 1 DAY), 10);

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

INSERT INTO RespuestasUsuario (idUsuario, idPregunta, esCorrecta)
VALUES (1, 1, 1);

