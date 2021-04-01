<?php
/**
* @package NairaTeam 2015
* @author Simple  <owned by plat vipez!>
* @version 1.0
*/
if ( !isset( $_GET['go'] ) ) {
die( 'Request Failed');
}
error_reporting(0);
require ( 'getid3/getid3.php' ) ;
if( isset($_POST['submit'])){
$now = time() ;
$default_mp3_directory =  "../wp-content/uploads/";
$default_album = "@NairaTeam_NG";
$default_year = date("Y", $now);
$default_genre = "NairaTeam.com";
$default_producer = '';
$default_cover = './Nairateam.jpg';
$shownUrl = 'http://www.nairateam.com/wp-content/uploads/';
# To the real uploads

$mp3_filepath = trim($_POST['url']);
$mp3_songname = trim($_POST['title']);
$mp3_comment = "Downloaded from NairaTeam.com";;
$mp3_artist = trim($_POST['artiste']);
$artiste = $mp3_artist;
$pFilename = trim($_POST['filename']);
$mp3_filename = empty( $pFilename ) ? $mp3_artist.' - '.$mp3_songname : $pFilename;
$mp3_album = empty ( $_POST['album'] )  ? $default_album : trim ( $_POST['album'] );
$mp3_year = empty ( $_POST['year'] ) || !is_numeric($_POST['year']) || strlen($_POST['year']) != 4 ? $default_year : $_POST['year'];
$mp3_genre = empty ( $_POST['genre'] ) ? $default_genre : $_POST['genre'];
$extra = array ( 'year' => $mp3_year, 'genre' => trim($_POST['genre']), 'album' => trim($_POST['album']), 'producer' => ! empty ( $_POST['producer']) ? trim($_POST['producer']) : $default_producer );
$error = '';

# Checking the mp3

if( !filter_var($mp3_filepath, FILTER_VALIDATE_URL)){
$error .= 'Invalid File URL<br>';
} else if ( empty($mp3_filename) OR empty ($mp3_songname) OR empty($mp3_artist)){
$error .= "Fields Marked * Are Required<br>";
} else if ( !file_exists($default_cover) ) {
$error .= 'The Photo Cover Has Not Been Uploaded';
} else {
$timeFolder .= date('Y', $now) . '/' . date('m', $now) . '/';
if(!file_exists($default_mp3_directory . $timeFolder)){ mkdir($default_mp3_directory . $timeFolder, 0777, true); }
$invalidchars = array("'", '"', '.', ',');
$storeName = str_replace($invalidchars, '', $mp3_filename); // . '_NairaTeam.com.mp3';
$storeName = preg_replace('/[^A-Z-a-z0-9\-_]/', '_', $storeName);
$storeName = preg_replace("/_{2,}/", "_", $storeName);
$storeName .= '_NairaTeam.com.mp3';
$sname = $default_mp3_directory . $timeFolder . $storeName;
if (file_exists($sname) ) {
$error = 'File Has Alread Been Uploaded As <a href="' . $shownUrl . $timeFolder . $storeName . '">' . $storeName . '</a>';
} else {
if(copy($mp3_filepath, $sname)){
# Rewrite tags
$mp3_tagformat = 'UTF-8';
$mp3_handler = new getID3;
$mp3_handler->setOption(array('encoding'=>$mp3_tagformat));

# The writer class
require ( 'getid3/write.php' ) ;

$mp3_writter = new getid3_writetags;


$mp3_writter->filename       = $sname;
$mp3_writter->tagformats     = array('id3v1', 'id3v2.3');
$mp3_writter->overwrite_tags = true;
$mp3_writter->tag_encoding   = $mp3_tagformat;
$mp3_writter->remove_other_tags = true;

$mp3_data['title'][]   = $mp3_songname.' via NairaTeam.com';
$mp3_data['artist'][]  = $mp3_artist;
$mp3_data['album'][]   = $mp3_album;
$mp3_data['year'][]    = $mp3_year;
$mp3_data['genre'][]   = $mp3_genre;
$mp3_data['comment'][] = $mp3_comment;
$mp3_data['attached_picture'][0]['data'] = file_get_contents($default_cover);
$mp3_data['attached_picture'][0]['picturetypeid'] = "image/jpeg";
$mp3_data['attached_picture'][0]['description'] = "Downloaded from NairaTeam.com";
$mp3_data['attached_picture'][0]['mime'] = "image/jpeg";
$mp3_writter->tag_data = $mp3_data;

if ( $mp3_writter->WriteTags() ) {
$link = $sname ;
$shownUrl .= $timeFolder . $storeName;
} else {
unlink ( $sname );
$error .= "Failed To Write Tags!<br><br><em>" . implode( "<br><br>", $mp3_writter->errors ) . '</em><br>';
}
} else {
$error .= "Unable To Copy File please contact author 08129741578";
}
}
}
}
?>
<!DOCTYPE html>
<html>
<head>
<title>NairaTeam Music Upload Via URL</title>
<meta name="robots" content="noindex,nofollow">
<style>
body { width: 1024px; font-size: 100%; background: #DEDEDE; margin: 0 auto; text-align: center;}
.wrapper { background: #F5F5F5; padding: 50px 100px; margin-top: 50px;}
.help { font-size: 80%; font-style: italic; font-weight: normal; }
.label { font-weight: bold;}
input[type=text] { padding: 5px; width: 70%; border-radius: 3px; }
input[type=submit] { padding: 10px 40px; cursor: pointer; background-color: #3366CC; color: #FFF; font-weight: bold; border-radius: 20px;  }
input[type=submit]:hover{ background-color: #000; }
.error { padding: 30px 50px; color: #000; background-color: #FFFFCC; border: 3px solid #000;}
.successbox { padding: 30px 50px; color: #000; background-color: #ffffcc; border: 3px solid #FF00FF; font-weight: bold; }
.successbox p input[type=text] { padding: 3px; width: 50%; border-radius: 7px; }
</style>
</head>
<body>
<div class="wrapper">
<h1>Welcome To NairaTeam Upload Panel</h1>
<?php if ( isset ( $link ) && ! empty ( $link ) ) { ?>
<div class="successbox">
File Uploaded Successfully
<p class="copy"><input type="text" value="<?php echo $shownUrl; ?>"></p>
</div>
<?php } else {  ?>
<?php if ( ! empty ( $error ) ) { ?>
<div class="error"><?php echo $error; ?></div>
<?php } ?>
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
<div>
<p class="label">Link</p>
<p class="input"><input type="text" name="url" value="<?php echo $_POST['url']; ?>"><br><span class="help">*Required : URL To File(e.g http://www.mysite.com/music.mp3)</span></p>
</div>
<div>
<p class="label">Artiste</p>
<p class="input"><input type="text" name="artiste" value="<?php echo $_POST['artiste']; ?>"><br><span class="help">*Required: Artiste's Name(e.g Eminem)</span></p>
</div>
<div>
<p class="label">Title</p>
<p class="input"><input type="text" name="title" value="<?php echo $_POST['title']; ?>"><br><span class="help">Required: Track Name (e.g Crack A Bottle (ft. 50Cent & Dr Dre))</span></p>
</div>
<div>
<p class="label">Album</p>
<p class="input"><input type="text" name="album" value="<?php echo $_POST['album']; ?>"><br><span class="help">Optional</span></p>
</div>
<div>
<p class="label">Producer</p>
<p class="input"><input type="text" name="producer" value="<?php echo $_POST['producer']; ?>"><br><span class="help">Optional</span></p>
</div>
<div>
<input type="submit" name="submit" value="Upload">
</div>
</form>
<?php } ?>
</div>
</body>
</html>
