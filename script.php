<?php
session_start();
$conn = mysqli_connect('localhost','root','','musica');
if($conn == false) die("errore con la connessione al db");

$ricerca = $_POST['input'];

$query = $conn->prepare("SELECT t.nome AS nome_traccia , a.genere AS genere, ar.nome AS nome_artista, a.nome AS nome_album, a.codice AS codice_album,
 CASE 
 WHEN t.nome = ? THEN 'match_traccia'
 WHEN a.nome = ? THEN 'match_album'
 WHEN ar.nome = ? THEN 'match_artista'
 WHEN a.genere = ? THEN 'match_genere'
 END AS match_trovato
    FROM traccia t, album a, artista ar  
    WHERE t.id_album = a.id_album AND a.id_artista = ar.id_artista 
    AND (a.nome = ? OR t.nome = ? OR ar.nome = ? OR a.genere = ?)");
$query->bind_param("ssssssss", $ricerca, $ricerca, $ricerca, $ricerca, $ricerca, $ricerca, $ricerca, $ricerca);
$query->execute();
 
$res = $query->get_result();


if(mysqli_num_rows($res) == 0 ){
    echo "Nessun risultato";

}else{
    $row = mysqli_fetch_assoc($res);
    if($row['match_trovato'] == 'match_album'){
    $codice = $row['codice_album'];
    echo "Risultati in base a: " . $row['nome_album'] . "<br>";
    ?>
    <iframe
  src="https://open.spotify.com/embed/album/<?php echo $codice ?>?utm_source=generator"
  width="600"
  height="600"
  frameborder="0"
  allow="encrypted-media">
  </iframe><br>
  <?php
    echo "Genere: " . $row['genere'];
    
}elseif($row['match_trovato'] == 'match_traccia'){
    echo "Risultati in base a: " . $row['nome_traccia'] . "<br>";
    echo "Artista: " . $row['nome_artista'] . "<br>";
    echo "Album: " . $row['nome_album'] . "<br>";
    echo "Genere: " . $row['genere'] . "<br>";

}elseif($row['match_trovato'] == 'match_artista'){
    $nome = $row['nome_artista'];
    echo "Risultati in base a: " . $row['nome_artista'] . "<br>";
    echo "Album dell'artista: " . "<br>";
    $query2 = "SELECT a.codice as codice_album
    FROM album a 
    JOIN artista ar ON a.id_artista = ar.id_artista 
    WHERE ar.nome = '$nome';";
    $result = mysqli_query($conn, $query2);
    $res = mysqli_fetch_all($result, MYSQLI_ASSOC);
    for($i = 0; $i < count($res); $i++){
    ?>
    <iframe
  src="https://open.spotify.com/embed/album/<?php echo $res[$i]['codice_album'] ?>?utm_source=generator"
  width="600"
  height="600"
  frameborder="0"
  allow="encrypted-media">
  </iframe>
  <?php
  }

}elseif($row['match_trovato'] == 'match_genere'){
    $codice = $row['codice_album'];
    echo "Risultati in base a: " . $row['genere'] . "<br>";
    echo "Album correlati: " . "<br>";
    ?>
    <iframe
  src="https://open.spotify.com/embed/album/<?php echo $codice ?>?utm_source=generator"
  width="600"
  height="600"
  frameborder="0"
  allow="encrypted-media">
  </iframe><br>
  <?php
    }
}

   mysqli_close($conn);
?> 
