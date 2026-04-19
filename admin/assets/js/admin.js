document.addEventListener('DOMContentLoaded', () => {
  initializeSidebar();
  initializeUserDropdowns();
});
function initializeSidebar() {
  const menu = document.getElementById("menu-image");
  const sidebar = document.getElementById("sidebar");
  const menuBar = document.getElementById("menu-bar");
  const close = document.getElementById("close");
  if(menu){
    menu.addEventListener('click', (e) =>{
      if(sidebar.style.display === "flex"){
        console.log("sidebar is open")
      }else{
        close.style.display = "flex";
        menuBar.style.display = "none";
        sidebar.style.display = "flex";
        sidebar.style.width = "100%";
        sidebar.style.zIndex = "999";
      }
    })
  }
  if(close){
    close.addEventListener('click', (e) =>{
      if(sidebar.style.display === "none"){
        console.log("sidebar is close")
      }else{
        menuBar.style.display = "flex";
        sidebar.style.display = "none";
        close.style.display = "none";
        sidebar.style.width = "0";
        sidebar.style.zIndex = "0";
      }
    })
}
}
// User Dropdowns
function initializeUserDropdowns() {
  const menuBar = document.getElementById("menu-bar");
  const userDropdown = document.querySelector('.user-dropdown');
  const profileDropdownTrigger = document.querySelector('.profile-dropdown-trigger');
  
  if (userDropdown) {
    userDropdown.addEventListener('click', (e) => {
    const drop = document.querySelector(".drop");

        if (drop.style.display === "flex") {
            drop.style.display = "none";
            menuBar.style.zIndex = "20";
        } else {
            menuBar.style.zIndex = "1";
            drop.style.display = "flex";
        }
        event.stopPropagation(); // Prevents event bubbling

    // Close drop when clicking outside
    document.addEventListener("click", function (event) {
        if (!userDropdown.contains(event.target) && !drop.contains(event.target)) {
            drop.style.display = "none";
        }
      // In a real implementation, this would toggle a dropdown menu
    });
  });
}
  if (profileDropdownTrigger) {
    profileDropdownTrigger.addEventListener('click', (e) => {
    const dropdown = document.querySelector(".dropdown-menu");

        if (dropdown.style.display === "flex") {
            dropdown.style.display = "none";
        } else {
            dropdown.style.display = "flex";
        }
        event.stopPropagation(); // Prevents event bubbling

    // Close dropdown when clicking outside
    document.addEventListener("click", function (event) {
        if (!profileDropdownTrigger.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.style.display = "none";
        }
    });

      console.log('Profile dropdown clicked');
    });
  }
}

 // Open Funds Modal
        function openFundsModal(type) {
            const modal = document.getElementById("fundsModal");
            const title = document.getElementById("fundsModalTitle");
            
            if (type === "deposit") {
                title.textContent = "Deposit Funds";
            } else {
                title.textContent = "Withdraw Funds";
            }

            modal.style.display = "flex";
        }

        // Close Modal
        function closeFundsModal() {
            document.getElementById("fundsModal").style.display = "none";
        }

        function toggleSidebar() {
            sidebar.classList.toggle('open');
            backdrop.classList.toggle('open');
        }

        function closeSidebar() {
            sidebar.classList.remove('open');
            backdrop.classList.remove('open');
        }

        // Open Transfer Modal
function openTransferModal() {
    document.getElementById("transferModal").style.display = "flex";
}

// Close Transfer Modal
function closeTransferModal() {
    document.getElementById("transferModal").style.display = "none";
}
function switchTab(type) {
    // Update hidden input value
    document.getElementById("transferType").value = type;

    // Hide external fields by default
    document.getElementById("externalFields").style.display = "none";

    // Update the form based on the selected tab
    if (type === "internal") {
        document.getElementById("toAccountLabel").innerText = "To Account";
        document.getElementById("receiverEmail").style.display = "none";
        document.getElementById("toAccount").style.display = "block";
    } else if (type === "user") {
        document.getElementById("toAccountLabel").innerText = "Recipient's Email";
        document.getElementById("receiverEmail").style.display = "block";
        document.getElementById("toAccount").style.display = "none";
    } else if (type === "external") {
        document.getElementById("toAccountLabel").innerText = "External Bank Transfer";
        document.getElementById("receiverEmail").style.display = "none";
        document.getElementById("toAccount").style.display = "none";
        document.getElementById("externalFields").style.display = "block";
    }

    // Update active tab styling
    document.querySelectorAll(".tab").forEach(btn => btn.classList.remove("active"));
    document.querySelector(`.tab[onclick="switchTab('${type}')"]`).classList.add("active");
}
       