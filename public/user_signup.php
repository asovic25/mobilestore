<?php
session_start();
require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/config.php';
include __DIR__ . '/../inc/head.php';
require_once __DIR__ . '/../inc/bootstrap.php';
include __DIR__ . '/../inc/header.php'; // centralized header

$errors = [];
$success = '';
// Embedded Nigerian states and their LGAs
$states_and_lgas = [
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


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die('Invalid CSRF token');
    }

    $role = in_array($_POST['role'] ?? 'buyer',['buyer','seller']) ? $_POST['role'] : 'buyer';
    $full_name = trim($_POST['full_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $address = trim($_POST['address'] ?? '');
    $state = $_POST['state'] ?? '';
    $lga = $_POST['lga'] ?? '';
    $phone = trim($_POST['phone'] ?? '');

    if ($full_name === '') $errors[] = 'Full name required';
    if ($username === '') $errors[] = 'Username required';
    if ($email === '' || !filter_var($email,FILTER_VALIDATE_EMAIL)) $errors[]='Valid email required';
    if (strlen($password)<8) $errors[]='Password must be at least 8 characters';
    if ($password!==$confirm_password) $errors[]='Passwords do not match';
    if ($state==='') $errors[]='Select a state';
    if ($lga==='') $errors[]='Select a local government';

    $avatar_filename = null;
    if (!empty($_FILES['avatar']['name'])) {
        $avatar = $_FILES['avatar'];
        if ($avatar['error']===UPLOAD_ERR_OK) {
            $allowed=['png','jpg','jpeg','webp'];
            $ext=strtolower(pathinfo($avatar['name'],PATHINFO_EXTENSION));
            if (!in_array($ext,$allowed)) $errors[]='Avatar must be an image';
            if ($avatar['size']>2*1024*1024) $errors[]='Avatar < 2MB';
        } else $errors[]='Avatar upload failed';
    }

    if (empty($errors)) {
        $stmt=$pdo->prepare("SELECT id FROM users WHERE email=? OR username=?");
        $stmt->execute([$email,$username]);
        if ($stmt->fetch()) $errors[]='Email or username already in use';
    }

 if(empty($errors)) {
    if(!empty($avatar)&&$avatar['error']===UPLOAD_ERR_OK){
        $avatarsDir=dirname(__DIR__).'/public/uploads/avatars/';
        if(!is_dir($avatarsDir)) mkdir($avatarsDir,0777,true);
        $avatar_filename='avatar_'.time().'_'.bin2hex(random_bytes(4)).'.'.$ext;
        move_uploaded_file($avatar['tmp_name'],$avatarsDir.$avatar_filename);
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $insert = $pdo->prepare("INSERT INTO users (full_name, username, email, password, phone, address, state, lga, role)
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $insert->execute([$full_name,$username,$email,$hash,$phone,$address,$state,$lga,$role]);

    // Redirect immediately BEFORE any HTML output
    header("Location: user_login.php");
    exit;  // important to stop further execution
}

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign Up | Rose Store</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
/* === Rose Store Color Palette === */
:root{
  --primary:#6A1B9A;
  --accent:#E91E63;
  --secondary:#F3E5F5;
  --rose-light:#f6d9ff;
  --text-dark:#1c0033;
  --white:#fff;
}

/* Body & Typography */
body {
  background-color: var(--secondary);
  font-family: "Poppins", sans-serif;
  color: var(--text-dark);
  margin:0; padding:0;
}

/* Navbar */
.navbar {
  background-color: var(--primary) !important;
}
.navbar-brand, .nav-link {
  color: #fff !important;
  font-weight: 500;
}
.nav-link:hover { color: var(--accent) !important; }

/* Hero Section */
.hero{
  background:linear-gradient(135deg,var(--primary),var(--accent));
  color:#fff;
  text-align:center;
  padding:70px 20px;
  border-radius:0 0 32px 32px;
}

.hero h1 { font-size: 2.8rem; margin-bottom: 15px; }
.hero p { font-size: 1.2rem; }

/* Form */
.form-control, .form-select {
  background-color: #fff;
  color: var(--text-dark);
  border: 1px solid #ccc;
}
.btn-rose {
  background-color: var(--accent);
  color: #fff;
  border: none;
}
.btn-rose:hover { background-color: #c2185b; }
.password-toggle {
  cursor: pointer;
  position: absolute;
  top: 50%;
  right: 12px;           /* adjust distance from right */
  transform: translateY(-50%);
  color: var(--accent);
  font-size: 1.2rem;
  z-index: 2;
}

.password-toggle i {
  pointer-events: none;
}


</style>
</head>

<body>

<!-- HERO -->
<section class="hero">
  <h1>Join the Rose Store Family 🌹</h1>
  <p>Become a buyer or seller and enjoy an elegant shopping experience.</p>
</section>

<!-- SIGNUP FORM -->
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
      <form method="post" enctype="multipart/form-data" class="p-4 rounded shadow-lg bg-white">
        <input type="hidden" name="csrf_token" value="<?= csrf_token(); ?>">

        <div class="text-center mb-3">
          <h3>Create Account</h3>
          <p>Already have an account? <a href="user_login.php" class="text-danger text-decoration-none">Login</a></p>
        </div>

        <?php if(!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach($errors as $err) echo htmlspecialchars($err).'<br>'; ?>
            </div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <div class="mb-3">
          <label class="form-label">I am a</label>
          <select name="role" class="form-select">
            <option value="buyer">Buyer</option>
            <option value="seller">Seller</option>
          </select>
        </div>

        <div class="mb-3"><label class="form-label">Full Name</label>
          <input name="full_name" class="form-control" required></div>

        <div class="mb-3"><label class="form-label">Username</label>
          <input name="username" class="form-control" required></div>

        <div class="mb-3"><label class="form-label">Email</label>
          <input name="email" type="email" class="form-control" required></div>

       <div class="mb-3 position-relative">
  <label class="form-label">Password</label>
  <input name="password" type="password" id="password" class="form-control" required>
  <span class="password-toggle"><i class="bi bi-eye"></i></span>
</div>

<div class="mb-3 position-relative">
  <label class="form-label">Confirm Password</label>
  <input name="confirm_password" type="password" id="confirm_password" class="form-control" required>
  <span class="password-toggle"><i class="bi bi-eye"></i></span>
</div>


        <div class="mb-3"><label class="form-label">Phone</label>
          <input name="phone" class="form-control" required></div>

        <div class="mb-3"><label class="form-label">Address</label>
          <input name="address" class="form-control" required></div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">State</label>
            <select name="state" id="state" class="form-select" required>
              <option value="">--Select State--</option>
              <?php foreach($states_and_lgas as $state_name => $lgas): ?>
                <option value="<?= htmlspecialchars($state_name) ?>"><?= htmlspecialchars($state_name) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Local Government</label>
            <select name="lga" id="lga" class="form-select" required>
              <option value="">--Select LGA--</option>
            </select>
          </div>
        </div>

        <div class="mb-3"><label class="form-label">Profile Image (optional)</label>
          <input name="avatar" type="file" accept="image/*" class="form-control"></div>

        <button class="btn btn-rose w-100 py-2">Create Account</button>
        <div class="text-center mt-3"><a href="index.php" class="text-dark text-decoration-none">← Back to Main Site</a></div>
      </form>
    </div>
  </div>
</div>
<script>
// Password toggle function
function togglePassword(id, icon) {
    const field = document.getElementById(id);
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}

// Add click listeners to all password toggle icons
document.querySelectorAll('.password-toggle').forEach(toggle => {
    toggle.addEventListener('click', () => {
        const inputId = toggle.previousElementSibling.id;
        const icon = toggle.querySelector('i');
        togglePassword(inputId, icon);
    });
});

// Populate LGAs dynamically
const statesAndLgas = <?= json_encode($states_and_lgas) ?>;
const stateSelect = document.getElementById('state');
const lgaSelect = document.getElementById('lga');

stateSelect.addEventListener('change', () => {
    const selectedState = stateSelect.value;
    lgaSelect.innerHTML = '<option value="">--Select LGA--</option>';
    if (selectedState && statesAndLgas[selectedState]) {
        statesAndLgas[selectedState].forEach(lga => {
            const opt = document.createElement('option');
            opt.value = lga;
            opt.textContent = lga;
            lgaSelect.appendChild(opt);
        });
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php include __DIR__ . '/../inc/footer.php'; ?>