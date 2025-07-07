<?php
include("conn.php");

// Silme işlemi
if (isset($_POST['sil_id'])) {
    $id = intval($_POST['sil_id']);
    $conn->query("DELETE FROM iletisim WHERE id = $id");
}

// Güncelleme işlemi
if (isset($_POST['guncelle_id'])) {
    $id = intval($_POST['guncelle_id']);
    $ad_soyad = $_POST['ad_soyad'];
    $mail_adr = $_POST['mail_adr'];
    $tel_no = $_POST['tel_no'];
    $msj_konu = $_POST['msj_konu'];
    $msj_icerik = $_POST['msj_icerik'];
    $secenek_id = intval($_POST['secenek_id']);
    $guncelleme_tarihi = date('Y-m-d H:i:s');

    $guncelle_sorgu = $conn->query("UPDATE iletisim SET 
        ad_soyad='$ad_soyad', 
        mail_adr='$mail_adr', 
        tel_no='$tel_no', 
        msj_konu='$msj_konu', 
        msj_icerik='$msj_icerik',
        secenek_id=$secenek_id,
        guncelleme_tarihi='$guncelleme_tarihi'
        WHERE id=$id");

    if ($guncelle_sorgu) {
        echo "<script>
            alert('Veri başarıyla güncellendi.');
            window.location.href = 'veriler.php';
        </script>";
        exit;
    } else {
        echo "<p style='color:red;'>Güncelleme sırasında hata oluştu.</p>";
    }
}

// İlgisi tablosunu çek
$secenek_aciklamalari = [];
$ilgisi_sorgu = $conn->query("SELECT ilgi_id, ilgi_adi FROM ilgisi");
if ($ilgisi_sorgu && $ilgisi_sorgu->num_rows > 0) {
    while ($secenek = $ilgisi_sorgu->fetch_assoc()) {
        $secenek_aciklamalari[$secenek['ilgi_id']] = $secenek['ilgi_adi'];
    }
}
?> 

<!DOCTYPE html>
<html>
<head>
    <title>Veriler</title>
    <style>
        body {
            background-color: rgb(11, 58, 17);
            color: white;
            font-family: Arial, sans-serif;
        }

        form {
            background-color: black;
            padding: 15px;
            width: 500px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        textarea,
        select {
            border: 1px dotted white;
            background-color: rgb(21,40,4);
            color: white;
            padding: 5px;
            border-radius: 4px;
            width: 500px;
            margin: 5px 0;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: white;
            color: black;
            font-weight: bold;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }

        .readonly-input {
            display: block;
            margin: 5px 0;
             width: 500px;
            color: white;
            background-color: rgb(21,40,4);
            border: 1px solid white;
            padding: 5px;
            border-radius: 4px;
        }

        .butonlar {
            margin-top: 10px;
            
        }

        .kayitlar-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
         }

        .kayitli-veriler {
            margin-bottom: 30px;
        }

        h2 {
            color: white;
        }
    </style>
</head>
<body>

<?php
// Güncelleme formu
if (isset($_GET['guncelle'])) {
    $id = intval($_GET['guncelle']);
    $result = $conn->query("SELECT * FROM iletisim WHERE id = $id");

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "<h2>Mesaj Güncelle</h2>";
        echo "<form method='post'>";
        echo "<input type='hidden' name='guncelle_id' value='" . $row['id'] . "'>";
        echo "Ad Soyad: <input type='text' name='ad_soyad' value='" . htmlspecialchars($row['ad_soyad']) . "'><br>";
        echo "E-posta: <input type='text' name='mail_adr' value='" . htmlspecialchars($row['mail_adr']) . "'><br>";
        echo "Telefon: <input type='text' name='tel_no' value='" . htmlspecialchars($row['tel_no']) . "'><br>";
        echo "Konu: <input type='text' name='msj_konu' value='" . htmlspecialchars($row['msj_konu']) . "'><br>";
        echo "Mesaj: <textarea name='msj_icerik'>" . htmlspecialchars($row['msj_icerik']) . "</textarea><br>";

        echo "İlgisi: <select name='secenek_id'>";
        foreach ($secenek_aciklamalari as $sec_id => $aciklama) {
            $selected = ($sec_id == $row['secenek_id']) ? "selected" : "";
            echo "<option value='$sec_id' $selected>$aciklama</option>";
        }
        echo "</select><br>";

        echo '<input type="submit" value="Kaydet">';
        echo "</form>";
    } else {
        echo "<p>Mesaj bulunamadı.</p>";
    }
}

// Kayıtları listele
if (!isset($_GET['guncelle'])) {
    $sql = "SELECT id, secenek_id, ad_soyad, mail_adr, tel_no, msj_konu, msj_icerik, tarih, guncelleme_tarihi FROM iletisim";
    $result = $conn->query($sql);}
   

if ($result->num_rows > 0) {
            echo "<h2>Kayıtlı Mesajlar:</h2>";

    while ($row = $result->fetch_assoc()) {
        $secenek_aciklama = $secenek_aciklamalari[$row["secenek_id"]] ?? "Tanımsız";
        $id = $row["id"];

        echo '<div class="kayitli-veriler">';
        echo '<input class="readonly-input" type="text" value="İlgisi: ' . htmlspecialchars($secenek_aciklama) . '" readonly>';
        echo '<input class="readonly-input" type="text" value="Ad Soyad: ' . htmlspecialchars($row["ad_soyad"]) . '" readonly>';
        echo '<input class="readonly-input" type="text" value="E-posta: ' . htmlspecialchars($row["mail_adr"]) . '" readonly>';
        echo '<input class="readonly-input" type="text" value="Telefon: ' . htmlspecialchars($row["tel_no"]) . '" readonly>';
        echo '<input class="readonly-input" type="text" value="Konu: ' . htmlspecialchars($row["msj_konu"]) . '" readonly>';
        echo '<textarea class="readonly-input" rows="3" readonly>' . htmlspecialchars($row["msj_icerik"]) . '</textarea>';
        echo '<input class="readonly-input" type="text" value="Tarih: ' . htmlspecialchars($row['tarih']) . '" readonly>';
        if (!empty($row['guncelleme_tarihi'])) {
            echo '<input class="readonly-input" type="text" value="Güncelleme Tarihi: ' . htmlspecialchars($row['guncelleme_tarihi']) . '" readonly>';
        }

         echo '<form method="get" style="display:inline;">
                <input type="hidden" name="guncelle" value="' . $id . '">
                <input type="submit" style="background-color:black; color:white;" value="Güncelle">
              </form>';

        echo '<form method="post" style="display:inline;" onsubmit="return confirm(\'Bu mesajı silmek istediğinize emin misiniz?\');">
                <input type="hidden" name="sil_id" value="' . $id . '">
                <input type="submit" style="background-color:black; color:white;" value="Sil">
              </form>';
        echo '</div>';
            echo '</div>';
    }
} else {
    echo "<p>Veri bulunamadı.</p>";
}

$conn->close();
?>
</body>
</html>