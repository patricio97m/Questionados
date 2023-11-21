drop database if exists entrega1;
create database if not exists entrega1;
use entrega1;

create table Usuario(
                        idUsuario int auto_increment not null primary key,
                        nombre varchar(32) not null,
                        apellido varchar(32) not null,
                        fecha_nac date not null,
                        sexo varchar(20) not null,
                        pais varchar(32) not null,
                        ciudad varchar(32) not null,
                        mail varchar(50) not null,
                        usuario varchar(25) not null unique,
                        contrasena varchar(32) not null,
                        estaVerificado boolean not null,
                        codigoVerificacion int,
                        fotoPerfil varchar(60),
                        esEditor boolean,
                        esAdmin boolean,
                        fecha_alta date not null
);

CREATE TABLE Categoria (
                        idCategoria INT AUTO_INCREMENT PRIMARY KEY,
                        nombre varchar(32) not null,
                        color varchar(16) not null,
                        fecha TIMESTAMP,
                        idAutor INT
);

CREATE TABLE Pregunta (
                          idPregunta INT AUTO_INCREMENT PRIMARY KEY,
                          pregunta VARCHAR(255),
                          idCategoria INT not null,
                          dificultad VARCHAR(15),
                          fecha_pregunta TIMESTAMP,
                          idUsuario INT,
                          esVerificada BOOLEAN,
                          FOREIGN KEY (idUsuario) REFERENCES Usuario(idUsuario),
                          FOREIGN KEY (idCategoria) REFERENCES Categoria(idCategoria) ON DELETE CASCADE
);
CREATE TABLE Respuesta (
                           idRespuesta INT AUTO_INCREMENT PRIMARY KEY,
                           idPregunta INT,
                           respuesta VARCHAR(255),
                           esCorrecta BOOLEAN,
                           FOREIGN KEY (idPregunta) REFERENCES Pregunta(idPregunta)
                            ON DELETE CASCADE
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
                                    ON DELETE CASCADE
);

CREATE TABLE Reporte (
                                   idReporte INT AUTO_INCREMENT PRIMARY KEY,
                                   idUsuario INT,
                                   idPregunta INT,
                                   motivoReporte VARCHAR(100),
                                   fechaReporte TIMESTAMP,
                                   FOREIGN KEY (idUsuario) REFERENCES Usuario(idUsuario),
                                   FOREIGN KEY (idPregunta) REFERENCES Pregunta(idPregunta)
                                    ON DELETE CASCADE
);

-- Inserts de datos

INSERT INTO Usuario (nombre, apellido, fecha_nac, sexo, pais, ciudad, mail, usuario, contrasena, estaVerificado, fotoPerfil, esEditor, esAdmin, fecha_alta)
VALUES ("Juan Alberto","Dominguez", "1980-01-01", "Hombre", "Argentina", "Haedo", "JuanAlberto@hotmail.com", "admin", "admin", true, "../public/fotosPerfil/1000.jpg", true, true,"2023-11-20"),
       ("Shaggy","Rogers", "1980-01-01", "Hombre", "Estados Unidos", "California", "shaggy@hotmail.com", "shaggy_08", "shaggy_08", true, "../public/fotosPerfil/1001.jpg", true, false,"2023-11-20"),
       ("Bruce","Wayne", "1975-01-01", "Hombre", "Estados Unidos", "Gotham city", "batman@hotmail.com", "batman.24", "batman.24", true, "../public/fotosPerfil/1002.jpg", false, false,"2023-11-20"),
       ("Carlos", "Rodriguez", "1990-01-01", "Hombre", "Argentina", "Castelar", "ejemplo1@example.com", "carlos10", "contrasena1", true, "../public/perfil_placeholder.png", false, false,"2023-11-20"),
       ("Marta", "Martinez", "1995-02-02", "Mujer", "Argentina", "Castelar", "ejemplo2@example.com", "marta.14", "contrasena2", true, "../public/perfil_placeholder.png", false, false,"2023-11-01"),
       ("Lucas", "Guzman", "1988-03-03", "Hombre", "Argentina", "Castelar", "ejemplo3@example.com", "lucas_53", "contrasena3", true, "../public/perfil_placeholder.png", false, false,"2023-11-01"),
       ("Pamela", "Fernandez", "1992-04-04", "Mujer", "Uruguay", "Montevideo", "ejemplo4@example.com", "pamela-22", "contrasena4", true, "../public/perfil_placeholder.png", false, false,"2023-10-01"),
       ("Nahuel", "Hernandez", "1987-05-05", "Hombre", "Brasil", "Sao Paulo", "ejemplo5@example.com", "nahuel77", "contrasena5", true, "../public/perfil_placeholder.png", false, false,"2023-10-01"),
       ("Nahir", "Nuñez", "1991-06-06", "Mujer", "Paraguay", "Ciudad del Este", "ejemplo6@example.com", "hanirN", "contrasena6", true, "../public/perfil_placeholder.png", false, false,"2023-10-01"),
       ("Pedro", "Lopez", "1989-07-07", "Hombre", "Chile", "Santiago de Chile", "ejemplo7@example.com", "pedro_lopez", "contrasena7", true, "../public/perfil_placeholder.png", false, false,"2023-01-01"),
       ("Rocío", "Cisneros", "1996-08-08", "Mujer", "Venezuela", "Caracas", "ejemplo8@example.com", "rocio2", "contrasena8", true, "../public/perfil_placeholder.png", false, false,"2022-11-20"),
       ("Nicolas", "Velíz", "1986-09-09", "Hombre", "Ecuador", "Lima", "ejemplo9@example.com", "nico44", "contrasena9", true, "../public/perfil_placeholder.png", false, false,"2022-11-20"),
       ("Brenda", "Muñoz", "1993-10-10", "Mujer", "Colombia", "Medellin", "ejemplo10@example.com", "brendaM", "contrasena10", true, "../public/perfil_placeholder.png", false, false,"2022-11-20");

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

INSERT INTO Categoria (nombre, color, fecha, idAutor) VALUES 
                        ('Geografía', '#007BFF', NOW(), 1),
                        ('Ciencia', '#28A745', NOW(), 1),
                        ('Historia', '#FFC107', NOW(), 1),
                        ('Entretenimiento', '#17A2B8', NOW(), 1),
                        ('Arte', '#DC3545', NOW(), 1),
                        ('Deporte', '#6C757D', NOW(), 1);

INSERT INTO Pregunta (pregunta, idCategoria, dificultad, fecha_pregunta, idUsuario, esVerificada) VALUES
                                               ('¿Cuál es la capital de Francia?', 1 , 'facil', NOW(), 1, true),
                                               ('¿Quién escribió Romeo y Julieta?', 5, 'medio', NOW(), 1, true),
                                               ('¿Cuál es el símbolo químico del oxígeno?', 2, 'facil', NOW(), 1, true),
                                               ('¿Cuál es el deporte más popular en Brasil?', 6, 'facil', NOW(), 1, true),
                                               ('¿Quién es el actor principal de la película "Titanic"?', 4, 'medio', NOW(), 1, true),
                                               ('¿En qué año comenzó la Primera Guerra Mundial?', 3, 'dificil', NOW(), 1, true),
                                               ('¿Cuál es el río más largo del mundo?', 1, 'dificil', NOW(), 1, true),
                                               ('¿En qué año se firmó la Declaración de Independencia de los Estados Unidos?', 3, 'dificil', NOW(), 1, true),
                                               ('¿Quién pintó la Mona Lisa?', 5, 'medio', NOW(), 1, true),
                                               ('¿Cuál es el planeta más cercano al Sol?', 2, 'dificil', NOW(), 1, true),
                                               ('¿Quién ganó el Mundial de Fútbol en 2022?', 6, 'facil', NOW(), 1, true),
                                               ('¿Cuál es la película más taquillera de todos los tiempos?', 4, 'medio', NOW(), 1, true),
                                               ('¿Cuál es el océano más grande del mundo?', 1, 'facil', NOW(), 1, true),
                                               ('¿Quién fue el primer presidente de los Estados Unidos?', 3, 'facil', NOW(), 1, true),
                                               ('¿Cuál es el elemento más abundante en la corteza terrestre?', 2, 'medio', NOW(), 1, true),
                                               ('¿Cuál es el país más poblado del mundo?', 1, 'dificil', NOW(), 1, true),
                                               ('¿Quién escribió Don Quijote de la Mancha?', 5, 'dificil', NOW(), 1, true),
                                               ('¿Cuál es el deporte que se juega en el Super Bowl?', 6, 'medio', NOW(), 1, true),
                                               ('¿En qué año se fundó Google?', 2, 'medio', NOW(), 1, true),
                                               ('¿Cuál es el río que atraviesa El Cairo?', 1, 'facil', NOW(), 1, true),
                                               ('¿Cuál es la capital de Noruega?', 1, 'dificil', NOW(), 1, true),
                                               ('¿Quién pintó La Noche Estrellada?', 5, 'dificil', NOW(), 1, true),
                                               ('¿Cuál es la capital de Japón?', 1, 'facil', NOW(), 1, true),
                                               ('¿Cuál es el país conocido como la Tierra del Sol Naciente?', 1, 'facil', NOW(), 1, true),
                                               ('¿Quién escribió Hamlet?', 5, 'dificil', NOW(), 1, true),
                                               ('¿Cuál es el país donde se originó el tango?', 5, 'medio', NOW(), 1, true),
                                               ('¿Cuál es el deporte que se juega en Wimbledon?', 6, 'facil', NOW(), 1, true),
                                               ('¿En qué año se estrenó la película Star Wars: Episodio IV - Una Nueva Esperanza?', 4, 'dificil', NOW(), 1, true);

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
                                            (12, 'Avengers: Endgame', 1),(12, 'Avatar', 0),(12, 'Titanic', 0),(12, 'Star Wars: El despertar de la Fuerza', 0),
                                            (13, 'Océano Pacífico', 1), (13, 'Océano Atlántico', 0), (13, 'Océano Índico', 0), (13, 'Océano Antártico', 0),
                                            (14, 'George Washington', 1), (14, 'Thomas Jefferson', 0), (14, 'John Adams', 0), (14, 'Benjamin Franklin', 0),
                                            (15, 'Oxígeno', 1), (15, 'Hidrógeno', 0), (15, 'Nitrógeno', 0), (15, 'Carbono', 0),
                                            (16, 'China', 1), (16, 'India', 0), (16, 'Estados Unidos', 0), (16, 'Brasil', 0),
                                            (17, 'Miguel de Cervantes', 1), (17, 'Gustavo Adolfo Bécquer', 0), (17, 'Federico García Lorca', 0), (17, 'Pablo Neruda', 0),
                                            (18, 'Fútbol americano', 1), (18, 'Fútbol soccer', 0), (18, 'Baloncesto', 0), (18, 'Béisbol', 0),
                                            (19, '1998', 0), (19, '2004', 0), (19, '2000', 1), (19, '1996', 0),
                                            (20, 'Nilo', 1), (20, 'Amazonas', 0), (20, 'Misisipi', 0), (20, 'Yangtsé', 0),
                                            (21, 'Oslo', 1), (21, 'Estocolmo', 0), (21, 'Helsinki', 0), (21, 'Copenhague', 0),
                                            (22, 'Van Gogh', 1), (22, 'Picasso', 0), (22, 'Rembrandt', 0), (22, 'Leonardo da Vinci', 0),
                                            (23, 'Tokio', 1), (23, 'Pekín', 0), (23, 'Bangkok', 0), (23, 'Nueva Delhi', 0),
                                            (24, 'Japón', 1), (24, 'China', 0), (24, 'Corea del Sur', 0), (24, 'India', 0),
                                            (25, 'William Shakespeare', 1), (25, 'Charles Dickens', 0), (25, 'Jane Austen', 0), (25, 'Oscar Wilde', 0),
                                            (26, 'Argentina', 1), (26, 'Uruguay', 0), (26, 'España', 0), (26, 'México', 0),
                                            (27, 'Tenis', 1), (27, 'Críquet', 0), (27, 'Rugby', 0), (27, 'Golf', 0),
                                            (28, '1977', 0), (28, '1980', 0), (28, '1979', 1), (28, '1975', 0);

INSERT INTO RespuestasUsuario (idUsuario, idPregunta, esCorrecta)
VALUES (1, 1, 1), (4, 1, 0), (4, 2, 1), (4, 3, 0), (4, 4, 1), (4, 5, 0), (4, 6, 0), (4, 7, 1), (4, 8, 0), (4, 9, 1),
       (7, 10, 0), (6, 11, 0), (5, 12, 1), (4, 13, 0), (3, 14, 1), (2, 15, 0), (1, 16, 0), (8, 17, 1), (7, 18, 0), (13, 9, 1);

