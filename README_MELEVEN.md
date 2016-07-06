# meleven - Ihre dynamische Image-Cloud

## Zusammenfassung
Meleven ist ein restful Image-Cloud Service, der es Kunden erlaubt, Bilder dynamisch während des Abrufs zu rendern. Das Bildmanagement
geschieht über eine [REST](http://de.wikipedia.org/wiki/Representational_State_Transfer) Schnittstelle. Der Abruf der Bilder über eine
URL-basierte HTTP Schnittstelle.

*Diese Doku befindet sich, ebenso wie der Service selbst, zurzeit noch im Aufbau.*

## HTTP URL Schnittstelle
Bilder können über den Channel und ihre ID eindeutig identifiziert und angezeigt werden. Möchte man ein Derivat des Originalbildes erzeugen,
bzw. anzeigen lassen, muss das entsprechende Kommando in der URL übergeben werden. Dabei können unterschiedliche Kommandos beliebig hintereinander
verkettet werden. Der grundsätzliche Aufbau der URL sieht wie folgt aus:

`https://api.meleven.de/out/{channel}/{command}/{publicID}`

### channel
Der Channel wird von meleven festgelegt und zusammen mit den Zugangsdaten ausgehändigt.

### command
Das Kommando ist ein optionaler Parameter. Wird er weggelassen, wird das Originalbild zurückgegeben. Die unterschiedlichen Kommandos werden weiter
unten detailiert beschrieben.

### publicID
Die publicID wird beim upload des Originalbildes erzeugt und zurückgegeben. Sie muss gespeichert werden, damit das Bild später bearbeitet, oder 
abgerufen
werden kann.

## Kommandos
Kommandos bestehen immer aus einem Operator (was soll gemacht werden?) und Parametern (wie soll es gemacht werden?). Dabei werden die Bezeichner mit
einem Unterstrich vom Übergabewert getrennt (Bsp.: `o_resize` oder `w_100`). Ein Kommando besteht meistens aus mehreren Bezeichnern und Werten, die 
jeweils
mit einem Komma von den anderen Bezeichnern und Werten getrennt werden (Bsp.: `w_100,o_resize`). Die Reihenfolge der Bezeichner/Werte Paare ist dabei
egal, `w_100,o_resize` und `o_resize,w_100` ergeben das selbe Bild.
Es können beliebig viele Kommandos hintereinander verkettet werden. Sie werden der Reihenfolge nach von links nach rechts abgearbeitet und basieren 
immer auf dem
zuletzt erzeugten Derivat. Die Reihenfolge ist also entscheidend für das Endresultat. Um mehrere Kommandos hintereinander zu hängen, werden sie mit
einem doppelten Doppelpunkt voneinander getrennt (Bsp: `w_100,o_resize::w_80,h_80,o_crop`).

### resize
Resize verkleinert/vergrößert ein Bild auf eine vorgegebene Weite (w width) und Höhe (h height). Wird nur einer dieser beiden Werte übergeben, 
skaliert meleven
das Bild proportional, ansonsten wird es wie vorgegeben verkleinert.

### resize::limit
Limit erstellt das größtmögliche Bild mit den Proportionen des Originals innerhalb der vorgegebenen maximalen Weite (w width) und Höhe(h height).
Das ausgegebene Bild kann also kleiner sein als die gegebene Fläche.

### resize::fill
Fill erstellt das kleinstmögliche Bild mit den Proportionen des Originals, welches die gegebene Fläche aus Weite(w width) und Höhe(h height) 
komplett ausfüllt.
Das ausgegebene Bild ist also größer als die gegeben Fläche.

#### Parameter
* **o_resize = Operator**
* w = Weite in Pixeln (Optional, falls h gesetzt)
* h = Höhe in Pixeln (Optional, falls w gesetzt)
* m = Modus (Optional m_limit, m_fill)

#### Beispiele
* `https:://api.meleven.de/out/meinChannel/w_100,o_resize/bild.jpg`
* `https:://api.meleven.de/out/meinChannel/h_100,o_resize/bild.jpg`
* `https:://api.meleven.de/out/meinChannel/w_200,h_100,o_resize/bild.jpg`
* `https:://api.meleven.de/out/meinChannel/w_200,h_100,o_resize,m_limit/bild.jpg`

### crop
Crop beschneidet das Bild auf eine vorgegebene Weite (w widht) und Höhe (h height). Optional kann zusätzlich ein horizontaler (x) und/oder 
vertikaler (y)
Offset angegeben werden. Standardmäßig sind diese beiden Offset-Werte null. Der Punkt 0/0 befindet sich in der linken, oberen Ecke. Werden also 
keine Werte
für x und y angegeben, startet der Beschnitt von dort.

#### Parameter
* **o_crop = Operator**
* **w = Weite in Pixeln**
* **h = Höhe in Pixeln**
* x = Horizontaler Offset in Pixeln (optional, Standard: 0)
* y = Vertikaler Offset in Pixeln (optional, Standard: 0)

#### Beispiele
* `https:://api.meleven.de/out/meinChannel/w_100,h_100,o_crop/bild.jpg`
* `https:://api.meleven.de/out/meinChannel/w_100,h_100,x_20,y_20,o_crop/bild.jpg`
* `https:://api.meleven.de/out/meinChannel/w_100,h_100,x_20,o_crop/bild.jpg`

### flip
Flip spiegelt das Bild um seine vertikale (v) oder horizontale (h) Achse.

#### Parameter
* **o_flip = Operator**
* **a = Achse (v oder h)**

#### Beispiele
* `https:://api.meleven.de/out/meinChannel/a_h,o_flip/bild.jpg`
* `https:://api.meleven.de/out/meinChannel/a_v,o_flip/bild.jpg`

### colorize
Colorize legt eine transparente Farbebene über das Bild. Die Farbe kann dabei den Parametern in RGB (r rot, g grün, b blau) oder als text ( c 
red,blue,green)
übergeben werden. RGB Farbcodes lassen Farbwerte zwischen 0,0,0 (schwarz) und 255,255,255 (weiß) zu. Wird nur ein Farbwert (z.b r_120) übergeben, 
werden die
übrigen Farbwerte automatisch auf 0 gesetzt (RGB -> 120 0 0).

#### Parameter
* **o_colorize = Operator**
* c = Farbe (optional red, green oder blue)
* r = RGB red (0-255)
* g = RGB green (0-255)
* b = RGB blue (0-255)

#### Beispiele
* `http://meleven.local/out/meinChannel/o_colorize,c_blue/bild.jpg`
* `http://meleven.local/out/meinChannel/o_colorize,r_30/bild.jpg`

### grayscale
Grayscale erstellt eine Graustufen-Version des gegebenen Bildes. Ausser dem Operator (o grayscale) wird kein weiterer Parameter benötigt.

#### Parameter
* ** o_grayscale = Operator**

#### Beispiele
* `http://meleven.local/out/meinChannel/o_grayscale/bild.jpg`

### rotate
Rotate dreht das Bild um den gegebenen Winkel im Uhrzeigersinn. Negative Winkel sind ebenfalls zulässig. Wird kein Winkel angegeben dann beträgt 
dieser standardmäßig
den Wert 0. Es wird also nicht rotiert.

#### Parameter
* ** o_rotate = Operator**
* d = Winkel (degrees)

#### Beispiele
* `http://meleven.local/out/meinChannel/o_rotate,d_45/bild.jpg`
* `http://meleven.local/out/meinChannel/o_rotate,d_90/bild.jpg`
* `http://meleven.local/out/meinChannel/o_rotate,d_360/bild.jpg`

### quality
Quality reduziert die Größe eine jpeg Bildes ohne "sichtbaren" Qualitätsverlust. Es gibt drei mögliche Werte zur Auswahl: "best", "high" und 
"medium". Das Originalbild
wird nicht komprimiert. Es kann immer in unkomprimierter Version ausgeliefert. Alle Derivate werden auf jeden Fall komprimiert. Dabei gibt es drei 
mögliche Konfigurationspunkte.
Die globale Konfiguration im config/production.php File, die Channel Konfiguration im Array des jeweiligen Users in der user.db.php und über die URL 
Parameter beim Bildaufruf.
Die Konfigurationen überschreiben sich in der genannten Reihenfolge - URL überschreibt User-Konfig überschreibt Globale-Konfig.

#### Parameter
* **o_quality = Operator**
* q = quality (best | high | medium | default)

'default' sagt Meleven, dass es den Wert nehmen soll, der beim Kunden als default in der Konfiguration hinterlegt ist.

#### Beispiele
* `http://meleven.local/out/meinChannel/o_quality,q_high/bild.jpg`

### overlay
Overlay fügt zwei Images zu einem Zusammen. Dabei gibt es ein unteres(botimage) und ein oberes(topimage) Image. Wird nur eine Image-ID in den 
Parametern
übergeben, so wird automatisch ein zweites, weißes Bild mit den gegebenen Maßen (w width, h height) erstellt. In diesem Fall ist das in der url 
übermittelte Image
das topimage und wird auf das weiße Image gelegt.
Falls zwei Image-IDs in den Parametern übermittelt werden, so ist das Image aus der Parameter ID (id_imageid) das topimage und wird auf das zweite 
Image kopiert. In diesem Fall
werden keine Maße (w, h)benötigt.
Zudem kann ein Offset (x, y) angegeben werden um das topimage auf dem botimage zu verschieben.

#### Parameter
* **o_overlay = Operator**
* id = Overlay-ID Bsp: id_83.34.05.sample.jpg
* w = width
* h = height
* x = x-offset (default 0)
* y = y-offset (default 0)

#### Beispiele
* https://api.meleven.de/out/meinChannel/o_overlay,w_990,h_740/d6.30.cb.brown_sheep.jpg
* https://api.meleven.de/out/meinChannel/o_overlay,w_990,h_740,x_90,y_350/d6.30.cb.brown_sheep.jpg
* https://api.meleven.de/out/meinChannel/o_overlay,id_83.34.05.sample.jpg,x_51/d6.30.cb.brown_sheep.jpg

### fill
Fill ist ein alias. Es ist eine Verkettung von resize::fill und crop
Fill erstellt das kleinstmögliche Bild mit den Proportionen des Originals, welches die gegebene Fläche aus Weite(w width) und Höhe(h height) 
komplett ausfüllt. Dannach wird ein crop
auf die gegebene Fläche angewendet. Um den angezeigten Bereich zu ändern kann ein offset (x, y) in den Parametern übergeben werden.

#### Parameter
* **o_fill = Operator**
* w = width
* h = height
* x = x-offset (default 0)
* y = y-offset (default 0)

#### Beispiele
https://api.meleven.de/out/meinChannel/o_fill,w_100,h_150/83.34.05.sample.jpg
https://api.meleven.de/out/meinChannel/o_fill,w_250,h_150,y_50/83.34.05.sample.jpg
https://api.meleven.de/out/meinChannel/o_fill,w_100,h_150,x_20,y_50/83.34.05.sample.jpg

### pad
Pad ist ein Alias. Es ist eine Verkettung von resize::limit und overlay.
Pad erstellt das größtmögliche Bild mit den Proportionen des Originals innerhalb der vorgegebenen maximalen Weite (w width) und Höhe(h height) 
und füllt den restlichen teil der gegebenen
Fläche mit einer Hintergrundfarbe (c). Des Weiteren kann ein Offset angegeben werden um das Overlay-Image auf der farbigen Fläche zu verschieben

#### Parameter
* **o_pad = Operator **
* w = width
* h = height
* x = x-offset (default 0)
* y = y-offset (default 0)
* c = HTML Farbcode ohne Raute (z.B. "000000" für schwarz, "FFFFF" für weiß)

#### Beispiele
https://api.meleven.de/out/meinChannel/o_pad,w_100,h_150/83.34.05.sample.jpg
https://api.meleven.de/out/meinChannel/o_pad,w_250,h_150,y_50/83.34.05.sample.jpg
https://api.meleven.de/out/meinChannel/o_pad,w_100,h_150,x_20,y_50/83.34.05.sample.jpg
https://api.meleven.de/out/meinChannel/o_pad,w_100,h_150,x_20,y_50,c_FFFFFF/83.34.05.sample.jpg

## REST Schnittstelle
Die REST API bietet die Möglichkeit Bilder zu meleven zu übertragen, diese zu bearbeiten und zu löschen.
Mit der Schnittstelle ist es ebenfalls möglich sämtliche Informationen zu einem Bild abzufragen. Diese Abfragen können auch
gesammelt für meherer Bilder bzw. den kompletten Channel stattfinden. In Zukunft soll es zudem möglich sein, Abfragen zu
hinterlegten Tags abzurufen (Gib mir alle Bilder mit dem Tag 'blau' zum Beispiel). Die Schnittstelle sendet als Antwort
immer ein JSON Objekt.

Bei allen Anfragen an die REST Endpunkte muss immer der Username und das Passwort zur Authentifizierung übermittelt werden.
Alle Antworten erfolgen immer im JSON Format und enthalten im Header den entsprechenden Statuscode.

Alle angeführten Beispiele sind mit dem [Guzzle](https://guzzle.readthedocs.org/en/latest/) erzeugt. Dies ist rein exemplarisch
und es kann natürlich jeder beliebige HTTP Client zur Erzeugung genutzt werden.

### GET /image
Der GET Request auf den Endpunkt `image` bietet die Möglichkeit, alle bei meleven gespeicherten Informationen zu einem, oder mehreren Bildern
abzurufen. Dazu kann der Parameter `ids` übergeben werden. Dieser Parameter ist optional und kann eine, oder mehrere, kommaseperierte
Bild-IDs enthalten. Wird der Parameter nicht gesetzt, werden alle im Channel enthaltenen Bilder (nur die IDs) zurückgegeben.


#### Beispiel - Abruf mit einer ID
`$client  = new Client('https://api.meleven.de');`

`$request = $client->get('/image')->setAuth('username', 'password');`
`$request->getQuery()->set('ids', 'meineBildId');`

`$response = $request->send();`
`echo $response->getBody();`

**Example Response:**
[{"id":"meineBildId","type":"master_image","channel":{"name":"meinChannel"},"totalSize":212487,"derivates":{"original":{"transformations":[],"original":true,"md5sum":"4ef7684eb9dfa7fe68440df10b844e2a","width":1900,"height":1483,"mimeType":"image\/jpeg","path":"meinChannel\/4e\/f7\/68\/schuh1.jpg","size":212487,"createdAt":null}},"tags":["12312","rot","Frau"],"createdAt":"18-02-2014 
10:26:11"}]


#### Beispiel - Abruf mit mehreren IDs
`$client  = new Client('https://api.meleven.de');`

`$request = $client->get('/image')->setAuth('username', 'password');`
`$request->getQuery()->set('ids', 'meineBildId1,meineBildId2');`

`$response = $request->send();`
`echo $response->getBody();`

**Example Response:**
["meinChannel.54.28.44.sakko.gif","meinChannel.86.b7.d8.bee.jpg","meinChannel.93.5e.37.bee.jpg"]


#### Beispiel - Abruf des ganzen channels
`$client  = new Client('https://api.meleven.de');`

`$request = $client->get('/image')->setAuth('username', 'password');`

`$response = $request->send();`
`echo $response->getBody();`

**Example Response:**
[{"id":"meineBIldId1","type":"master_image","channel":{"name":"meinChannel"},"totalSize":230207,"derivates":{"original":{"transformations":[],"original":true,"md5sum":"415cb82885544c3706ab5cb33fabc8e2","width":1900,"height":1389,"mimeType":"image\/jpeg","path":"meinChannel\/41\/5c\/b8\/schuh2.jpg","size":230207,"createdAt":null}},"tags":{"artnum":"23423","Farbe":"blau"},"createdAt":"18-02-2014 
10:26:11"},{"id":"meineBIldId2","type":"master_image","channel":{"name":"meinChannel"},"totalSize":212487,"derivates":{"original":{"transformations":[],"original":true,"md5sum":"4ef7684eb9dfa7fe68440df10b844e2a","width":1900,"height":1483,"mimeType":"image\/jpeg","path":"meinChannel\/4e\/f7\/68\/schuh1.jpg","size":212487,"createdAt":null}},"tags":["12312","rot","Frau"],"createdAt":"18-02-2014 
10:26:11"},{"id":"meinChannel.c4.1f.6a.mtb.gif","type":"master_image","channel":{"name":"meinChannel"},"totalSize":67781,"derivates":{"original":{"transformations":[],"original":true,"md5sum":"c41f6a880975fd275189c9e69e98c2b0","width":1057,"height":1185,"mimeType":"image\/gif","path":"meinChannel\/c4\/1f\/6a\/mtb.gif","size":67781,"createdAt":null}},"tags":{"artnum":"2a542x3","Farbe":"blau"},"createdAt":"18-02-2014 
10:26:11"},{"id":"meinChannel.fa.93.42.bild mit k$omischem 
namen.png","type":"master_image","channel":{"name":"meinChannel"},"totalSize":749985,"derivates":{"original":{"transformations":[],"original":true,"md5sum":"fa9342a5c6d87c3f785256241d4ac1db","width":711,"height":474,"mimeType":"image\/png","path":"meinChannel\/fa\/93\/42\/bild 
mit k$omischem namen.png","size":749985,"createdAt":null}},"tags":{"artnum":"2a542x3","Farbe":"blau"},"createdAt":"18-02-2014 
10:26:11"},{"id":"meinChannel.fa.93.42.mtb.png","type":"master_image","channel":{"name":"meinChannel"},"totalSize":749985,"derivates":{"original":{"transformations":[],"original":true,"md5sum":"fa9342a5c6d87c3f785256241d4ac1db","width":711,"height":474,"mimeType":"image\/png","path":"meinChannel\/fa\/93\/42\/mtb.png","size":749985,"createdAt":null}},"tags":{"artnum":"25423","Farbe":"blau"},"createdAt":"18-02-2014 
10:26:11"}]


### POST /image
Der POST Request auf den Endpunkt `image` bietet die Möglichkeit, ein Bild zu meleven hochzuladen. Als Parameter werden `image`
und optional `tags` entgegengenommen. Das Plichtfeld `image` enthält das hochzuladene Bild, `tags` ist ein Key/Value Array mit Werten,
unter denen das Bild später selektiert werden können soll. Als Rückgabewert sendet die Funktion ein JSON Objekt mit allen relevanten
Daten zum Bild. Darunter auch die ID, unter der das Bild später wieder abgerufen werden kann.

#### Beispiel - Upload eine Bildes mit tags
`$request = $client->post('/image', array(), array(`
`    'tags'  => array('artnum' => '25423', 'Farbe' => 'blau'),`
`    'image' => '@files/mtb.png'`
`))->setAuth('username', 'password');`
`$response = $request->send();`

**Example Response:**
[{"id":"meineBildId","type":"master_image","channel":{"name":"meinChannel"},"totalSize":230207,"derivates":{"original":{"transformations":[],"original":true,"md5sum":"415cb82885544c3706ab5cb33fabc8e2","width":1900,"height":1389,"mimeType":"image\/jpeg","path":"meinChannel\/41\/5c\/b8\/schuh2.jpg","size":230207,"createdAt":null}},"tags":{"artnum":"23423","Farbe":"blau"},"createdAt":"18-02-2014 
10:26:11"}]

**Normalisierung**
Beim Upload werden die Bildnamen normalisiert. Das bedeutet, dass die Umlaute ss, ä, ü und ö zu ss, ae, ue und oe werden, Leerzeichen und alle 
sonstigen Sonderzeichen werden aus dem String entfernt. Ein Beispiel: steak_Hüfte!.png wird zu steakhuefte.png

### PUT /image
DER PUT Request auf den Endpunkt `image` ermöglicht Änderung an einem bestehenden Dokument. Zur Zeit wird nur das überschreiben der
tags erlaubt. Weitere Möglichkeiten folgen. Als Parameter werden `id` und optional `tags` mit den neuen Tags erwartet. Alle übergebenen
Werte überschreiben die vorhandenen Werte des geladenen Dokuments. Als Rückgabewert sendet die Funktion ein JSON Objekt mit allen
relevanten Daten zum Bild.

#### Beispiel - Upload eine Bildes mit tags
`$request = $client->put('/image', array(), array(`
`    'id' => 'meineBildId'`
`    'tags'  => array('artnum' => '25423', 'Farbe' => 'blau'),`
`))->setAuth('username', 'password');`
`$response = $request->send();`

**Example Response:**
[{"id":"meineBildId","type":"master_image","channel":{"name":"meinChannel"},"totalSize":230207,"derivates":{"original":{"transformations":[],"original":true,"md5sum":"415cb82885544c3706ab5cb33fabc8e2","width":1900,"height":1389,"mimeType":"image\/jpeg","path":"meinChannel\/41\/5c\/b8\/schuh2.jpg","size":230207,"createdAt":null}},"tags":{"artnum":"23423","Farbe":"blau"},"createdAt":"18-02-2014 
10:26:11"}]


### DELETE /image
Der DELETE Request auf den Endpunkt `image` ermöglicht das Löschen eines Bildes. Erwartet wird hier lediglich der Parameter `id`, um das
zu löschende Bild zu identifizieren. Der Rückgabewert bei erfolgreicher Löschung ist ein leeres JSON Objekt mit dem HTTP Code 200.

#### Beispiel - Upload eine Bildes mit tags
`$request = $client->delete('/image')->setAuth('username', 'password');`
`$request->getQuery()->set('id', 'meinBildId');`

`$response = $request->send();`
`echo $response->getBody();`

**Example Response:**
{}
