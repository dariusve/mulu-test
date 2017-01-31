mulu-test
===========

Esta aplicación toma como entrada un archivo csv con este formato: 

```
name,zipcode
Michael,85273
James,85750
Brian,85751
Nicholas,85383
Jennifer,85716
Christopher,85014
Michael,85751
Patricia,95032
```

La solución contepla lo siguiente:
- leer el archivo suministrado 
- almacenar los registros
- obtener los zipcodes del agente1 y agente2 
- presentar un listado con los contactos mas cercanos a cada agente

Ahora bien, investigando sobre como obtener la ubicación geografica dado un zipcode y he encontrado que mediante varias API puedo obtenerla:

- OpenStreetMap
- Geonames
- Google Maps
- MapQuest
- ZipcodeAPI

Estas API ofrecen sus servicios gratuitamente, pero limitando la cantidad de consultas permitidas que van desde 1 solicitud por segundo hasta 30.000 solicitudes por día.

Se usa el API de [Geonames](http://www.geonames.org/export/), puesto he utilizando sus servicios previamente y poseo un api key que permite 30 mil consultas por día.

Una llamada tipica al API de Geonames:
[http://api.geonames.org/postalCodeSearchJSON?postalcode=33166&country=US&maxRows=10&username=demo](http://api.geonames.org/postalCodeSearchJSON?postalcode=33166&country=US&maxRows=10&username=demo)

El API retorna en formato JSON la información asociada al zipcode:

```
{"postalCodes":[{"adminCode2":"086","adminCode1":"FL","adminName2":"Miami-Dade County","lng":-80.292572,
"countryCode":"US","postalCode":"33166","adminName1":"Florida","placeName":"Miami","lat":25.830124}]}

```
Inicialmente se realizaban las consultas al API desde el Controlador, pero el tiempo de respuesta era muy alto, para minimizar esto, seutiliza llamadas Ajax al API de Geonames desde el lado del cliente.

La aplicación mustra el formulario para obtener los zip codes de los agentes una vez que se han obtenido las ubicaciones geograficas de los contactos.

La aplicación verifica que los zip codes para los agentes sean validos antes de realizar la agrupación de los contactos.

Una vez obtenida la información solo basta obtener las distancias entre cada uno de los contactos y los agentes.

El calculo se realiza mediante la [Formula Haversine](https://en.wikipedia.org/wiki/Haversine_formula), que es utilizada para determinar la distancia entre dos puntos dadas sus posicion geografica (latitud y longitud).

Esta solución está desarrolada con [CakePHP](https://book.cakephp.org/2.0/en/index.html) un Framework MVC de PHP, se utiliza adiciocalmente Jquery 2.2.4 para el manejo de las peticiones Ajax y la interaccion con el usuario, tambien se utiliza el Bootstrap 3.3.7 para el estilo visual de la UI
