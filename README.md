<p align="center"><a href="" target="_blank"><img src="https://i.ibb.co/nj7xQB9/1cd956689d78bef96c51ed19a6be1af9.png" alt="1cd956689d78bef96c51ed19a6be1af9" border="0"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# API REST destinado a un CRM para la administración clientes
¡Bienvenido a la mejor la primera API he desarrollado!

MonkeyShop es una API basada en [Laravel](https://laravel.com/), con plenas competencias de uso para la administración de datos de usuarios y clientes, de forma que cualquier desarrollador Front-end pueda desarrollar un CRM de forma segura y estable.

Tecnologias utilizadas:

- [PHP 7.4.16](https://www.php.net/)
- [Laravel](https://laravel.com/) version 8.55.0.
  - [Laravel Sanctum](https://laravel.com/docs/8.x/sanctum#introduction).
  - [Laravel Breeze](https://laravel.com/docs/8.x/starter-kits#laravel-breeze).
- [Composer](https://getcomposer.org/) version 2.0.7.



## Descripción

La API se ha desarrollado usando Laravel Sanctun como servicio de autentificación y tokenización, el servicio contiene un semillero para probar de forma sencilla su funcionamiento.

En siguientes apartados se detallará lo necesario para su instalación.

## Instalación

En la máquina a clonar deberá tener instalado [Composer](https://getcomposer.org/).

Una vez clonado el repositorio se actualizará Composer con el siguiente comando:
- ````$ composer update```` Actualizará las dependencias.
- Crear una base de datos para la administración de los mismos.
- A partir del archivo [.env.example]() craremos uno igual en contenido y llamalo [.env]().
- Introducir los datos pertinentes teniendo en cuenta:
  ````DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=apimonkeyshop
  DB_USERNAME=root
  DB_PASSWORD=
  ````
  #### Migración elegiremos que vamos a usar con o sin datos.
- ````$ php artisan migrate```` Realizaremos la migración de tablas en la Base de Datos.

- ````$  php artisan migrate:fresh --seed```` Con ello activaremos el semillero con 10 usuarios y 10 clientes.

- 
-  ````$ php artisan serve```` Con ello activamos el servidor Laravel según el entorno que estemos utilizando.

Una vez hecho los anteriores pasos ya debería estar...
### ¿Listo para comenzar? ¡Excelente!
Debemos de tener en cuenta varias cuestiones.
- El entorno de datos se ha realizado de la siguiente forma:
    - **Usuarios:**
      - Name.
      - Surname.
      - isAdmin. --bool-- nos asignará servicios de creación de usuarios nuevos, cambios de rol, creacion y actualización de clientes.
      - email. Con el lo utilizaremos para la obtención de los TOkEN de autorización.
      - password. Cifrado en la Base de Datos con un Hash.
    - **Clientes:**
        - Name.
        - Surname.
        - cif. Este dato será único para cada cliente y se tomará como referencia.
        - image. Cada cliente tendrá un Avatar, se guardará en la carpeta [/public/storage/storage]() y se irán borrando según se vayan introduciendo en el sistema.
        - idUser. Llevará la id del usuario que ha creado el cliente.
        - mCIdUser. Obtendrá la id del último usuario que ha actualizado al cliente.

    
Aquí describiré las rutas que se han desarrollado para su uso.

- **[[post]]()** 
  http://www.monkeyShop.com/api/users/login?email={email}&password={password} Acceso al sistema devuelve el token.
  ````{
  "access_token": "2|AZr5h99GJZgU0U6rxaDSOtnBYoIzJTthKi7lFI0F",
  "token_type": "Bearer"
  }
  ````
- **[[post]]()**
  http://127.0.0.1:8000/api/users/register?name={name}&surname={surname}&email={email}&password={password}&isAdmin={true=1orfalse=0} Acceso al sistema devuelve el token.
  
    ````  
    {"name":"name","surname":"surname","email":"email","isAdmin":"1","updated_at":"2021-08-21T15:16:09.000000Z","created_at":"2021-08-21T15:16:09.000000Z","id":3}

    ````
- **[[post]]()** http://127.0.0.1:8000/api/clients/addClient?cif={cif}&name={name}&surname={surname}

    Nos retornará uno de estos resultados:
    - ````New registered customer```` 
    - ````Already registered customer````


- **[[get]]()** http://127.0.0.1:8000/api/clients/list
    Retorna la lista de clientes.

    ````[
    {
    "idClient": 1,
    "name": "Tomas",
    "surname": "surname",
    "cif": "fFNE1q6MEH",
    "image": null,
    "idUser": "3",
    "mCIdUser": "4",
    "created_at": null,
    "updated_at": null
    },
    {
    "idClient": 2,
    "name": "Jennifer",
    "surname": "surname",
    "cif": "NTsu2qO2Bg",
    "image": null,
    "idUser": "5",
    "mCIdUser": "7",
    "created_at": null,
    "updated_at": null
    },...
    ]
    ````

- **[[get]]()** http://127.0.0.1:8000/api/clients/client?cif={cif}&name={name}&surname={surname} Devuelve el cliente según el dato aportado.
  ````[
  {
  "idClient": 11,
  "name": "name",
  "surname": "surname",
  "cif": "cif",
  "image": "public/storage/09HKpiNoqaISPLA9JIqamxFZ3m1HITVupDhLVCmh.jpg",
  "idUser": "1",
  "mCIdUser": "1",
  "created_at": "2021-08-20 09:34:49",
  "updated_at": "2021-08-20 09:34:49"
  }
  ]
  ````
 
- **[[post]]()** http://127.0.0.1:8000/api/clients/updateClient?name={name}&surname={surname}&cif=12345
Actualizaremos los datos del cliente con cif determinado podemos cambia los datos según se requiera uno o los dos.
  
    Podremos recibir varios resultados:

    - ````You do not have Administrator permissions````
    - ````Update Client CIF: cif new name: name and surname: surname```` 

        Este tipo de mensaje lo recibiremos dependiendo que dato hayamos enviado.


- **[[post]]()** http://127.0.0.1:8000/api/clients/updateImageClient?cif=cif Se subirá, actualizara y borrará imagen anterior. Se adjuntará a la petición el archivo *.jpg *.png con dimenciones :min_width=200,min_height=200.


- **[[post]]()** http://127.0.0.1:8000/api/users/changePassword?email={email}&newPassword={newPassword} Cambio de password.


- **[[post]]()** http://127.0.0.1:8000/api/users/changeRole?id={id}&name={name} Los datos introducidos son del usuario a cambiar el role, hay que tener en cuenta que solo los usuarios administradores autentificados tienen poder de cambio.
    - ````User is now Administrator````
    - ````The user is no longer an administrator````
  

- **[[post]]()** http://127.0.0.1:8000/api/clients/deleteClient?cif={cif} Borra el cliente por un usuario autorizado y autenticado.


- **[[get]]()** http://127.0.0.1:8000/api/users/user Devuelve el usuario autenticado.

  ````{
  "id": 2,
  "name": "Jorge",
  "surname": "surname",
  "isAdmin": 0,
  "email": "Jorge@gmail.com",
  "email_verified_at": null,
  "created_at": null,
  "updated_at": null
  }
  ````


## Seguridad

Las autorizaciones de usuarios se realizan por **TOKEN Bearer**. En el proyecto se puede implementar un sistema **OAuth2** con la libreria de Laravel **Passport** construido sobre el servidor *League OAuth2* el cual generará las migraciones necesarias como la implementación de una forma sencilla el sistema.

Se han hecho pruebas de **Inyección SQL**, no han sido exhaustivas, pero han dado un resultado adecuado.


## Documentacion

[Laravel documentation](https://laravel.com/docs/contributions).

## Codigo de Conducta

Se ha intentado crear un producto acorde a principios de aplicación [12factor](https://12factor.net).


## Licencia

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
