# Store API

Backend desarrollado en Symfony (Arquitectura Hexagonal / DDD) servido con **FrankenPHP**, base de datos **MySQL**, colas asíncronas con **Redis Streams** y autenticación **JWT**.

## Requisitos previos

Antes de instalar el proyecto, asegúrate de tener instalado en tu máquina local:
* **Docker** y **Docker Compose**
* **Make** (herramienta de consola para ejecutar el Makefile)

## Pasos para la instalación inicial

Si te acabas de descargar el proyecto por primera vez, sigue estos sencillos pasos en tu terminal para levantar todo el entorno de forma automática:

### 1. Configurar las variables de entorno
Copia el archivo de configuración por defecto para crear tu entorno local:
```bash
cp .env .env.local
```

### 2. Ejecutar la instalación automática
Lanza el comando maestro de automatización. Este comando detendrá contenedores previos, compilará la imagen de FrankenPHP, descargará MySQL y Redis, instalará las dependencias de Composer, limpiará la caché y cargará las fixtures:
```bash
make install
```

### 3. Generar las llaves criptográficas para el Login JWT
Como las llaves SSH (`.pem`) no se suben al repositorio de Git por motivos de seguridad, debes generarlas en tu contenedor local ejecutando el siguiente comando:
```bash
docker compose exec store_api bin/console lexik:jwt:generate-keypair
```

¡Listo! El servidor web estará corriendo y escuchando peticiones en:
**`http://localhost:8080`**

---

## Comandos útiles en el día a día

El proyecto incluye un `Makefile` con alias rápidos para facilitarte el desarrollo:

* `make up`: Enciende los contenedores en segundo plano.
* `make down`: Apaga los contenedores y libera los recursos.
* `make bash`: Te mete dentro de la terminal interactiva de la API de Symfony.
* `make server-logs`: Muestra los logs en tiempo real de Symfony, MySQL y Redis.
* `make migration-create`: Genera un archivo de migración en blanco (escribe el SQL manual en `up()`).
* `make db-migrate`: Ejecuta únicamente las migraciones que tengas pendientes en tu MySQL.
* `make db-fresh`: Borra por completo tu base de datos local y la vuelve a crear limpia aplicando las migraciones y las fixtures desde cero.
