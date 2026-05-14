function toggleForms() {
  const loginForm = document.getElementById("login-form");
  const registerForm = document.getElementById("register-form");

  if (loginForm.style.display === "none") {
    loginForm.style.display = "block";
    registerForm.style.display = "none";
  } else {
    loginForm.style.display = "none";
    registerForm.style.display = "block";
  }
}

function toggleStoreInput() {
  const storeOption = document.querySelector(
    'input[name="store_option"]:checked'
  ).value;
  const existingStoreGroup = document.getElementById("existing-store-group");
  const newStoreGroup = document.getElementById("new-store-group");
  const storeSelect = document.getElementById("store-select");
  const customStoreName = document.getElementById("custom-store-name");

  if (storeOption === "existing") {
    existingStoreGroup.style.display = "block";
    newStoreGroup.style.display = "none";
    storeSelect.required = true;
    customStoreName.required = false;
  } else {
    existingStoreGroup.style.display = "none";
    newStoreGroup.style.display = "block";
    storeSelect.required = false;
    customStoreName.required = true;
  }
}

// Load stores from database
function loadStores() {
  fetch("php/get_stores.php")
    .then((response) => response.json())
    .then((data) => {
      const storeSelect = document.getElementById("store-select");
      data.forEach((store) => {
        const option = document.createElement("option");
        option.value = store.store_id;
        option.textContent = store.store_name;
        storeSelect.appendChild(option);
      });
    })
    .catch((error) => console.error("Error loading stores:", error));
}

// Load provinces
function loadProvinces() {
  fetch("php/get_provinces.php")
    .then((response) => response.json())
    .then((data) => {
      const provinceSelect = document.getElementById("province");
      data.forEach((province) => {
        const option = document.createElement("option");
        option.value = province.prov_id;
        option.textContent = province.prov_name;
        provinceSelect.appendChild(option);
      });
    })
    .catch((error) => console.error("Error loading provinces:", error));
}

// Load municipalities based on province
document.addEventListener("DOMContentLoaded", function () {
  const provinceSelect = document.getElementById("province");
  const municipalitySelect = document.getElementById("municipality");
  const barangaySelect = document.getElementById("barangay");

  provinceSelect.addEventListener("change", function () {
    const provinceId = this.value;
    municipalitySelect.innerHTML =
      '<option value="">-- Select Municipality --</option>';
    barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
    barangaySelect.disabled = true;

    if (provinceId) {
      fetch(`php/get_municipalities.php?province_id=${provinceId}`)
        .then((response) => response.json())
        .then((data) => {
          data.forEach((municipality) => {
            const option = document.createElement("option");
            option.value = municipality.mun_id;
            option.textContent = municipality.mun_name;
            municipalitySelect.appendChild(option);
          });
          municipalitySelect.disabled = false;
        })
        .catch((error) =>
          console.error("Error loading municipalities:", error)
        );
    } else {
      municipalitySelect.disabled = true;
    }
  });

  municipalitySelect.addEventListener("change", function () {
    const municipalityId = this.value;
    barangaySelect.innerHTML =
      '<option value="">-- Select Barangay --</option>';

    if (municipalityId) {
      fetch(`php/get_barangays.php?municipality_id=${municipalityId}`)
        .then((response) => response.json())
        .then((data) => {
          data.forEach((barangay) => {
            const option = document.createElement("option");
            option.value = barangay.brgy_id;
            option.textContent = barangay.brgy_name;
            barangaySelect.appendChild(option);
          });
          barangaySelect.disabled = false;
        })
        .catch((error) => console.error("Error loading barangays:", error));
    } else {
      barangaySelect.disabled = true;
    }
  });
});

// Load stores when page loads
window.addEventListener("load", loadStores);
window.addEventListener("load", loadProvinces);

// Display messages if they exist in URL
const params = new URLSearchParams(window.location.search);
const message = params.get("message");
const error = params.get("error");

if (message) {
  const loginMsg = document.getElementById("login-message");
  loginMsg.textContent = message;
  loginMsg.classList.add("success");
}
if (error) {
  const form =
    params.get("form") === "register" ? "register-form" : "login-form";
  const msgDiv =
    form === "register-form"
      ? document.getElementById("register-message")
      : document.getElementById("login-message");
  msgDiv.textContent = error;
  msgDiv.classList.add("error");
  if (form === "register-form") {
    document.getElementById("login-form").style.display = "none";
    document.getElementById("register-form").style.display = "block";
  }
}
