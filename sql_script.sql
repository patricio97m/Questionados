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

-- Inserts de datos

insert into Usuario (nombre, apellido, fecha_nac, sexo, pais, ciudad, mail, usuario, contrasena, fotoPerfil)
values ("Juan Alberto","Dominguez", "1980", "Hombre", "Argentina", "Haedo", "JuanAlberto@hotmail.com", "admin", "admin", "../public/fotosPerfil/1.jpg");

INSERT INTO Pregunta (pregunta, categoria) VALUES
                                               ('¿Cuál es la capital de Francia?', 'Geografía'),
                                               ('¿Quién escribió Romeo y Julieta?', 'Arte'),
                                               ('¿Cuál es el símbolo químico del oxígeno?', 'Ciencia');

INSERT INTO Respuesta (idPregunta, respuesta, esCorrecta) VALUES
                                                              (1, 'París', 1),
                                                              (1, 'Londres', 0),
                                                              (1, 'Lisboa', 0),
                                                              (1, 'Madrid', 0);

INSERT INTO Respuesta (idPregunta, respuesta, esCorrecta) VALUES
                                                              (2, 'William Shakespeare', 1),
                                                              (2, 'Charles Dickens', 0),
                                                              (2, 'Guy Fawkes', 0),
                                                              (2, 'Jane Austen', 0);

INSERT INTO Respuesta (idPregunta, respuesta, esCorrecta) VALUES
                                                              (3, 'O', 0),
                                                              (3, 'H2O', 0),
                                                              (3, 'H', 0),
                                                              (3, 'O2', 1);