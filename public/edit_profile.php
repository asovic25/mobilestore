<?php
// public/edit_profile.php
session_start();
require_once '../inc/db.php';
require_once '../inc/functions.php';
require_once __DIR__ . '/../inc/config.php';
include __DIR__ . '/../inc/head.php';
require_once __DIR__ . '/../inc/bootstrap.php';

if (!isset($_SESSION['user']['id'])) {
    header("Location: user_login.php");
    exit;
}

$userId = $_SESSION['user']['id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_destroy();
    header("Location: user_login.php");
    exit;
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $state = $_POST['state'] ?? '';
    $lga = $_POST['lga'] ?? '';
    $address = trim($_POST['address'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '') $errors[] = 'Username is required.';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email required.';

    $avatar_filename = $user['avatar'] ?? null;
    if (!empty($_FILES['avatar']['name'])) {
        $avatar = $_FILES['avatar'];
        if ($avatar['error'] === UPLOAD_ERR_OK) {
            $allowed = ['png','jpg','jpeg','webp'];
            $ext = strtolower(pathinfo($avatar['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) {
                $errors[] = 'Avatar must be png, jpg, jpeg, or webp.';
            } elseif ($avatar['size'] > 2*1024*1024) {
                $errors[] = 'Avatar must be less than 2MB.';
            } else {
                $avatarsDir = __DIR__.'/uploads/avatars/';
                if (!is_dir($avatarsDir)) mkdir($avatarsDir,0777,true);
                $avatar_filename = 'avatar_'.time().'_'.bin2hex(random_bytes(4)).'.'.$ext;
                $targetPath = $avatarsDir.$avatar_filename;
                if (!move_uploaded_file($avatar['tmp_name'], $targetPath)) {
                    $errors[] = 'Failed to save uploaded avatar.';
                }
            }
        } else {
            $errors[] = 'Avatar upload failed.';
        }
    }

    if (empty($errors)) {
        $fields = "username=?, email=?, phone=?, state=?, lga=?, address=?, avatar=?";
        $params = [$username, $email, $phone, $state, $lga, $address, $avatar_filename];

        if ($password !== '') {
            $fields .= ", password=?";
            $params[] = password_hash($password, PASSWORD_DEFAULT);
        }
        $params[] = $userId;

        $stmt = $pdo->prepare("UPDATE users SET $fields WHERE id=?");
        $stmt->execute($params);

        $_SESSION['user']['username'] = $username;
        $_SESSION['user']['avatar'] = $avatar_filename;

        $success = 'Profile updated successfully.';
    }
}

// ✅ Fetch all states & LGAs for dropdown
$statesLGA = [
    "Abia" => ["Aba North","Aba South","Arochukwu","Bende","Ikwuano","Isiala Ngwa North","Isiala Ngwa South","Isuikwuato","Obi Ngwa","Ohafia","Osisioma","Ugwunagbo","Ukwa East","Ukwa West","Umuahia North","Umuahia South","Umu Nneochi"],
    "Adamawa" => ["Demsa","Fufore","Ganye","Girei","Gombi","Guyuk","Hong","Jada","Lamurde","Madagali","Maiha","Mayo-Belwa","Michika","Mubi North","Mubi South","Numan","Shelleng","Song","Yola North","Yola South"],
    "Akwa Ibom" => ["Abak","Eastern Obolo","Eket","Esit Eket","Essien Udim","Etim Ekpo","Etinan","Ibeno","Ibesikpo Asutan","Ibiono-Ibom","Ika","Ikono","Ikot Abasi","Ikot Ekpene","Ini","Itu","Mbo","Mkpat-Enin","Nsit-Atai","Nsit-Ibom","Nsit-Ubium","Obot Akara","Okobo","Onna","Oron","Oruk Anam","Udung-Uko","Ukanafun","Uruan","Urue-Offong/Oruko","Uyo"],
    "Anambra" => ["Aguata","Awka North","Awka South","Anambra East","Anambra West","Anaocha","Ayamelum","Dunukofia","Ekwusigo","Idemili North","Idemili South","Ihiala","Njikoka","Nnewi North","Nnewi South","Ogbaru","Onitsha North","Onitsha South","Orumba North","Orumba South","Oyi"],
    "Bauchi" => ["Alkaleri","Bauchi","Bogoro","Damban","Darazo","Dass","Gamawa","Ganjuwa","Giade","Itas/Gadau","Jama'are","Katagum","Kirfi","Misau","Ningi","Shira","Tafawa Balewa","Toro","Warji","Zaki"],
    "Bayelsa" => ["Brass","Ekeremor","Kolokuma/Opokuma","Nembe","Ogbia","Sagbama","Southern Jaw","Yenagoa"],
    "Benue" => ["Ado","Agatu","Apa","Buruku","Gboko","Guma","Gwer East","Gwer West","Katsina-Ala","Konshisha","Kwande","Logo","Makurdi","Obi","Ogbadibo","Ohimini","Oju","Okpokwu","Otukpo","Tarka","Ukum","Ushongo","Vandeikya"],
    "Borno" => ["Abadam","Askira/Uba","Bama","Bayo","Biu","Chibok","Damboa","Dikwa","Gubio","Guzamala","Gwoza","Hawul","Jere","Kaga","Kala/Balge","Konduga","Kukawa","Kwaya Kusar","Mafa","Magumeri","Maiduguri","Marte","Mobbar","Monguno","Ngala","Nganzai","Shani"],
    "Cross River" => ["Akpabuyo","Odukpani","Akamkpa","Biase","Abi","Ikom","Yarkur","Etung","Boki","Calabar South","Calabar Municipality","Obanliku","Obubra","Obudu","Bekwara","Bakassi"],
    "Delta" => ["Oshimili North","Oshimili South","Aniocha North","Aniocha South","Ika North East","Ika South","Ndokwa East","Ndokwa West","Isoko North","Isoko South","Bomadi","Burutu","Ughelli North","Ughelli South","Ethiope East","Ethiope West","Sapele","Okpe","Warri North","Warri South","Warri South West","Udu","Ukwuani","Oshimili"],
    "Ebonyi" => ["Abakaliki","Afikpo North","Afikpo South","Ebonyi","Ezza North","Ezza South","Ikwo","Ishielu","Ivo","Izzi","Ohaozara","Ohaukwu","Onicha"],
    "Edo" => ["Akoko-Edo","Egor","Esan Central","Esan North-East","Esan South-East","Esan West","Etsako Central","Etsako East","Etsako West","Igueben","Ikpoba Okha","Oredo","Orhionmwon","Ovia North-East","Ovia South-West","Owan East","Owan West","Uhunmwonde"],
    "Ekiti" => ["Ado Ekiti","Efon","Ekiti East","Ekiti South-West","Ekiti West","Emure","Gbonyin","Ido-Osi","Ijero","Ikere","Ikole","Ilejemeje","Irepodun/Ifelodun","Ise/Orun","Moba","Oye"],
    "Enugu" => ["Aninri","Awgu","Enugu East","Enugu North","Enugu South","Ezeagu","Igbo Etiti","Igbo Eze North","Igbo Eze South","Isi Uzo","Nkanu East","Nkanu West","Nsukka","Oji River","Udenu","Udi","Uzo-Uwani"],
    "Gombe" => ["Akko","Balanga","Billiri","Dukku","Kaltungo","Kwami","Nafada/Bajoga","Shongom","Funakaye","Gombe"],
    "Imo" => ["Aboh Mbaise","Ahiazu Mbaise","Ehime Mbano","Ezinihitte","Ideato North","Ideato South","Ihitte/Uboma","Ikeduru","Isiala Mbano","Isu","Mbaitoli","Ngor Okpala","Njaba","Nkwerre","Nwangele","Obowo","Oguta","Ohaji/Egbema","Okigwe","Onuimo","Orlu","Orsu","Oru East","Oru West","Owerri Municipal","Owerri North","Owerri West"],
    "Jigawa" => ["Auyo","Babura","Biriniwa","Birnin Kudu","Buji","Dutse","Gagarawa","Garki","Gumel","Guri","Gwaram","Gwiwa","Hadejia","Jahun","Kafin Hausa","Kaugama","Kazaure","Kiri Kasama","Kiyawa","Maigatari","Malam Madori","Miga","Ringim","Roni","Sule Tankarkar","Taura","Yankwashi"],
    "Kaduna" => ["Birnin Gwari","Chikun","Giwa","Igabi","Ikara","Jaba","Jema'a","Kachia","Kaduna North","Kaduna South","Kagarko","Kajuru","Kaura","Kauru","Kubau","Kudan","Lere","Makarfi","Sabon Gari","Sanga","Soba","Zangon Kataf","Zaria"],
    "Kano" => ["Ajingi","Albasu","Bagwai","Bebeji","Bichi","Bunkure","Dala","Dambatta","Dawakin Kudu","Dawakin Tofa","Doguwa","Fagge","Gabasawa","Garko","Garun Mallam","Gaya","Gezawa","Gwale","Gwarzo","Kabo","Kano Municipal","Karaye","Kibiya","Kiru","Kumbotso","Kunchi","Kura","Madobi","Makoda","Minjibir","Nasarawa","Rano","Rimin Gado","Rogo","Shanono","Sumaila","Takai","Tarauni","Tofa","Tsanyawa","Tudun Wada","Ungogo","Warawa","Wudil"],
    "Katsina" => ["Bakori","Batagarawa","Batsari","Baure","Bindawa","Charanchi","Dandume","Danja","Daura","Dutsi","Dutsin Ma","Faskari","Funtua","Ingawa","Jibia","Kafur","Kaita","Kankara","Kankia","Kasarawa","Katsina","Kurfi","Kusada","Mai'Adua","Malumfashi","Mani","Mashi","Matazu","Musawa","Rimi","Sabuwa","Safana","Sandamu","Zango"],
    "Kebbi" => ["Aleiro","Arewa Dandi","Argungu","Augie","Bagudo","Birnin Kebbi","Bunza","Dandi","Fakai","Gwandu","Jega","Kalgo","Koko/Besse","Maiyama","Ngaski","Sakaba","Shanga","Suru","Wasagu/Danko","Yauri","Zuru"],
    "Kogi" => ["Adavi","Ajaokuta","Ankpa","Bassa","Dekina","Ibaji","Idah","Igalamela/Odolu","Ijumu","Kabba/Bunu","Koton Karfe","Lokoja","Mopa-Muro","Ofu","Ogori/Magongo","Okehi","Okene","Olamaboro","Omala","Yagba East","Yagba West"],
    "Kwara" => ["Asa","Baruten","Edu","Ekiti","Ifelodun","Ilorin East","Ilorin South","Ilorin West","Irepodun","Isin","Kaiama","Moro","Offa","Oke Ero","Oyun","Pategi"],
    "Lagos" => ["Agege","Ajeromi-Ifelodun","Alimosho","Amuwo-Odofin","Apapa","Badagry","Epe","Eti-Osa","Ibeju-Lekki","Ifako-Ijaiye","Ikeja","Ikorodu","Kosofe","Lagos Island","Lagos Mainland","Mushin","Ojo","Oshodi-Isolo","Shomolu","Surulere"],
    "Nasarawa" => ["Akwanga","Awe","Doma","Karu","Keana","Keffi","Kokona","Lafia","Nasarawa","Nasarawa Eggon","Obi","Toto","Wamba"],
    "Niger" => ["Agaie","Agwara","Bida","Borgu","Bosso","Chanchaga","Edati","Gbako","Gurara","Katcha","Kontagora","Lapai","Lavun","Mokwa","Moshegu","Munya","Paikoro","Rafi","Rijau","Shiroro","Suleja","Tafa","Wushishi"],
    "Ogun" => ["Abeokuta North","Abeokuta South","Ado-Odo/Ota","Egbado North","Egbado South","Ewekoro","Ifo","Ijebu East","Ijebu North","Ijebu North East","Ijebu Ode","Ikenne","Imeko Afon","Ipokia","Obafemi Owode","Odeda","Odogbolu","Remo North","Shagamu"],
    "Ondo" => ["Akoko North-East","Akoko North-West","Akoko South-East","Akoko South-West","Akure North","Akure South","Ese Odo","Idanre","Ifedore","Ilaje","Ile Oluji/Okeigbo","Irele","Odigbo","Okitipupa","Ondo East","Ondo West","Ose","Owo"],
    "Osun" => ["Aiyedade","Aiyedire","Atakumosa East","Atakumosa West","Boluwaduro","Boripe","Ede North","Ede South","Egbedore","Ejigbo","Ife Central","Ife East","Ife North","Ife South","Ifedayo","Ifelodun","Ila","Ilesa East","Ilesa West","Irepodun","Irewole","Isokan","Iwo","Obokun","Odo-Otin","Ola-Oluwa","Olorunda","Oriade","Orolu","Osogbo"],
    "Oyo" => ["Afijio","Akinyele","Atiba","Atisbo","Egbeda","Ibadan North","Ibadan North-East","Ibadan North-West","Ibadan South-East","Ibadan South-West","Ibarapa Central","Ibarapa East","Ibarapa North","Ido","Irepo","Iseyin","Itesiwaju","Iwajowa","Kajola","Lagelu","Ogbomosho North","Ogbomosho South","Ogo Oluwa","Olorunsogo","Oluyole","Ona Ara","Orelope","Ori Ire","Oyo East","Oyo West","Saki East","Saki West","Surulere"],
    "Plateau" => ["Barikin Ladi","Bokkos","Barkin Ladi","Bukuru","Jos East","Jos North","Jos South","Kanam","Kanke","Langtang North","Langtang South","Mangu","Mikang","Pankshin","Qua'an Pan","Riyom","Shendam","Wase"],
    "Rivers" => ["Abua/Odual","Ahoada East","Ahoada West","Akuku Toru","Andoni","Asari-Toru","Bonny","Degema","Eleme","Emuoha","Etche","Gokana","Ikwerre","Khana","Obio/Akpor","Ogba/Egbema/Ndoni","Ogu/Bolo","Okrika","Omuma","Opobo/Nkoro","Oyigbo","Port Harcourt","Tai"],
    "Sokoto" => ["Binji","Bodinga","Dange Shuni","Gada","Goronyo","Gudu","Gwadabawa","Illela","Isa","Kebbe","Kware","Rabah","Sabon Birni","Shagari","Silame","Sokoto North","Sokoto South","Tambuwal","Tangaza","Tureta","Wamako","Wurno","Yabo"],
    "Taraba" => ["Ardo Kola","Bali","Donga","Gashaka","Gassol","Ibi","Jalingo","Karim Lamido","Kumi","Lau","Sardauna","Takum","Ussa","Wukari","Yorro","Zing"],
    "Yobe" => ["Bade","Bursari","Damaturu","Fika","Fune","Geidam","Gujba","Gulani","Jakusko","Karasuwa","Machina","Nangere","Nguru","Potiskum","Tarmuwa","Yunusari","Yusufari"],
    "Zamfara" => ["Anka","Bakura","Birnin Magaji/Kiyaw","Bukkuyum","Bungudu","Gummi","Gusau","Kaura Namoda","Maradun","Shinkafi","Talata Mafara","Chafe","Zurmi"]
];
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Profile | Rose Store</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
:root {--primary:#6A1B9A;--accent:#E91E63;--secondary:#F3E5F5;--white:#fff;}
body {background: var(--secondary); font-family:'Poppins',sans-serif;}
.card {border-radius:12px; box-shadow:0 5px 20px rgba(0,0,0,0.1);}
.form-label {font-weight:500;}
.btn-rose {background-color: var(--primary); color: #fff; font-weight:600;}
.btn-rose:hover {background-color:#5A137F; color: var(--accent);}
</style>
<script>
const statesLGA = <?= json_encode($statesLGA) ?>;
function updateLGAs(){
    const state = document.getElementById('state').value;
    const lgaSelect = document.getElementById('lga');
    lgaSelect.innerHTML = '';
    (statesLGA[state] || []).forEach(l=>{
        const opt = document.createElement('option');
        opt.value = l; opt.text = l;
        if(l==='<?= addslashes($user['lga'] ?? '') ?>') opt.selected=true;
        lgaSelect.appendChild(opt);
    });
}
function togglePassword(){
    const pass = document.getElementById('password');
    pass.type = pass.type==='password'?'text':'password';
}
</script>
<?php include __DIR__ . '/../inc/header.php'; ?>
</head>
<body>
<div class="container py-5">
<div class="card p-4 mx-auto" style="max-width:600px;">
<h3 class="text-center mb-4" style="color:var(--primary)">Edit Profile 🌹</h3>

<?php if($errors): ?>
<div class="alert alert-danger"><ul><?php foreach($errors as $e) echo "<li>".htmlspecialchars($e)."</li>";?></ul></div>
<?php endif; ?>
<?php if($success): ?>
<div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
<div class="text-center mb-3">
<img src="uploads/avatars/<?= htmlspecialchars($user['avatar'] ?? 'default.png') ?>" 
     alt="Avatar" class="rounded-circle" width="100" height="100">
<p class="text-muted small mt-2">Current profile picture</p>
</div>

<div class="mb-3">
<label class="form-label">Username</label>
<input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>">
</div>

<div class="mb-3">
<label class="form-label">Email</label>
<input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>">
</div>

<div class="mb-3">
<label class="form-label">Phone</label>
<input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
</div>

<div class="row">
<div class="col-md-6 mb-3">
<label class="form-label">State</label>
<select name="state" id="state" class="form-select" onchange="updateLGAs()">
<option value="">Select State</option>
<?php foreach($statesLGA as $stateName => $lgas): ?>
<option value="<?= $stateName ?>" <?= ($user['state'] ?? '')==$stateName?'selected':'' ?>><?= $stateName ?></option>
<?php endforeach; ?>
</select>
</div>
<div class="col-md-6 mb-3">
<label class="form-label">LGA</label>
<select name="lga" id="lga" class="form-select"></select>
</div>
</div>

<div class="mb-3">
<label class="form-label">Address</label>
<input type="text" name="address" class="form-control" value="<?= htmlspecialchars($user['address'] ?? '') ?>">
</div>

<div class="mb-3">
<label class="form-label">Password (leave blank to keep current)</label>
<div class="input-group">
<input type="password" id="password" name="password" class="form-control">
<button type="button" class="btn btn-outline-secondary" onclick="togglePassword()">👁️</button>
</div>
</div>

<div class="mb-3">
<label class="form-label">Profile Image (optional)</label>
<input type="file" name="avatar" class="form-control">
</div>

<div class="d-grid">
<button class="btn btn-rose">Update Profile</button>
<a href="user_dashboard.php" class="btn btn-outline-secondary mt-2">Back to Dashboard</a>
</div>
</form>
</div>
</div>
<script>updateLGAs();</script>
<?php include __DIR__ . '/../inc/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>