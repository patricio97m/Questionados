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
            		contrasena varchar(32) not null                
);

-- Inserts de datos

insert into Usuario (nombre, apellido, fecha_nac, sexo, pais, ciudad, mail, usuario, contrasena)
values ("Juan Alberto","Dominguez", "1980", "Hombre", "Argentina", "Haedo", "JuanAlberto@hotmail.com", "admin", "admin");