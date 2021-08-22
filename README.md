<p align="center"><a href="" target="_blank"><img src="https://i.ibb.co/nj7xQB9/1cd956689d78bef96c51ed19a6be1af9.png" alt="1cd956689d78bef96c51ed19a6be1af9" border="0"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# API REST destinado a un CRM para la administración clientes
¡Bienvenido a la mejor la primera API que he desarrollado!

MonkeyShop es una API basada en [Laravel](https://laravel.com/), con plenas competencias de uso para la administración de datos de usuarios y clientes, de forma que cualquier desarrollador Front-end pueda desarrollar un CRM de manera segura y estable.

Tecnologias utilizadas:

- [PHP 7.4.16](https://www.php.net/)
- [Laravel](https://laravel.com/) version 8.55.0.
  - [Laravel Sanctum](https://laravel.com/docs/8.x/sanctum#introduction).
  - [Laravel Breeze](https://laravel.com/docs/8.x/starter-kits#laravel-breeze).
- [Composer](https://getcomposer.org/) version 2.0.7.



## Descripción

La API se ha desarrollado usando Laravel Sanctun como servicio de autentificación y tokenización. El servicio contiene un semillero para probar de forma sencilla su funcionamiento.

En siguientes apartados se detallará lo necesario para su instalación.

## Instalación

La máquina en la que se va a clonar el repositorio deberá tener instalado [Composer](https://getcomposer.org/).

Clonado el repositorio debe actualizarse Composer con el siguiente comando:
- ````$ composer update```` Actualizará las dependencias.
- Crear una base de datos para la administración de los mismos.
- A partir del archivo [.env.example]() debe crearse uno igual en contenido y nombrarlo [.env]().
- Introducir los datos pertinentes teniendo en cuenta:
  ````DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=apimonkeyshop
  DB_USERNAME=root
  DB_PASSWORD=
  ````
  #### Migración, deberá elegir el uso que se le va a da; con o sin datos.
- ````$ php artisan migrate```` Realizaremos la migración de tablas en la Base de Datos.

- ````$  php artisan migrate:fresh --seed```` Activaremos el semillero con 10 usuarios y 10 clientes.


-  ````$ php artisan serve```` Arranco el servidor Laravel según el entorno que estemos utilizando.

Ejecutados los pasos indicados ya debería estar activa...
### ¿Listo para comenzar? ¡Excelente!
Debemos de tener en cuenta varias cuestiones.
- El entorno de datos se ha realizado de la siguiente forma:
    - ###Usuarios:
      - Name.
      - Surname.
      - isAdmin. --bool-- nos asignará servicios de creación de usuarios nuevos, cambios de rol, creacion y actualización de clientes.
      - email. Se usará para la obtención de los TOkEN de autorización.
      - password. Cifrado en la Base de Datos con un Hash.
    - ###Clientes:
        - Name.
        - Surname.
        - cif. Este dato será único para cada cliente y se tomará como referencia.
        - image. Cada cliente tendrá un Avatar, se guardará en la carpeta [/public/storage/storage]() y se irán borrando según se vayan introduciendo en el sistema.
        - idUser. Llevará la id del usuario que ha creado el cliente.
        - mCIdUser. Obtendrá la id del último usuario que ha actualizado al cliente.


##Rutas
En este punto describiremos de forma sencilla las rutas que se han desarrollado para su uso.

Tendrán que tener que la información suministrada en las llamadas se enviará como parte del body de la siguiente forma. Adjuntando los datos requeridos según cada petición en un JSON.
````
{
"email": "email@gmail.com",
"password": "password"
}
````
- **[[post]]()** 
  http://www.monkeyShop.com/api/users/login. Requiere de email y password y devuelve el Token de la siguiente forma.

  ````
  {
  "access_token": "2|AZr5h99GJZgU0U6rxaDSOtnBYoIzJTthKi7lFI0F",
  "token_type": "Bearer"
  }
  ````
- **[[post]]()**
  http://127.0.0.1:8000/api/users/register. Requiere de name, surname, email, password, isAdmin.
    ````  
    {"name":"name","surname":"surname","email":"email","isAdmin":"1","updated_at":"2021-08-21T15:16:09.000000Z","created_at":"2021-08-21T15:16:09.000000Z","id":3}

    ````
- **[[post]]()** http://127.0.0.1:8000/api/clients/addClient Requiere el uso de por lo menos uno de los siguientes datos cif, nombre y apellidos.

    Nos retornará uno de estos resultados:
    - ````New registered customer```` 
    - ````Already registered customer````


- **[[get]]()** http://127.0.0.1:8000/api/clients/list
    Retorna la lista de clientes.

    ````
  [
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

- **[[get]]()** http://127.0.0.1:8000/api/clients/client?cif={cif}&name={name}&surname={surname} Requiere cif, name y surname, y devuelve el cliente según el dato aportado.
  ````
  [
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
 
- **[[post]]()** http://127.0.0.1:8000/api/clients/updateClient Requiere de name, surname y cif.
Hay que tener en cuenta que actualizaremos los datos del cliente con respecto al cif aportado y podemos cambiar los datos según se requiera uno o los dos.
  
    Podremos recibir varios resultados:

    - ````You do not have Administrator permissions````
    - ````Update Client CIF: cif new name: name and surname: surname```` 

        Este tipo de mensaje lo recibiremos dependiendo que dato hayamos enviado.


- **[[post]]()** http://127.0.0.1:8000/api/clients/updateImageClient Se subirá, actualizará y borrará imagen anterior del cliente con cif enviado. Se adjuntará a la petición el archivo *.jpg *.png con dimenciones :min_width=200,min_height=200.


- **[[post]]()** http://127.0.0.1:8000/api/users/changePassword  Cambio de password. Requerirá el email y newPassword además de estar autenticado.


- **[[post]]()** http://127.0.0.1:8000/api/users/changeRole Los datos a introducir son del idUser y el name para cambiar el rol y también hay que tener en cuenta que solo los usuarios administradores autentificados tienen poder de cambio.
    - ````User is now Administrator````
    - ````The user is no longer an administrator````
  

- **[[post]]()** http://127.0.0.1:8000/api/clients/deleteClient Borra el cliente con cif suministrado por un usuario autorizado y autenticado.


- **[[get]]()** http://127.0.0.1:8000/api/users/user Devuelve el usuario autenticado.

  ````
  {
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

Las autorizaciones de usuarios se realizan por **TOKEN Bearer**. 

En el proyecto se puede implementar un sistema **OAuth2** con la librería de Laravel **Passport** construido sobre el servidor *League OAuth2* generará las migraciones necesarias para la implementación de una forma sencilla el sistema.

Se han hecho pruebas de **Inyección SQL**, dando un resultado positivo.


## Documentacion

[Laravel documentation](https://laravel.com/docs/contributions).

## Codigo de Conducta

Se ha intentado crear un producto acorde a principios de aplicación [12factor](https://12factor.net).


## Licencia

El marco de Laravel es un software de código abierto con licencia bajo la [MIT license](https://opensource.org/licenses/MIT).
