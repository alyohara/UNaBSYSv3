# UNaBSYSV3

# Sistema de Gestión Docente - UNaB

Este proyecto es un sistema de gestión docente desarrollado para la Universidad Nacional de Burlanda (UNaB). El sistema permite la administración de docentes, usuarios, materias, carreras, departamentos, y la gestión de cargos docentes. Está construido utilizando Laravel 10 con Blade para las vistas y MariaDB como base de datos.

## Características

- **Gestión de Usuarios**:
  - Altas, bajas y modificaciones de diferentes tipos de usuarios: administradores, personal administrativo (nivel 1 y 2), coordinadores (de materias, carreras y departamentos), y bedeles.
- **Gestión de Docentes**:
  - Registro de docentes.
  - Asignación y renovación de cargos docentes.
- **Gestión Académica**:
  - Alta, baja y modificación de materias, carreras y departamentos.
  - Asignación de coordinadores a materias, carreras y departamentos.
- **Roles y Permisos**:
  - Control de acceso basado en roles con permisos específicos para cada tipo de usuario.

## Requisitos del Sistema

- **Servidor Web**: Apache/Nginx
- **PHP**: Versión 8.1 o superior
- **Composer**
- **MariaDB**: Versión 10.4 o superior

## Instalación

### Clonar el Repositorio

```bash
git clone https://github.com//alyohara/UNaBSYSv3.git
cd UNaBSYSv3
```

### Configuración del Entorno

#### Renombra el archivo .env.example a .env y configura tus credenciales de base de datos y otros parámetros necesarios:

```bash
cp .env.example .env
```

#### Edita el archivo .env con tus datos de configuración:

```bash

APP_NAME="Sistema Gestión Docente"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nombre_base_datos
DB_USERNAME=nombre_usuario
DB_PASSWORD=contraseña
```
#### Instalar Dependencias

```bash
composer install
```

#### Generar la Clave de la Aplicación

```bash
php artisan key:generate
```
### Migrar la Base de Datos

```bash
php artisan migrate
```

#### Poblar la Base de Datos (opcional)

Para cargar datos iniciales como usuarios de ejemplo:

```bash
php artisan db:seed
```


### Iniciar el Servidor de Desarrollo

```bash
php artisan serve
```


## Uso

Una vez que el servidor esté funcionando, accede al sistema a través de http://localhost:8000 o la URL configurada en tu entorno.

## Roles de Usuario

Dependiendo del rol de usuario, se tendrá acceso a diferentes secciones y funcionalidades del sistema:

    - Administrador: Gestión completa de usuarios, docentes, materias, carreras y departamentos.
    - Administrativo:
        - Nivel 2: Acceso limitado a ciertas funcionalidades administrativas.
        - Nivel 1: Mayor acceso que Nivel 2, con permisos adicionales.
    - Coordinadores: Gestión y coordinación de materias, carreras y departamentos específicos; junto a la desiganción y renovación de cargos.
    - Bedeles: Gestión de la carga horaria y asistencia de los docentes.


## Licencia

Este proyecto está licenciado bajo la MIT License.

## Autores

    - Diego Agustín Ambrossio - diego.ambrossio@unab.edu.ar
    - Angel Leonardo Bianco - angel.bianco@unab.edu.ar
