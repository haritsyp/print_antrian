<?php
require __DIR__ . './vendor/autoload.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: HEAD, GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: x-socket-id,x-nt-key,X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");

if (isset($_POST['queue_no'])) {
    $logo = $_POST['logo'] ?? '';
    $company_name = $_POST['company_name'] ?? '';
    $address = $_POST['address'] ?? '';
    $created_at = $_POST['created_at'] ?? '';
    $queue_no = $_POST['queue_no'] ?? '';
    $website = $_POST['website'] ?? '';
} else {
    echo json_encode(['Gagal Print']);
    exit;
}

$headers = array();
foreach ($_SERVER as $key => $value) {
    if (strpos($key, 'HTTP_') === 0) {
        $headers[str_replace(' ', '', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))))] = $value;
    }
}

$config = json_decode(file_get_contents('config.json'), true);

if ($config['key'] != $headers['XNtKey']) {
    echo json_encode(['Gagal Print']);
    exit;
}

list($type, $logo) = explode(';', $logo);
list(, $logo) = explode(',', $logo);
$data = base64_decode($logo);
file_put_contents('tmp/image.png', $data);
$data = resize_image('tmp/image.png', 140, 140);
imagepng($data, 'tmp/image.png');
$tux = EscposImage::load("tmp/image.png", true);

/* Fill in your own connector here */
$connector = new WindowsPrintConnector("antrian_printer");
$printer = new Printer($connector);

$printer->initialize();
$printer->setJustification(Printer::JUSTIFY_CENTER); // Setting teks menjadi rata tengah
$printer->bitImage($tux);

// Membuat judul
$printer->initialize();
$printer->selectPrintMode(Printer::MODE_DOUBLE_HEIGHT); // Setting teks menjadi lebih besar
$printer->setJustification(Printer::JUSTIFY_CENTER); // Setting teks menjadi rata tengah
$printer->text("$company_name\n");

$printer->initialize();
$printer->setJustification(Printer::JUSTIFY_CENTER); // Setting teks menjadi rata tengah
$printer->text("$address\n");
$printer->text("----------------------------------------\n");
$printer->text("NOMOR ANTRIAN ANDA : \n\n");

$printer->initialize();
$printer->setTextSize(6, 6); // Setting teks menjadi lebih besar
$printer->setJustification(Printer::JUSTIFY_CENTER); // Setting teks menjadi rata tengah
$printer->text("$queue_no\n");
$printer->text("\n");


// Pesan penutup
$printer->initialize();
$printer->setJustification(Printer::JUSTIFY_CENTER);
$printer->text("Dibuat Tanggal : " . date('Y-m-d H:i:s') . "\n");
$printer->text("----------------------------------------\n");
$printer->text("Terima kasih\n");
$printer->text("$website\n");

$printer->feed(2); // mencetak 2 baris kosong, agar kertas terangkat ke atas

/* Cut the receipt and open the cash drawer */
$printer->cut();
$printer->pulse();

$printer->close();


/**
 * Resize image given a height and width and return raw image data.
 *
 * Note : You can add more supported image formats adding more parameters to the switch statement.
 *
 * @param type $file filepath
 * @param type $w width in px
 * @param type $h height in px
 * @param type $crop Crop or not
 * @return type
 */
function resize_image($file, $w, $h, $crop = false)
{
    list($width, $height) = getimagesize($file);
    $r = $width / $height;
    if ($crop) {
        if ($width > $height) {
            $width = ceil($width - ($width * abs($r - $w / $h)));
        } else {
            $height = ceil($height - ($height * abs($r - $w / $h)));
        }
        $newwidth = $w;
        $newheight = $h;
    } else {
        if ($w / $h > $r) {
            $newwidth = $h * $r;
            $newheight = $h;
        } else {
            $newheight = $w / $r;
            $newwidth = $w;
        }
    }

    //Get file extension
    $exploding = explode(".", $file);
    $ext = explode('/', mime_content_type($file))[1];
    switch ($ext) {
        case "png":
            $src = imagecreatefrompng($file);
            break;
        case "jpeg":
            $src = imagecreatefromjpeg($file);
            break;
        case "jpg":
            $src = imagecreatefromjpeg($file);
            break;
        case "gif":
            $src = imagecreatefromgif($file);
            break;
        default:
            $src = imagecreatefromjpeg($file);
            break;
    }

    $dst = imagecreate($newwidth, $newheight);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

    return $dst;
}