<?php
include ("conn.php");


function test_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

$secenekler = $ad = $mail = $telNo = $konu = $mesaj = "";

// Seçenekleri veritabanından çek
$secenek_aciklamalari = [];
$ilgisi_sorgu = $conn->query("SELECT ilgi_id, ilgi_adi FROM ilgisi");
if ($ilgisi_sorgu && $ilgisi_sorgu->num_rows > 0) {
    while ($secenek = $ilgisi_sorgu->fetch_assoc()) {
        $secenek_aciklamalari[$secenek['ilgi_id']] = $secenek['ilgi_adi'];
    }
}

// Form gönderildiyse
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_gonder'])) {
    $secenek_id = test_input($_POST['secenekler1']);
    $ad_soyad = test_input($_POST['ad']);
    $mail_adr = test_input($_POST['mail']);
    $tel_no = test_input($_POST['telNo']);
    $msj_konu = test_input($_POST['konu']);
    $msj_icerik = test_input($_POST['mesaj']);

    $sql = "INSERT INTO iletisim (secenek_id, ad_soyad, mail_adr, tel_no, msj_konu, msj_icerik) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("isssss", $secenek_id, $ad_soyad, $mail_adr, $tel_no, $msj_konu, $msj_icerik);
        if ($stmt->execute()) {
            echo "<p style='background-color:rgb(58, 3, 17); font-weight:bold; color:white; padding-lheft:150px;'>Mesajınız başarıyla iletilmiştir..</p>";
        } else {
            echo "<p style='color:red; padding-left:150px;'>Hata oluştu: " . $stmt->error . "</p>";
        }
        $stmt->close();
    } else {
        echo "<p style='color:red; padding-left:150px;'>Sorgu hazırlanamadı: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html style="
  background-color: rgb(58, 3, 3);">
<head>
    <meta charset="UTF-8">
    <title>İletişim</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   
    <style>
    * {
       box-sizing: border-box;
      }
.sayfastil {
  background-color: rgb(58, 3, 3);
  padding: 10px;
  margin-right: 200px;
  margin: 10px 200px 10px 10px;
  border: 1px solid #ccc;
  color: white;
  text-align: center;
  font-size: larger;
  font-weight: bold;
  width: 300px;
}
    </style>
      
</head>
<body>

<a href="https://www.istanbuleczaciodasi.org.tr/">
    <img src="banner.jpg" alt="Logo" style="margin-left:auto; margin-right: auto; padding-left: 150px;">
</a>
<h1 style="font-family:'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif; padding-left: 150px; color:white;">Bize Ulaşın!</h1>

<form method="post" action="">
        <div style="margin: 10px; padding:10px; width: fit-content; "> 
            <div style=" width: 500px;">
    <label for="lbl_ilgi" class="sayfastil" >İlgisi:</label>
    <select id="secenekler" name="secenekler1"  class="sayfastil" required>
        <option value="">-- Seçiniz --</option>
        <?php
        foreach ($secenek_aciklamalari as $id => $aciklama) {
            $selected = ($id == $secenekler) ? "selected" : "";
            echo "<option value='$id' $selected>$aciklama</option>";
        }
        ?>
    </select><br><br>
            </div>
            <div style=" width: 500px;">
    <label for="lbl_adsoyad" class="sayfastil" >Adınız ve Soyadınız:</label>
    <input type="text" name="ad" id="ad" class="sayfastil" value="<?= $ad ?>" required><br><br>
</div>
<div style="  width: 500px;">
    <label for="lbl_eposta" class="sayfastil" >E-Posta Adresiniz:</label>
    <input type="email" name="mail" id="mail" class="sayfastil" value="<?= $mail ?>" required><br><br>
</div>
<div style=" width: 500px;">
    <label for="lbl_telNo" class="sayfastil" >Telefon Numaranız:</label>
    <input type="number" name="telNo" id="telNo" class="sayfastil" pattern="\d{10,}" value="<?= $telNo ?>" required><br><br>
    </div>
<div style=" width: 500px;">
    <label for="lbl_konu" class="sayfastil" >Mesajın Konusu:</label>
    <textarea name="konu" rows="2" id="konu" class="sayfastil" required><?= $konu ?></textarea><br>
</div>
<div style="  width: 500px;">
    <label for="lbl_mesaj" class="sayfastil" >Mesajın İçeriği:</label>
    <textarea name="mesaj" rows="5" id="mesaj" class="sayfastil" required><?= $mesaj ?></textarea><br>
</div>
    </div>

    <button type="submit" name="btn_gonder" style="margin: 100px 0 50px 50px; width: 200px; height: 50px; background-color: rgb(58, 3, 3); color:white; font-size: larger; font-weight: bold;">Gönder</button>
    <button type="button" id="resetBtn" onclick="window.location.href = window.location.pathname" style="margin: left 5px;  width: 100px; height: 50px;   background-color: rgb(58, 3, 3); color:white; font-size: larger; font-weight: bold;">Sıfırla</button> 
    <button type="submit" name="listele" style=" padding: 2px; width: 200px; height: 50px; background-color: rgb(58, 3, 3); color:white; font-size: larger; font-weight: bold;" formaction="veriler.php" formtarget="_blank" formnovalidate>
        Verileri Göster
    </button> <br>
    <input type="file" id="belgeYukle" accept=".pdf" style="margin: 0 0 50px 100px; padding:2px; width: 200px; height: 50px;  background-color: rgb(58, 3, 3); color:white; align-items: center;">
    <button type="button" id="belgeAc" style="margin: 0 0 10px 0; width: 100px; height: 50px;   background-color: rgb(58, 3, 3); color:white; font-size: larger; font-weight: bold;" onclick="showPDF()">PDF Göster</button>
    <iframe id="pdfGoster" style="display: none; width: 100%; height: 1500px; margin: 0 0 100px 100px; border: 1px solid black;   background-color: rgb(58, 3, 3); color:white; font-size: larger; font-weight: bold;"></iframe>
</form>

<script>
function showPDF() {
    const input = document.getElementById('belgeYukle');
    const file = input.files[0];
    if (file && file.type === "application/pdf") {
        const fileURL = URL.createObjectURL(file);
        const iframe = document.getElementById('pdfGoster');
        iframe.src = fileURL;
        iframe.style.display = 'block';
    }
}
</script>

</body>
</html>