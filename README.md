# <img src="public/logo.png" alt="logo de questionados" width="25"> Questionados


## Descripción del proyecto 
El proyecto consiste en el desarrollo de un juego de preguntas y respuestas en formato web, con una interfaz optimizada para su visualización en dispositivos móviles. Los usuarios podrán registrarse proporcionando información personal, y una vez validada su cuenta, podrán acceder a un lobby que les permitirá crear nuevas partidas, ver el ranking de puntajes acumulados, y revisar el historial de partidas jugadas.

## Objetivo
El objetivo principal del proyecto es ofrecer a los usuarios una experiencia interactiva a través de un juego de preguntas y respuestas. Se busca fomentar la participación y competencia entre los jugadores, así como permitir la contribución de estos a través de la creación de preguntas.

## Alcances
- Registro de usuarios con validación por correo electrónico.
- Lobby con información personalizada, creación de partidas y acceso al ranking.
- Juego de preguntas y respuestas con categorías y sistema de puntajes.
- Funcionalidad para reportar preguntas inválidas y crear nuevas preguntas.
- Roles de usuario: jugador, editor (gestión de preguntas) y administrador (reportes y estadísticas).
- Herramientas de administración para la revisión y aprobación de preguntas.

### Resultados Esperados

1. **Interfaz de Registro:**
    - Formulario de registro con campos para nombre completo, año de nacimiento, sexo, país, ciudad, correo electrónico, contraseña, repetición de contraseña y foto de perfil.
    - Validación de cuentas mediante correo electrónico.
    - API de Google Maps para que el usuario indique de manera interactiva la ciudad y el país al que pertenece

2. **Lobby:**
    - Visualización personalizada con nombre, puntaje y acceso a nuevas partidas.
    - Botones para crear partidas y acceder al ranking.
    - Historial de partidas jugadas con resultados.

3. **Juego:**
    - Preguntas aleatorias con opciones de respuesta (ABCD).
    - Categorías identificadas por colores.
    - Sistema de puntajes y retroalimentación al jugador.
    - Sistema dinámico de dificultad de preguntas según el ratio de preguntas correctas o incorrectas del usuario.

4. **Reportes y Estadísticas:**
    - Panel administrativo con información detallada.
    - Gráficos y tablas sobre cantidad de jugadores, partidas, preguntas, usuarios nuevos, etc.
    - Filtros temporales (día, semana, mes, año) y capacidad de impresión de tablas de datos y gráficos en formato PDF.

5. **Roles de Usuario:**
    - Usuarios editores pueden dar de alta, baja y modificar preguntas, así como revisar y aprobar preguntas reportadas o sugeridas por otros usuarios. Además de poder aportar nuevas categorías para las preguntas del juego. 
    - Usuario administrador con acceso a reportes y estadísticas.

## Tecnologías utilizadas
- Lenguaje backend utilizado: Php 7.1
- Servidor web utilizado: Apache server 2.4
- Editor de código fuente: PhpStorm
- Librería CSS: Bootstrap 5.3.2
- Base de datos: Mysql